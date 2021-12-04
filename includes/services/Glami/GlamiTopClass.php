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

namespace Mergado\Glami;

use Mergado\Tools\Settings;

class GlamiTopClass
{
	const ACTIVE = 'glami-top-form-active';
	const SELECTION = 'glami-selection-top';
	const CODE = 'glami-form-top';

	const LANGUAGES = array(
		array('id_option' => 1, 'name' => 'glami.cz', 'type_code' => 'cz'),
		array('id_option' => 2, 'name' => 'glami.de', 'type_code' => 'de'),
		array('id_option' => 3, 'name' => 'glami.fr', 'type_code' => 'fr'),
		array('id_option' => 4, 'name' => 'glami.sk', 'type_code' => 'sk'),
		array('id_option' => 5, 'name' => 'glami.ro', 'type_code' => 'ro'),
		array('id_option' => 6, 'name' => 'glami.hu', 'type_code' => 'hu'),
		array('id_option' => 7, 'name' => 'glami.ru', 'type_code' => 'ru'),
		array('id_option' => 8, 'name' => 'glami.gr', 'type_code' => 'gr'),
		array('id_option' => 9, 'name' => 'glami.com.tr', 'type_code' => 'tr'),
		array('id_option' => 10, 'name' => 'glami.bg', 'type_code' => 'bg'),
		array('id_option' => 11, 'name' => 'glami.hr', 'type_code' => 'hr'),
		array('id_option' => 12, 'name' => 'glami.si', 'type_code' => 'si'),
		array('id_option' => 13, 'name' => 'glami.es', 'type_code' => 'es'),
		array('id_option' => 14, 'name' => 'glami.com.br', 'type_code' => 'br'),
		array('id_option' => 15, 'name' => 'glami.eco', 'type_code' => 'eco'),
	);

	private $active;
	private $selection;
	private $code;

	/******************************************************************************************************************
	 * IS
	 ******************************************************************************************************************/

	/**
	 * @return bool
	 */
	public function isActive() {
		$active = $this->getActive();
		$code = $this->getCode();

		if ( $active === '1' && $code && $code !== '') {
			return true;
		} else {
			return false;
		}
	}

	/*******************************************************************************************************************
	 * GET
	 *******************************************************************************************************************/

	/**
	 * @return false|mixed|void
	 */
	public function getActive()
	{
		if ( ! is_null( $this->active ) ) {
			return $this->active;
		}

		$this->active = get_option( self::ACTIVE, 0 );

		return $this->active;
	}

	/**
	 * @return array|false
	 */
	public function getSelection() {
		$activeLangId = get_option(self::SELECTION);

		foreach(self::LANGUAGES as $item) {
			if($item['id_option'] === (int)$activeLangId) {
				$this->selection = $item;
				return $this->selection;
			}
		}

		return false;
	}

	/**
	 * @return false|mixed|void
	 */
	public function getCode() {
		if ( ! is_null( $this->code ) ) {
			return $this->code;
		}

		$this->code = get_option( self::CODE, '' );

		return $this->code;
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
			[
				self::ACTIVE,
			],[
				self::CODE,
				self::SELECTION,
			]
		);
	}
}
