<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.mergado.cz
 * @since      1.0.0
 *
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/admin
 */

use Mergado\Tools\ImportPricesClass;
use Mergado\Tools\RssClass;
use Mergado\Tools\Settings;
use Mergado\Tools\XML\EanClass;
use Mergado\Tools\XMLCategoryFeed;
use Mergado\Tools\XMLClass;
use Mergado\Tools\XMLProductFeed;
use Mergado\Tools\XMLStockFeed;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/admin
 * @author     Mergado technologies, s. r. o. <info@mergado.cz>
 */

require_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Imported because of is_plugin_active usage

if (!is_plugin_active( 'woocommerce/woocommerce.php')) {
    deactivate_plugins(plugin_basename( __MERGADO_BASE_FILE__ ));
    die(WOOCOMMERCE_DEPENCENCY_MESSAGE);
} else {
    if (!defined('WC_ABSPATH')) {
        define('WC_ABSPATH', dirname(__FILE__) . '/../../woocommerce/');
    }
    include_once( WC_ABSPATH . '/includes/export/class-wc-product-csv-exporter.php' );
    if (!class_exists('WC_CSV_Batch_Exporter', false)) {
        include_once( WC_ABSPATH . '/includes/export/abstract-wc-csv-batch-exporter.php' );
    }
    if (!class_exists('WC_CSV_Exporter', false)) {
        include_once( WC_ABSPATH . '/includes/export/abstract-wc-csv-exporter.php' );
    }
}

include_once __MERGADO_DIR__ . 'autoload.php';


class Megrado_export extends WC_Product_CSV_Exporter {

    public function generate_data($start = 0, $limit = null) {

        if($limit === NULL) {
            $limit = 9999999;
        }

        $this->set_page($start);
        $this->set_limit($limit);
        $this->prepare_data_to_export();
        return $this->row_data;
    }

}

