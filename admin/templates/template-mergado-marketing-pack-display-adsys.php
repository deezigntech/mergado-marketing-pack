<?php

use Mergado\Arukereso\ArukeresoService;
use Mergado\Etarget\EtargetService;
use Mergado\Facebook\FacebookService;
use Mergado\Glami\GlamiPixelService;
use Mergado\Glami\GlamiTopService;
use Mergado\Google\GoogleAnalyticsRefundService;
use Mergado\Google\GoogleAdsService;
use Mergado\Google\GoogleReviewsService;
use Mergado\Google\GoogleTagManagerService;
use Mergado\Kelkoo\KelkooService;
use Mergado\NajNakup\NajNakupService;
use Mergado\Pricemania\PricemaniaService;
use Mergado\Tools\BannersClass;
use Mergado\Tools\CookieClass;
use Mergado\Tools\Settings;
use Mergado\Zbozi\ZboziService;

include_once __MERGADO_DIR__ . 'autoload.php';

include_once( 'partials/template-mergado-marketing-pack-header.php' );

// Todo move us to our files and import here

if (isset($_POST["submit-save"])) {

    /**
     * Glami PiXel settings
     */
    GlamiPixelService::saveFields($_POST);

    /**
     * Biano settings
     */
    $inputs = [];
    $checkboxes = [];
    foreach (Settings::BIANO['LANG_OPTIONS'] as $item) {
        $inputs[] = Settings::BIANO['MERCHANT_ID'] . '-' . $item;
        $checkboxes[] = Settings::BIANO['FORM_ACTIVE'] . '-' . $item;
    }

    $checkboxes[] = Settings::BIANO['ACTIVE'];
    $checkboxes[] = Settings::BIANO['CONVERSION_VAT_INCL'];

    Settings::saveOptions($_POST,
        $checkboxes
        ,
        $inputs
    );

    /**
     * Glami TOP settings
     */
    GlamiTopService::saveFields($_POST);


    /**
     * Facebook settings
     */
    FacebookService::saveFields($_POST);


    /**
     * Sklik settings
     */
    Settings::saveOptions($_POST, [
        Settings::SKLIK['CONVERSION_ACTIVE'],
        Settings::SKLIK['RETARGETING_ACTIVE'],
        Settings::SKLIK['CONVERSION_VAT_INCL'],
    ], [
        Settings::SKLIK['CONVERSION_CODE'],
        Settings::SKLIK['CONVERSION_VALUE'],
        Settings::SKLIK['RETARGETING_ID'],
    ]);

    /**
     * Adwords settings
     */

    GoogleAdsService::saveFields($_POST);


    /**
     * Google analytics (gtag.js) settings
     */
    Settings::saveOptions($_POST, [
        Settings::GOOGLE_GTAGJS['ACTIVE'],
        Settings::GOOGLE_GTAGJS['TRACKING'],
        Settings::GOOGLE_GTAGJS['ECOMMERCE'],
        Settings::GOOGLE_GTAGJS['ECOMMERCE_ENHANCED'],
        Settings::GOOGLE_GTAGJS['CONVERSION_VAT_INCL'],
    ], [
        Settings::GOOGLE_GTAGJS['CODE'],
    ]);

	/**
	 * GaRefund settings
	 */
	GoogleAnalyticsRefundService::saveFields($_POST);

    /**
     * Google reviews settings
     */
    GoogleReviewsService::saveFields($_POST);

    /**
     * Árukereső
     */
    ArukeresoService::saveFields($_POST);

    /**
     * Google analytics (Google Tag Manager) settings
     */
    GoogleTagManagerService::saveFields($_POST);

    /**
     * ETARGET settings
     */
    EtargetService::saveFields($_POST);

    /**
     * NajNakup settings
     */
    NajNakupService::saveFields($_POST);

    /**
     * Pricemania settings
     */
    PricemaniaService::saveFields($_POST);

    /**
     * Zbozi settings
     */
    ZboziService::saveFields($_POST);


    /**
     * Kelkoo settings
     */
    KelkooService::saveFields($_POST);


    /**
     * Heureka settings
     */
    Settings::saveOptions($_POST, [
        Settings::HEUREKA['ACTIVE_CZ'],
        Settings::HEUREKA['ACTIVE_SK'],
        Settings::HEUREKA['ACTIVE_TRACK_CZ'],
        Settings::HEUREKA['ACTIVE_TRACK_SK'],
        Settings::HEUREKA['WIDGET_CZ'],
//        Settings::HEUREKA['WIDGET_CZ_SHOW_MOBILE'],
//        Settings::HEUREKA['WIDGET_SK_SHOW_MOBILE'],
        Settings::HEUREKA['WIDGET_SK'],
	    Settings::HEUREKA['CONVERSION_VAT_INCL_CZ'],
	    Settings::HEUREKA['CONVERSION_VAT_INCL_SK'],
        Settings::HEUREKA['STOCK_FEED']
    ], [
        Settings::HEUREKA['VERIFIED_CZ'],
        Settings::HEUREKA['VERIFIED_SK'],
        Settings::HEUREKA['TRACK_CODE_CZ'],
        Settings::HEUREKA['TRACK_CODE_SK'],
        Settings::HEUREKA['WIDGET_CZ_ID'],
//        Settings::HEUREKA['WIDGET_CZ_HIDE_WIDTH'],
        Settings::HEUREKA['WIDGET_CZ_MARGIN'],
        Settings::HEUREKA['WIDGET_SK_ID'],
//        Settings::HEUREKA['WIDGET_SK_HIDE_WIDTH'],
        Settings::HEUREKA['WIDGET_SK_MARGIN'],
    ],
    [
        Settings::HEUREKA['WIDGET_CZ_POSITION'],
        Settings::HEUREKA['WIDGET_SK_POSITION'],
    ]);

    $OptOutTexts = [];

    foreach(get_available_languages() as $lang) {
       $OptOutTexts[] = 'heureka-verify-opt-out-text-' . $lang;
    }

    $OptOutTexts[] = 'heureka-verify-opt-out-text-en_US';

    Settings::saveOptions($_POST, [], $OptOutTexts);

    CookieClass::saveFields($_POST);
}

$tabsSettings = [
    'tabs' => [
        'cookies' => ['title' => '', 'icon' => 'mmp_icons.svg#cookies'],
        'google' => ['title' => 'Google', 'active' => true],
        'facebook' => ['title' => 'Facebook'],
        'heureka' => ['title' => 'Heureka'],
        'glami' => ['title' => 'GLAMI'],
        'seznam' => ['title' => 'Seznam'],
        'etarget' => ['title' => 'Etarget'],
        'najnakup' => ['title' => 'Najnakup.sk'],
        'pricemania' => ['title' => 'Pricemania'],
        'kelkoo' => ['title' => 'Kelkoo'],
        'biano' => ['title' => 'Biano'],
        'arukereso' => ['title' => 'Árukereső']
    ],
    'tabContentPath' => wp_normalize_path( __DIR__ . '/partials/tabs-adsys/adsys-' )
];
?>

<div class="wrap">
    <div class="rowmer">
        <div class="col-content">
            <form method="post" id="glami-form" action="">
                <?php include( __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'tabs/tabs.php' ); ?>
            </form>
        </div>
        <div class="col-side col-side-extra">
            <?= BannersClass::getSidebar() ?>
        </div>
    </div>
    <div class="merwide">
        <?= BannersClass::getWide() ?>
    </div>
</div>
