<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.mergado.cz
 * @since             1.0.0
 * @package           Mergado_Marketing_Pack
 *
 * @wordpress-plugin
 * Plugin Name:       Mergado marketing pack
 * Plugin URI:        https://www.mergado.cz
 * Description:       Earn more on price comparator sites. <strong>REQUIRES: Woocommerce</strong>
 * Version:           3.1.1
 * Author:            Mergado technologies, s. r. o.
 * Author URI:        https://www.mergado.cz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mergado-marketing-pack
 * Domain Path:       /languages
 *
 * WC requires at least: 3.0
 * WC tested up to: 5.5.2
 */
// If this file is called directly, abort.
use Mergado\Tools\CronRunningException;
use Mergado\Tools\ImportPricesClass;
use Mergado\Tools\Languages;
use Mergado\Tools\NewsClass;
use Mergado\Tools\Settings;
use Mergado\Tools\XMLCategoryFeed;
use Mergado\Tools\XMLClass;
use Mergado\Tools\XMLProductFeed;
use Mergado\Tools\XMLStockFeed;

if (!defined('WPINC')) {
    die;
}

define('PLUGIN_VERSION', '3.1.1');
define('WOOCOMMERCE_DEPENCENCY_MESSAGE', __('Mergado Pack plugin requires <a href="/wp-admin/plugin-install.php?tab=plugin-information&plugin=woocommerce" target="_top">WooCommerce</a> plugin to be active!', 'mergado-marketing-pack'));
define( '__MERGADO_DIR__', plugin_dir_path( __FILE__ ) );
define( '__MERGADO_BASE_FILE__', plugin_dir_path( __FILE__ ) . 'mergado-marketing-pack.php' );
define( '__MERGADO_MMP_UPLOAD_DIR__', wp_get_upload_dir()['basedir'] . '/mmp/' );
define( '__MERGADO_TMP_DIR__', wp_get_upload_dir()['basedir'] . '/mergado/tmp/' );
define( '__MERGADO_XML_DIR__', wp_get_upload_dir()['basedir'] . '/mergado/data/' );
define( '__MERGADO_XML_URL__', wp_get_upload_dir()['baseurl'] . '/mergado/data/' );
define( '__MERGADO_SERVICES_DIR__', __MERGADO_DIR__ . 'includes/services/' );
define( '__MERGADO_TOOLS_DIR__', __MERGADO_DIR__ . 'includes/tools/');
define( '__MERGADO_TOOLS_XML_DIR__', __MERGADO_DIR__ . 'includes/tools/XML/');
define( '__MERGADO_UPDATES_DIR__', __MERGADO_DIR__ . 'updates/');
define( '__MERGADO_ADMIN_IMAGES_DIR__', __MERGADO_DIR__ . 'admin/img/');
define( '__MERGADO_TEMPLATE_DIR__', __MERGADO_DIR__ . 'admin/templates/partials/');
define( '__MERGADO_TEMPLATE_COMPONENTS_DIR__', __MERGADO_DIR__ . 'admin/templates/partials/components/');
define( '__MERGADO_API_DIR__', __MERGADO_DIR__ . 'includes/api/');

include_once __MERGADO_DIR__ . 'autoload.php';

function plugin_name_load_plugin_textdomain() {
    $domain = 'mergado-marketing-pack';

    $locale = Languages::getLocale();

    $locale = apply_filters( 'plugin_locale', $locale, $domain );

    unload_textdomain( $domain );
    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
    load_plugin_textdomain( $domain, false, plugin_basename( dirname( WC_PLUGIN_FILE ) ) . '/i18n/languages' );

    // Called always .. transient me if i am making problems
    $alertClass = new AlertClass();
    $alertClass->checkIfErrorsShouldBeActive();
}
add_action('init', 'plugin_name_load_plugin_textdomain');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mergado-marketing-pack-activator.php
 */
function activate_mergado_marketing_pack() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-mergado-marketing-pack-activator.php';
    Mergado_Marketing_Pack_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mergado-marketing-pack-deactivator.php
 */
function deactivate_mergado_marketing_pack() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-mergado-marketing-pack-deactivator.php';
    Mergado_Marketing_Pack_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_mergado_marketing_pack');
register_deactivation_hook(__FILE__, 'deactivate_mergado_marketing_pack');

