<?php

namespace Mergado\Google;

use DateTime;
use Mergado;

class GoogleReviewsClass
{
    //Both services
    const MERCHANT_ID = 'gr_merchant_id';

    //Opt-in
    const OPT_IN_ACTIVE = 'gr_optin_active';
    const OPT_IN_POSITION = 'gr_optin_position';
    const OPT_IN_GTIN = 'gr_optin_gtin';
    const OPT_IN_DELIVERY_DATE = 'gr_optin_delivery_date';

    //Badge
    const BADGE_ACTIVE = 'gr_badge_active';
    const BADGE_POSITION = 'gr_badge_position';

    const LANGUAGE = 'gr_badge_language';

    private $merchantId;
    private $optInActive;
    private $optInPosition;
    private $optInDeliveryDate;
    private $badgeActive;
    private $badgePosition;
    private $language;
    private $gtin;

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    /**
     * @return bool
     */
    public function isOptInActive()
    {
        $active = $this->getOptInActive();
        $merchantId = $this->getMerchantId();

        if ($active === '1' && $merchantId && $merchantId !== '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isBadgeActive()
    {
        $optInActive = $this->getOptInActive();
        $active = $this->getBadgeActive();
        $merchantId = $this->getMerchantId();

        if ($optInActive === '1' && $active === '1' && $merchantId && $merchantId !== '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isPositionInline()
    {
        return $this->getBadgePosition() == self::BADGE_POSITIONS_FOR_SELECT()[2]['id'];
    }

    /*******************************************************************************************************************
     * Get constants that need to be translated
     *******************************************************************************************************************/

    public static function OPT_IN_POSITIONS_FOR_SELECT()
    {
        return array(
            0 => ['id' => 0, 'name' => __('Center', 'mergado-marketing-pack'), 'codePosition' => 'CENTER_DIALOG'],
            1 => ['id' => 1, 'name' => __('Bottom right', 'mergado-marketing-pack'), 'codePosition' => 'BOTTOM_RIGHT_DIALOG'],
            2 => ['id' => 2, 'name' => __('Bottom left', 'mergado-marketing-pack'), 'codePosition' => 'BOTTOM_LEFT_DIALOG'],
            3 => ['id' => 3, 'name' => __('Top right', 'mergado-marketing-pack'), 'codePosition' => 'TOP_RIGHT_DIALOG'],
            4 => ['id' => 4, 'name' => __('Top left', 'mergado-marketing-pack'), 'codePosition' => 'TOP_LEFT_DIALOG'],
            5 => ['id' => 5, 'name' => __('Bottom tray', 'mergado-marketing-pack'), 'codePosition' => 'BOTTOM_TRAY']
        );
    }

    public static function BADGE_POSITIONS_FOR_SELECT() {
        return array(
            0 => ['id' => 0, 'name' => __('Bottom right', 'mergado-marketing-pack'), 'codePosition' => 'BOTTOM_RIGHT'],
            1 => ['id' => 1, 'name' => __('Bottom left', 'mergado-marketing-pack'), 'codePosition' => 'BOTTOM_LEFT'],
            2 => ['id' => 2, 'name' => __('No floating badge', 'mergado-marketing-pack'), 'codePosition' => 'INLINE'],
        );
    }

    public static function LANGUAGES() {
        return array(
            0 => ['id' => 0, 'name' => __('automatically', 'mergado-marketing-pack')],
            1 => ['id' => 1, 'name' => 'af'],
            2 => ['id' => 2, 'name' => 'ar'],
            3 => ['id' => 3, 'name' => 'cs'],
            4 => ['id' => 4, 'name' => 'da'],
            5 => ['id' => 5, 'name' => 'de'],
            6 => ['id' => 6, 'name' => 'en'],
            7 => ['id' => 7, 'name' => 'en-AU'],
            8 => ['id' => 8, 'name' => 'en-GB'],
            9 => ['id' => 9, 'name' => 'en-US'],
            10 => ['id' => 10, 'name' => 'es'],
            11 => ['id' => 11, 'name' => 'es-419'],
            12 => ['id' => 12, 'name' => 'fil'],
            13 => ['id' => 13, 'name' => 'fr'],
            14 => ['id' => 14, 'name' => 'ga'],
            15 => ['id' => 15, 'name' => 'id'],
            16 => ['id' => 16, 'name' => 'it'],
            17 => ['id' => 17, 'name' => 'ja'],
            18 => ['id' => 18, 'name' => 'ms'],
            19 => ['id' => 19, 'name' => 'nl'],
            20 => ['id' => 20, 'name' => 'no'],
            21 => ['id' => 21, 'name' => 'pl'],
            22 => ['id' => 22, 'name' => 'pt-BR'],
            23 => ['id' => 23, 'name' => 'pt-PT'],
            24 => ['id' => 24, 'name' => 'ru'],
            25 => ['id' => 25, 'name' => 'sv'],
            26 => ['id' => 26, 'name' => 'tr'],
            27 => ['id' => 27, 'name' => 'zh-CN'],
            28 => ['id' => 28, 'name' => 'zh-TW']
        );
    }

    /******************************************************************************************************************
     * GET TEMPLATES
     ******************************************************************************************************************/

    public function getBadgeTemplate()
    {
        if ($this->isBadgeActive()) {

            // used inside template
            $googleBadge = [
                'IS_INLINE' => $this->isPositionInline(),
                'MERCHANT_ID' => $this->getMerchantId(),
                'POSITION' => self::BADGE_POSITIONS_FOR_SELECT()[$this->getBadgePosition()]['codePosition'],
                'LANGUAGE' => self::LANGUAGES()[$this->getLanguage()]['name'],
            ];

            include_once(__DIR__ . '/templates/badge.php');
        }
    }

    public function getOptInTemplate($order)
    {
        if ($this->isOptInActive()) {
            $selectedGtin = $this->getGtinValue();
            $gtins = [];

            foreach ($order->get_items() as $item) {
                $product = wc_get_product($item->get_product_id());

                if ($selectedGtin !== '_sku') {
                    $sku = get_post_meta($item->get_product_id(), $selectedGtin);
                } else {
                    $sku = $product->get_sku();
                }

                if ($sku !== []) {
                    $gtins[] = ['gtin' => $sku[0]];
                }
            }

            if ($gtins === []) {
                $gtins = false;
            }

            $deliveryDate = new DateTime('now');

            if (is_numeric($this->getOptInDeliveryDate())) {
                $deliveryDate = $deliveryDate->modify( '+' . $this->getOptInDeliveryDate() . ' days');
            }

            // used inside template

            $googleReviewsOptIn = [
                'MERCHANT_ID' => $this->getMerchantId(),
                'POSITION' => self::OPT_IN_POSITIONS_FOR_SELECT()[$this->getOptInPosition()]['codePosition'],
                'LANGUAGE' => self::LANGUAGES()[$this->getLanguage()]['name'],
                'ORDER' => array(
                    'ID' => $order->get_id(),
                    'CUSTOMER_EMAIL' => $order->get_billing_email(),
                    'COUNTRY_CODE' => $order->get_billing_country(),
                    'ESTIMATED_DELIVERY_DATE' => $deliveryDate->format('Y-m-d'),
                    'PRODUCTS' => json_encode($gtins)
                ),
            ];

            include_once(__DIR__ . '/templates/optIn.php');
        }
    }

    /******************************************************************************************************************
     * GET FIELDS
     ******************************************************************************************************************/

    /**
     * @return false|string|null
     */
    public function getOptInActive()
    {
        if (!is_null($this->optInActive)) {
            return $this->optInActive;
        }

        $this->optInActive = get_option(self::OPT_IN_ACTIVE, 0);

        return $this->optInActive;
    }

    /**
     * @return false|mixed|void
     */
    public function getOptInPosition()
    {
        if (!is_null($this->optInPosition)) {
            return $this->optInPosition;
        }

        $this->optInPosition = get_option(self::OPT_IN_POSITION, 0);

        return $this->optInPosition;
    }

    /**
     * @return array|false|string
     */
    public function getOptInDeliveryDate()
    {
        if (!is_null($this->optInDeliveryDate)) {
            return $this->optInDeliveryDate;
        }

        $this->optInDeliveryDate = get_option(self::OPT_IN_DELIVERY_DATE, '');

        return $this->optInDeliveryDate;
    }

    /**
     * @return false|string|null
     */
    public function getBadgeActive()
    {
        if (!is_null($this->badgeActive)) {
            return $this->badgeActive;
        }

        $this->badgeActive = get_option(self::BADGE_ACTIVE, 0);

        return $this->badgeActive;
    }

    /**
     * @return false|string|null
     */
    public function getMerchantId()
    {
        if (!is_null($this->merchantId)) {
            return $this->merchantId;
        }

        $this->merchantId = get_option(self::MERCHANT_ID, '');

        return $this->merchantId;
    }

    /**
     * @return false|string|null
     */
    public function getBadgePosition()
    {
        if (!is_null($this->badgePosition)) {
            return $this->badgePosition;
        }

        $this->badgePosition = get_option(self::BADGE_POSITION, 0);

        return $this->badgePosition;
    }

    /**
     * @return false|string|null
     */
    public function getLanguage()
    {
        if (!is_null($this->language)) {
            return $this->language;
        }

        $this->language = get_option(self::LANGUAGE, 0);

        return $this->language;
    }

    /**
     * @return false|string|null
     */
    public function getGtinValue()
    {
        if (!is_null($this->gtin)) {
            return $this->gtin;
        }

        $this->gtin = get_option(self::OPT_IN_GTIN, '_sku');

        return $this->gtin;
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    /**
     * @param $post
     */
    public static function saveFields($post)
    {
        Mergado\Tools\Settings::saveOptions($post,
            [
                self::OPT_IN_ACTIVE,
                self::BADGE_ACTIVE
            ],
            [
                self::MERCHANT_ID,
                self::OPT_IN_DELIVERY_DATE,
                self::OPT_IN_GTIN,
            ],
            [
                self::LANGUAGE,
                self::BADGE_POSITION,
                self::OPT_IN_POSITION
            ]
        );
    }
}