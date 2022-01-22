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

namespace Mergado\Zbozi;

use Mergado;

class ZboziService
{
    const ZBOZI_SANDBOX = false;

    const ACTIVE = 'zbozi-form-active';
    const STANDARD_ACTIVE = 'zbozi-form-standard-active';
    const ID = 'zbozi-form-id';
    const KEY = 'zbozi-form-secret-key';
    const CONVERSION_VAT_INCL = 'zbozi-vat-included';
    const OPT_IN = 'zbozi-verify-opt-in-text-';

    const DEFAULT_OPT = 'Do not send a satisfaction questionnaire within the Zboží.cz program.';

    private $active;
    private $standardActive;
    private $id;
    private $conversionVatIncluded;
    private $key;

    /*******************************************************************************************************************
     *******************************************************************************************************************
     ********************************************** DEFAULT CLASS OPTIONS
     ******************************************************************************************************************
     ******************************************************************************************************************/

    public function isActive()
    {
        $active = $this->getActive();
        $id = $this->getId();
        $key = $this->getKey();

        if ($active == '1' && $id && $key && $id !== '' && $key !== '') {
            return true;
        } else {
            return false;
        }
    }

    public function isAdvanced()
    {
        $active = $this->getStandardActive();

        if ($active == '1') {
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
    public function getActive()
    {
        if (!is_null($this->active)) {
            return $this->active;
        }

        $this->active = get_option(self::ACTIVE, 0);

        return $this->active;
    }

    /**
     * @return false|string|null
     */
    public function getStandardActive()
    {
        if (!is_null($this->standardActive)) {
            return $this->standardActive;
        }

        $this->standardActive = get_option(self::STANDARD_ACTIVE, 0);

        return $this->standardActive;
    }

    /**
     * @return false|string|null
     */
    public function getId()
    {
        if (!is_null($this->id)) {
            return $this->id;
        }

        $this->id = get_option(self::ID, '');

        return $this->id;
    }

    /**
     * @return false|string|null
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
     * @return false|string|null
     */
    public function getKey()
    {
        if (!is_null($this->key)) {
            return $this->key;
        }

        $this->key = get_option(self::KEY, '');

        return $this->key;
    }

    /**
     * @param $lang
     * @return false|string|null
     */
    public function getOptOut($lang)
    {
        $this->key = get_option(self::OPT_IN . $lang, '');
        return $this->key;
    }



    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    /**
     * @param $post
     */
    public static function saveFields($post)
    {
        foreach(get_available_languages() as $lang) {
            $inputs[] = self::OPT_IN . $lang;
        }

        $inputs[] = self::OPT_IN . 'en_US';

        $inputs[] = self::ID;
        $inputs[] = self::KEY;

        Mergado\Tools\Settings::saveOptions($post, [
            self::ACTIVE,
            self::STANDARD_ACTIVE,
            self::CONVERSION_VAT_INCL,
        ], $inputs);
    }
};
