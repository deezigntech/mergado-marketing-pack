<?php

/**
 * NOTICE OF LICENSE.
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    www.mergado.cz
 * @copyright 2016 Mergado technologies, s. r. o.
 * @license   LICENSE.txt
 */

namespace Mergado\Tools;

use Mergado\Arukereso\ArukeresoClass;
use Mergado\Facebook\FacebookClass;
use Mergado\Glami\GlamiPixelClass;
use Mergado\Glami\GlamiTopClass;
use Mergado\Google\GaRefundClass;
use Mergado\Google\GoogleAdsClass;
use Mergado\Google\GoogleReviewsClass;
use Mergado\Google\GoogleTagManagerClass;
use Mergado\Zbozi\ZboziClass;

include_once __MERGADO_DIR__ . 'autoload.php';

class Settings
{
    const KELKOO = array(
        'ACTIVE' => 'kelkoo_active',
        'COM_ID' => 'kelkoo_merchant_id',
        'COUNTRY' => 'kelkoo_country',
        'CONVERSION_VAT_INCL' => 'kelkoo-vat-included',
    );

    const KELKOO_COUNTRIES = array(
        array('id_option' => 1, 'name' => 'Austria', 'type_code' => 'at'),
        array('id_option' => 2, 'name' => 'Belgium', 'type_code' => 'be'),
        array('id_option' => 3, 'name' => 'Brazil', 'type_code' => 'br'),
        array('id_option' => 4, 'name' => 'Switzerland', 'type_code' => 'ch'),
        array('id_option' => 5, 'name' => 'Czech Republic', 'type_code' => 'cz'),
        array('id_option' => 6, 'name' => 'Germany', 'type_code' => 'de'),
        array('id_option' => 7, 'name' => 'Denmark', 'type_code' => 'dk'),
        array('id_option' => 8, 'name' => 'Spain', 'type_code' => 'es'),
        array('id_option' => 9, 'name' => 'Finland', 'type_code' => 'fi'),
        array('id_option' => 10, 'name' => 'France', 'type_code' => 'fr'),
        array('id_option' => 11, 'name' => 'Ireland', 'type_code' => 'ie'),
        array('id_option' => 12, 'name' => 'Italy', 'type_code' => 'it'),
        array('id_option' => 13, 'name' => 'Mexico', 'type_code' => 'mx'),
        array('id_option' => 14, 'name' => 'Flemish Belgium', 'type_code' => 'nb'),
        array('id_option' => 15, 'name' => 'Netherlands', 'type_code' => 'nl'),
        array('id_option' => 16, 'name' => 'Norway', 'type_code' => 'no'),
        array('id_option' => 17, 'name' => 'Poland', 'type_code' => 'pl'),
        array('id_option' => 18, 'name' => 'Portugal', 'type_code' => 'pt'),
        array('id_option' => 19, 'name' => 'Russia', 'type_code' => 'ru'),
        array('id_option' => 20, 'name' => 'Sweden', 'type_code' => 'se'),
        array('id_option' => 21, 'name' => 'United Kingdom', 'type_code' => 'uk'),
        array('id_option' => 22, 'name' => 'United States', 'type_code' => 'us'),
    );

    const SKLIK = [
        'CONVERSION_ACTIVE' => 'sklik-form-conversion-active',
        'RETARGETING_ACTIVE' => 'sklik-form-retargeting-active',
        'CONVERSION_CODE' => 'sklik-form-conversion-code',
        'CONVERSION_VALUE' => 'sklik-form-conversion-value',
        'RETARGETING_ID' => 'sklik-form-retargeting-id',
        'CONVERSION_VAT_INCL' => 'sklik-vat-included',
    ];

    const GOOGLE_GTAGJS = array(
        'ACTIVE' => 'mergado_google_analytics_active',
        'CODE' => 'mergado_google_analytics_code',
        'TRACKING' => 'mergado_google_analytics_tracking',
        'ECOMMERCE' => 'mergado_google_analytics_ecommerce',
        'ECOMMERCE_ENHANCED' => 'mergado_google_analytics_ecommerce_enhanced',
        'CONVERSION_VAT_INCL' => 'gtagjs-vat-included',
    );

    const ETARGET = [
        'ACTIVE' => 'etarget-form-active',
        'HASH' => 'etarget-form-hash',
        'ID' => 'etarget-form-id'
    ];

