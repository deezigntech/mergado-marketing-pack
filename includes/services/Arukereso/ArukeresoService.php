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

namespace Mergado\Arukereso;

use Exception;
use Mergado;
use TrustedShop;

include_once __MERGADO_DIR__ . 'autoload.php';

class ArukeresoService
{
    // BASE
    const ACTIVE = 'arukereso-active';
    const WEB_API_KEY = 'arukereso-web-api-key';
    const OPT_OUT = 'arukereso-verify-opt-out-text-';

    //WIDGET
    const WIDGET_ACTIVE = 'arukereso-widget-active';
    const WIDGET_DESKTOP_POSITION = 'arukereso-widget-desktop-position';
    const WIDGET_MOBILE_POSITION = 'arukereso-widget-mobile-position';
    const WIDGET_MOBILE_WIDTH = 'arukereso-widget-mobile-width';
    const WIDGET_APPEARANCE_TYPE = 'arukereso-widget-appearance-type';

    const DEFAULT_OPT = 'Do not send a satisfaction questionnaire within the Trusted Shop program.';

    private $active;
    private $webApiKey;
    private $widgetActive;
    private $widgetDesktopPosition;
    private $widgetMobilePosition;
    private $widgetMobileWidth;
    private $widgetAppearanceType;

    /*******************************************************************************************************************
     *******************************************************************************************************************
     ********************************************** DEFAULT CLASS OPTIONS
     ******************************************************************************************************************
     ******************************************************************************************************************/

    public function isActive()
    {
        $active = $this->getActive();
        $webApiKey = $this->getWebApiKey();

        if ($active == '1' && $webApiKey && $webApiKey != '') {
            return true;
        } else {
            return false;
        }
    }

    public function isWidgetActive()
    {
        $active = $this->getActive();
        $activeWidget = $this->getWidgetActive();
        $webApiKey = $this->getWebApiKey();

        if ($active == '1' && $activeWidget == '1' && $webApiKey && $webApiKey != '') {
            return true;
        } else {
            return false;
        }
    }

    /*******************************************************************************************************************
     * Get constants that need to be translated
     *******************************************************************************************************************/

    public static function getMobilePositionsConstant()
    {
        return array(
            0 => array('id_option' => 0, 'name' => __('On the left side', 'mergado-marketing-pack'), 'value' => 'L', 'mergado-marketing-pack'),
            1 => array('id_option' => 1, 'name' => __('On the right side', 'mergado-marketing-pack'), 'value' => 'R', 'mergado-marketing-pack'),
            2 => array('id_option' => 2, 'name' => __('At the left bottom of the window', 'mergado-marketing-pack'), 'value' => 'BL', 'mergado-marketing-pack'),
            3 => array('id_option' => 3, 'name' => __('At the right bottom of the window', 'mergado-marketing-pack'), 'value' => 'BR', 'mergado-marketing-pack'),
            4 => array('id_option' => 4, 'name' => __('Wide button at the bottom of the page', 'mergado-marketing-pack'), 'value' => 'W', 'mergado-marketing-pack'),
            5 => array('id_option' => 5, 'name' => __('On the left, only the badge is visible', 'mergado-marketing-pack'), 'value' => 'LB', 'mergado-marketing-pack'),
            6 => array('id_option' => 6, 'name' => __('On the left, only the text is visible', 'mergado-marketing-pack'), 'value' => 'LT', 'mergado-marketing-pack'),
            7 => array('id_option' => 7, 'name' => __('On the right, only badge is visible', 'mergado-marketing-pack'), 'value' => 'RB', 'mergado-marketing-pack'),
            8 => array('id_option' => 8, 'name' => __('On the right, only the text is visible', 'mergado-marketing-pack'), 'value' => 'RT', 'mergado-marketing-pack'),
            9 => array('id_option' => 9, 'name' => __('At the left bottom of the window, only the badge is visible', 'mergado-marketing-pack'), 'value' => 'BLB', 'mergado-marketing-pack'),
            10 => array('id_option' => 10, 'name' => __('At the left bottom of the window, only the text is visible', 'mergado-marketing-pack'), 'value' => 'BLT', 'mergado-marketing-pack'),
            11 => array('id_option' => 11, 'name' => __('At the right bottom of the window, only the badge is visible', 'mergado-marketing-pack'), 'value' => 'BRB', 'mergado-marketing-pack'),
            12 => array('id_option' => 12, 'name' => __('At the right bottom of the window, only the text is visible', 'mergado-marketing-pack'), 'value' => 'BRT', 'mergado-marketing-pack'),
            13 => array('id_option' => 13, 'name' => __('Don\'t show on mobile devices', 'mergado-marketing-pack'), 'value' => '', 'mergado-marketing-pack'),
        );
    }

