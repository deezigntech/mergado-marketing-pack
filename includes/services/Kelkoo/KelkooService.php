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

class KelkooService
{
	const ACTIVE = 'kelkoo_active';
	const ID = 'kelkoo_merchant_id';
	const COUNTRY = 'kelkoo_country';
	const CONVERSION_VAT_INCL = 'kelkoo-vat-included';

	const COUNTRIES = array(
		array('id_option' => 1, 'name' => 'Austria', 'type_code' => 'at'),
		array('id_option' => 2, 'name' => 'Belgium', 'type_code' => 'be'),
		array('id_option' => 3, 'name' => 'Brazil', 'type_code' => 'br'),
		array('id_option' => 4, 'name' => 'Switzerland', 'type_code' => 'ch'),
		array('id_option' => 5, 'name' => 'Czech Republic', 'type_code' => 'cz'),
		array('id_option' => 6, 'name' => 'Germany', 'type_code' => 'de'),
		array('id_option' => 7, 'name' => 'Denmark', 'type_code' => 'dk'),
		array('id_option' => 8, 'name' => 'Spain', 'type_code' => 'es'),
		array('id_option' => 9, 'name' => 'Finland', 'type_code' => 'fi'),
		array('id_option' => 10, 'name' => 'France', 'type_code' => 'fr'),
		array('id_option' => 11, 'name' => 'Ireland', 'type_code' => 'ie'),
		array('id_option' => 12, 'name' => 'Italy', 'type_code' => 'it'),
		array('id_option' => 13, 'name' => 'Mexico', 'type_code' => 'mx'),
		array('id_option' => 14, 'name' => 'Flemish Belgium', 'type_code' => 'nb'),
		array('id_option' => 15, 'name' => 'Netherlands', 'type_code' => 'nl'),
		array('id_option' => 16, 'name' => 'Norway', 'type_code' => 'no'),
		array('id_option' => 17, 'name' => 'Poland', 'type_code' => 'pl'),
		array('id_option' => 18, 'name' => 'Portugal', 'type_code' => 'pt'),
		array('id_option' => 19, 'name' => 'Russia', 'type_code' => 'ru'),
		array('id_option' => 20, 'name' => 'Sweden', 'type_code' => 'se'),
		array('id_option' => 21, 'name' => 'United Kingdom', 'type_code' => 'uk'),
		array('id_option' => 22, 'name' => 'United States', 'type_code' => 'us'),
	);

	private $active;
	private $country;
	private $id;
	private $conversionVatIncluded;

	/******************************************************************************************************************
	 * IS
	 ******************************************************************************************************************/

	/**
	 * @return bool
	 */
	public function isActive() {
		$active = $this->getActive();
		$code = $this->getId();
		$activeDomain = $this->getCountryActiveDomain();

		if ( $active === '1' && $code && $code !== '' && $activeDomain && $activeDomain !== '') {
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
	public function getCountry() {
		$activeLangId = get_option(self::COUNTRY);

		if ( ! is_null( $this->country ) ) {
			return $this->country;
		}

		foreach(self::COUNTRIES as $item) {
			if($item['id_option'] === (int)$activeLangId) {
				$this->country = $item;
				return $this->country;
			}
		}

		return false;
	}

	/**
	 * @return false|mixed|void
	 */
	public function getId() {
		if ( ! is_null( $this->id ) ) {
			return $this->id;
		}

		$this->id = get_option( self::ID, '' );

		return $this->id;
	}

	/**
	 * @return false|mixed|void
	 */
	public function getConversionVatIncluded() {
		if ( ! is_null( $this->conversionVatIncluded ) ) {
			return $this->conversionVatIncluded;
		}

		$this->conversionVatIncluded = get_option( self::CONVERSION_VAT_INCL, 0 );

		return $this->conversionVatIncluded;
	}

	/**
	 * Return active language options for Kelkoo
	 * @return bool|mixed
	 */
	public function getCountryActiveDomain()
	{
		if ( ! is_null( $this->country ) ) {
			return $this->country['type_code'];
		}

		$country = $this->getCountry();

        if ($country) {
            return $country['type_code'];
        } else {
            return false;
        }

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
				self::CONVERSION_VAT_INCL,
			],[
				self::ID,
				self::COUNTRY,
			]
		);
	}
}