    const NAJNAKUP = [
        'ACTIVE' => 'najnakup-form-active',
        'ID' => 'najnakup-form-id'
    ];

    const PRICEMANIA = [
        'ACTIVE' => 'pricemania-form-active',
        'ID' => 'pricemania-form-id'
    ];

    const HEUREKA = [
        //Verified
        'ACTIVE_CZ' => 'heureka-verified-cz-form-active',
        'ACTIVE_SK' => 'heureka-verified-sk-form-active',
        'VERIFIED_CZ' => 'heureka-verified-cz-form-code',
        'VERIFIED_SK' => 'heureka-verified-sk-form-code',

        //Verified - WIDGET
        'WIDGET_CZ' => 'heureka-widget-cz-form-active',
        'WIDGET_CZ_ID' => 'heureka-widget-cz-id',
        'WIDGET_CZ_POSITION' => 'heureka-widget-cz-position',
        'WIDGET_CZ_MARGIN' => 'heureka-widget-cz-margin',
        'WIDGET_CZ_SHOW_MOBILE' => 'heureka-widget-cz-show-mobile',
        'WIDGET_CZ_HIDE_WIDTH' => 'heureka-widget-cz-hide-width',
        'WIDGET_SK' => 'heureka-widget-sk-form-active',
        'WIDGET_SK_ID' => 'heureka-widget-sk-id',
        'WIDGET_SK_POSITION' => 'heureka-widget-sk-position',
        'WIDGET_SK_MARGIN' => 'heureka-widget-sk-margin',
        'WIDGET_SK_SHOW_MOBILE' => 'heureka-widget-sk-show-mobile',
        'WIDGET_SK_HIDE_WIDTH' => 'heureka-widget-sk-hide-width',

        //Tracking
        'ACTIVE_TRACK_CZ' => 'heureka-track-cz-form-active',
        'ACTIVE_TRACK_SK' => 'heureka-track-sk-form-active',
        'TRACK_CODE_CZ' => 'heureka-track-cz-form-code',
        'TRACK_CODE_SK' => 'heureka-track-sk-form-code',

        // Other - feed
        'STOCK_FEED' => 'heureka-stock-feed-form-active',
        'CONVERSION_VAT_INCL_CZ' => 'heureka-vat-cz-included',
        'CONVERSION_VAT_INCL_SK' => 'heureka-vat-sk-included',
    ];

    const IMPORT = [
        'LAST_UPDATE' => 'mergado_last_prices_import',
        'COUNT' => 'import-form-products'
    ];

    const CRONS = [
        'ACTIVE_PRODUCT_FEED' => 'wp-cron-product-feed-active',
        'ACTIVE_STOCK_FEED' => 'wp-cron-stock-feed-active',
        'ACTIVE_CATEGORY_FEED' => 'wp-cron-category-feed-active',
        'ACTIVE_IMPORT_FEED' => 'wp-cron-import-feed-active',
        'SCHEDULE_PRODUCT_FEED' => 'wp-cron-product-feed-schedule',
        'SCHEDULE_STOCK_FEED' => 'wp-cron-stock-feed-schedule',
        'SCHEDULE_CATEGORY_FEED' => 'wp-cron-category-feed-schedule',
        'SCHEDULE_IMPORT_FEED' => 'wp-cron-import-feed-schedule',
        'START_PRODUCT_FEED' => 'wp-cron-product-feed-start',
        'START_STOCK_FEED' => 'wp-cron-stock-feed-start',
        'START_CATEGORY_FEED' => 'wp-cron-category-feed-start',
        'START_IMPORT_FEED' => 'wp-cron-import-feed-start',
    ];

    const OPTIMIZATION = [
        'PRODUCT_FEED' => 'feed-form-products',
        'STOCK_FEED' => 'feed-form-stock',
        'CATEGORY_FEED' => 'feed-form-category',
        'IMPORT_FEED' => 'import-form-products',
    ];

    const VAT = 'm_feed_vat_option';

    const BIANO = array(
        'ACTIVE' => 'biano_active',
        'MERCHANT_ID' => 'biano_merchant_id',
        'FORM_ACTIVE' => 'biano-form-active-lang',
        'LANG_OPTIONS' => array('CZ', 'SK', 'RO', 'NL', 'HU'),
        'CONVERSION_VAT_INCL' => 'biano-vat-included',
    );

