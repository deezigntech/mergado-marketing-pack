<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.mergado.cz
 * @since      1.0.0
 *
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/includes
 */


use Mergado\Arukereso\ArukeresoService;
use Mergado\Facebook\FacebookService;
use Mergado\Glami\GlamiPixelService;
use Mergado\Glami\GlamiTopService;
use Mergado\Google\GoogleAdsService;
use Mergado\Google\GoogleAnalyticsRefundService;
use Mergado\Google\GoogleReviewsService;
use Mergado\Google\GoogleTagManagerService;
use Mergado\Heureka\HeurekaService;
use Mergado\NajNakup\NajNakup;
use Mergado\Pricemania\Pricemania;
use Mergado\Tools\CookieClass;
use Mergado\Tools\Settings;
use Mergado\Tools\Languages;
use Mergado\Zbozi\Zbozi;
use Mergado\Zbozi\ZboziService;
use Mergado\Etarget\EtargetServiceIntegration;
use Mergado\Kelkoo\KelkooServiceIntegration;

include_once __MERGADO_DIR__ . 'autoload.php';

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/includes
 * @author     Mergado technologies, s. r. o. <info@mergado.cz>
 */
class Mergado_Marketing_Pack
{
    const TESTING = false; // Set true to enable unlimited ORDER SUMMARY event sending / Turn off for production

    // Languages
    const LANG_CS = 'cs';
    const LANG_SK = 'sk';
    const LANG_EN = 'en';
    const LANG_PL = 'pl';

    const LANG_AVAILABLE = array(
        self::LANG_EN,
        self::LANG_CS,
        self::LANG_SK,
    );

    const MERGADO_UPDATE = 'mergado_db_version';

    const TABLE_NEWS_NAME = 'mergado_news';

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Mergado_Marketing_Pack_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    //Data that should be shown in footer after adding to cart
    protected $headerExtra = '';

    // Service classes
	/**
	 * @var ArukeresoService
	 */
    private $arukeresoService;

	/**
	 * @var GoogleAdsService
	 */
    private $googleAdsService;

	/**
	 * @var GoogleTagManagerService
	 */
    private $googleTagManagerService;

	/**
	 * @var GlamiPixelService
	 */
    private $glamiPixelService;

	/**
	 * @var GlamiTopService
	 */
    private $glamiTopService;

	/**
	 * @var FacebookService
	 */
    private $facebookService;

	/**
	 * @var EtargetServiceIntegration
	 */
    private $etargetServiceIntegration;