// Direction browser CRON actions
add_action('wp_loaded', function() {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $token = isset($_GET['token']) ? $_GET['token'] : '';

    if ($action !== '' && $token !== '' && add_query_arg(array('action' => NULL, 'token' => NULL)) === '/mergado/') {
        if ($token != Mergado_Marketing_Pack_Admin::getToken()) {
            echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: red; margin-right: 8px;"></span>';
            _e('ERROR: Invalid token', 'mergado-marketing-pack');
            exit;
        } else {
            // Disable Jetpack image url transformation (Jetpack_Photon) if exists and cron called
            if (is_plugin_active( 'jetpack/jetpack.php') && class_exists('Jetpack_Photon')) {
                remove_filter('image_downsize', [Jetpack_Photon::instance(), 'filter_image_downsize'], 10);
            }
        }

        try {
            switch ($action) {
                case 'productCron':
                    $xmlProductFeed = new XMLProductFeed();
                    $xmlProductFeed->cron('');
                    echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: green; margin-right: 8px;"></span>';
                    _e('SUCCESS: Product feed generated', 'mergado-marketing-pack');
                    exit;

                case 'stockCron':
                    $xmlStockFeed = new XMLStockFeed();
                    $result = $xmlStockFeed->generateStockXML('');

                    echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: green; margin-right: 8px;"></span>';
                    _e('SUCCESS: Heureka availability feed generated', 'mergado-marketing-pack');
                    exit;
                case 'categoryCron':
                    $xmlCategoryFeed = new XMLCategoryFeed();
                    $xmlCategoryFeed->generateCategoryXML('');
                    echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: green; margin-right: 8px;"></span>';
                    _e('SUCCESS: Category feed generated', 'mergado-marketing-pack');
                    exit;
                case 'importPrices':
                    $importPrices = new ImportPricesClass();
                    $result = $importPrices->importPrices('');

                    if($result) {
                        echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: green; margin-right: 8px;"></span>';
                        _e('SUCCESS: Mergado prices imported', 'mergado-marketing-pack');
                    } else {
                        echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: red; margin-right: 8px;"></span>';
                        _e('ERROR: Error importing prices. Do you have correct URL in settings?', 'mergado-marketing-pack');
                    }
                    exit;
            }
        } catch (CronRunningException $e) {
            echo __('The cron is probably already running. Please try again later.');
            exit;
        } catch (Exception $e) {
            echo '<h2>' . __('An error occurred during cron run.') . '</h2><br>';
            echo '<br>';
            echo '<strong>' . __('If your problem persist, send following error to our support with your message.') . '</strong><br>';
            echo '<div class="mmpErrorCode" style="border: 1px solid black; padding: 13px; background-color: #ffffec; width: 1000px; max-width: 100%;">' . $e . '</div>';
            exit;
        }
    }
});

/** Plugin Name: Add Admin Bar Icon */
add_action( 'admin_bar_menu', function( \WP_Admin_Bar $bar )
{
    $iconspan = '<span class="mergado-custom-icon"></span>';
    $title = '';

    $bar->add_menu( array(
        'id'     => 'wpse',
        'title'  => $iconspan.$title,
        'href'   => admin_url('admin.php?page=mergado-news&showNews=true'),
        'meta'   => array(
            'target'   => '_self',
            'html'     => '<!-- Custom HTML that goes below the item -->',
        ),
    ) );
}, 510 ); // <-- THIS INTEGER DECIDES WHERE THE ITEM GETS ADDED (Low = left, High = right)
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-mergado-marketing-pack.php';


/*******************************************************************************************************************
 * API - AJAX
 *******************************************************************************************************************/

include_once __MERGADO_API_DIR__ . 'ScheduleEstimate.php';
include_once __MERGADO_API_DIR__ . 'Alerts.php';
include_once __MERGADO_API_DIR__ . 'CronSettings.php';
include_once __MERGADO_API_DIR__ . 'FeedGeneration.php';
include_once __MERGADO_API_DIR__ . 'Wizard.php';
include_once __MERGADO_API_DIR__ . 'Cookies.php';
include_once __MERGADO_API_DIR__ . 'News.php';

