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

namespace Mergado\Kelkoo;

use Mergado\Tools\Settings;

class KelkooClass
{
    /**
     * Return active language options for Kelkoo
     * @return bool|mixed
     */
    public static function getKelkooActiveDomain()
    {
        $activeLangId = get_option(Settings::KELKOO['COUNTRY']);

        foreach(Settings::KELKOO_COUNTRIES as $item) {
            if($item['id_option'] === (int)$activeLangId) {
                return $item['type_code'];
            }
        }

        return false;
    }
}
