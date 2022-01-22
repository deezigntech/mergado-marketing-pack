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

namespace Mergado\Etarget;

use Mergado;

class EtargetService
{
	const ACTIVE = 'etarget-form-active';
	const HASH = 'etarget-form-hash';
	const ID = 'etarget-form-id';

    private $active;
    private $hash;
    private $id;

    /*******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

	/**
	 * @return bool
	 */

    public function isActive() : bool
    {
        $active = $this->getActive();
        $id = $this->getId();
        $hash = $this->getHash();

        if ($active == '1' && $id && $hash && $id !== '' && $hash !== '') {
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
    public function getHash()
    {
        if (!is_null($this->hash)) {
            return $this->hash;
        }

        $this->hash = get_option(self::HASH, '');

        return $this->hash;
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
            self::ACTIVE,
        ], [
	        self::ID,
	        self::HASH,
        ]);
    }
};
