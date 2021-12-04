<?php

use Mergado\Arukereso\ArukeresoClass;
use Mergado\Facebook\FacebookClass;
use Mergado\Glami\GlamiPixelClass;
use Mergado\Glami\GlamiTopClass;
use Mergado\Google\GaRefundClass;
use Mergado\Google\GoogleAdsClass;
use Mergado\Google\GoogleReviewsClass;
use Mergado\Google\GoogleTagManagerClass;
use Mergado\Tools\BannersClass;
use Mergado\Tools\Settings;
use Mergado\Zbozi\ZboziClass;

include_once __MERGADO_DIR__ . 'autoload.php';

include_once( 'partials/template-mergado-marketing-pack-header.php' );

// Todo move us to our files and import here

if (isset($_POST["submit-save"])) {

    /**
     * Glami PiXel settings
     */
    GlamiPixelClass::saveFields($_POST);

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
    GlamiTopClass::saveFields($_POST);


    /**
     * Facebook settings
     */
    FacebookClass::saveFields($_POST);


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

    GoogleAdsClass::saveFields($_POST);


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
	GaRefundClass::saveFields($_POST);

    /**
     * Google reviews settings
     */
    GoogleReviewsClass::saveFields($_POST);

    /**
     * Árukereső
     */
    ArukeresoClass::saveFields($_POST);

    /**
     * Google analytics (Google Tag Manager) settings
     */
    GoogleTagManagerClass::saveFields($_POST);

    /**
     * ETARGET settings
     */
    Settings::saveOptions($_POST, [
        Settings::ETARGET['ACTIVE'],
    ], [
        Settings::ETARGET['HASH'],
        Settings::ETARGET['ID'],
    ]);


    /**
     * NajNakup settings
     */
    Settings::saveOptions($_POST, [
        Settings::NAJNAKUP['ACTIVE'],
    ], [
        Settings::NAJNAKUP['ID'],
    ]);


    /**
     * Pricemania settings
     */
    Settings::saveOptions($_POST, [
        Settings::PRICEMANIA['ACTIVE'],
    ], [
        Settings::PRICEMANIA['ID'],
    ]);


    /**
     * Zbozi settings
     */
    ZboziClass::saveFields($_POST);


    /**
     * Kelkoo settings
     */
    Settings::saveOptions($_POST, [
        Settings::KELKOO['ACTIVE'],
	    Settings::KELKOO['CONVERSION_VAT_INCL'],
    ], [
        Settings::KELKOO['COUNTRY'],
        Settings::KELKOO['COM_ID'],
    ]);


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
}

$tabsSettings = [
    'tabs' => [
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