class Mergado_Marketing_Pack_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;


        self::init_hooks();
    }


    public static function init_hooks() {
        add_action('admin_menu', array('Mergado_Marketing_Pack_Admin', 'admin_menu'));

		add_action('init', function () {
			$cronRss = new RssClass();
			$cronRss->getFeed();
		});
    }

    public static function admin_menu() {
        self::load_menu();
    }

    public static function load_menu() {
    	$alertClass = new AlertClass();
	    $errors = $alertClass->getMergadoErrors();

	    if ($errors['total'] == 0) {
	    	$mergadoMainText = __('Mergado Pack', 'mergado-marketing-pack');
	    } else {
	    	$mergadoMainText = sprintf(__('Mergado <span class="awaiting-mod">%d</span>', 'mergado-marketing-pack'), $errors['total']);
	    }

	    if ($errors['product'] == 0) {
	    	$productItemText =  __('Product feeds', 'mergado-marketing-pack');
	    } else {
	    	$productItemText = sprintf(__('Product feeds <span class="awaiting-mod">%d</span>', 'mergado-marketing-pack'), $errors['product']);
	    }

	    if ($errors['other'] == 0) {
		    $otherItemText =  __('Other feeds', 'mergado-marketing-pack');
	    } else {
		    $otherItemText = sprintf(__('Other feeds <span class="awaiting-mod">%d</span>', 'mergado-marketing-pack'), $errors['other']);
	    }

	    // Add main menu pages
	    add_menu_page(
		    __('Mergado Pack', 'mergado-marketing-pack'),
		    $mergadoMainText,
		    'manage_options',
		    'mergado-config',
            array('Mergado_Marketing_Pack_Admin', 'display'),
            plugins_url('mergado-marketing-pack/admin/img/') . 'rsz_mergado_pack_logo_menu.png',
            58);

        // Add page to woocomerce
        add_submenu_page('woocommerce', __('Mergado Pack', 'mergado-marketing-pack'),
            $mergadoMainText,
	        'manage_options', 'mergado-config', array('Mergado_Marketing_Pack_Admin', 'display'));

        // Add submenu pages
        add_submenu_page('mergado-config', __('Product feeds', 'mergado-marketing-pack'),
	        $productItemText,
	        'manage_options', 'mergado-feeds-product', array('Mergado_Marketing_Pack_Admin', 'display'));
        add_submenu_page('mergado-config', __('Other feeds', 'mergado-marketing-pack'),
	        $otherItemText,
	        'manage_options', 'mergado-feeds-other', array('Mergado_Marketing_Pack_Admin', 'display'));
        add_submenu_page('mergado-config', __('Ad Systems', 'mergado-marketing-pack'), __('Ad Systems', 'mergado-marketing-pack'), 'manage_options', 'mergado-adsys', array('Mergado_Marketing_Pack_Admin', 'display'));
        add_submenu_page('mergado-config',
            __('Cookies', 'mergado-marketing-pack'),
            __('Cookies', 'mergado-marketing-pack'),
            'manage_options',
            'mergado-cookies',
            array('Mergado_Marketing_Pack_Admin', 'display'));

        add_submenu_page('mergado-config', __('News', 'mergado-marketing-pack'), __('News', 'mergado-marketing-pack'), 'manage_options', 'mergado-news', array('Mergado_Marketing_Pack_Admin', 'display'));
        add_submenu_page('mergado-config', __('Support', 'mergado-marketing-pack'), __('Support', 'mergado-marketing-pack'), 'manage_options', 'mergado-support', array('Mergado_Marketing_Pack_Admin', 'display'));
        add_submenu_page('mergado-config', __('Licence', 'mergado-marketing-pack'), __('Licence', 'mergado-marketing-pack'), 'manage_options', 'mergado-licence', array('Mergado_Marketing_Pack_Admin', 'display'));
    }

    public static function display() {
        global $wp;
        $token = self::getToken();
        $currentBlogId = Settings::getCurrentBlogId();

        if (isset($_GET['feed'])) {
            $feed = $_GET['feed'];
        } else {
            $feed = '';
        }

        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if (isset($_GET['action'])) {
                if (isset($_GET['token']) && $_GET['token'] = $token && $token != '' && $token != NULL) {
                    $action = $_GET['action'];
                    switch ($action) {
                        case 'downloadFeed':
                            switch ($feed) {
                                case 'product':
                                    $xmlProductFeed = new XMLProductFeed();
                                    $file = $xmlProductFeed->getFeedPath();
                                    break;
                                case 'category':
                                    $xmlCategoryFeed = new XMLCategoryFeed();
                                    $file = $xmlCategoryFeed->getFeedPath();
                                    break;
                                case 'stock':
                                    $xmlStockFeed = new XMLStockFeed();
                                    $file = $xmlStockFeed->getFeedPath();
                                    break;
                            }

                            if (isset($file)) {
                                XMLClass::download($page, $file);
                            }
                            break;
                        case 'deleteFeed':
                            switch ($feed) {
                                case 'product':
                                    $xmlClass = new XMLProductFeed();
                                    $file = $xmlClass->getFeedPath();
                                    $redirectUrl = 'admin.php?page=' . 'mergado-feeds-product&flash=productDeleted&mmp-tab=product';
                                    break;
                                case 'category':
                                    $xmlClass = new XMLCategoryFeed();
                                    $file = $xmlClass->getFeedPath();
                                    $redirectUrl = 'admin.php?page=' . 'mergado-feeds-other&flash=categoryDeleted&mmp-tab=category';
                                    break;
                                case 'stock':
                                    $xmlClass = new XMLStockFeed();
                                    $file = $xmlClass->getFeedPath();
                                    $redirectUrl = 'admin.php?page=' . 'mergado-feeds-other&flash=stockDeleted&mmp-tab=stock';
                                    break;
                            }

                            if (isset($xmlClass) && isset($file) && isset($redirectUrl)) {
                                unlink($file);
                                $xmlClass->setFeedCount(0);
                                $xmlClass->deleteTemporaryFiles();
                                $alertClass = new AlertClass();
                                $alertClass->setErrorInactive($feed, AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION']);
                                wp_redirect($redirectUrl);
                            }
                            break;
                        default:
                            exit;
                    }
                } else {
                    wp_redirect('admin.php?page=' . $page);
                }
            } else {
                switch ($page) {
                    case 'mergado-settings':
                        require_once ( __DIR__ . '/templates/template-mergado-marketing-pack-display-settings.php' );
                        break;
                    case 'mergado-config':
                        require_once ( __DIR__ . '/templates/template-mergado-marketing-pack-display-info.php' );
                        break;
                    case 'mergado-cron':
                        require_once ( __DIR__ . '/templates/template-mergado-marketing-pack-display-cron.php' );
                        break;
                    case 'mergado-feed':
                        require_once ( __DIR__ . '/templates/template-mergado-marketing-pack-display-feed.php' );
                        break;
                    case 'mergado-licence':
                        require_once ( __DIR__ . '/templates/template-mergado-marketing-pack-display-licence.php' );
                        break;
                    case 'mergado-support':
                        require_once ( __DIR__ . '/templates/template-mergado-marketing-pack-display-support.php' );
                        break;
                    case 'mergado-adsys':
                        require_once ( __DIR__ . '/templates/template-mergado-marketing-pack-display-adsys.php' );
                        break;
                    case 'mergado-news':
                        require_once ( __DIR__ . '/templates/template-mergado-marketing-pack-display-news.php' );
                        break;
	                case 'mergado-cookies':
		                require_once ( __DIR__ . '/templates/template-mergado-marketing-pack-display-adsys.php' );
		                break;
                    case 'mergado-feeds-product':
                        require_once ( __DIR__ . '/templates/template-mergado-marketing-pack-display-feeds-product.php' );
                        break;
                    case 'mergado-feeds-other':
                        require_once ( __DIR__ . '/templates/template-mergado-marketing-pack-display-feeds-other.php' );
                        break;
                    default:
                        exit;
                        break;
                }
            }
        } else {
            die;
        }
    }

    /*******************************************************************************************************************
     * ENQUEUE
     *******************************************************************************************************************/

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Mergado_Marketing_Pack_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Mergado_Marketing_Pack_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/mergado-marketing-pack-admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'yesno', plugin_dir_url(__FILE__) . 'vendors/yesno/src/index.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Mergado_Marketing_Pack_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Mergado_Marketing_Pack_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . 'tabs', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-tabs.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . 'wizard', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-wizard.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . 'iframe', plugin_dir_url(__FILE__) . 'vendors/iframe-resizer/js/iframeResizer.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . 'iframe-enabler', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-iframe.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . 'dropdownBox', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-dropdown.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . 'popper', plugin_dir_url(__FILE__) . 'vendors/tippy/popper.min.js', false, $this->version, true);
        wp_enqueue_script($this->plugin_name . 'tooltip', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-tooltip.js', false, $this->version, true);
        wp_enqueue_script($this->plugin_name . 'tippy', plugin_dir_url(__FILE__) . 'vendors/tippy/tippy-bundle.umd.min.js', false, $this->version, true);
        wp_enqueue_script($this->plugin_name . 'yesno', plugin_dir_url(__FILE__) . 'vendors/yesno/src/index.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . 'cron-estimate', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-cron-estimate.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . 'alerts', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-alerts.js', array('jquery'), $this->version, true);
    }

    public function setDefaultEan() {
        if ( is_admin() && get_option( 'mmp_plugin_ean_default_set', 0 ) == 0 ) {

            try {
                // if plugin option not set before
                if (get_option( EanClass::EAN_PLUGIN, 'neverSelected') === 'neverSelected') {
                    EanClass::getDefaultEanAfterInstalation();
                }

                update_option( 'mmp_plugin_ean_default_set', 1 );
            } catch(Exception $e) {
                // Not important
            }
        }
    }

    /*******************************************************************************************************************
     * TOKEN
     *******************************************************************************************************************/

    public static function generateToken() {
        $token = sha1(get_site_url() . 'MMP2017WP' . uniqid());
        update_option('mmp_token', $token);
        return $token;
    }

    public static function getToken() {
        $token = get_option('mmp_token');

        if ($token === NULL || $token === '' || !$token) {
            $token = self::generateToken();
        }

        return $token;
    }

    public static function deleteToken() {
        delete_option('mmp_token');
    }

    /*******************************************************************************************************************
     * CREATE DIR
     *******************************************************************************************************************/

    public static function createDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    public static function checkAndCreateTmpDataDir()
    {
        if (is_dir(__MERGADO_MMP_UPLOAD_DIR__)) {
            mkdir(__MERGADO_MMP_UPLOAD_DIR__);
        }

        if(!is_dir(__MERGADO_TMP_DIR__)) {
            mkdir(__MERGADO_TMP_DIR__);
        }

        if(!is_dir(__MERGADO_XML_DIR__)) {
            mkdir(__MERGADO_XML_DIR__);
        }
    }
}
