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

namespace Mergado\Tools\XML;

include_once __MERGADO_DIR__ . 'autoload.php';

use Exception;
use Mergado\Tools\Settings;
use Mergado\Tools\XML\Ean\CeskeSluzby;
use Mergado\Tools\XML\Ean\EanForWoocommerce;
use Mergado\Tools\XML\Ean\ProductGtinEanUpcIsbn;
use Mergado\Tools\XML\Ean\WooAddGtin;
use Mergado\Tools\XML\Ean\WpssoWcMetadata;

class EanClass
{
    const EAN_PLUGIN = 'mmp_ean_plugin_selected';
    const EAN_PLUGIN_FIELD = 'mmp_ean_field_selected';

    const CESKE_SLUZBY = 'ceske-sluzby';
    const PRODUCT_GTIN_EAN_UPC_ISBN = 'product-gtin-ean-upc-isbn';
    const WOO_ADD_GTIN = 'woo-add-gtin';
    const EAN_FOR_WOO = 'ean-for-woo';
    const WPSSO_WC_METADATA = 'wpsso-wc-metadata';

    protected $logger;

    private $selectedPlugin;
    private $selectedPluginField;
    private $isPluginActive;
    private $selectedPluginInstance;

    public function __construct()
    {
        $this->logger = wc_get_logger();
        $this->setData();
    }

    private function setData()
    {
        $this->selectedPlugin = $this->getSelectedPlugin();
        $this->selectedPluginField = $this->getSelectedPluginField();
        $this->isPluginActive = $this->isPluginActive();
    }

    public static function getSelectedPlugin()
    {
        return get_option( self::EAN_PLUGIN, 0 );
    }

    public static function getSelectedPluginField()
    {
        return get_option( self::EAN_PLUGIN_FIELD, '');
    }

    public static function getDefaultEanAfterInstalation()
    {
        $activeItems = [];

        foreach(self::getOptionsForSelect() as $key => $item) {

            if ($item['active']) {
                $activeItems[$key] = $item;
            }
        }
        
        if (count($activeItems) === 1) {
            $selectedPlugin = array_key_first($activeItems);

            $item = array_shift($activeItems);
            
            if ($item['hasFields']) {
                $subOptions = self::getSuboptionsForSelect();
                $selectedPluginField = array_key_first($subOptions[$selectedPlugin]);
                self::saveFields([self::EAN_PLUGIN => $selectedPlugin, self::EAN_PLUGIN_FIELD => $selectedPluginField]);
            } else {
                self::saveFields([self::EAN_PLUGIN => $selectedPlugin]);
            }
        }
    }

    public static function getOptionsForSelect()
    {
        $ceskeSluzby = new CeskeSluzby();
        $productGtinEanUpcIsbn = new ProductGtinEanUpcIsbn();
        $wooAddGtin = new WooAddGtin();
        $eanForWoo = new EanForWoocommerce();
        $wpssoWcMetadata = new WpssoWcMetadata();

        return [
            self::CESKE_SLUZBY => $ceskeSluzby->getPluginDataForSelect(),
            self::PRODUCT_GTIN_EAN_UPC_ISBN => $productGtinEanUpcIsbn->getPluginDataForSelect(),
            self::WOO_ADD_GTIN => $wooAddGtin->getPluginDataForSelect(),
            self::EAN_FOR_WOO => $eanForWoo->getPluginDataForSelect(),
            self::WPSSO_WC_METADATA => $wpssoWcMetadata->getPluginDataForSelect(),
        ];
    }

    public static function getSuboptionsForSelect()
    {
        $ceskeSluzby = new CeskeSluzby();
        $productGtinEanUpcIsbn = new ProductGtinEanUpcIsbn();
        $wooAddGtin = new WooAddGtin();
        $eanForWoo = new EanForWoocommerce();
        $wpssoWcMetadata = new WpssoWcMetadata();

        return [
            self::CESKE_SLUZBY => $ceskeSluzby->getPluginDataForSubselect(),
            self::PRODUCT_GTIN_EAN_UPC_ISBN => $productGtinEanUpcIsbn->getPluginDataForSubselect(),
            self::WOO_ADD_GTIN => $wooAddGtin->getPluginDataForSubselect(),
            self::EAN_FOR_WOO => $eanForWoo->getPluginDataForSubselect(),
            self::WPSSO_WC_METADATA => $wpssoWcMetadata->getPluginDataForSubselect(),
        ];
    }

    public function isPluginActive()
    {
        switch ($this->selectedPlugin) {
            case self::CESKE_SLUZBY:
                $this->selectedPluginInstance = new CeskeSluzby();
                break;

            case self::PRODUCT_GTIN_EAN_UPC_ISBN:
                $this->selectedPluginInstance = new ProductGtinEanUpcIsbn();
                break;

            case self::WOO_ADD_GTIN:
                $this->selectedPluginInstance = new WooAddGtin();
                break;

            case self::EAN_FOR_WOO:
                $this->selectedPluginInstance = new EanForWoocommerce();
                break;

            case self::WPSSO_WC_METADATA:
                $this->selectedPluginInstance = new WpssoWcMetadata();
                break;
        }

        if ($this->selectedPluginInstance) {
            return $this->selectedPluginInstance->isActive();
        } else {
            return false;
        }
    }

    public function getEan($product, $productParentId, $type)
    {
        try {
            if ($this->isPluginActive && $this->selectedPluginInstance) {
                switch ($this->selectedPlugin) {
                    case self::CESKE_SLUZBY:
                        $ean = $this->selectedPluginInstance->getEan($product, $productParentId, $this->selectedPluginField);
                        break;
                    case self::WOO_ADD_GTIN:
                    case self::EAN_FOR_WOO:
                    case self::WPSSO_WC_METADATA:
                    case self::PRODUCT_GTIN_EAN_UPC_ISBN:
                        $ean = $this->selectedPluginInstance->getEan($product['id'], $productParentId, $this->selectedPluginField);
                        break;

                    default:
                        $ean = false;
                        break;
                }

                return $ean;
            } else {
                return false;
            }

        } catch (Exception $e) {
            $properties = ['selectedPlugin' => $this->selectedPlugin, 'isPluginActive' => $this->isPluginActive, 'product' => $product, 'type' => $type];

            $this->logger->error('Ean->getEan() method - params => ' . $properties . ' - error - ' . $e, 'mergado');
        }

        return false;
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    /**
     * @param $post
     */
    public static function saveFields($post)
    {
        Settings::saveOptions($post,
            [],[], [
                self::EAN_PLUGIN,
                self::EAN_PLUGIN_FIELD,
            ]
        );
    }
}