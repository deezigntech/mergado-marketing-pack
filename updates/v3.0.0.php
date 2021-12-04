<?php

include_once __MERGADO_TOOLS_XML_DIR__ . 'XMLCategoryFeed.php';
include_once __MERGADO_TOOLS_XML_DIR__ . 'XMLProductFeed.php';
include_once __MERGADO_TOOLS_XML_DIR__ . 'XMLStockFeed.php';
include_once __MERGADO_TOOLS_DIR__ . 'XMLClass.php';

use Mergado\Tools\Settings;
use Mergado\Tools\XML\Ean\CeskeSluzby;
use Mergado\Tools\XML\Ean\EanForWoocommerce;
use Mergado\Tools\XML\Ean\ProductGtinEanUpcIsbn;
use Mergado\Tools\XML\Ean\WooAddGtin;
use Mergado\Tools\XML\Ean\WpssoWcMetadata;
use Mergado\Tools\XML\EanClass;
use Mergado\Tools\XMLClass;

$token = get_option('mmp_token');

if ($token !== NULL && $token !== '' && $token) {
	if ( is_multisite() ) {
		$sites = get_sites();

		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );

			$currentBlogId = get_current_blog_id();

			setWizardFinishedIfFeedExist( $currentBlogId, $token );
			setDefaultEANIfNotSet();

			restore_current_blog();
		}
	} else {
		$currentBlogId = 0;
		setWizardFinishedIfFeedExist( $currentBlogId, $token );
		setDefaultEANIfNotSet();
	}
}

function setWizardFinishedIfFeedExist($currentBlogId, $token)
{
	if (file_exists( __MERGADO_XML_DIR__ . $currentBlogId . '/' . 'products_' . $token . '.xml')) {
		update_option(Settings::WIZARD['FINISHED_PRODUCT'], 1);
	}

	if (file_exists( __MERGADO_XML_DIR__ . $currentBlogId . '/' . 'stock_' . $token . '.xml')) {
		update_option(Settings::WIZARD['FINISHED_STOCK'], 1);
	}

	if (file_exists( __MERGADO_XML_DIR__ . $currentBlogId . '/' . 'category_' . $token . '.xml')) {
		update_option(Settings::WIZARD['FINISHED_CATEGORY'], 1);
	}

    update_option(XMLClass::DEFAULT_ITEMS_STEP['PRODUCT_FEED'], 1500);
    update_option(XMLClass::DEFAULT_ITEMS_STEP['CATEGORY_FEED'], 3000);
    update_option(XMLClass::DEFAULT_ITEMS_STEP['STOCK_FEED'], 5000);

    //File not exist and field is empty
    update_option(XMLClass::DEFAULT_ITEMS_STEP['IMPORT_FEED'], 3000);
}

function setDefaultEANIfNotSet()
{
    $services = [
      EanClass::PRODUCT_GTIN_EAN_UPC_ISBN => new ProductGtinEanUpcIsbn(),
      EanClass::WOO_ADD_GTIN => new WooAddGtin(),
      EanClass::EAN_FOR_WOO => new EanForWoocommerce(),
      EanClass::WPSSO_WC_METADATA => new WpssoWcMetadata(),
      EanClass::CESKE_SLUZBY => new CeskeSluzby(),
    ];

    foreach($services as $service => $object) {
        $alreadySet = get_option(EanClass::EAN_PLUGIN, false);

        if ($alreadySet === false) {
            if ($object->isActive()) {
                update_option(EanClass::EAN_PLUGIN, $service);

                $subselectData = $object->getPluginDataForSubselect();

                if (!$subselectData) {
                    update_option(EanClass::EAN_PLUGIN_FIELD, array_key_first($subselectData));
                }

                break;
            }
        }
    }
}

if (!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}