function dashboardNews() {
    global $pagenow;
    if ($pagenow !== 'index.php') {
        return;
    }

    $unreadedNews = NewsClass::getNewsByStatusAndLanguageAndCategory(0, Languages::getLocale(), '', 3, false, 'DESC');
    $token = Mergado_Marketing_Pack_Admin::getToken();
    $newsCookie = get_option(Settings::COOKIE_NEWS);
    $now = new DateTime();
    $now = $now->format(NewsClass::DATE_FORMAT);


    if($newsCookie <= $now){
        if($unreadedNews && $unreadedNews != []){
            echo '<div class="wrap">';
            echo '<div class="mergado-updated-notice news">';
            echo '<div class="mmp-news__holder">';
                foreach($unreadedNews as $item){
                    echo '<a href="/wp-admin/admin.php?page=mergado-news" class="mergado-link mmp-news__item">';
                    echo '<p class="mmp-news__title">'.$item->title.'</p>';
                    echo '<p><span class="mmp-badge mmp-badge--'.$item->category.'">'.$item->category.'</span> <span class="mmp-news__date">' . NewsClass::getFormattedDate($item->pubDate) . '</span></p></a>';
                }
                echo '</div>';
                echo '<span data-cookie="mmp-cookie-news" data-token="'.$token.'" class="mmp-cross mmp-close-cross">🞩</span>';
                echo '</div>';
                echo '</div>';
        }
    }
}
add_action( 'admin_notices', 'dashboardNews' );

/*******************************************************************************************************************
 * WP_CRON - ADD CUSTOM MERGADO SCHEDULE
 *******************************************************************************************************************/

add_filter( 'cron_schedules', 'quarterhour_schedule' );
function quarterhour_schedule( $schedules ) {
    $schedules['quarterhour'] = array(
        'interval' => 15 * 60, //15 minutes * 60 seconds
        'display' => __( 'Every 15 minutes', 'mergado-marketing-pack' ),
    );

    return $schedules;
}

add_filter('cron_schedules','tenminutes_schedule');
function tenminutes_schedule($schedules){
    if(!isset($schedules["10min"])){
        $schedules["10min"] = array(
            'interval' => 10 * 60,
            'display' => __('Once every 10 minutes'));
    }

    return $schedules;
}

/*******************************************************************************************************************
 * WP_CRON - MERGADO ACTIONS
 *******************************************************************************************************************/

function cron_products_action() {
    $logger = wc_get_logger();
    $logger->info('========= WP_CRON: Products start ========');
    $xmlProductFeed = new XMLProductFeed();
    $xmlProductFeed->cron('');
    $logger->info('========= WP_CRON: Products end ========');
}
add_action('wp-cron-product-feed-hook', 'cron_products_action');

function cron_stock_action() {
    $logger = wc_get_logger();
    $logger->info('========= WP_CRON: Stock start ========');
    $xmlStockFeed = new XMLStockFeed();
    $xmlStockFeed->generateStockXML('');
    $logger->info('========= WP_CRON: Stock end ========');
}
add_action('wp-cron-stock-feed-hook', 'cron_stock_action');

function cron_category_action() {
    $logger = wc_get_logger();
    $logger->info('========= WP_CRON: Category start ========');
    $xmlCategoryFeed = new XMLCategoryFeed();
    $xmlCategoryFeed->generateCategoryXML('');
    $logger->info('========= WP_CRON: Category end ========');
}
add_action('wp-cron-category-feed-hook', 'cron_category_action');

function cron_import_action() {
    $logger = wc_get_logger();
    $logger->info('========= WP_CRON: Import start ========');
    $importPrices = new ImportPricesClass();
    $importPrices->importPrices('');
    $logger->info('========= WP_CRON: Import end ========');
}
add_action('wp-cron-import-feed-hook', 'cron_import_action');


/**
 * REGISTER CRON TO CHECK IF NEW PLUGIN VERSIONS AVAILABLE EVERY 5 MINUTES
 */

if(!wp_next_scheduled('schedule_update_hook')){
    wp_schedule_event(time(), '10min', 'schedule_update_hook');
}
// Add schedule
add_action('schedule_update_hook', 'schedule_update_watcher');
function schedule_update_watcher(){
    wp_update_plugins();
}

function my_plugin_action_links( $links ) {

    $links = array_merge( array(
        '<a href="' . esc_url( admin_url( 'admin.php?page=mergado-config' ) ) . '">' . __( 'Get started', 'textdomain' ) . '</a>',
        '<a href="' . esc_url( admin_url( 'admin.php?page=mergado-support' ) ) . '">' . __( 'Support', 'textdomain' ) . '</a>',
        '<a style="color: #7FBA2C !important; font-weight: 500;" href="' . esc_url( 'https://pack.mergado.com/woocommerce/?utm_source=mp&utm_medium=link&utm_campaign=official_webiste' ) . '">' . __( 'Official website', 'textdomain' ) . '</a>',
    ), $links );

    return $links;

}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'my_plugin_action_links' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mergado_marketing_pack() {
    $plugin = new Mergado_Marketing_Pack();
    $plugin->run();
}

run_mergado_marketing_pack();
