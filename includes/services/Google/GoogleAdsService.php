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

class GoogleAdsService
{
    const CONVERSION_ACTIVE = 'adwords-form-conversion-active';
    const REMARKETING_ACTIVE = 'adwords-form-remarketing-active';
    const CONVERSION_CODE = 'adwords-form-conversion-code';
    const CONVERSION_LABEL = 'adwords-form-conversion-label';

    private $conversionActive;
    private $remarketingActive;
    private $conversionCode;
    private $conversionLabel;

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    /**
     * @return bool
     */
    public function isConversionActive()
    {
        $active = $this->getConversionActive();
        $code = $this->getConversionCode();
        $label = $this->getConversionLabel();

        if ($active === '1' && $code && $code !== '' && $label && $label !== '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isRemarketingActive()
    {
        $active = $this->getRemarketingActive();
        $code = $this->getConversionCode();

        if ($active === '1' && $code && $code !== '') {
            return true;
        } else {
            return false;
        }
    }


    /*******************************************************************************************************************
     * Get field value
     *******************************************************************************************************************/

    /**
     * @return false|string|null
     */
    public function getConversionActive()
    {
        if (!is_null($this->conversionActive)) {
            return $this->conversionActive;
        }

        $this->conversionActive = get_option(self::CONVERSION_ACTIVE, 0);

        return $this->conversionActive;
    }

    /**
     * @return false|string|null
     */
    public function getRemarketingActive()
    {
        if (!is_null($this->remarketingActive)) {
            return $this->remarketingActive;
        }

        $this->remarketingActive = get_option(self::REMARKETING_ACTIVE, 0);

        return $this->remarketingActive;
    }


    /**
     * @return false|string|null
     */
    public function getConversionCode()
    {
        if (!is_null($this->conversionCode)) {
            return $this->conversionCode;
        }

        $code = get_option(self::CONVERSION_CODE, '');

        if (trim($code) !== '' && substr( $code, 0, 3 ) !== "AW-") {
            $this->conversionCode = 'AW-' . $code;
        } else {
            $this->conversionCode = $code;
        }

        return $this->conversionCode;
    }

    /**
     * @return false|string|null
     */
    public function getConversionLabel()
    {
        if (!is_null($this->conversionLabel)) {
            return $this->conversionLabel;
        }

        $this->conversionLabel = get_option(self::CONVERSION_LABEL, '');

        return $this->conversionLabel;
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    /**
     * @param $post
     */
    public static function saveFields($post)
    {
        Mergado\Tools\Settings::saveOptions($post, [
            self::CONVERSION_ACTIVE,
            self::REMARKETING_ACTIVE,
        ], [
            self::CONVERSION_CODE,
            self::CONVERSION_LABEL
        ]);
    }
}