    const WIZARD = [
        'FINISHED_PRODUCT' => 'mmp-wizard-finished-product',
        'FINISHED_STOCK' => 'mmp-wizard-finished-stock',
        'FINISHED_CATEGORY' => 'mmp-wizard-finished-category',
        'FINISHED_IMPORT' => 'mmp-wizard-finished-import',
    ];

    const COOKIE_FIRST_RATING = 'mmp-cookie-first-rating';
    const COOKIE_RATING = 'mmp-cookie-rating';
    const COOKIE_NEWS = 'mmp-cookie-news';

	const WP_CRON_FORCED = 'mmp-wp-cron-forced'; // enable wp cron forms even with DISABLE_WP_CRON = true in wp-config.php

    /*******************************************************************************************************************
     * SAVE OPTIONS
     *******************************************************************************************************************/

    /**
     * @param $post
     * @param array $checkboxes
     * @param array $inputs
     * @param array|null $selectboxes
     */

    public static function saveOptions($post, array $checkboxes, array $inputs = [], array $selectboxes = null)
    {

        $logger = wc_get_logger();

        // Log if changed
        foreach ($checkboxes as $checkbox) {

            // Log if changed
            if (isset($post[$checkbox])) {
                if (get_option($checkbox) !== str_replace('off', '0', str_replace('on', '1', $post[$checkbox])) && isset($post[$checkbox])) {
                    $logger->info('OPTION CHANGED: ' . $checkbox . ' changed to ' . $post[$checkbox]);
                } elseif (get_option($checkbox) !== '' && !isset($post[$checkbox])) {
                    $logger->info('OPTION CHANGED: ' . $checkbox . ' changed to off');
                }
            }

            // Save
            if (isset($post[$checkbox]) && $post[$checkbox] === 'on') {
                update_option($checkbox, 1);

                // Schedule or remove cron from wp_cron
                if (strpos($checkbox, 'wp-cron-') !== false && strpos($checkbox, '-active') !== false) {
                    $name = explode('-active', $checkbox)[0];
                    // first start is disabled in cron settings from v3.0
                    // Crons::addTask($name, $post[$name . '-schedule'], $post[$name . '-start']);
                    Crons::addTask($name, $post[$name . '-schedule'], '');
                }
            } else {
                update_option($checkbox, 0);

                // Schedule or remove cron from wp_cron
                if (strpos($checkbox, 'wp-cron-') !== false && strpos($checkbox, '-active') !== false) {
                    $name = explode('-active', $checkbox)[0];
                    Crons::removeTask($name);
                }
            }
        }

	    if ($inputs !== null) {
		    foreach ( $inputs as $input ) {
			    // Log if changed
			    if ( ( isset( $input ) && isset( $post[ $input ] ) ) && get_option( $input ) !== $post[ $input ] ) {
				    $logger->info( 'OPTION CHANGED: ' . $input . ' changed to ' . $post[ $input ] );

				    if ( $input === Settings::OPTIMIZATION['PRODUCT_FEED'] ) {
					    update_option( XMLClass::FEED_COUNT['PRODUCT'], 0 );

					    $files = glob( __MERGADO_TMP_DIR__ . Settings::getCurrentBlogId() . '/productFeed/*' );

					    foreach ( $files as $file ) {
						    if ( is_file( $file ) ) {
							    unlink( $file );
						    }
					    }
				    } else if ( $input === Settings::OPTIMIZATION['CATEGORY_FEED'] ) {
					    update_option( XMLClass::FEED_COUNT['CATEGORY'], 0 );

					    $files = glob( __MERGADO_TMP_DIR__ . Settings::getCurrentBlogId() . '/categoryFeed/*' );

					    foreach ( $files as $file ) {
						    if ( is_file( $file ) ) {
							    unlink( $file );
						    }
					    }
				    } else if ( $input === Settings::OPTIMIZATION['STOCK_FEED'] ) {
					    update_option( XMLClass::FEED_COUNT['STOCK'], 0 );

					    $files = glob( __MERGADO_TMP_DIR__ . Settings::getCurrentBlogId() . '/stockFeed/*' );

					    foreach ( $files as $file ) {
						    if ( is_file( $file ) ) {
							    unlink( $file );
						    }
					    }
				    }

			    }

			    if ( isset( $post[ $input ] ) ) {
				    update_option( $input, trim( $post[ $input ] ) );
			    }
		    }
	    }

        if ($selectboxes !== null) {
            foreach ($selectboxes as $select) {
                // Log if changed
                if ((isset($select) && isset($post[$select])) && get_option($select) !== $post[$select]) {
                    $logger->info('OPTION CHANGED: ' . $select . ' changed to ' . $post[$select]);
                }

                if (isset($post[$select])) {
                    update_option($select, $post[$select]);
                }
            }
        }
    }

