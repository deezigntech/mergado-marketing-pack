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

namespace Mergado\Google;

use Mergado;

class GoogleTagManagerService
{
    const ACTIVE = 'mergado_google_tag_manager_active';
    const CODE = 'mergado_google_tag_manager_code';
    const ECOMMERCE_ACTIVE = 'mergado_google_tag_manager_ecommerce';
    const ECOMMERCE_ENHANCED_ACTIVE = 'mergado_google_tag_manager_ecommerce_enhanced';
    const CONVERSION_VAT_INCL = 'gtm-vat-included';
    const VIEW_LIST_ITEMS_COUNT = 'mergado_google_tag_manager_view_list_items_count';

    private $active;
    private $code;
    private $ecommerceActive;
    private $enhancedEcommerceActive;
    private $conversionVatIncluded;
    private $viewListItemsCount;

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    /**
     * @return bool
     */
    public function isActive()
    {
        $active = $this->getActive();
        $code = $this->getCode();

        if ($active === '1' && $code && $code !== '') {
            return true;
        } else {
            return false;
        }
    }

    public function isEcommerceActive()
    {
        $active = $this->getActive();
        $code = $this->getCode();
        $ecommerceActive = $this->getEcommerceActive();

        if ($active === '1' && $code && $code !== '' && $ecommerceActive === '1') {
            return true;
        } else {
            return false;
        }
    }

    public function isEnhancedEcommerceActive()
    {
        $active = $this->getActive();
        $code = $this->getCode();
        $ecommerceActive = $this->getEcommerceActive();
        $enhancedEcommerceActive = $this->getEnhancedEcommerceActive();

        if ($active === '1' && $code && $code !== '' && $ecommerceActive === '1' && $enhancedEcommerceActive === '1') {
            return true;
        } else {
            return false;
        }
    }


    /*******************************************************************************************************************
     * GET
     *******************************************************************************************************************/

    /**
     * @return false|string|null
     */
    public function getActive()
    {
        if (!is_null($this->active)) {
            return $this->active;
        }

        $this->active = get_option(self::ACTIVE, 0);

        return $this->active;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        if (!is_null($this->code)) {
            return $this->code;
        }

        $code = get_option(self::CODE, '');

        if (trim($code) !== '' && substr( $code, 0, 4 ) !== "GTM-") {
            $this->code = 'GTM-' . $code;
        } else {
            $this->code = $code;
        }

        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getEcommerceActive()
    {
        if (!is_null($this->ecommerceActive)) {
            return $this->ecommerceActive;
        }

        $this->ecommerceActive = get_option(self::ECOMMERCE_ACTIVE, 0);

        return $this->ecommerceActive;
    }

    /**
     * @return mixed
     */
    public function getEnhancedEcommerceActive()
    {
        if (!is_null($this->enhancedEcommerceActive)) {
            return $this->enhancedEcommerceActive;
        }

        $this->enhancedEcommerceActive = get_option(self::ECOMMERCE_ENHANCED_ACTIVE, 0);

        return $this->enhancedEcommerceActive;
    }

    /**
     * @return mixed
     */
    public function getConversionVatIncluded()
    {
        if (!is_null($this->conversionVatIncluded)) {
            return $this->conversionVatIncluded;
        }

        $this->conversionVatIncluded = get_option(self::CONVERSION_VAT_INCL, 1);

        return $this->conversionVatIncluded;
    }

    /**
     * @return mixed
     */
    public function getViewListItemsCount()
    {
        if (!is_null($this->viewListItemsCount)) {
            return $this->viewListItemsCount;
        }

        $this->viewListItemsCount = get_option(self::VIEW_LIST_ITEMS_COUNT, 0);

        return $this->viewListItemsCount;
    }


    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields($post)
    {
        Mergado\Tools\Settings::saveOptions($post, [
            self::ACTIVE,
            self::ECOMMERCE_ACTIVE,
            self::ECOMMERCE_ENHANCED_ACTIVE,
            self::CONVERSION_VAT_INCL,
        ], [
            self::CODE,
            self::VIEW_LIST_ITEMS_COUNT,
        ]);
    }
}
