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

class GlamiPixelClass {
	const ACTIVE = 'glami-form-active';
	const ACTIVE_LANG = 'glami-form-active-lang';
	const CODE = 'glami-form-pixel';
	const CONVERSION_VAT_INCL = 'glami-vat-included';
	const LANGUAGES = [ 'CZ', 'DE', 'SK', 'RO', 'HU', 'RU', 'GR', 'TR', 'BG', 'HR', 'SI', 'ES', 'BR', 'ECO' ];


	private $active;
	private $conversionVatIncluded;

	/******************************************************************************************************************
	 * IS
	 ******************************************************************************************************************/

	/**
	 * @param $lang
	 *
	 * @return bool
	 */
	public function isActive( $lang ) {
		$active         = $this->getActive();
		$code           = $this->getCode( $lang );
		$activeLanguage = $this->getActiveLang( $lang );

		if ( $active === '1' && $code && $code !== '' && $activeLanguage === '1' ) {
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
	public function getActive() {
		if ( ! is_null( $this->active ) ) {
			return $this->active;
		}

		$this->active = get_option( self::ACTIVE, 0 );

		return $this->active;
	}

	/**
	 * @param $lang
	 */
	public function getActiveLang( $lang ) {
		if ( '' === trim( $lang ) ) {
			return 0;
		}

		return get_option( $this->getActiveLangName( $lang ), 0 );
	}

	/**
	 * @param $lang
	 *
	 * @return false|mixed|void
	 */
	public function getCode( $lang ) {
		if ( '' === trim( $lang ) ) {
			return '';
		}

		return get_option( $this->getCodeName( $lang ), '' );
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

	/*******************************************************************************************************************
	 * GET NAMES
	 ******************************************************************************************************************/

	/**
	 * @param $lang
	 */
	public static function getCodeName( $lang ) {
		return self::CODE . '-' . $lang;
	}

	/**
	 * @param $lang
	 */
	public static function getActiveLangName( $lang ) {
		return self::ACTIVE_LANG . '-' . $lang;
	}

	/*******************************************************************************************************************
	 * SAVE FIELDS
	 ******************************************************************************************************************/

	/**
	 * @param $post
	 */
	public static function saveFields( $post ) {
		$inputs     = [];
		$checkboxes = [];

		foreach ( self::LANGUAGES as $key => $item ) {
			$inputs[]     = self::getCodeName( $item );
			$checkboxes[] = self::getActiveLangName( $item );
		}

		$checkboxes[] = self::ACTIVE;
		$checkboxes[] = self::CONVERSION_VAT_INCL;

		Settings::saveOptions( $post,
			$checkboxes
			,
			$inputs
		);
	}
}
