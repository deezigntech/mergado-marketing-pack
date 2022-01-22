<?php

namespace Mergado\Tools;

include_once __MERGADO_DIR__ . 'autoload.php';


class CookieClass {

	// Cookie names
	const COOKIE_YES_ADVERTISEMENT = 'cookielawinfo-checkbox-advertisement';
	const COOKIE_YES_ANALYTICAL = 'cookielawinfo-checkbox-analytics';
	const COOKIE_YES_FUNCTIONAL = 'cookielawinfo-checkbox-functional';

	// Cookie form
	const FIELD_COOKIES_ENABLE = 'form-cookie-enable-always';
	const FIELD_ADVERTISEMENT_USER = 'form-cookie-advertisement';
	const FIELD_ANALYTICAL_USER = 'form-cookie-analytical';
	const FIELD_FUNCTIONAL_USER = 'form-cookie-functional';

	/**
     * Google Analytics (gtag.js)
     *
	 * @return bool
	 */
	public static function analyticalEnabled() {
		if ( self::isCookieBlockingEnabled() ) {
			if ( self::isCookieActive( self::COOKIE_YES_ANALYTICAL ) ) {
				return true;
			} else {
				$cookieName = self::getAnalyticalCustomName();

				if ($cookieName !== '' && self::isCookieActive($cookieName)) {
					return true;
				} else {
					return false;
				}
			}
		} else {
            return true;
        }
	}

	/**
     * Glami Pixel, Biano Pixel, etarget, Sklik, Kelkoo, Heureka order confirmation
     *
	 * @return bool
	 */
	public static function advertismentEnabled() {
		if ( self::isCookieBlockingEnabled() ) {
			if ( self::isCookieActive( self::COOKIE_YES_ADVERTISEMENT ) ) {
				return true;
			} else {
				$cookieName = self::getAdvertisementCustomName();

				if ($cookieName !== '' && self::isCookieActive($cookieName)) {
					return true;
				} else {
					return false;
				}
			}
		} else {
            return true;
        }
	}

	/**
     * Heureka widget
     *
	 * @return bool
	 */
	public static function functionalEnabled() {
		if ( self::isCookieBlockingEnabled() ) {
			if ( self::isCookieActive( self::COOKIE_YES_FUNCTIONAL ) ) {
				return true;
			} else {
				$cookieName = self::getFunctionalCustomName();

				if ($cookieName !== '' && self::isCookieActive($cookieName)) {
					return true;
				} else {
					return false;
				}
			}
		} else {
            return true;
        }
	}


	// HELPERS
	public static function isCookieActive( $cookieName ) {
		if ( isset( $_COOKIE[ $cookieName ] ) && filter_var( $_COOKIE[ $cookieName ], FILTER_VALIDATE_BOOLEAN ) ) {
			return true;
		} else {
			return false;
		}
	}

	// ADMIN FORM VALUES
	public static function isCookieBlockingEnabled() {
		$val = get_option( self::FIELD_COOKIES_ENABLE );

		if ( trim( $val ) !== '' ) {
			if ( filter_var( $val, FILTER_VALIDATE_BOOLEAN ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function getAdvertisementCustomName() {
		$val = get_option( self::FIELD_ADVERTISEMENT_USER );

		if ( trim( $val ) !== '' ) {
			return $val;
		} else {
			return '';
		}
	}

	public static function getAnalyticalCustomName() {
		$val = get_option( self::FIELD_ANALYTICAL_USER );

		if ( trim( $val ) !== '' ) {
			return $val;
		} else {
			return '';
		}
	}

	public static function getFunctionalCustomName() {
		$val = get_option( self::FIELD_FUNCTIONAL_USER );

		if ( trim($val) !== '' ) {
			return $val;
		} else {
			return '';
		}
	}

    /*******************************************************************************************************************
     * Javascript
     ******************************************************************************************************************/

    public static function createJsVariables()
    {
        self::jsAddCustomerVariableNames();
    }

    public static function jsAddCustomerVariableNames()
    {
        $analyticalNames = implode('", "',array_filter([self::COOKIE_YES_ANALYTICAL, self::getAnalyticalCustomName()]));
        $advertisementNames = implode('", "',array_filter([self::COOKIE_YES_ADVERTISEMENT, self::getAdvertisementCustomName()]));
        $functionalNames = implode('", "',array_filter([self::COOKIE_YES_FUNCTIONAL, self::getFunctionalCustomName()]));

        ?>
            <script>
               window.mmp.cookies = {
                  functions: {},
                  sections: {
                    functional: {
                      onloadStatus: <?php echo (int) self::functionalEnabled() ?>,
                      functions: {},
                      names: {}
                    },
                    analytical: {
                      onloadStatus: <?php echo (int) self::analyticalEnabled() ?>,
                      functions: {},
                      names: {}
                    },
                    advertisement: {
                      onloadStatus: <?php echo (int) self::advertismentEnabled() ?>,
                      functions: {},
                      names: {}
                    }
                 }
               };

                window.mmp.cookies.sections.functional.names = ["<?php echo $functionalNames ?>"];
                window.mmp.cookies.sections.advertisement.names = ["<?php echo $advertisementNames ?>"];
                window.mmp.cookies.sections.analytical.names = ["<?php echo $analyticalNames ?>"];
            </script>
        <?php
    }

	/*******************************************************************************************************************
	 * SAVE FIELDS
	 ******************************************************************************************************************/

	/**
	 * @param $post
	 */
	public static function saveFields($post) {
		Settings::saveOptions($post, [
			CookieClass::FIELD_COOKIES_ENABLE,
		], [
			CookieClass::FIELD_ANALYTICAL_USER,
			CookieClass::FIELD_ADVERTISEMENT_USER,
			CookieClass::FIELD_FUNCTIONAL_USER
		]);
	}
}