    /**
     * Return current blog ID
     * - Due of compatibility returning '' for non multisite (in future change to 0)
     * @return int|string
     */

    public static function getCurrentBlogId()
    {
        if (is_multisite()) {
            $currentBlogId = get_current_blog_id();
        } else {
            $currentBlogId = 0;
        }

        return $currentBlogId;
    }

    /**
     * Return if tax is calculated in woocommerce
     *
     * @return bool
     */
    public static function isTaxCalculated()
    {
        global $wpdb;
        $tax = $wpdb->get_col($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s", 'woocommerce_calc_taxes'));

        return array_pop($tax) == 'yes';
    }

    /**
     * Return if tax is already included in product price
     *
     * @return bool
     */
    public static function isTaxIncluded()
    {
        global $wpdb;
        $taxInc = $wpdb->get_col($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s", 'woocommerce_prices_include_tax'));

        return array_pop($taxInc) == 'yes';
    }

    /**
     * Return all rates options
     *
     * @return array|object|null
     */
    public static function getTaxRates()
    {
        global $wpdb;
        $query = $wpdb->prepare("
                SELECT DISTINCT tax_rate_country
                FROM {$wpdb->prefix}woocommerce_tax_rates
                WHERE tax_rate_country != %s
                ORDER BY tax_rate_country", '');

        return $wpdb->get_results($query);
    }

    /**
     * Return tax rate by ID
     *
     * @param $id
     * @return array|object|null
     */
    public static function getTaxRateById($id)
    {
        global $wpdb;
        $prepare = $wpdb->prepare(
            "SELECT * 
                    FROM {$wpdb->prefix}woocommerce_tax_rates 
                    WHERE tax_rate_id = %s
                    ORDER BY tax_rate_country", $id
        );

        $output = $wpdb->get_row($prepare)->tax_rate;

        if ($output === NULL) {
            $langISO = Languages::getLangIso();
            $output = self::getTaxRateForCountry($langISO);
        }

        return $output;
    }

    /**
     * Return tax rate with highest priority for countrycode (takes * if has higher priority)
     * @param string $countryCode
     * @return array|object|null
     */
    public static function getTaxRateForCountry($countryCode)
    {
        global $wpdb;
        $rates = $wpdb->get_row(
            $wpdb->prepare("
                SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates
                WHERE tax_rate_country = %s || tax_rate_country = ''
                ORDER BY tax_rate_priority
                DESC", $countryCode)
        );

        return $rates->tax_rate;
    }

    public static function getTaxRatesForCountry($countryCode, $tax_rate_class)
    {
        global $wpdb;
        $prepare = $wpdb->prepare(
            "SELECT * 
                    FROM {$wpdb->prefix}woocommerce_tax_rates 
                    WHERE tax_rate_country = %s && tax_rate_class = %s
                    ORDER BY tax_rate_priority
                    DESC", $countryCode, $tax_rate_class
        );

        $output = @$wpdb->get_row($prepare)->tax_rate;

        if (is_null($output)) {
            global $wpdb;
            $prepare = $wpdb->prepare(
                "SELECT * 
                    FROM {$wpdb->prefix}woocommerce_tax_rates 
                    WHERE tax_rate_country = %s && tax_rate_class = %s
                    ORDER BY tax_rate_priority
                    DESC", '', $tax_rate_class
            );

            $output = @$wpdb->get_row($prepare)->tax_rate;

            if (is_null($output)) {
                $output = 0; // 0% if not set any rate
            }
        }

        return $output;
    }

    public static function getInformationsForSupport()
    {
        $xmlProductFeed = new XMLProductFeed();
        $xmlCategoryFeed = new XMLCategoryFeed();
        $xmlStockFeed = new XMLStockFeed();
        $importPricesClass = new ImportPricesClass();
        $googleAds = new GoogleAdsClass();
        $googleTagManager = new GoogleTagManagerClass();
        $googleReviewsClass = new GoogleReviewsClass();
        $arukeresoClass = new ArukeresoClass();
        $glamiPixelClass = new GlamiPixelClass();
        $glamiTopClass = new GlamiTopClass();
        $zboziClass = new ZboziClass();
        $facebookClass = new FacebookClass();

        if (class_exists('WooCommerce')) {
            global $woocommerce;
            $woocomerceVersion = $woocommerce->version;
        } else {
            $woocomerceVersion = __('Not available', 'mergado-marketing-pack');
        }

        return [
            'base' => [
                'web_url' => [
                    'name' => __('Web URL', 'mergado-marketing-pack'),
                    'value' => get_site_url(),
                ],
                'token' => [
                    'name' => __('Token', 'mergado-marketing-pack'),
                    'value' => get_option('mmp_token'),
                ],
                'wp_version' => [
                    'name' => __('WP version', 'mergado-marketing-pack'),
                    'value' => get_bloginfo('version'),
                ],
                'wc_version' => [
                    'name' => __('WC version', 'mergado-marketing-pack'),
                    'value' => $woocomerceVersion,
                ],
                'mp_version' => [
                    'name' => __('MP version', 'mergado-marketing-pack'),
                    'value' => PLUGIN_VERSION,
                ],
                'php' => [
                    'name' => __('PHP', 'mergado-marketing-pack'),
                    'value' => phpversion(),
                ],
                'product_feed_url' => [
                    'name' => __('Product feed URL', 'mergado-marketing-pack'),
                    'value' => $xmlProductFeed->getFeedUrl(),
                ],
                'product_cron_url' => [
                    'name' => __('Product cron URL', 'mergado-marketing-pack'),
                    'value' => $xmlProductFeed->getCronUrl(),
                ],
                'product_feed_last_change' => [
                    'name' => __('Product feed last change time', 'mergado-marketing-pack'),
                    'value' => $xmlProductFeed->getLastFeedChange(),
                ],
                'product_wp_cron_active' => [
                    'name' => __('WP product cron - status', 'mergado-marketing-pack'),
                    'value' => self::boolToActive($xmlProductFeed->isWpCronActive()),
                ],
                'product_wp_cron_schedule' => [
                    'name' => __('WP product cron - schedule', 'mergado-marketing-pack'),
                    'value' => Crons::getTaskByVariable($xmlProductFeed->getCronSchedule()),
                ],
                'category_feed_url' => [
                    'name' => __('Category feed URL', 'mergado-marketing-pack'),
                    'value' => $xmlCategoryFeed->getFeedUrl(),
                ],
                'category_cron_url' => [
                    'name' => __('Category cron URL', 'mergado-marketing-pack'),
                    'value' => $xmlCategoryFeed->getCronUrl(),
                ],
                'category_wp_cron_active' => [
                    'name' => __('WP category cron - status', 'mergado-marketing-pack'),
                    'value' => self::boolToActive($xmlCategoryFeed->isWpCronActive()),
                ],
                'category_wp_cron_schedule' => [
                    'name' => __('WP category cron - schedule', 'mergado-marketing-pack'),
                    'value' => Crons::getTaskByVariable($xmlCategoryFeed->getCronSchedule()),
                ],
                'stock_feed_url' => [
                    'name' => __('Stock feed URL', 'mergado-marketing-pack'),
                    'value' => $xmlStockFeed->getFeedUrl(),
                ],
                'stock_cron_url' => [
                    'name' => __('Stock cron URL', 'mergado-marketing-pack'),
                    'value' => $xmlStockFeed->getCronUrl(),
                ],
                'stock_wp_cron_active' => [
                    'name' => __('WP stock cron - status', 'mergado-marketing-pack'),
                    'value' => self::boolToActive($xmlStockFeed->isWpCronActive()),
                ],
                'stock_wp_cron_schedule' => [
                    'name' => __('WP stock cron - schedule', 'mergado-marketing-pack'),
                    'value' => Crons::getTaskByVariable($xmlStockFeed->getCronSchedule()),
                ],
                'import_feed_url' => [
                    'name' => __('Import prices feed URL', 'mergado-marketing-pack'),
                    'value' => $importPricesClass->getImportUrl(),
                ],
                'import_cron_url' => [
                    'name' => __('Import prices cron URL', 'mergado-marketing-pack'),
                    'value' => $importPricesClass->getCronUrl(),
                ],
                'import_wp_cron_active' => [
                    'name' => __('WP import cron - status', 'mergado-marketing-pack'),
                    'value' => self::boolToActive($importPricesClass->isWpCronActive()),
                ],
                'import_wp_cron_schedule' => [
                    'name' => __('WP import cron - schedule', 'mergado-marketing-pack'),
                    'value' => Crons::getTaskByVariable($importPricesClass->getCronSchedule()),
                ],
            ],
            'adsystems' => [
                'googleAds' => self::boolToActive($googleAds->getConversionActive()),
                'googleAdsRemarketing' => self::boolToActive($googleAds->getRemarketingActive()),
                'googleAnalytics' => self::boolToActive(get_option(Settings::GOOGLE_GTAGJS['ACTIVE'], 0)),
                'googleAnalyticsRefunds' => self::boolToActive(get_option(GaRefundClass::ACTIVE, 0)),
                'googleTagManager' => self::boolToActive($googleTagManager->getActive()),
                'googleTagManagerEcommerce' => self::boolToActive($googleTagManager->getEcommerceActive()),
                'googleTagManagerEnhancedEcommerce' => self::boolToActive($googleTagManager->getEnhancedEcommerceActive()),
                'googleCustomerReviews' => self::boolToActive($googleReviewsClass->getOptInActive()),
                'googleCustomerReviewsBadge' => self::boolToActive($googleReviewsClass->getBadgeActive()),
                'facebookPixel' => self::boolToActive($facebookClass->getActive()),
                'heurekaVerify' => self::boolToActive(get_option(Settings::HEUREKA['ACTIVE_CZ'], 0)),
                'heurekaVerifyWidget' => self::boolToActive(get_option(Settings::HEUREKA['WIDGET_CZ'], 0)),
                'heurekaConversions' => self::boolToActive(get_option(Settings::HEUREKA['ACTIVE_TRACK_CZ'], 0)),
                'heurekaVerifySk' => self::boolToActive(get_option(Settings::HEUREKA['ACTIVE_SK'], 0)),
                'heurekaVerifySkWidget' => self::boolToActive(get_option(Settings::HEUREKA['WIDGET_SK'], 0)),
                'heurekaConversionsSk' => self::boolToActive(get_option(Settings::HEUREKA['ACTIVE_TRACK_SK'], 0)),
                'glamiPixel' => self::boolToActive($glamiPixelClass->getActive()),
                'glamiTop' => self::boolToActive($glamiTopClass->getActive()),
                'sklik' => self::boolToActive(get_option(Settings::SKLIK['CONVERSION_ACTIVE'], 0)),
                'sklikRetargeting' => self::boolToActive(get_option(Settings::SKLIK['RETARGETING_ACTIVE'], 0)),
                'zbozi' => self::boolToActive($zboziClass->getActive()),
                'etarget' => self::boolToActive(get_option(Settings::ETARGET['ACTIVE'], 0)),
                'najnakup' => self::boolToActive(get_option(Settings::NAJNAKUP['ACTIVE'], 0)),
                'pricemania' => self::boolToActive(get_option(Settings::PRICEMANIA['ACTIVE'], 0)),
                'kelkoo' => self::boolToActive(get_option(Settings::KELKOO['ACTIVE'], 0)),
                'biano' => self::boolToActive(get_option(Settings::BIANO['ACTIVE'], 0)),
                'arukereso' => self::boolToActive($arukeresoClass->getActive()),
                'arukeresoWidget' => self::boolToActive($arukeresoClass->getWidgetActive()),
            ]

        ];
    }

    public static function boolToActive($bool)
    {
        if ($bool) {
            return 'active';
        } else {
            return 'inactive';
        }
    }

    public static function getScheduleEstimate($currentProductsPerStep, $totalProducts, $cronSchedule)
    {

	    if ($currentProductsPerStep == 0 || trim($currentProductsPerStep) === '') {
	    	$currentProductsPerStep = $totalProducts;
	    }

	    $numberOfRuns = $totalProducts / $currentProductsPerStep;
	    $scheduleSeconds = Crons::getScheduleInSeconds($cronSchedule);

	    $totalTimeToGenerate = $numberOfRuns * $scheduleSeconds;

	    if ($totalTimeToGenerate < $scheduleSeconds) {
	    	return self::human_time_diff_mmp(0, $scheduleSeconds);
	    } else {
		    return self::human_time_diff_mmp(0, $totalTimeToGenerate);
	    }
    }

	/**
	 * Determines the difference between two timestamps.
	 *
	 * The difference is returned in a human readable format such as "1 hour",
	 * "5 mins", "2 days".
	 *
	 * @since 1.5.0
	 * @since 5.3.0 Added support for showing a difference in seconds.
	 *
	 * @param int $from Unix timestamp from which the difference begins.
	 * @param int $to   Optional. Unix timestamp to end the time difference. Default becomes time() if not set.
	 * @return string Human readable time difference.
	 */
	public static function human_time_diff_mmp( $from, $to = 0 ) {
		if ( empty( $to ) ) {
			$to = time();
		}

		$diff = (int) abs( $to - $from );

		if ( $diff < MINUTE_IN_SECONDS ) {
			$secs = $diff;
			if ( $secs <= 1 ) {
				$secs = 1;
			}
			/* translators: Time difference between two dates, in seconds. %s: Number of seconds. */
			$since = sprintf( _n( '%s second', '%s seconds', $secs, 'mergado-marketing-pack' ), $secs );
		} elseif ( $diff < HOUR_IN_SECONDS && $diff >= MINUTE_IN_SECONDS ) {
			$mins = round( $diff / MINUTE_IN_SECONDS );
			if ( $mins <= 1 ) {
				$mins = 1;
			}
			/* translators: Time difference between two dates, in minutes (min=minute). %s: Number of minutes. */
			$since = sprintf( _n( '%s min', '%s mins', $mins, 'mergado-marketing-pack' ), $mins );
		} elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
			$hours = round( $diff / HOUR_IN_SECONDS );
			if ( $hours <= 1 ) {
				$hours = 1;
			}
			/* translators: Time difference between two dates, in hours. %s: Number of hours. */
			$since = sprintf( _n( '%s hour', '%s hours', $hours, 'mergado-marketing-pack' ), $hours );
		} elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
			$days = round( $diff / DAY_IN_SECONDS );
			if ( $days <= 1 ) {
				$days = 1;
			}
			/* translators: Time difference between two dates, in days. %s: Number of days. */
			$since = sprintf( _n( '%s day', '%s days', $days, 'mergado-marketing-pack' ), $days );
		} elseif ( $diff < MONTH_IN_SECONDS && $diff >= WEEK_IN_SECONDS ) {
			$weeks = round( $diff / WEEK_IN_SECONDS );
			if ( $weeks <= 1 ) {
				$weeks = 1;
			}
			/* translators: Time difference between two dates, in weeks. %s: Number of weeks. */
			$since = sprintf( _n( '%s week', '%s weeks', $weeks, 'mergado-marketing-pack' ), $weeks );
		} elseif ( $diff < YEAR_IN_SECONDS && $diff >= MONTH_IN_SECONDS ) {
			$months = round( $diff / MONTH_IN_SECONDS );
			if ( $months <= 1 ) {
				$months = 1;
			}
			/* translators: Time difference between two dates, in months. %s: Number of months. */
			$since = sprintf( _n( '%s month', '%s months', $months, 'mergado-marketing-pack' ), $months );
		} elseif ( $diff >= YEAR_IN_SECONDS ) {
			$years = round( $diff / YEAR_IN_SECONDS );
			if ( $years <= 1 ) {
				$years = 1;
			}
			/* translators: Time difference between two dates, in years. %s: Number of years. */
			$since = sprintf( _n( '%s year', '%s years', $years, 'mergado-marketing-pack' ), $years );
		}

		/**
		 * Filters the human readable difference between two timestamps.
		 *
		 * @since 4.0.0
		 *
		 * @param string $since The difference in human readable text.
		 * @param int    $diff  The difference in seconds.
		 * @param int    $from  Unix timestamp from which the difference begins.
		 * @param int    $to    Unix timestamp to end the time difference.
		 */
		return apply_filters( 'human_time_diff', $since, $diff, $from, $to );
	}
}