	/**
	 * @var KelkooServiceIntegration
	 */
    private $kelkooServiceIntegration;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('PLUGIN_VERSION')) {
            $this->version = PLUGIN_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'mergado-marketing-pack';


        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        $this->updateDatabase();

        // Services inited
        $this->arukeresoService = new Mergado\Arukereso\ArukeresoService();
        $this->googleAdsService = new Mergado\Google\GoogleAdsService();
        $this->googleTagManagerService = new Mergado\Google\GoogleTagManagerService();
        $this->glamiPixelService = new Mergado\Glami\GlamiPixelService();
        $this->glamiTopService = new Mergado\Glami\GlamiTopService();
        $this->facebookService = new FacebookService();

        // Integrations
        $this->etargetServiceIntegration = new EtargetServiceIntegration();
        $this->kelkooServiceIntegration = new KelkooServiceIntegration();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Mergado_Marketing_Pack_Loader. Orchestrates the hooks of the plugin.
     * - Mergado_Marketing_Pack_i18n. Defines internationalization functionality.
     * - Mergado_Marketing_Pack_Admin. Defines all hooks for the admin area.
     * - Mergado_Marketing_Pack_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-mergado-marketing-pack-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-mergado-marketing-pack-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-mergado-marketing-pack-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mergado-marketing-pack-public.php';

        $this->loader = new Mergado_Marketing_Pack_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Mergado_Marketing_Pack_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Mergado_Marketing_Pack_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Mergado_Marketing_Pack_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_init', $plugin_admin, 'setDefaultEan');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Mergado_Marketing_Pack_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
        $this->processFrontView();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Mergado_Marketing_Pack_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     *  Update database
     * - IF YOU ARE ADDING NEW TABLE! ADD IT ALSO TO FUNCTION checkIfAllTablesExists() that is executed during plugin activation process
     * - ALSO ADD CHECK TO UPDATE IF TABLE EXIST! (like in v2.0.0 update)
     */
    private function updateDatabase()
    {
        //if (!get_option(self::MERGADO_UPDATE) && PLUGIN_VERSION > '1.2.0') {
            include_once __MERGADO_UPDATES_DIR__ . 'v2.0.0.php';
            //update_option(Mergado_Marketing_Pack::MERGADO_UPDATE, '2.0.0');
        //}
            // Next updates will be like this:
            // if(get_option(self::MERGADO_UPDATE) === '2.0.0') {}
            // and so on...

        if($this->get_version() === '2.1.5' && (get_option(self::MERGADO_UPDATE) == false || get_option(self::MERGADO_UPDATE) < '2.1.5')) {
            include_once __MERGADO_UPDATES_DIR__ . 'v2.1.5.php';
            $this->updateDbVersionMultisite('2.1.5');
        }

        if($this->get_version() === '2.3.0' && (get_option(self::MERGADO_UPDATE) == false || get_option(self::MERGADO_UPDATE) < '2.3.0')) {
            include_once __MERGADO_UPDATES_DIR__ . 'v2.3.0.php';
            $this->updateDbVersionMultisite('2.3.0');
        }

        if($this->get_version() === '2.3.36' && (get_option(self::MERGADO_UPDATE) == false || get_option(self::MERGADO_UPDATE) < '2.3.36')) {
            include_once __MERGADO_UPDATES_DIR__ . 'v2.3.36.php';
            $this->updateDbVersionMultisite('2.3.36');
        }

	    if($this->get_version() === '3.0.0' && (get_option(self::MERGADO_UPDATE) == false || get_option(self::MERGADO_UPDATE) < '3.0.0')) {
		    include_once __MERGADO_UPDATES_DIR__ . 'v3.0.0.php';
		    $this->updateDbVersionMultisite('3.0.0');
	    }
    }

    private function updateDbVersionMultisite($version)
    {
        if(is_multisite()) {
            $sites = get_sites();

            foreach($sites as $site) {
                switch_to_blog( $site->blog_id );
                    update_option(Mergado_Marketing_Pack::MERGADO_UPDATE, $version);
                restore_current_blog();
            }
        } else {
            update_option(Mergado_Marketing_Pack::MERGADO_UPDATE, $version);
        }
    }

    /**
     * Create table if not exist
     */

    public static function checkIfAllTablesExist() {
        include_once __MERGADO_UPDATES_DIR__ . 'v2.0.0.php';
    }

    public function processFrontView()
    {
        //Data to templates
        add_action("woocommerce_after_shop_loop_item", [$this, "productListData"], 99);

	    /**
	     * ADVERTISEMENT
	     */

        // GLAMI
        add_action( 'wp_head', [ $this, 'glamiData' ], 98 ); // GLAMI
        add_action('woocommerce_add_to_cart', [$this, 'glamiPixelAddToCart'], 99); // GLAMI


        // BIANO
	    if (CookieClass::advertismentEnabled()) {
		    add_action( 'woocommerce_add_to_cart', [ $this, 'bianoAddToCart' ], 99 ); // BIANO
	    }

        // FB PIXEL
        add_action("woocommerce_before_checkout_billing_form", [$this, "fbPixel_initiateCheckout"], 99);
        add_action('woocommerce_add_to_cart', [$this, 'fbPixelAddToCart'], 99);

	    /**
	     * ANALYTICS
	     */

        // GTAG
        add_action("woocommerce_after_cart", [$this, 'gtagjsRemoveFromCartAjax'], 99);
        add_action("woocommerce_before_checkout_billing_form", [$this, "gtagjs_checkout_step_1"], 99);
        add_action("woocommerce_before_checkout_billing_form", [$this, "gtagjs_CheckoutManipulation"], 99);
        add_action("woocommerce_after_cart", [$this, "gtagjs_CheckoutManipulation"], 99);

        add_action('woocommerce_after_single_product', [$this, 'gtagjsProductDetailView'], 98); // GDPR resolved inside
        add_action('woocommerce_add_to_cart', [$this, 'gtagjsAddToCart'], 99); // GDPR resolved inside
        add_action( "wp_footer", [ $this, "gtagjs_listView" ], 99 ); // GDPR resolved inside

        // GTM
        add_action('woocommerce_add_to_cart', [$this, 'googleTagManagerAddToCart'], 99);
        add_action("woocommerce_before_checkout_billing_form", [$this, "googleTagManager_CheckoutManipulation"], 99);
        add_action("woocommerce_after_cart", [$this, "googleTagManager_CheckoutManipulation"], 99);
        add_action("wp_footer", [$this, "googleTagManager_listView"], 99);


        // GA refund - backend - not part of GDPR
        add_action('woocommerce_order_fully_refunded', [$this, 'refundFull']);
        add_action('woocommerce_order_status_changed', [$this, 'orderStatusChanged']);

        //Checkout steps/options - checkboxs - not part of GDPR
        add_action("woocommerce_review_order_before_submit", [$this, "heurekaAddCheckboxVerifyOptOut"], 10);
        add_action("woocommerce_review_order_before_submit", [$this, "zboziAddCheckboxVerifyOptIn"], 10);
        add_action("woocommerce_review_order_before_submit", [$this, "arukeresoAddCheckboxVerifyOptOut"], 10);
        add_action("woocommerce_checkout_update_order_meta", [$this, "heurekaSetOrderMetaData"], 10);
        add_action("woocommerce_checkout_update_order_meta", [$this, "zboziSetOrderMetaData"], 10);
        add_action("woocommerce_checkout_update_order_meta", [$this, "arukeresoSetOrderMetaData"], 10);

        //add_action('woocommerce_after_single_product', [$this, 'adWordsRemarketingProduct'], 99); TODO: delete function in future
        //add_action('woocommerce_after_cart', [$this, 'adsRemarketingCart'], 99); TODO: delete function in future
        //add_action('woocommerce_after_single_product', [$this, 'googleTagManagerProductDetailView'], 98);
        //add_action("woocommerce_before_checkout_billing_form", [$this, "googleTagManager_checkout_step"], 99);
        //add_action("woocommerce_after_checkout_billing_form", [$this, "checkout_carrier_set"], 99); // not possible
        //add_action("woocommerce_after_checkout_billing_form", [$this, "checkout_payment_set"], 99); // not possible
        //add_action('woocommerce_order_partially_refunded', [$this, 'refundPartial']); // Disabled for now

        // Multi
        add_action('wp_head', [$this, 'mergadoHeaderSetup'], 99);
        add_action('wp_footer', [$this, 'mergadoFooterSetup'], 98);
        add_action('woocommerce_thankyou', [$this, 'mergadoOrderConfirmed'], 99);

        if (function_exists( 'wp_body_open' ) ) {
            add_action('wp_body_open', [$this, 'wpBodyOpeningTag']);
        }
    }

    // Make full refund based on selected statuses
    public function orderStatusChanged($orderId)
    {
	    $alreadyRefunded = get_post_meta($orderId, 'orderFullyRefunded-' . $orderId, true);

        $GaRefundClass = new GoogleAnalyticsRefundService();

        if ($GaRefundClass->isActive()) {
            if ($GaRefundClass->isStatusActive($_POST['order_status'])) {

	            // Check if backend data already sent
	            if (empty($alreadyRefunded)) {
		            update_post_meta( $orderId, 'orderFullyRefunded-' . $orderId, 1 );
		            $GaRefundClass->sendRefundCode( [], $orderId, false );
	            }
            }
        }
    }

    // Only available for WP 5.2+ !! Need falback if inserted here !!
    function wpBodyOpeningTag() {
        $this->googleTagManagerAfterBody();
    }


    // Can't be commented and replaced with orderStatusChanged, because some situations in partial refund ends with this
    public function refundFull($orderId)
    {
        //Change status to refunded or if all prices filled when clicked refund button
	    $GaRefundClass = new GoogleAnalyticsRefundService();
	    if ($GaRefundClass->isActive()) {

		    $alreadyRefunded = get_post_meta($orderId, 'orderFullyRefunded-' . $orderId, true);

		    if (empty($alreadyRefunded)) {
			    update_post_meta( $orderId, 'orderFullyRefunded-' . $orderId, 1 );
                $GaRefundClass->sendRefundCode([], $orderId,  false);
		    }
	    }
    }

    // Refund only whole items.. not if lower price
    public function refundPartial($orderId)
    {
        $GaRefundClass = new GoogleAnalyticsRefundService();
        if ($GaRefundClass->isActive()) {
            $data = json_decode(stripslashes( $_POST['line_item_qtys']));

            $products = [];

            foreach ($data as $id => $quantity) {
                $productId = wc_get_order_item_meta( $id, '_product_id', true );
                $variationId = wc_get_order_item_meta( $id, '_variation_id', true );
                if ($variationId != 0) {
                    $id = $productId . '-' . $variationId;
                } else {
                    $id = $productId;
                }

                $products[$id] = $quantity;
            }

            // Check if products are empty ==> (products not refunded.. just discounted)
            if (!empty($products)) {
                $GaRefundClass->sendRefundCode($products, $orderId,  true);
            }
        }
    }

    public function productListData()
    {
        global $product;

        $category = get_the_terms($product->get_id(), "product_cat");
        $categories = [];

        if ($category) {
            foreach ($category as $term) {
                $categories[] = $term->name;
            }
        }

        $categories = join(', ', $categories);

        $productData = [];

        $productData['base_id'] = $product->get_id();

        if (!$product->is_type('variable')) {
            $productData['full_id'] = $product->get_id();
            $productData['has_variation'] = false;
        } else {
            $productData['variation_id'] = $product->get_id();
            $productData['full_id'] = $product->get_id() . '-' . $product->get_id(); // Product can't be shown in specific variation so its always like 11 - 11
            $productData['has_variation'] = true;
        }

        $productData['name'] = $product->get_name();
        $productData['category'] = $categories;
        $productData['price'] = $product->get_price();
        $productData['currency'] = get_woocommerce_currency();

        ?>

        <div data-metadata-product-list='<?= htmlspecialchars(json_encode($productData), ENT_QUOTES) ?>'></div>
        <?php
    }

    public function fbPixel_initiateCheckout($params)
    {
        $active = $this->facebookService->getActive();

        if ($active):
            global $woocommerce;

            $currency = get_woocommerce_currency();
            $fbWithVat = $this->facebookService->getConversionVatIncluded();

            $products = [];
            $quantity = 0;

            foreach($woocommerce->cart->cart_contents as $key => $item):
                if ($item['variation_id'] == 0) {
                    $id = $item['product_id'];
                } else {
                    $id = $item['product_id'] . '-' . $item['variation_id'];
                }

                $products['ids'][] = "'" . $id . "'";
                $products['contents'][] = "{'id':'".$id."', 'quantity':'" . $item['quantity'] . "'}";
                $quantity = $quantity + $item['quantity'];
            endforeach;

            if ($fbWithVat == 1) {
                $conversionValue = number_format( (float) $woocommerce->cart->get_cart_contents_total(), wc_get_price_decimals(), '.', '' );
            } else {
                $conversionValue = number_format( (float) $woocommerce->cart->get_cart_contents_total() - $woocommerce->cart->get_cart_contents_tax(), wc_get_price_decimals(), '.', '' );
            }

            ?>
            <script>
               document.addEventListener("DOMContentLoaded", function () {
                 fbq('track', 'InitiateCheckout', {
                  content_ids: [<?= implode(',', $products['ids']); ?>],
                  contents: [<?= implode(',', $products['contents']); ?>],
                  content_type: 'product',
                  value: <?= $conversionValue ?>,
                  currency: '<?= $currency ?>',
                  num_items: <?= $quantity ?>
                 });
               });
            </script>
        <?php
        endif;
    }

    public function gtagjs_checkout_step_1($params)
    {
        $active = get_option(Settings::GOOGLE_GTAGJS['ACTIVE'], 0);
        $code = $this->getFormattedAnalyticsCode();
        $tracking = get_option(Settings::GOOGLE_GTAGJS['TRACKING']);
        $ecommerce = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE']);
        $ecommerceEnhanced = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE_ENHANCED']);

        if($active == 1 && $tracking == 1 && $code != '' && $ecommerce == 1 && $ecommerceEnhanced == 1):
            global $woocommerce;

            $products = [];

            foreach($woocommerce->cart->cart_contents as $key => $item):
                if ($item['variation_id'] == 0) {
                    $id = $item['product_id'];
                } else {
                    $id = $item['product_id'] . '-' . $item['variation_id'];
                }

                $category = get_the_terms($id, "product_cat");
                $categories = [];

                if ($category) {
                    foreach ($category as $term) {
                        $categories[] = $term->name;
                    }
                }

                $name = $item['data']->get_name();
                $category = join(', ', $categories);
                $price = $item['data']->get_price();

                $products[] = [
                    'id' => $id,
                    'name' => $name,
                    'category' => $category,
                    'price' => $price,
                ];

                $coupons = join(', ', $woocommerce->cart->get_applied_coupons());
            endforeach;

            ?>
            <script>
              document.addEventListener('DOMContentLoaded', function () {
                gtag('event', 'begin_checkout', {
                  "currency": "<?= get_woocommerce_currency() ?>",
                  "checkout_step": 1,
                  "items": <?= json_encode($products) ?>,
                  "coupon": '<?= $coupons ?>'
                });
              });
            </script>
            <?php
        endif;
    }

    public function gtagjs_listView ()
    {
        if(is_shop() || is_product_category() || is_search()) {
            if(is_shop()) {
                $list_name = 'shop';
            } else if (is_product_category()) {
                $list_name = get_queried_object()->name;
            } else if (is_search()) {
                $list_name = 'search';
            } else {
                $list_name = '';
            }

            $active = get_option(Settings::GOOGLE_GTAGJS['ACTIVE'], 0);
            $code = $this->getFormattedAnalyticsCode();
            $tracking = get_option(Settings::GOOGLE_GTAGJS['TRACKING']);
            $ecommerce = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE']);
            $ecommerceEnhanced = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE_ENHANCED']);

            $googleAdsRemarketingActive = $this->googleAdsService->isRemarketingActive();

            if(($active == 1 && $tracking == 1 && $code != '' && $ecommerce == 1 && $ecommerceEnhanced == 1 ) || ($googleAdsRemarketingActive)):

                $sendTo = implode(',', [$code, $this->googleAdsService->getConversionCode()]);
                ?>
                    <script>
                      document.addEventListener('DOMContentLoaded', function () {
                        var $ = jQuery;
                        var list_name = '<?= $list_name ?>';

                        if ($('[data-metadata-product-list]').length > 0) {
                          var items = {};

                          $.each($('[data-metadata-product-list]'), function (key, value) {
                            var values = JSON.parse($(value).attr('data-metadata-product-list'));
                            items[key] = {};
                            items[key]['id'] = values['full_id'];
                            items[key]['name'] = values['name'];
                            items[key]['list_name'] = list_name;
                            items[key]['category'] = values['category'];
                            items[key]['list_position'] = key;
                            items[key]['price'] = values['price'];
                            items[key]['google_business_vertical'] = 'retail';
                            // items[]['brand'] = values[''];
                            // items[]['variant'] = values[''];
                            // items[]['quantity'] = values[''];
                          });

                          gtag('event', 'viewItemList', {
                            'currency': '<?= get_woocommerce_currency() ?>',
                            "items": items,
                            'send_to': '<?= $sendTo ?>',
                          });
                        }
                      });
                    </script>
                    <?php
                endif;
        }
    }

    public function googleTagManager_listView ()
    {
        if(is_shop() || is_product_category() || is_search()) {
            if(is_shop()) {
                $list_name = 'shop';
            } else if (is_product_category()) {
                $list_name = get_queried_object()->name;
            } else if (is_search()) {
                $list_name = 'search';
            } else {
                $list_name = '';
            }

            $active = $this->googleTagManagerService->isActive();
            $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();
            $viewListItemsCount = $this->googleTagManagerService->getViewListItemsCount();

            if($active && $enhancedEcommerceActive):
                ?>
                <script>
                  document.addEventListener('DOMContentLoaded', function () {
                    var $ = jQuery;
                    var list_name = '<?= $list_name ?>';

                    if($('[data-metadata-product-list]').length > 0) {
                        var items = {};
                        var currency = '';
                        var viewListCount = <?= (int) $viewListItemsCount ?>;

                        $.each($('[data-metadata-product-list]'), function (key, value) {
                            var values = JSON.parse($(value).attr('data-metadata-product-list'));
                            currency = values['currency'];

                            items[key] = {};
                            items[key]['id'] = values['full_id'];
                            items[key]['name'] = values['name'];
                            items[key]['list'] = list_name;
                            items[key]['category'] = values['category'];
                            items[key]['position'] = key;
                            items[key]['price'] = values['price'];
                            // items[]['brand'] = values[''];
                            // items[]['variant'] = values[''];
                            // items[]['quantity'] = values[''];

                            // MAGIC! If null is set in viewListCount, it will fail everytime .. haha
                            if ((key + 1) === viewListCount) {
                              return false;
                            }
                        });

                        dataLayer.push({
                            'event': 'view_item_list',
                            'ecommerce': {
                                'currencyCode': '<?= get_woocommerce_currency() ?>',
                                'impressions': items
                            }
                        });
                    }
                  });
                </script>
            <?php
            endif;
        }
    }

    public function googleTagManagerCheckoutStep()
    {
        if (is_cart() || is_checkout()):
            $active = $this->googleTagManagerService->isActive();
            $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

            if($active && $enhancedEcommerceActive):
                global $woocommerce;

                $products = [];

                foreach($woocommerce->cart->cart_contents as $key => $item):
                    if ($item['variation_id'] == 0) {
                        $id = $item['product_id'];
                    } else {
                        $id = $item['product_id'] . '-' . $item['variation_id'];
                    }

                    $category = get_the_terms($id, "product_cat");
                    $categories = [];

                    if ($category) {
                        foreach ($category as $term) {
                            $categories[] = $term->name;
                        }
                    }

                    $name = $item['data']->get_name();
                    $category = join(', ', $categories);
                    $price = $item['data']->get_price();

                    $products[] = [
                        'id' => $id,
                        'name' => $name,
                        'category' => $category,
                        'price' => $price,
                    ];
                endforeach;

                if(is_cart()) {
                    $step = 1;
                } else if (is_checkout()) {
                    $step = 2;
                } else {
                    $step = 0;
                }
                ?>
                <script>
                    dataLayer.push({
                      'event': 'checkout',
                      'ecommerce': {
                        'currencyCode': '<?= get_woocommerce_currency() ?>',
                        'checkout': {
                          'actionField': {'step': <?= $step ?>},
                          'products': <?= json_encode($products) ?>
                        }
                      }
                    });
                </script>
            <?php
            endif;
        endif;
    }

    public function googleTagManager_CheckoutManipulation($params)
    {
        $active = $this->googleTagManagerService->isActive();
        $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

        if($active && $enhancedEcommerceActive):
            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var $ = jQuery;

                    //Payment
                    $('body').on('payment_method_selected', function () {
                        // $('body').on('click', 'input[type="radio"][name="payment_method"]', function () {
                        var step;
                        var val = $('input[type="radio"][name="payment_method"]:checked').val();

                        if ($('body').hasClass('woocommerce-checkout')) {
                            step = 1;
                        } else {
                            step = 0;
                        }

                        dataLayer.push({
                            'event': 'checkoutOption',
                            'ecommerce': {
                                'checkout_option': {
                                    'actionField': {'step': step, 'option': val}
                                }
                            }
                        });
                    });

                    //Delivery
                    // $('body').on('updated_shipping_method', function () {
                    $('body').on('click', 'input[type="radio"][name*="shipping_method"]', function () {
                        var step;
                        var val = $('input[type="radio"][name*="shipping_method"]:checked').val();

                        if ($('body').hasClass('woocommerce-checkout')) {
                            step = 1;
                        } else {
                            step = 0;
                        }

                        dataLayer.push({
                            'event': 'checkoutOption',
                            'ecommerce': {
                                'checkout_option': {
                                    'actionField': {'step': step, 'option': val}
                                }
                            }
                        });
                    });
                });
            </script>
        <?php
        endif;
    }

    public function gtagjs_CheckoutManipulation($params)
    {
        $active = get_option(Settings::GOOGLE_GTAGJS['ACTIVE'], 0);
        $code = $this->getFormattedAnalyticsCode();
        $tracking = get_option(Settings::GOOGLE_GTAGJS['TRACKING']);
        $ecommerce = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE']);
        $ecommerceEnhanced = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE_ENHANCED']);

        if($active == 1 && $tracking == 1 && $code != '' && $ecommerce == 1 && $ecommerceEnhanced == 1):
            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var $ = jQuery;

                    //Coupons
                    var appliedCouponsCart = '';
                    var discounts = [];

                    $('[data-coupon]').each(function () {
                        discounts.push($(this).attr('data-coupon'));
                    });

                    appliedCouponsCart = discounts.join(', ');

                    $('body').on('updated_cart_totals, updated_checkout', function () {
                        var discounts = [];
                        $('[data-coupon]').each(function () {
                            discounts.push($(this).attr('data-coupon'));
                        });

                        discounts = discounts.join(', ');

                        if (appliedCouponsCart !== discounts) {
                            appliedCouponsCart = discounts;
                            var cartData = JSON.parse($('[data-mergado-cart-data]').attr('data-mergado-cart-data'));

                            var items = [];

                            $.each(cartData, function (key, val) {
                                items.push(val);
                            });

                            gtag('event', 'checkout_progress', {
                                "items": items,
                                "coupon": discounts,
                            });
                        }
                    });


                    //Payment
                    $('body').on('payment_method_selected', function () {
                    // $('body').on('click', 'input[type="radio"][name="payment_method"]', function () {
                        var step;
                        var val = $('input[type="radio"][name="payment_method"]:checked').val();

                        if ($('body').hasClass('woocommerce-checkout')) {
                            step = 1;
                        } else {
                            step = 0;
                        }

                        gtag('event', 'set_checkout_option', {
                            "checkout_step": step,
                            "checkout_option": "payment method",
                            "value": val
                        });
                    });


                    //Delivery
                    // $('body').on('updated_shipping_method', function () {
                    $('body').on('click', 'input[type="radio"][name*="shipping_method"]', function () {
                        var step;
                        var val = $('input[type="radio"][name*="shipping_method"]:checked').val();

                        if ($('body').hasClass('woocommerce-checkout')) {
                            step = 1;
                        } else {
                            step = 0;
                        }

                        gtag('event', 'set_checkout_option', {
                            "checkout_step": step,
                            "checkout_option": "payment method",
                            "value": val
                        });
                    });
                });
            </script>
        <?php
        endif;
    }

    /**
     * FOR PRODUCT DETAIL
     */

    public function glamiPixelAddToCart()
    {
        // Disable if woodmart theme because of incompatibility
        if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], ['xoo_wsc_add_to_cart', 'woodmart_ajax_add_to_cart'])) {
            return false;
        }

        $lang = Languages::getLang();
        $active = $this->glamiPixelService->isActive($lang);

        if ($active) {
            if (isset($_POST['add-to-cart'])) {

                $product = wc_get_product($_POST['add-to-cart']);

                if ($product->get_type() === 'grouped'): // Check if grouped product
                    if (!isset($_POST['groupedGlami'])): // Check if request is duplicate (grouped products send two posts with same data)
                        $_POST['groupedGlami'] = true; // Set variable that disable next call of same addToCart
                        ob_start();
                        foreach ($_POST['quantity'] as $id => $quantity):
                            $product = wc_get_product($id); // No need for ID changing because only simple products can be added on grouped page
                            ?>
                            <script>
                              document.addEventListener("DOMContentLoaded", function() {
                                glami('track', 'AddToCart', {
                                  item_ids: ['<?php echo $id; ?>'],
                                  product_names: ['<?php echo $product->get_name(); ?>'],
                                  value: <?php echo $product->get_price(); ?>,
                                  currency: '<?php echo get_woocommerce_currency(); ?>',
                                  consent: window.mmp.cookies.sections.advertisement.onloadStatus,
                                });
                              });
                            </script>
                        <?php
                        endforeach;
                        $this->headerExtra .= ob_get_contents();
                        ob_end_clean();
                    endif;
                else: //Simple and complicated products
                    if (isset($_POST['variation_id']) && $_POST['variation_id'] && $_POST['variation_id'] !== '') {
                        $id = $product->get_data()['id'] . '-' . $_POST['variation_id'];
                    } else {
                        $id = $product->get_data()['id'];
                    }

                    ob_start();
                    ?>
                    <script>
                      document.addEventListener("DOMContentLoaded", function() {
                        glami('track', 'AddToCart', {
                          item_ids: ['<?php echo $id; ?>'],
                          product_names: ['<?php echo $product->get_name(); ?>'],
                          value: <?php echo $product->get_price(); ?>,
                          currency: '<?php echo get_woocommerce_currency(); ?>',
                          consent: window.mmp.cookies.sections.advertisement.onloadStatus,
                        });
                      });
                    </script>
                    <?php

                    $this->headerExtra .= ob_get_contents();
                    ob_end_clean();
                endif;
            }
        }
    }

    public function fbPixelAddToCartAjax()
    {
        $active = $this->facebookService->getActive();


        if ($active) {
            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var $ = jQuery;

                    var mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .add_to_cart_button';

                    if (typeof window.xoo_wsc_params !== 'undefined') {
                      mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .add_to_cart_button, body.single-product .single_add_to_cart_button';
                    }

                    $(mmpSelector).on('click', function(e) {
                        if(!$(this).hasClass('product_type_variable')) {
                        if($('[data-metadata-product-list]').length > 0) {
                            var prodData = JSON.parse(jQuery(this).closest('.product').find('[data-metadata-product-list]').attr('data-metadata-product-list'));

                            var $_currency = prodData['currency'];
                            var $_id = prodData['full_id'];
                            var $_name = prodData['name'];
                            var $_price = prodData['price'];
                          var $_qty = 1;
                        } else {
                            var $_currency = $('#mergadoSetup').attr('data-currency');
                            var $_id = $(this).closest('li.product').find('[data-product_id]').attr('data-product_id');
                            var $_name = $(this).closest('li.product').find('.woocommerce-loop-product__title').text();
                            var $_qty = $(this).closest('li.product').find('[name="quantity"]').val();
                            var $_priceClone = $(this).closest('li.product').clone();
                            $_priceClone.find('del').remove();
                            $_priceClone.find('.woocommerce-Price-currencySymbol').remove();
                            var $_price = $_priceClone.find('.woocommerce-Price-amount.amount').text();
                        }

                            fbq('track', 'AddToCart', {
                                content_name: $_name,
                                // content_category: 'Apparel & Accessories > Shoes',
                                content_ids: [$_id],
                                contents: [{'id':$_id, 'quantity':$_qty}],
                                content_type: 'product',
                                value: Number($_price.replace(' ', '')),
                                currency: $_currency
                            });
                        }
                    });
                });
            </script>
            <?php
        }
    }

    public function fbPixelAddToCart()
    {
        // Disable if woodmart theme because of incompatibility
        if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], ['xoo_wsc_add_to_cart', 'woodmart_ajax_add_to_cart'])) {
            return false;
        }

        $active = $this->facebookService->isActive();
        $currency = get_woocommerce_currency();

        if ($active) {
            if (isset($_POST['add-to-cart'])) {
                $product = wc_get_product($_POST['add-to-cart']);

                if ($product->get_type() === 'grouped'): // Check if grouped product
                    if (!isset($_POST['groupedFbPixel'])): // Check if request is duplicate (grouped products send two posts with same data)
                        $_POST['groupedFbPixel'] = true; // Set variable that disable next call of same addToCart
                        ob_start();
                        foreach ($_POST['quantity'] as $id => $quantity):
                            $product = wc_get_product($id); // No need for ID changing because only simple products can be added on grouped page
                            ?>
                            <script>
                              document.addEventListener("DOMContentLoaded", function() {
                                fbq('track', 'AddToCart', {
                                  product_name: ['<?= $product->get_name(); ?>'],
                                  content_ids: ['<?= $id; ?>'],
                                  contents: [{'id':'<?= $id; ?>', 'quantity':'<?= $quantity; ?>'}],
                                  content_type: 'product',
                                  value: <?= $product->get_price(); ?>,
                                  currency: '<?= $currency ?>'
                                });
                              });
                            </script>
                        <?php

                        endforeach;
                    endif;
                else:
                    if (isset($_POST['variation_id']) && $_POST['variation_id'] && $_POST['variation_id'] !== '') {
                        $id = $product->get_data()['id'] . '-' . $_POST['variation_id'];
                    } else {
                        $id = $product->get_data()['id'];
                    }

                    $quantity = 1;
                    if (isset($_POST['quantity'])) {
                        $quantity = (int)$_POST['quantity'];
                    }
                    ?>
                        <script>
                          document.addEventListener("DOMContentLoaded", function() {
                            fbq('track', 'AddToCart', {
                              product_name: ['<?= $product->get_name(); ?>'],
                              content_ids: ['<?= $id; ?>'],
                              contents: [{'id':'<?= $id; ?>', 'quantity':'<?= $quantity; ?>'}],
                              content_type: 'product',
                              value: <?= $product->get_price(); ?>,
                              currency: '<?= $currency ?>'
                            });
                          });
                        </script>
                    <?php
                endif;
            }
        }
    }

	public function bianoAddToCartAjax()
	{
		$lang = Languages::getLang();

		$bianoActive = get_option(Settings::BIANO['ACTIVE']);
		$bianoLanguageActive = get_option(Settings::BIANO['FORM_ACTIVE'] . '-' . $lang);
		$bianoMerchantId = get_option(Settings::BIANO['MERCHANT_ID'] . '-' . $lang);

		if ($bianoActive == '1' && $bianoLanguageActive == '1' && $bianoMerchantId && $bianoMerchantId !== ''):
			?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var $ = jQuery;

                    var mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .add_to_cart_button';

                    if (typeof window.xoo_wsc_params !== 'undefined') {
                      mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .add_to_cart_button, body.single-product .single_add_to_cart_button';
                    }

                    $(mmpSelector).on('click', function(e) {
                        if(!$(this).hasClass('product_type_variable')) {
                            if($('[data-metadata-product-list]').length > 0) {
                                var prodData = JSON.parse(jQuery(this).closest('.product').find('[data-metadata-product-list]').attr('data-metadata-product-list'));

                                var $_currency = prodData['currency'];
                                var $_id = prodData['full_id'];
                                var $_price = prodData['price'];
                            } else {
                                var $_currency = $('#mergadoSetup').attr('data-currency');
                                var $_id = $(this).closest('li.product').find('[data-product_id]').attr('data-product_id');
                                var $_priceClone = $(this).closest('li.product').clone();
                                $_priceClone.find('del').remove();
                                $_priceClone.find('.woocommerce-Price-currencySymbol').remove();
                                var $_price = $_priceClone.find('.woocommerce-Price-amount.amount').text().replace(' ', '');
                            }

                            bianoTrack('track', 'add_to_cart', {
                                id: $_id.toString(),
                                quantity: 1,
                                unit_price: Number($_price),
                                currency: $_currency,
                            });
                        }
                    });
                });
            </script>
        <?php endif;
    }

	public function bianoAddToCart()
	{
        // Disable if woodmart theme because of incompatibility
        if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], ['xoo_wsc_add_to_cart', 'woodmart_ajax_add_to_cart'])) {
            return false;
        }

		$currency = get_woocommerce_currency();
		$lang = Languages::getLang();

		$bianoActive = get_option(Settings::BIANO['ACTIVE']);
		$bianoLanguageActive = get_option(Settings::BIANO['FORM_ACTIVE'] . '-' . $lang);
		$bianoMerchantId = get_option(Settings::BIANO['MERCHANT_ID'] . '-' . $lang);

		if ($bianoActive == '1' && $bianoLanguageActive == '1' && $bianoMerchantId && $bianoMerchantId !== ''):
			if (isset($_POST['add-to-cart'])) {
				$product = wc_get_product($_POST['add-to-cart']);

				if ($product->get_type() === 'grouped'): // Check if grouped product
					if (!isset($_POST['groupedBianoPixel'])): // Check if request is duplicate (grouped products send two posts with same data)
						$_POST['groupedBianoPixel'] = true; // Set variable that disable next call of same addToCart
						ob_start();
						foreach ($_POST['quantity'] as $id => $quantity):
							$product = wc_get_product($id); // No need for ID changing because only simple products can be added on grouped page
                            ?>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    bianoTrack('track', 'add_to_cart', {
                                        id: '<?= $id; ?>',
                                        quantity: <?= $quantity ?>,
                                        unit_price: <?= $product->get_price(); ?>,
                                        currency: '<?= $currency ?>',
                                    });
                                });
                            </script>
						<?php

						endforeach;
					endif;
				else:
					if (isset($_POST['variation_id']) && $_POST['variation_id'] && $_POST['variation_id'] !== '') {
						$id = $product->get_data()['id'] . '-' . $_POST['variation_id'];
					} else {
						$id = $product->get_data()['id'];
					}

					?>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            bianoTrack('track', 'add_to_cart', {
                                id: '<?= $id; ?>',
                                quantity: <?= $_POST['quantity'] ?>,
                                unit_price: <?= $product->get_price(); ?>,
                                currency: '<?= $currency ?>',
                            });
                        });
                    </script>
				<?php
				endif;
			}
		endif;
	}

    /*******************************************************************************************************************
     * ORDER CONFIMARTION - NAJNAKUP, PRICEMANIA, ZBOZI.CZ, ADWORDS, SKLIK, FBPIXEL, GLAMI
     *******************************************************************************************************************/

    /**
     * Najnakup integration
     *
     * @param $orderId
     */
    public function mergadoOrderConfirmed($orderId)
    {
        $order = wc_get_order($orderId);
        $confirmed = get_post_meta($orderId, 'orderConfirmed-' . $order->get_order_number(), true);

        // Check if backend data already sent
        if (empty($confirmed) || self::TESTING) {
            $googleReviewsClass = new GoogleReviewsService();
            update_post_meta($orderId, 'orderConfirmed-' . $order->get_order_number(), 1);

            Zbozi::sendZbozi($orderId);
            HeurekaService::heurekaVerify($orderId);
            NajNakup::sendNajnakupValuation($orderId);
            Pricemania::sendPricemaniaOverenyObchod($orderId);
            $googleReviewsClass->getOptInTemplate($order);
            ArukeresoService::orderConfirmation($orderId);
            $this->fbPixelPurchased($orderId);
            $this->adWordsConversions($orderId);

            if(CookieClass::advertismentEnabled()) {
                HeurekaService::heurekaOrderConfirmation($orderId);
                $this->glamiTOP($orderId);
                $this->kelkooServiceIntegration->kelkooPurchase($orderId);
                $this->bianoPurchased($orderId);
            }

            $this->glamiPixelPurchased($orderId);
            $this->zboziConversions($orderId);
            $this->sklikConversions($orderId); // GDPR got custom integration in platform
            $this->gtagjsPurchased($orderId);
        }
    }


    public function adWordsConversions($order_id)
    {
        $order = wc_get_order($order_id);

        $active = $this->googleAdsService->isConversionActive();
        $code = $this->googleAdsService->getConversionCode();
        $label = $this->googleAdsService->getConversionLabel();

        $currency = get_woocommerce_currency();
        $orderTotal = $order->get_total();

        if ($active) {
            ?>
            <div id="adwordsConversions">
                <script>
                  document.addEventListener('DOMContentLoaded', function () {
                    gtag('event', 'conversion', {
                      'send_to': '<?= $code ?>/<?= $label ?>',
                      'value': <?= $orderTotal ?>,
                      'currency': '<?= $currency ?>',
                      'transaction_id': '<?= $order_id ?>'
                    });
                  });
                </script>
            </div>
            <?php
        }
    }


    public function glamiPixelPurchased($orderId)
    {
        $lang = Languages::getLang();

        $active = $this->glamiPixelService->isActive($lang);
	    $withVat = $this->glamiPixelService->getConversionVatIncluded();

        if ($active) {
            $order = wc_get_order($orderId);
            $products_tmp = $order->get_items();
            $products = array();

            foreach ($products_tmp as $product) {
                if ($product->get_data()['variation_id'] == 0) {
                    $id = $product->get_data()['product_id'];
                } else {
                    $id = $product->get_data()['product_id'] . '-' . $product->get_data()['variation_id'];
                }

                $products['ids'][] = "'" . $id . "'";
                $products['name'][] = "'" . $product->get_name() . "'";
            }

	        if ($withVat == 1) {
		        $conversionValue = number_format( (float) $order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '' );
	        } else {
		        $conversionValue = number_format( (float) $order->get_total() - $order->get_total_tax() - $order->get_shipping_total(), wc_get_price_decimals(), '.', '' );
	        }

            ?>
            <script>
                window.addEventListener("load", function (event) {
                    glami('track', 'Purchase', {
                        item_ids: [<?php echo implode(',', $products['ids']); ?>],
                        product_names: [<?php echo implode(',', $products['name']); ?>],
                        value: <?php echo $conversionValue ?>,
                        currency: '<?php echo get_woocommerce_currency(); ?>',
                        transaction_id: '<?php echo $orderId; ?>',
                        consent: window.mmp.cookies.sections.advertisement.onloadStatus,
                    });
                });
            </script>
            <?php
        }
    }


    public function fbPixelPurchased($orderId)
    {
        $active = $this->facebookService->isActive();
        $currency = get_woocommerce_currency();
	    $fbWithVat = $this->facebookService->getConversionVatIncluded();

        if ($active) {
            $order = wc_get_order($orderId);
            $products_tmp = $order->get_items();

            $products = [];
            foreach ($products_tmp as $product) {
                if ($product->get_variation_id() == 0) {
                    $id = $product->get_data()['product_id'];
                } else {
                    $id = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
                }
                $products['ids'][] = "'" . $id . "'";
                $products['name'][] = "'" . $product->get_name() . "'";
                $products['contents'][] = "{'id':'".$id."', 'quantity':'" . $product['quantity'] . "'}";
            }

	        if ($fbWithVat == 1) {
		        $conversionValue = number_format( (float) $order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '' );
	        } else {
		        $conversionValue = number_format( (float) $order->get_total() - $order->get_total_tax() - $order->get_shipping_total(), wc_get_price_decimals(), '.', '' );
	        }

            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    fbq('track', 'Purchase', {
                        content_ids: [<?php echo implode(',', $products['ids']); ?>],
                        contents: [<?php echo implode(',', $products['contents']); ?>],
                        content_type: 'product',
                        value: <?php echo $conversionValue ?>,
                        currency: '<?php echo $currency ?>'
                    });
                });
            </script>
            <?php
        }
    }

    public function sklikConversions($orderId)
    {
        $active = get_option(Settings::SKLIK['CONVERSION_ACTIVE']);
        $conversionCode = get_option(Settings::SKLIK['CONVERSION_CODE']);
        $conversionValue = get_option(Settings::SKLIK['CONVERSION_VALUE']);
        $sklikWithVat = get_option(Settings::SKLIK['CONVERSION_VAT_INCL'], 0);

        if ($active == 1 && $conversionCode != '') {
            if ($conversionValue == '') {
                $order = wc_get_order($orderId);

                if ($sklikWithVat == 1) {
                    $conversionValue = number_format( (float) $order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '' );
                } else {
                    //Price of items
                    $conversionValue = number_format( (float) $order->get_total() - $order->get_total_tax() - $order->get_shipping_total(), wc_get_price_decimals(), '.', '' );
                }
            }

            ?>
            <!-- Měřící kód Sklik.cz -->
            <script type="text/javascript">
                var seznam_cId = <?php echo $conversionCode ?>;
                var seznam_value = <?php echo $conversionValue ?>;
                var rc = rc || {};

                <?php if (CookieClass::advertismentEnabled()): ?>
                    rc.consent = 1; // CCC = 0 nebo 1
                <?php else: ?>
                    rc.consent = 0; // CCC = 0 nebo 1
                <?php endif; ?>

            </script>
            <script type="text/javascript" src="https://www.seznam.cz/rs/static/rc.js" async></script>
            <?php
        }
    }


    public function glamiPixel()
    {
        $lang = Languages::getLang();
        $active = $this->glamiPixelService->isActive($lang);

        if ($active) {

            ?>
            <script>

                <?php
                // CATEGORY PAGE
                if(is_product_category()) :
                $category = get_queried_object();
                $products_tmp = wc_get_products(array('category' => array($category->slug)));
                $products = array();

                foreach ($products_tmp as $product) {
                    if ($product->get_id() == 0) {
                        $id = $product->get_id();
                    } else {
                        $id = $product->get_id() . '-' . $product->get_id(); // Product can't be shown in specific variation so its always like 11 - 11
                    }

                    $products['ids'][] = "'" . $id . "'";
                    $products['name'][] = "'" . $product->name . "'";
                }
                ?>
                document.addEventListener('DOMContentLoaded', function () {
                    glami('track', 'ViewContent', {
                        content_type: 'category',
                        item_ids: [<?php echo implode(',', $products['ids']); ?>],
                        product_names: [<?php echo implode(',', $products['name']); ?>],
                        category_id: '<?php echo $category->term_id; ?>',
                        category_text: '<?php echo $category->name; ?>',
                        consent: window.mmp.cookies.sections.advertisement.onloadStatus,
                    });
                });
                <?php endif; ?>


                <?php
                // PRODUCT PAGE
                if(is_product()) :
                $product = get_queried_object();

                // No way to get variation ID. So always the default product

//                if ($product->get_variation_id() == 0) {
                    $id = $product->ID;
//                } else {
//                    $id = $product->get_id() . '-' . $product->get_variation_id();
//                }

                ?>
                document.addEventListener('DOMContentLoaded', function () {
                    glami('track', 'ViewContent', {
                        content_type: 'product',
                        item_ids: ['<?php echo $id; ?>'],
                        product_names: ['<?php echo $product->post_title; ?>'],
                        consent: window.mmp.cookies.sections.advertisement.onloadStatus,
                    });
                });
                <?php endif; ?>
            </script>

            <?php
        }
    }

    public function gtagjsProductDetailView()
    {
        $active = get_option(Settings::GOOGLE_GTAGJS['ACTIVE'], 0);
        $code = $this->getFormattedAnalyticsCode();
        $tracking = get_option(Settings::GOOGLE_GTAGJS['TRACKING']);
        $ecommerce = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE']);
        $ecommerceEnhanced = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE_ENHANCED']);

        $googleAdsRemarketingActive = $this->googleAdsService->isRemarketingActive();

        if(($active == 1 && $tracking == 1 && $code != '' && $ecommerce == 1 && $ecommerceEnhanced == 1) || ($googleAdsRemarketingActive)):
            $sendTo = implode(',', [$code, $this->googleAdsService->getConversionCode()]);

            if(is_product()):
//                $productID = get_queried_object_id();
//                $product = wc_get_product($productID);

//                if ($product->get_variation_id() == 0) {
//                    $id = $product->get_data()['product_id'];
//                } else {
//                    $id = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
//                }

                $id = get_queried_object_id();
                $product = wc_get_product($id);

                $categories = get_the_terms($id, 'product_cat');

                $output = [];
                if ($categories) {
                    foreach ($categories as $category) {
                        $output[] = $category->name;
                    }
                }

                $productCategories = join(", ", $output);

                $productData = [
                    'id' => $id,
                    'name' => $product->get_name(),
    //                    'list_name' => '',
    //                    'brand' => '',
                    'category' => $productCategories,
    //                    'variant' => '',
    //                    'list_position' => ,
    //                'quantity' => (int)$product->get_quantity(),
                    'price' => (float)$product->get_price(),
                    'google_business_vertical' => 'retail',
                ];

                ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            gtag('event', 'view_item', {
                                'currency': '<?= get_woocommerce_currency() ?>',
                                "items": <?= json_encode($productData) ?>,
                                'send_to': '<?= $sendTo ?>',
                            });
                        });
                    </script>
                <?php


                //If user come from my url === clicked on product url
                if($_SERVER["HTTP_REFERER"]) {
                    if(strpos($_SERVER["HTTP_REFERER"], get_site_url()) !== false) {
                        ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                              gtag('event', 'select_content', {
                                'currency': '<?= get_woocommerce_currency() ?>',
                                "content_type": "product",
                                "items": <?= json_encode($productData) ?>
                              });
                            });
                        </script>
                        <?php
                    }
                }
            endif;
        endif;
    }

    public function googleTagManagerProductDetailView()
    {
        if(is_product()):
            $active = $this->googleTagManagerService->isActive();
            $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

            if($active && $enhancedEcommerceActive):
    //                $productID = get_queried_object_id();
    //                $product = wc_get_product($productID);

    //                if ($product->get_variation_id() == 0) {
    //                    $id = $product->get_data()['product_id'];
    //                } else {
    //                    $id = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
    //                }

                    $id = get_queried_object_id();
                    $product = wc_get_product($id);

                    $categories = get_the_terms($id, 'product_cat');

                    $output = [];
                    if ($categories) {
                        foreach ($categories as $category) {
                            $output[] = $category->name;
                        }
                    }

                    $productCategories = join(", ", $output);

                    $productData[] = [
                        'name' => $product->get_name(),
                        'id' => (string) $id,
                        'price' => (float)$product->get_price(),
                        'category' => $productCategories,
                    ];

                    ?>
                    <script>
                      // document.addEventListener('DOMContentLoaded', function () {
                        dataLayer.push({
                            'event': 'viewItem',
                            'ecommerce': {
                                'currencyCode': '<?= get_woocommerce_currency() ?>',
                                'detail': {
                                    'products': <?= json_encode($productData) ?>
                                }
                            }
                        });
                      // });
                    </script>
                    <?php


                    //If user come from my url === clicked on product url
                    $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
                    if(!$pageWasRefreshed) {
                        if(strpos($_SERVER["HTTP_REFERER"], get_site_url()) !== false) {
                            ?>
                            <script>
                               // document.addEventListener('DOMContentLoaded', function () {
                                 dataLayer.push({
                                   'event': 'productClick',
                                   'ecommerce': {
                                    'currencyCode': '<?= get_woocommerce_currency() ?>',
                                     'click': {
                                      'products': <?= json_encode($productData) ?>
                                     }
                                   }
                                 });
                               // });
                            </script>
                            <?php
                        }
                    }
            endif;
        endif;
    }

    // Add checkbox if user want customer review email
    public function heurekaAddCheckboxVerifyOptOut()
    {
        $CZverifiedActive = get_option(Settings::HEUREKA['ACTIVE_CZ']);
        $SKverifiedActive = get_option(Settings::HEUREKA['ACTIVE_SK']);

        if (($CZverifiedActive && $CZverifiedActive == 1) || ($SKverifiedActive && $SKverifiedActive == 1)) {
            $lang = get_locale();

            $defaultText = stripslashes(get_option('heureka-verify-opt-out-text-en_US', 0));
            $checkboxText = stripslashes(get_option('heureka-verify-opt-out-text-' . $lang, 0));

            if ($checkboxText === 0 || trim($checkboxText) === '') {
                $checkboxText = $defaultText;
            }

            if ($checkboxText === 0 || trim($checkboxText) === '') {
                $checkboxText = HeurekaService::DEFAULT_OPT;
            }

            woocommerce_form_field( 'heureka-verify-checkbox', array( // CSS ID
                'type'          => 'checkbox',
                'class'         => array('form-row heureka-verify-checkbox'), // CSS Class
                'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
                'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
                'required'      => false, // Mandatory or Optional
                'label'         => $checkboxText,
            ));
        }
    }

    // Add checkbox if user want customer review email
    public function zboziAddCheckboxVerifyOptIn()
    {
        $ZboziClass = new ZboziService();

        if ($ZboziClass->isActive()) {
            $lang = get_locale();

            $defaultText = stripslashes($ZboziClass->getOptOut('en_US'));
            $checkboxText = stripslashes($ZboziClass->getOptOut($lang));

            if ($checkboxText === 0 || trim($checkboxText) === '') {
                $checkboxText = $defaultText;
            }

            if ($checkboxText === 0 || trim($checkboxText) === '') {
                $checkboxText = ZboziService::DEFAULT_OPT;
            }

            woocommerce_form_field( 'zbozi-verify-checkbox', array( // CSS ID
                'type'          => 'checkbox',
                'class'         => array('form-row zbozi-verify-checkbox'), // CSS Class
                'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
                'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
                'required'      => false, // Mandatory or Optional
                'label'         => $checkboxText,
            ));
        }
    }


    // Add checkbox if user want customer review email
    public function arukeresoAddCheckboxVerifyOptOut()
    {
        if ($this->arukeresoService->isActive()) {
            $lang = get_locale();
            $defaultText = stripslashes($this->arukeresoService->getOptOut('en_US'));
            $checkboxText = stripslashes($this->arukeresoService->getOptOut($lang));

            if ($checkboxText === 0 || trim($checkboxText) === '') {
                $checkboxText = $defaultText;
            }

            if ($checkboxText === 0 || trim($checkboxText) === '') {
                $checkboxText = ArukeresoService::DEFAULT_OPT;
            }

            woocommerce_form_field( 'arukereso-verify-checkbox', array( // CSS ID
                'type'          => 'checkbox',
                'class'         => array('form-row arukereso-verify-checkbox'), // CSS Class
                'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
                'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
                'required'      => false, // Mandatory or Optional
                'label'         => $checkboxText,
            ));
        }
    }


    // Set to order meta if user want heureka review email
    public function heurekaSetOrderMetaData($orderId)
    {
        if (isset($_POST['heureka-verify-checkbox']) && $_POST['heureka-verify-checkbox']){
            update_post_meta( $orderId, 'heureka-verify-checkbox', esc_attr($_POST['heureka-verify-checkbox']));
        }
    }

    // Set to order meta if user want zbozi review email
    public function zboziSetOrderMetaData($orderId)
    {
        if (isset($_POST['zbozi-verify-checkbox']) && $_POST['zbozi-verify-checkbox']){
            update_post_meta( $orderId, 'zbozi-verify-checkbox', esc_attr($_POST['zbozi-verify-checkbox']));
        }
    }

    // Set to order meta if user want zbozi review email
    public function arukeresoSetOrderMetaData($orderId)
    {
        if (isset($_POST['arukereso-verify-checkbox']) && $_POST['arukereso-verify-checkbox']){
            update_post_meta( $orderId, 'arukereso-verify-checkbox', esc_attr($_POST['arukereso-verify-checkbox']));
        }
    }

    public function mergadoHeaderSetup()
    {
        $this->createJsVariables();

        $this->bianoHeader();

        $this->gtagjsHeader(); // GDPR resolved inside

        $this->googleTagManagerInitDataLayer();
        $this->googleTagManagerProductDetailView(); // must be before GTM
        $this->googleTagManagerTransaction();
        $this->googleTagManagerPurchased();
        $this->googleTagManagerCheckoutStep();
        $this->googleTagManagerHeader();
    }

    public function googleTagManagerInitDataLayer()
    {
        ?>
            <script>
                window.dataLayer = window.dataLayer || [];
            </script>
        <?php
    }

    //TODO move this to classes pls...

    public function gtagjsHeader()
    {
        if(CookieClass::analyticalEnabled()) {
            $analyticalStorage = 'granted';
        } else {
            $analyticalStorage = 'denied';
        }

        if (CookieClass::advertismentEnabled()) {
            $advertisementStorage = 'granted';
        } else {
            $advertisementStorage = 'denied';
        }

        //Gtagjs - GA
        $gtagActive = get_option(Settings::GOOGLE_GTAGJS['ACTIVE']);
        $gtagTracking = get_option(Settings::GOOGLE_GTAGJS['TRACKING']);
        $gaMeasurementId = $this->getFormattedAnalyticsCode();

        $gtagMainCode = '';

        //Google ADS
        $googleAdsConversionsActive = $this->googleAdsService->isConversionActive();
        $googleAdsRemarketingActive = $this->googleAdsService->isRemarketingActive();

        //Primarily use code for anayltics so no need for config on all functions
        if ($gtagActive == 1 && $gtagTracking == 1 && $gaMeasurementId !== '') {
            $gtagMainCode = $gaMeasurementId;
            $gtagAnalyticsCode = $gaMeasurementId;
        }

        if ( $googleAdsRemarketingActive || $googleAdsConversionsActive ) {
            $googleAdsConversionCode = $this->googleAdsService->getConversionCode();

            if ( $gtagMainCode == '' ) {
                $gtagMainCode = $googleAdsConversionCode;
            }
        }

        if(isset($gtagMainCode) && $gtagMainCode !== ''):
            ?>
                <!-- Global site tag (gtag.js) - Google Analytics -->
                <script async src="//www.googletagmanager.com/gtag/js?id=<?= $gtagMainCode ?>"></script>
                <script>
                  // document.addEventListener('DOMContentLoaded', function () {
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                        gtag('js', new Date());

                        gtag('consent', 'default', {
                          'analytics_storage': '<?php echo $analyticalStorage ?>',
                          'ad_storage': '<?php echo $advertisementStorage ?>',
                        });

                        <?php if (isset($gtagAnalyticsCode)): ?>
                            gtag('config', '<?= $gtagAnalyticsCode ?>');

                            window.mmp.cookies.sections.analytical.functions.gtagAnalytics = function () {
                                gtag('consent', 'update', {
                                  'analytics_storage': 'granted'
                                });
                            };
                        <?php endif; ?>

                    <?php if (isset($googleAdsConversionCode) && $googleAdsRemarketingActive): ?>
                        <?php if(CookieClass::advertismentEnabled()): ?>
                            gtag('config', '<?= $googleAdsConversionCode ?>');
                        <?php else: ?>
                            gtag('config', '<?= $googleAdsConversionCode ?>', {'allow_ad_personalization_signals': false});
                        <?php endif; ?>
                    <?php elseif (isset($googleAdsConversionCode)): ?>
                            gtag('config', '<?= $googleAdsConversionCode ?>', {'allow_ad_personalization_signals': false});
                    <?php endif; ?>

                    <?php if (isset($googleAdsConversionCode)): ?>
                        window.mmp.cookies.sections.advertisement.functions.gtagAds = function () {
                          gtag('consent', 'update', {
                            'ad_storage': 'granted'
                          });

                          gtag('config', '<?= $googleAdsConversionCode ?>', {'allow_ad_personalization_signals': true});
                        };
                    <?php endif; ?>
                  // });
                </script>
        <?php endif;
    }

    public function googleTagManagerHeader()
    {
        $active = $this->googleTagManagerService->isActive();
        $code = $this->googleTagManagerService->getCode();

        if($active):
            ?>
            <!-- Google Tag Manager -->
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                    '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','<?= $code ?>');</script>
            <!-- End Google Tag Manager -->
        <?php
        endif;
    }

    public function googleTagManagerAfterBody()
    {
        $active = $this->googleTagManagerService->isActive();
        $code = $this->googleTagManagerService->getCode();

        if($active):
            ?>
            <!-- Google Tag Manager (noscript) -->
            <noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?= $code ?>"
                              height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->
        <?php
        endif;
    }

    public function gtagjsPurchased($orderId)
    {
        $active = get_option(Settings::GOOGLE_GTAGJS['ACTIVE']);
        $tracking = get_option(Settings::GOOGLE_GTAGJS['TRACKING']);
        $ecommerce = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE']);
        $code = $this->getFormattedAnalyticsCode();
	    $gtagjsWithVat = get_option(Settings::GOOGLE_GTAGJS['CONVERSION_VAT_INCL'], 1);

        if($active == 1 && $tracking == 1 && $ecommerce == 1 && $code !== ''):
            $order = wc_get_order($orderId);
            $products_tmp = $order->get_items();

            $products = array();

            foreach ($products_tmp as $product) {
                if ($product->get_variation_id() == 0) {
                    $id = $product->get_data()['product_id'];
                } else {
                    $id = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
                }

                $categories = get_the_terms($id, 'product_cat');

                $output = [];
                if ($categories) {
                    foreach ($categories as $category) {
                        $output[] = $category->name;
                    }
                }

                $productCategories = join(", ", $output);

	            if ($gtagjsWithVat == 1) {
		            $productPrice = ($product->get_total() + $product->get_total_tax()) / $product->get_quantity();
	            } else {
		            $productPrice = $product->get_total() / $product->get_quantity();
	            }

	            $products[] = [
                    'id' => $id,
                    'name' => $product->get_name(),
//                    'list_name' => '',
//                    'brand' => '',
                    'category' => $productCategories,
//                    'variant' => '',
//                    'list_position' => ,
                    'quantity' => (int)$product->get_quantity(),
                    'price' => (float) $productPrice,
                ];
            }

        ?>
            <script>
              document.addEventListener('DOMContentLoaded', function () {
                gtag('event', 'purchase', {
                  "transaction_id": "<?= $order->get_id() ?>",
                  "affiliation": "<?= get_bloginfo('name') ?>",
                  "value": <?= $order->get_total() ?>,
                  "currency": "<?= $order->get_currency() ?>",
                  "tax": <?= $order->get_total_tax() ?>,
                  "shipping": <?= $order->get_shipping_total() ?>,
                  "items": <?= json_encode($products); ?>,
                  "google_business_vertical": "retail"
                });
              });
            </script>
        <?php
        endif;
    }

    public function googleTagManagerTransaction()
    {
        if (is_order_received_page()):
            $orderId = empty( $_GET['order'] ) ? ( $GLOBALS['wp']->query_vars['order-received'] ? $GLOBALS['wp']->query_vars['order-received'] : 0 ) : absint( $_GET['order'] );
            $orderId_filter = apply_filters( 'woocommerce_thankyou_order_id', $orderId );

            if ( $orderId_filter != '' ) {
                $orderId = $orderId_filter;
            }

            $active = $this->googleTagManagerService->isActive();
            $ecommerceActive = $this->googleTagManagerService->isEcommerceActive();
            $conversionVatIncluded = $this->googleTagManagerService->getConversionVatIncluded();

            if($active && $ecommerceActive):
                $order = wc_get_order($orderId);
                $products_tmp = $order->get_items();

                $products = array();

                foreach ($products_tmp as $product) {
                    if ($product->get_variation_id() == 0) {
                        $id = $product->get_data()['product_id'];
                    } else {
                        $id = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
                    }

                    $categories = get_the_terms($id, 'product_cat');

                    $output = [];
                    if ($categories) {
                        foreach ($categories as $category) {
                            $output[] = $category->name;
                        }
                    }

                    $productCategories = join(", ", $output);

                    if ($conversionVatIncluded == 1) {
                        $productPrice = ($product->get_total() + $product->get_total_tax()) / $product->get_quantity();
                    } else {
                        $productPrice = $product->get_total() / $product->get_quantity();
                    }

                    $products[] = [
                        'name' => $product->get_name(),
                        'sku' => (string)$id,
                        'category' => $productCategories,
                        'quantity' => (int)$product->get_quantity(),
                        'price' => (float) $productPrice,
                    ];

                }

                ?>
                <script>
                  dataLayer.push({
                    'transactionId': "<?= $order->get_id() ?>",
                    'transactionAffiliation': "<?= get_bloginfo('name') ?>",
                    'transactionTotal': <?= $order->get_total() ?>,
                    'transactionTax': <?= $order->get_total_tax() ?>,
                    'transactionShipping': <?= $order->get_shipping_total() ?>,
                    'transactionProducts': <?= json_encode($products) ?>
                  });
                </script>
            <?php
            endif;
        endif;
    }

    public function googleTagManagerPurchased()
    {
        if (is_order_received_page()):
            $orderId = empty( $_GET['order'] ) ? ( $GLOBALS['wp']->query_vars['order-received'] ? $GLOBALS['wp']->query_vars['order-received'] : 0 ) : absint( $_GET['order'] );
            $orderId_filter = apply_filters( 'woocommerce_thankyou_order_id', $orderId );

            if ( $orderId_filter != '' ) {
                $orderId = $orderId_filter;
            }

            global $woocommerce;

            $active = $this->googleTagManagerService->isActive();
            $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();
            $conversionVatIncluded = $this->googleTagManagerService->getConversionVatIncluded();

            if($active && $enhancedEcommerceActive):
                $order = wc_get_order($orderId);
                $products_tmp = $order->get_items();

                $products = array();

                foreach ($products_tmp as $product) {
                    if ($product->get_variation_id() == 0) {
                        $id = $product->get_data()['product_id'];
                    } else {
                        $id = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
                    }

                    $categories = get_the_terms($id, 'product_cat');

                    $output = [];
                    if ($categories) {
                        foreach ($categories as $category) {
                            $output[] = $category->name;
                        }
                    }

                    $productCategories = join(", ", $output);

                    if ($conversionVatIncluded == 1) {
                        $productPrice = ($product->get_total() + $product->get_total_tax()) / $product->get_quantity();
                    } else {
                        $productPrice = $product->get_total() / $product->get_quantity();
                    }

                    $products[] = [
                        'name' => $product->get_name(),
                        'id' => $id,
                        'category' => $productCategories,
                        'quantity' => (int)$product->get_quantity(),
                        'price' => (float) $productPrice,
                    ];

                }

                $coupons = join(', ', $woocommerce->cart->get_applied_coupons());
                ?>
                <script>
                    dataLayer.push({
                        'event': 'purchase',
                        'ecommerce': {
                            'currencyCode' : "<?= $order->get_currency() ?>",
                            'purchase': {
                                'actionField': {
                                    'id': "<?= $order->get_id() ?>",
                                    'affiliation': "<?= get_bloginfo('name') ?>",
                                    'revenue': '<?= $order->get_total() ?>',
                                    'tax': '<?= $order->get_total_tax() ?>',
                                    'shipping': '<?= $order->get_shipping_total()?>',
                                    'coupon': '<?= $coupons ?>'
                                },
                                'products': <?= json_encode($products) ?>
                            }
                        }
                    });
                </script>
            <?php
            endif;
        endif;
    }

    public function gtagjsAddToCart()
    {
        // Disable if woodmart theme because of incompatibility
        if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], ['xoo_wsc_add_to_cart', 'woodmart_ajax_add_to_cart'])) {
            return false;
        }

        $active = get_option(Settings::GOOGLE_GTAGJS['ACTIVE'], 0);
        $code = $this->getFormattedAnalyticsCode();
        $tracking = get_option(Settings::GOOGLE_GTAGJS['TRACKING']);
        $ecommerce = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE']);
        $ecommerceEnhanced = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE_ENHANCED']);

        $googleAdsRemarketingActive = $this->googleAdsService->isRemarketingActive();

        if (($active == 1 && $tracking == 1 && $code != '' && $ecommerce == 1 && $ecommerceEnhanced == 1) || ($googleAdsRemarketingActive)):
            $sendTo = implode(',', [$code, $this->googleAdsService->getConversionCode()]);

            if (isset($_POST['add-to-cart'])):
                $product = wc_get_product($_POST['add-to-cart']);

                if ($product->get_type() === 'grouped'): // Check if grouped product
                    if (!isset($_POST['groupedGTAG'])): // Check if request is duplicate (grouped products send two posts with same data)
                        $_POST['groupedGTAG'] = true; // Set variable that disable next call of same addToCart

                        foreach ($_POST['quantity'] as $id => $quantity):
                            $product = wc_get_product($id); // No need for ID changing because only simple products can be added on grouped page
                            $categories = get_the_terms($id, 'product_cat');

                            $output = [];
                            if ($categories) {
                                foreach ($categories as $category) {
                                    $output[] = $category->name;
                                }
                            }

                            $productCategories = join(", ", $output);

                            ?>
                                <script>
                                  document.addEventListener("DOMContentLoaded", function () {
                                    gtag('event', 'add_to_cart', {
                                      'currency': '<?= get_woocommerce_currency() ?>',
                                      "items": [
                                        {
                                          "id": "<?= $id ?>",
                                          "name": "<?= $product->get_name() ?>",
                                          // "list_name": "Search Results",
                                          // "brand": "Google",
                                          "category": "<?= $productCategories ?>",
                                          // "variant": "Black",
                                          // "list_position": 1,
                                          "quantity": <?= $quantity ?>,
                                          "price": '<?= $product->get_price() ?>',
                                          "google_business_vertical": "retail"
                                        }
                                      ]
                                    });
                                  });
                                </script>
                            <?php
                        endforeach;
                    endif;
                else:
                    $product = wc_get_product($_POST['add-to-cart']);

                    if (isset($_POST['variation_id']) && $_POST['variation_id'] && $_POST['variation_id'] !== '') {
                        $id = $product->get_data()['id'] . '-' . $_POST['variation_id'];
                    } else {
                        $id = $product->get_data()['id'];
                    }

                    $categories = get_the_terms($id, 'product_cat');

                    $output = [];
                    if ($categories) {
                        foreach ($categories as $category) {
                            $output[] = $category->name;
                        }
                    }

                    $productCategories = join(", ", $output);

                    ?>
                        <script>
                          document.addEventListener("DOMContentLoaded", function () {
                            gtag('event', 'add_to_cart', {
                              'currency': '<?= get_woocommerce_currency() ?>',
                              "items": [
                                {
                                  "id": "<?= $id ?>",
                                  "name": "<?= $product->get_name() ?>",
                                  // "list_name": "Search Results",
                                  // "brand": "Google",
                                  "category": "<?= $productCategories ?>",
                                  // "variant": "Black",
                                  // "list_position": 1,
                                  "quantity": <?= $_POST['quantity'] ?>,
                                  "price": '<?= $product->get_price() ?>',
                                  "google_business_vertical": "retail"
                                }
                              ],
                              'send_to': '<?= $sendTo ?>',
                            });
                          });
                        </script>
                    <?php
                endif;
            endif;
        endif;
    }

    public function googleTagManagerAddToCart()
    {
        // Disable if woodmart theme because of incompatibility
        if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], ['xoo_wsc_add_to_cart', 'woodmart_ajax_add_to_cart'])) {
            return false;
        }

        $active = $this->googleTagManagerService->isActive();
        $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

        if($active && $enhancedEcommerceActive):
            if (isset($_POST['add-to-cart'])):
                $product = wc_get_product($_POST['add-to-cart']);

                if ($product->get_type() === 'grouped'): // Check if grouped product
                    if (!isset($_POST['groupedGTM'])): // Check if request is duplicate (grouped products send two posts with same data)
                        $_POST['groupedGTM'] = true; // Set variable that disable next call of same addToCart

                        foreach($_POST['quantity'] as $id => $quantity):
                            $product = wc_get_product($id); // No need for ID changing because only simple products can be added on grouped page

                            $categories = get_the_terms($id, 'product_cat');
                            $output = [];
                            if ($categories) {
                                foreach ($categories as $category) {
                                    $output[] = $category->name;
                                }
                            }

                            $productCategories = join(", ", $output);
                            ?>
                                <script>
                                  document.addEventListener("DOMContentLoaded", function () {
                                    dataLayer.push({
                                      'event' : 'addToCart',
                                      'ecommerce' : {
                                        'currencyCode': '<?= get_woocommerce_currency() ?>',
                                        'add' : {
                                          'products': [{
                                            'name': "<?= $product->get_name() ?>",
                                            'id': "<?= $id ?>",
                                            'price': '<?= $product->get_price() ?>',
                                            'quantity': <?= $quantity ?>,
                                            'category': "<?= $productCategories ?>"
                                          }]
                                        }
                                      }
                                    })
                                  });
                                </script>
                            <?php
                        endforeach;
                    endif;
                else:// Simple and cimplicated products
                    $product = wc_get_product($_POST['add-to-cart']);


                    if (isset($_POST['variation_id']) && $_POST['variation_id'] && $_POST['variation_id'] !== '') {
                        $id = $product->get_data()['id'] . '-' . $_POST['variation_id'];
                    } else {
                        $id = $product->get_data()['id'];
                    }

                    $categories = get_the_terms($id, 'product_cat');

                    $output = [];
                    if ($categories) {
                        foreach ($categories as $category) {
                            $output[] = $category->name;
                        }
                    }

                    $productCategories = join(", ", $output);

                    ?>
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            dataLayer.push({
                                'event' : 'addToCart',
                                'ecommerce' : {
                                    'currencyCode': '<?= get_woocommerce_currency() ?>',
                                'add' : {
                                    'products': [{
                                        'name': "<?= $product->get_name() ?>",
                                        'id': "<?= $id ?>",
                                        'price': '<?= $product->get_price() ?>',
                                        'quantity': <?= $_POST['quantity'] ?>,
                                        'category': "<?= $productCategories ?>"
                                    }]
                                }
                            }
                        })
                        });
                    </script>
                <?php
                endif;
            endif;
        endif;
    }

    public function gtagjsAddToCartAjax()
    {
        $active = get_option(Settings::GOOGLE_GTAGJS['ACTIVE'], 0);
        $code = $this->getFormattedAnalyticsCode();
        $tracking = get_option(Settings::GOOGLE_GTAGJS['TRACKING']);
        $ecommerce = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE']);
        $ecommerceEnhanced = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE_ENHANCED']);

        $googleAdsRemarketingActive = $this->googleAdsService->isRemarketingActive();

        if(($active == 1 && $tracking == 1 && $code != '' && $ecommerce == 1 && $ecommerceEnhanced == 1) || ($googleAdsRemarketingActive)):
            $sendTo = implode(',', [$code, $this->googleAdsService->getConversionCode()]);
            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .ajax_add_to_cart';

                    if (typeof window.xoo_wsc_params !== 'undefined') {
                      mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .ajax_add_to_cart, body.single-product .single_add_to_cart_button';
                    }

                    jQuery(mmpSelector).on('click', function () {

                        var prodData = JSON.parse(jQuery(this).closest('.product').find('[data-metadata-product-list]').attr('data-metadata-product-list'));

                        gtag('event', 'add_to_cart', {
                            'currency': '<?= get_woocommerce_currency() ?>',
                            "items": [
                                {
                                    "id": prodData['full_id'],
                                    "name": prodData['name'],
                                    // "list_name": "Search Results",
                                    // "brand": "Google",
                                    "category": prodData['category'],
                                    // "variant": "Black",
                                    // "list_position": 1,
                                    "quantity": 1,
                                    "price": prodData['price'],
                                    "google_business_vertical": "retail"
                                }
                            ],
                          'send_to': '<?= $sendTo ?>',
                        });
                    });
                });
            </script>
        <?php
        endif;
    }

    public function googleTagManagerAddToCartAjax()
    {

        $active = $this->googleTagManagerService->isActive();
        $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

        if($active && $enhancedEcommerceActive):
            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .ajax_add_to_cart';

                    if (typeof window.xoo_wsc_params !== 'undefined') {
                      mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .ajax_add_to_cart, body.single-product .single_add_to_cart_button';
                    }

                    jQuery(mmpSelector).on('click', function () {

                        var prodData = JSON.parse(jQuery(this).closest('.product').find('[data-metadata-product-list]').attr('data-metadata-product-list'));

                        dataLayer.push({
                            'event' : 'addToCart',
                            'ecommerce' : {
                                'currencyCode': jQuery('#mergadoSetup').attr('data-currency'),
                                'add' : {
                                    'products': [{
                                        'name': prodData['name'],
                                        'id': prodData['full_id'],
                                        'price': prodData['price'],
                                        'quantity': 1,
                                        'category': prodData['category']
                                    }]
                                }
                            }
                        })
                    });
                });
            </script>
        <?php
        endif;
    }

    public function gtagjsRemoveFromCartAjax()
    {

        $active = get_option(Settings::GOOGLE_GTAGJS['ACTIVE'], 0);
        $code = $this->getFormattedAnalyticsCode();
        $tracking = get_option(Settings::GOOGLE_GTAGJS['TRACKING']);
        $ecommerce = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE']);
        $ecommerceEnhanced = get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE_ENHANCED']);

        if($active == 1 && $tracking == 1 && $code != '' && $ecommerce == 1 && $ecommerceEnhanced == 1):
            global $woocommerce;
            $products = [];

            foreach($woocommerce->cart->cart_contents as $key => $item):
                if ($item['variation_id'] == 0) {
                    $id = $item['product_id'];
                } else {
                    $id = $item['product_id'] . '-' . $item['variation_id'];
                }

                $category = get_the_terms($id, "product_cat");
                $categories = [];

                if ($category) {
                    foreach ($category as $term) {
                        $categories[] = $term->name;
                    }
                }

                $id = $item['product_id'];
                $name = $item['data']->get_name();
                $category = join(', ', $categories);
                $price = $item['data']->get_price();

                $products[wc_get_cart_remove_url($key)] = [
                    'id' => $id,
                    'name' => $name,
                    'category' => $category,
                    'price' => $price,
                ];
            endforeach;

            ?>
            <div style="display: none;" data-mergado-cart-data='<?= htmlspecialchars(json_encode($products), ENT_QUOTES) ?>'></div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var checkit = window.check_var;

                    if(checkit === undefined) {
                        window.check_var = 1;
                        var mergadoProductsData = JSON.parse(jQuery('[data-mergado-cart-data]').attr('data-mergado-cart-data'));

                        jQuery('body').on('click', '.product-remove a.remove', function () {
                            var href = jQuery(this).attr('href');

                            gtag('event', 'remove_from_cart', {
                                'currency': '<?= get_woocommerce_currency() ?>',
                                "items": mergadoProductsData[href]
                            });
                        });
                    }
                });
            </script>
        <?php
        endif;
    }

    public function googleTagManagerRemoveFromCartAjax()
    {
        $active = $this->googleTagManagerService->isActive();
        $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

        if($active && $enhancedEcommerceActive):
            global $woocommerce;
            $products = [];

            foreach($woocommerce->cart->cart_contents as $key => $item):
                if ($item['variation_id'] == 0) {
                    $id = $item['product_id'];
                } else {
                    $id = $item['product_id'] . '-' . $item['variation_id'];
                }

                $category = get_the_terms($id, "product_cat");
                $categories = [];

                if ($category) {
                    foreach ($category as $term) {
                        $categories[] = $term->name;
                    }
                }

                $id = $item['product_id'];
                $name = $item['data']->get_name();
                $category = join(', ', $categories);
                $price = $item['data']->get_price();

                $products[wc_get_cart_remove_url($key)] = [
                    'name' => $name,
                    'id' => $id,
                    'category' => $category,
                    'price' => $price,
                ];
            endforeach;

            ?>

            <div style="display: none;" data-mergado-gtm-cart-data='<?= json_encode($products) ?>'></div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var mergadoProductsData = JSON.parse(jQuery('[data-mergado-gtm-cart-data]').attr('data-mergado-gtm-cart-data'));

                    jQuery('body').on('click', '.product-remove a.remove',  function () {
                        var href = jQuery(this).attr('href');

                        dataLayer.push({
                            'event': 'removeFromCart',
                            'ecommerce': {
                                'currencyCode': '<?= get_woocommerce_currency() ?>',
                                'remove': {
                                    'products': mergadoProductsData[href]
                                }
                            }
                        });
                    });
                });
            </script>
        <?php
        endif;
    }


    public function bianoHeader()
    {
        $lang = Languages::getLang();

        $bianoActive = get_option(Settings::BIANO['ACTIVE']);
        $bianoLanguageActive = get_option(Settings::BIANO['FORM_ACTIVE'] . '-' . $lang);
        $bianoMerchantId = get_option(Settings::BIANO['MERCHANT_ID'] . '-' . $lang);

        if ($bianoActive == '1' && $bianoLanguageActive == '1' && $bianoMerchantId && $bianoMerchantId !== ''):
            if (in_array($lang, Settings::BIANO['LANG_OPTIONS'])):
                if (CookieClass::advertismentEnabled()): ?>
                    <!-- Biano Pixel Code -->
                    <script>
                        var merchantId = '<?= $bianoMerchantId ?>';
                        !function(b,i,a,n,o,p,x)
                        {if(b.bianoTrack)return;o=b.bianoTrack=function(){o.callMethod?
                            o.callMethod.apply(o,arguments):o.queue.push(arguments)};
                            o.push=o;o.queue=[];p=i.createElement(a);p.async=!0;p.src=n;
                            x=i.getElementsByTagName(a)[0];x.parentNode.insertBefore(p,x)
                         }(window,document,'script','https://pixel.biano.<?=strtolower($lang)?>/min/pixel.js');
                         //}(window,document,'script','https://pixel.biano.<?=strtolower($lang)?>/debug/pixel.js'); // Debug
                        bianoTrack('init', merchantId);

                        <?php if(is_product()): ?>
                            bianoTrack('track', 'product_view', {id: '<?=wc_get_product()->get_id(); ?>'});
                        <?php else: ?>
                            bianoTrack('track', 'page_view');
                        <?php endif ?>
                    </script>
                    <!-- End Biano Pixel Code -->
                <?php else: ?>
                    <script>
                        window.mmp.cookies.sections.advertisement.functions.bianoPixel = function () {

                            <!-- Biano Pixel Code -->
                                var merchantId = '<?= $bianoMerchantId ?>';
                                !function(b,i,a,n,o,p,x)
                                {if(b.bianoTrack)return;o=b.bianoTrack=function(){o.callMethod?
                                    o.callMethod.apply(o,arguments):o.queue.push(arguments)};
                                    o.push=o;o.queue=[];p=i.createElement(a);p.async=!0;p.src=n;
                                    x=i.getElementsByTagName(a)[0];x.parentNode.insertBefore(p,x)
                                }(window,document,'script','https://pixel.biano.<?=strtolower($lang)?>/min/pixel.js');
                                //}(window,document,'script','https://pixel.biano.<?=strtolower($lang)?>/debug/pixel.js'); // Debug
                                bianoTrack('init', merchantId);
                            <!-- End Biano Pixel Code -->

	                        <?php if(is_product()): ?>
                                bianoTrack('track', 'product_view', {id: '<?=wc_get_product()->get_id(); ?>'});
	                        <?php else: ?>
                                bianoTrack('track', 'page_view');
	                        <?php endif ?>
                        };
                    </script>
                <?php endif; ?>
            <?php else:
	            if (CookieClass::advertismentEnabled()): ?>
                    <script>
                        !function(b,i,a,n,o,p,x) {if(b.bianoTrack)return;o=b.bianoTrack=function(){ o.queue.push(arguments)};o.push=o;o.queue=[]; }(window,document);

                        <?php if(is_product()): ?>
                            bianoTrack('track', 'product_view', {id: '<?=wc_get_product()->get_id(); ?>'});
                        <?php else: ?>
                            bianoTrack('track', 'page_view');
                        <?php endif ?>
                    </script>
                <?php else: ?>
                    <script>
                        window.mmp.cookies.sections.advertisement.functions.bianoPixel = function () {

                            !function(b,i,a,n,o,p,x) {if(b.bianoTrack)return;o=b.bianoTrack=function(){ o.queue.push(arguments)};o.push=o;o.queue=[]; }(window,document);

                            <?php if(is_product()): ?>
                                bianoTrack('track', 'product_view', {id: '<?=wc_get_product()->get_id(); ?>'});
                            <?php else: ?>
                                bianoTrack('track', 'page_view');
                            <?php endif ?>
                        };
                    </script>
                <?php endif; ?>
            <?php endif ?>
        <?php endif;
    }

    public function bianoPurchased($orderId)
    {
        $bianoActive = get_option(Settings::BIANO['ACTIVE']);
        $bianoWithVat = get_option(Settings::BIANO['CONVERSION_VAT_INCL'], 0);

        if ($bianoActive == '1'):
                $order = wc_get_order($orderId);
                $products_tmp = $order->get_items();
                $email = $order->get_billing_email();

                //Set prices with or without vat
                // Specification looks that `quantity * unit_price` should be order_total
                if ($bianoWithVat == 1) {
                    $orderPrice = number_format( (float) $order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '' );
                } else {
                    $orderPrice = number_format( (float) $order->get_total() - $order->get_total_tax() - $order->get_shipping_total(), wc_get_price_decimals(), '.', '' );
                }

                $products = array();
                foreach ($products_tmp as $product) {
                    if ($product->get_variation_id() == 0) {
                        $id = $product->get_data()['product_id'];
                    } else {
                        $id = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
                    }

                    //Set prices with or without vat
                    if ($bianoWithVat == 1) {
	                    $products[] = [
	                            'id' => (string)$id,
                                'quantity' => (int)$product->get_quantity(),
                                'unit_price' => (float) ($product->get_total() + $product->get_total_tax()),
                        ];
                    } else {
	                    $products[] = [
		                    'id'         => (string) $id,
		                    'quantity'   => (int) $product->get_quantity(),
		                    'unit_price' => (float) $product->get_total()
	                    ];
                    }
                }

                ?>
                <script>
                    bianoTrack('track', 'purchase', {
                        id: '<?php echo $orderId ?>',
                        customer_email: '<?php echo $email; ?>,
                        order_price: <?php echo (float)$orderPrice ?>,
                        currency: '<?php echo $order->get_currency() ?>',
                        items: <?php echo json_encode($products) ?>});
                </script>
            <?php
        endif;
    }

    public function glamiTOP($orderId)
    {
        $lang = Languages::getLang();
        $langISO = Languages::getLangIso();

        $active = $this->glamiTopService->isActive();
        $selection = $this->glamiTopService->getSelection();
        $code = $this->glamiTopService->getCode();

        if ($active && $selection) {
            $domain = $selection['name'];

            $order = wc_get_order($orderId);
            $products_tmp = $order->get_items();

            $products = array();

            foreach ($products_tmp as $product) {
                if ($product->get_variation_id() == 0) {
                    $id = $product->get_data()['product_id'];
                } else {
                    $id = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
                }

                $products[] = ['id' => $id, 'name' => $product->get_name()];
            }

            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    (function (f, a, s, h, i, o, n) {
                        f['GlamiOrderReview'] = i;
                        f[i] = f[i] || function () {(f[i].q = f[i].q || []).push(arguments);};
                        o = a.createElement(s), n = a.getElementsByTagName(s)[0];
                        o.async = 1; o.src = h; n.parentNode.insertBefore(o, n);
                    })(window,document,'script','//www.<?= $domain ?>/js/compiled/or.js', 'glami_or');

                    glami_or('addParameter', 'merchant_id', '<?= $code ?>', '<?= strtolower($lang) ?>');
                    glami_or('addParameter', 'order_id', '<?= $orderId; ?>');
                    glami_or('addParameter', 'email', '<?= $order->get_billing_email() ?>');
                    glami_or('addParameter', 'language', '<?= strtolower($langISO) ?>');
                    glami_or('addParameter', 'items', <?= json_encode($products) ?>);

                    glami_or('create');
                });
            </script>
            <?php
        }
    }


    private function zboziConversions($orderId)
    {
        $ZboziClass = new ZboziService();

        if ($ZboziClass->isActive()):

            echo '<script>
            var conversionOrderId = ' . $orderId . ';
            var conversionZboziShopId = ' . $ZboziClass->getId() . ';
            var useSandbox = ' . (int) ZboziService::ZBOZI_SANDBOX . ';
            var consent = ' . (int)CookieClass::advertismentEnabled() . ';
    
            // Set cookie to prevent sending same order for zbozi.cz multiple times
            function setCookie(cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d.toUTCString();
                document.cookie = getCookieName(conversionZboziShopId, conversionOrderId) + "=" + cname + ";" + expires + ";path=/";
            }
    
            function getCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(";");
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == " ") {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }
    
            function getCookieName(cshop, cid) {
                return "_" + cshop + "_" + cid;
            }
    
        </script>';

            if ($ZboziClass->isAdvanced()):
                echo '<script>
                    if (getCookie(getCookieName(conversionZboziShopId, conversionOrderId)) === "") {
                        (function (w, d, s, u, n, k, c, t) {
                            w . ZboziConversionObject = n;
                            w[n] = w[n] || function ()
                            {
                                (w[n] . q = w[n] . q || []) . push(arguments)
                            };
                            w[n] . key = k;
                            c = d . createElement(s);
                            t = d . getElementsByTagName(s)[0];
                            c . async = 1;
                            c . src = u;
                            t . parentNode . insertBefore(c, t)
                        })
                        (window, document, "script", "https://www.zbozi.cz/conversion/js/conv-v3.js", "zbozi", conversionZboziShopId);
        
                        if (useSandbox) {
                            zbozi("useSandbox");
                        }
        
                        zbozi("setOrder", {
                            "orderId": conversionOrderId,
                            "consent": consent
                        });
        
                        zbozi("send");
                        setCookie(conversionOrderId, conversionZboziShopId, 15);
                    }
                </script >';
            else:
                echo '<script>
                        if (getCookie(getCookieName(conversionZboziShopId, conversionOrderId)) === "") {
                        (function (w, d, s, u, n, k, c, t) {
                            w.ZboziConversionObject = n;
                            w[n] = w[n] || function () {
                                (w[n].q = w[n].q || []).push(arguments)
                            };
                            w[n].key = k;
                            c = d.createElement(s);
                            t = d.getElementsByTagName(s)[0];
                            c.async = 1;
                            c.src = u;
                            t.parentNode.insertBefore(c, t)
                        })(window, document, "script", "https://www.zbozi.cz/conversion/js/conv.js", "zbozi", conversionZboziShopId);
        
                        if(useSandbox) {
                            zbozi("useSandbox");
                        }
        
                        zbozi("setOrder", {
                            "orderId": conversionOrderId,
                            "consent": consent
                        });
        
                        zbozi("send");
                        setCookie(conversionOrderId, getCookieName(conversionZboziShopId, conversionOrderId), 15);
                    }
                </script>';
            endif;
        endif;
    }

    /*******************************************************************************************************************
     * FOOTER SETUP - SKLIK, ADWORDS, ETARGET
     *******************************************************************************************************************/

    public function glamiData()
    {
        $lang = Languages::getLang();
        $active = $this->glamiPixelService->isActive($lang);
        $code = $this->glamiPixelService->getCode($lang);

        if($active) {
        ?>

        <script>
            var __glamiActive = <?= (int) $active ?>;
            var __glamiCode = '<?= $code ?>';
            var __lang = '<?= strtolower($lang) ?>';
        </script>
        <?php
        }
    }

    public function mergadoFooterSetup($orderId)
    {
        $googleReviewsClass = new GoogleReviewsService();

        echo '<div id="mergadoSetup" data-currency="' . get_woocommerce_currency() . '"></div>';

        //Should be after body tag but not available in old (< 5.2) versions ... fallback
        if ( ! function_exists( 'wp_body_open' ) ) {
            $this->googleTagManagerAfterBody();
        }

        $this->fbPixel( $orderId ); // GDPR managed inside own logic
        $this->fbPixelAddToCartAjax();

	    if (CookieClass::advertismentEnabled()) {
		    $this->etargetServiceIntegration->etargetRetarget();
		    $this->bianoAddToCartAjax();
	    }

        $this->glamiPixel();
        $this->sklikRetargeting();

        $googleReviewsClass->getBadgeTemplate();
        HeurekaService::heurekaWidget();
        $this->arukeresoService->getWidgetTemplate();

        $this->gtagjsRemoveFromCartAjax();

        $this->googleTagManagerRemoveFromCartAjax();
        $this->googleTagManagerAddToCartAjax();

        $this->gtagjsAddToCartAjax(); // GDPR resolved inside

	    if (CookieClass::advertismentEnabled()) {
            // Code for glami.js
            $lang = Languages::getLang();

            if($lang == 'CS') {
                $lang = 'CZ';
            }

            $active = $this->glamiPixelService->isActive($lang);
            $code = $this->glamiPixelService->getCode($lang);

            if ($active) {
            ?>

            <script>
                var __glamiActive = <?= (int) $active ?>;
                var __glamiCode = '<?= $code ?>';
                var __lang = '<?= strtolower($lang) ?>';
            </script>
            <?php
            }
	    }

        //Method that need to be called later (because of initializing their object)
        echo $this->headerExtra;
    }

    private function sklikRetargeting()
    {
        $sklikActive = get_option(Settings::SKLIK['RETARGETING_ACTIVE']);
        $sklikRetargeting = get_option(Settings::SKLIK['RETARGETING_ID']);

        if ($sklikActive != 0 && $sklikRetargeting != '') {
            echo '<div id="sklikRetargeting">
            <script type="text/javascript">
            /* <![CDATA[ */
            var seznam_retargeting_id = ' . $sklikRetargeting . '
            var rc = rc || {};
            rc.consent = ' . (int) CookieClass::advertismentEnabled() . '; // CCC = 0 nebo 1
            /* ]]> */
            </script>
            <script type="text/javascript" src="//c.imedia.cz/js/retargeting.js"></script></div>';

            echo '<script>
                window.mmp.cookies.sections.advertisement.functions.sklikRetargeting = function () {
                  rc.consent = 1;
                };
            </script>';
        }
    }

    private function fbPixel($orderId)
    {
        $active = $this->facebookService->isActive();
        $code = $this->facebookService->getCode();

        if ($active) {
            ?>

            <script>
              !function (f, b, e, v, n, t, s) {
                    if (f.fbq)
                        return;
                    n = f.fbq = function () {
                        n.callMethod ?
                            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                    };
                    if (!f._fbq)
                        f._fbq = n;
                    n.push = n;
                    n.loaded = !0;
                    n.version = '2.0';
                    n.queue = [];
                    t = b.createElement(e);
                    t.async = !0;
                    t.src = v;
                    s = b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t, s)
                }(window,
                    document, 'script', '//connect.facebook.net/en_US/fbevents.js');

                <?php if(CookieClass::advertismentEnabled()): ?>
                    fbq('consent', 'grant');
                <?php else: ?>
                    fbq('consent', 'revoke');
                <?php endif; ?>

                window.mmp.cookies.sections.advertisement.functions.fbpixel = function () {
                  fbq('consent', 'grant');
                  fbq('track', 'PageView');
                };

                fbq('init', '<?= $code; ?>');
                fbq('track', 'PageView');

                <?php
                if(is_product()) {
                    $product = get_queried_object();
                ?>

                var id = '';

                if (document.getElementsByClassName('variation_id')[0] && document.getElementsByClassName('variation_id')[0].value != 0) {
                    id = <?= $product->ID ?> + '-' + document.getElementsByClassName('variation_id')[0].value;
                } else {
                    id = <?= $product->ID ?>;
                }

                fbq('trackCustom', 'ViewContent', {
                    content_name: '<?php echo $product->post_title; ?>',
                    content_type: 'product',
                    content_ids: [id]
                });

                <?php
                } elseif (is_product_category()) {
                $category = get_queried_object();
                $products_tmp = wc_get_products(['category' => [$category->slug]]);
                $products = [];

                foreach ($products_tmp as $product) {
                    $id = $product->get_id();

                    $products['ids'][] = "'" . $id . "'";
                    $products['name'][] = "'" . $product->get_name() . "'";
                }
                ?>

                fbq('trackCustom', 'ViewCategory', {
                    content_name: '<?php echo $category->name; ?>',
                    content_type: 'product',
                    content_ids: [<?php echo implode(',', $products['ids']); ?>]
                });
                <?php
                } elseif (is_search()) {
                    $searchQuery = get_search_query();
                    $products = ['ids' => []];

                    global $wp_query;

                    $posts = $wp_query->get_posts();

                    foreach($posts as $post) {
                        if(get_post_type($post) === 'product') {
                            $product = wc_get_product($post->ID);

                            $products['ids'][] = "'" . $product->get_id() . "'";
                        }
                    }
                ?>
                     fbq('track', 'Search', {
                        search_string: '<?= $searchQuery ?>',
                        content_ids: [<?= implode(',', $products['ids']); ?>],
                        content_type: 'product',
                     });
                    <?php
//                } else {
                ?>
                // fbq('track', 'ViewContent', {
                //    content_name: '<?php //echo get_the_title(); ?>//'
                // });
                <?php
                }
                ?>
            </script>
            <?php

        }
    }

    public function createJsVariables()
    {
        // Basic wrapper
        ?>
        <script type="text/javascript">
            window.mmp = {};
        </script>
        <?php

        CookieClass::createJsVariables();
    }

    public function getFormattedAdsCode($code)
    {
        $exploded = explode('-', $code);

        if (!isset($exploded[1])) {
            return $code;
        } else {
            return $exploded[1];
        }
    }

    public function getFormattedAnalyticsCode()
    {
        $gaMeasurementId = get_option(Settings::GOOGLE_GTAGJS['CODE']);

        // add prefix if not exist
        if (trim($gaMeasurementId) !== '' && substr( $gaMeasurementId, 0, 3 ) !== "UA-") {
            $gaMeasurementId = 'UA-' . $gaMeasurementId;
        }

        return $gaMeasurementId;
    }
}
