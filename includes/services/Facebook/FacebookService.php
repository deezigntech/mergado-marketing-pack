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

namespace Mergado\Facebook;

use Mergado\Tools\Settings;

class FacebookService {
	const ACTIVE = 'facebook-form-active';
	const CODE = 'facebook-form-pixel';
	const CONVERSION_VAT_INCL = 'facebook-vat-included';

	private $active;
	private $code;
	private $conversionVatIncluded;

	/******************************************************************************************************************
	 * IS
	 ******************************************************************************************************************/

	/**
	 * @return bool
	 */
	public function isActive() {
		$active         = $this->getActive();
		$code           = $this->getCode();

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
	public function getActive() {
		if ( ! is_null( $this->active ) ) {
			return $this->active;
		}

		$this->active = get_option(self::ACTIVE, 0);

		return $this->active;
	}


	/**
	 * @param $lang
	 *
	 * @return false|mixed|void
	 */
	public function getCode() {
		if ( ! is_null( $this->code ) ) {
			return $this->code;
		}

		$this->code = get_option(self::CODE, '');

		return $this->code;
	}


	/**
	 * @return false|mixed|void
	 */
	public function getConversionVatIncluded() {
		if ( ! is_null( $this->conversionVatIncluded ) ) {
			return $this->conversionVatIncluded;
		}

		$this->conversionVatIncluded = get_option(self::CONVERSION_VAT_INCL, 0);

		return $this->conversionVatIncluded;
	}

	/*******************************************************************************************************************
	 * SAVE FIELDS
	 ******************************************************************************************************************/

	/**
	 * @param $post
	 */
	public static function saveFields($post) {
		Settings::saveOptions($post, [
			self::ACTIVE,
			self::CONVERSION_VAT_INCL,
		], [
			self::CODE,
		]);
	}
}