    public static function DESKTOP_POSITIONS() {
        return array(
            0 => array('id_option' => 0, 'name' => __('Left', 'mergado-marketing-pack'), 'value' => 'L', 'mergado-marketing-pack'),
            1 => array('id_option' => 1, 'name' => __('Right', 'mergado-marketing-pack'), 'value' => 'R', 'mergado-marketing-pack'),
            2 => array('id_option' => 2, 'name' => __('Bottom left', 'mergado-marketing-pack'), 'value' => 'BL', 'mergado-marketing-pack'),
            3 => array('id_option' => 3, 'name' => __('Bottom right', 'mergado-marketing-pack'), 'value' => 'BR', 'mergado-marketing-pack'),
        );
    }

    public static function APPEARANCE_TYPES() {
        return array(
            0 => array('id_option' => 0, 'name' => __('By placing the cursor over a widget', 'mergado-marketing-pack'), 'value' => 0),
            1 => array('id_option' => 1, 'name' => __('With a click', 'mergado-marketing-pack'), 'value' => 1),
        );
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
    public function getWebApiKey()
    {
        if (!is_null($this->webApiKey)) {
            return $this->webApiKey;
        }

        $this->webApiKey = get_option(self::WEB_API_KEY, '');

        return $this->webApiKey;
    }

    /**
     * @param $lang
     * @return false|string|null
     */
    public function getOptOut($lang)
    {
        $this->key = get_option(self::OPT_OUT . $lang, '');
        return $this->key;
    }

    /**
     * @return false|string|null
     */
    public function getWidgetActive()
    {
        if (!is_null($this->widgetActive)) {
            return $this->widgetActive;
        }

        $this->widgetActive = get_option(self::WIDGET_ACTIVE, 0);

        return $this->widgetActive;
    }

    /**
     * @return false|string|null
     */
    public function getWidgetDesktopPosition()
    {
        if (!is_null($this->widgetDesktopPosition)) {
            return $this->widgetDesktopPosition;
        }

        $this->widgetDesktopPosition = get_option(self::WIDGET_DESKTOP_POSITION, 0);

        return $this->widgetDesktopPosition;
    }

        /**
         * @return false|string|null
         */
    public function getWidgetMobilePosition()
    {
        if (!is_null($this->widgetMobilePosition)) {
            return $this->widgetMobilePosition;
        }

        $this->widgetMobilePosition = get_option(self::WIDGET_MOBILE_POSITION, 0);

        return $this->widgetMobilePosition;
    }

        /**
         * @return false|string|null
         */
    public function getWidgetMobileWidth()
    {
        if (!is_null($this->widgetMobileWidth)) {
            return $this->widgetMobileWidth;
        }

        $this->widgetMobileWidth = get_option(self::WIDGET_MOBILE_WIDTH, 480);

        return $this->widgetMobileWidth;
    }

        /**
         * @return false|string|null
         */
    public function getWidgetAppearanceType()
    {
        if (!is_null($this->widgetAppearanceType)) {
            return $this->widgetAppearanceType;
        }

        $this->widgetAppearanceType = get_option(self::WIDGET_APPEARANCE_TYPE, 0);

        return $this->widgetAppearanceType;
    }
    /*******************************************************************************************************************
     * GET TEMPLATE
     ******************************************************************************************************************/
    public function getWidgetTemplate()
    {
        if ($this->isWidgetActive()) {

            // used inside template
            $arukeresoWidget = [
                "WEB_API_KEY" => $this->getWebApiKey(),
                "DESKTOP_POSITION" => self::DESKTOP_POSITIONS()[$this->getWidgetDesktopPosition()]['value'],
                "MOBILE_POSITION" => self::getMobilePositionsConstant()[$this->getWidgetMobilePosition()]['value'],
                "MOBILE_WIDTH" => $this->getWidgetMobileWidth(),
                "APPEARANCE_TYPE" => self::APPEARANCE_TYPES()[$this->getWidgetAppearanceType()]['value']
            ];

            include_once(__DIR__ . '/templates/widget.php');
        }
    }

    /*******************************************************************************************************************
     * FUNCTIONS
     ******************************************************************************************************************/

    public static function orderConfirmation($orderId)
    {
        $confirmed = get_post_meta($orderId, 'arukereso-verify-checkbox', true);
        $arukeresoService = new ArukeresoService();

        if ($arukeresoService->isActive()) {
            if (empty($confirmed)) {
                $order = wc_get_order($orderId);
                $products = [];

                foreach($order->get_items() as $item) {
                    if ($item->get_data()['variation_id'] == 0) {
                        $id = $item->get_data()['product_id'];
                    } else {
                        $id = $item->get_data()['product_id'] . '-' . $item->get_data()['variation_id'];
                    }

                    $name = $item->get_name();

                    /** Assign product to array */
                    $products[$id] = $name;
                }

                try {
                    /** Provide your own WebAPI key. You can find your WebAPI key on your partner portal. */
                    $Client = new TrustedShop($arukeresoService->getWebApiKey());

                    /** Provide the e-mail address of your customer. You can retrieve the e-amil address from the webshop engine. */
                    $Client->SetEmail($order->get_billing_email());

                    /** Customer's cart example. */
                    $Cart = $products;

                    /** Provide the name and the identifier of the purchased products.
                     * You can get those from the webshop engine.
                     * It must be called for each of the purchased products. */
                    foreach($Cart as $ProductIdentifier => $ProductName) {
                        /** If both product name and identifier are available, you can provide them this way: */
                        $Client->AddProduct($ProductName, $ProductIdentifier);
                        /** If neither is available, you can leave out these calls. */
                    }

                    /** This method perpares to send us the e-mail address and the name of the purchased products set above.
                     *  It returns an HTML code snippet which must be added to the webshop's source.
                     *  After the generated code is downloaded into the customer's browser it begins to send purchase information. */
                    echo $Client->Prepare();
                    /** Here you can implement error handling. The error message can be obtained in the manner shown below. This step is optional. */
                } catch (Exception $Ex) {
                    $ErrorMessage = $Ex->getMessage();
                }
            }
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
        $optLanguages = [];

        foreach(get_available_languages() as $lang) {
            $optLanguages[] = self::OPT_OUT . $lang;
        }

        $optLanguages[] = self::OPT_OUT . 'en_US';

        $otherInputs = [
            self::WEB_API_KEY,
            self::WIDGET_DESKTOP_POSITION,
            self::WIDGET_MOBILE_POSITION,
            self::WIDGET_MOBILE_WIDTH,
            self::WIDGET_APPEARANCE_TYPE
        ];

        $inputs = array_merge($optLanguages, $otherInputs);

        Mergado\Tools\Settings::saveOptions($post, [
            self::ACTIVE,
            self::WIDGET_ACTIVE
        ], $inputs);
    }
};
