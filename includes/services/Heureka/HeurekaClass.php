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

namespace Mergado\Heureka;

use Exception;
use Mergado\Tools\Languages;
use Mergado\Tools\Settings;

class HeurekaClass
{
    const HEUREKA_URL = 'https://www.heureka.cz/direct/dotaznik/objednavka.php';
    const HEUREKA_URL_SK = 'https://www.heureka.sk/direct/dotaznik/objednavka.php';

    const DEFAULT_OPT = 'Do not send a satisfaction questionnaire within the Verified by Customer program.';

    /**
     * Send heureka request
     *
     * @param $url
     * @return string
     * @throws Exception
     */
    private static function sendRequest($url)
    {
        try {
            $parsed = parse_url($url);
            $fp = fsockopen($parsed['host'], 80, $errno, $errstr, 5);

            if (!$fp) {
                throw new Exception($errstr . ' (' . $errno . ')');
            } else {
                $return = '';
                $out = 'GET ' . $parsed['path'] . '?' . $parsed['query'] . " HTTP/1.1\r\n" .
                    'Host: ' . $parsed['host'] . "\r\n" .
                    "Connection: Close\r\n\r\n";
                fputs($fp, $out);
                while (!feof($fp)) {
                    $return .= fgets($fp, 128);
                }
                fclose($fp);
                $returnParsed = explode("\r\n\r\n", $return);

                return empty($returnParsed[1]) ? '' : trim($returnParsed[1]);
            }
        }  catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /*******************************************************************************************************************
     * CHECK STATUS AND DO
     *******************************************************************************************************************/

    public static function heurekaWidget()
    {
        $lang = Languages::getLang();

        if (in_array($lang, ['CZ', 'SK'])) {
            $widgetActive = get_option(Settings::HEUREKA['WIDGET_' . $lang], 0);

            if ($widgetActive == 1) {
                $marginTop = get_option(Settings::HEUREKA['WIDGET_' . $lang . '_MARGIN'], '60');
                $position = get_option(Settings::HEUREKA['WIDGET_' . $lang . '_POSITION'], '21');
                $minWidth = get_option(Settings::HEUREKA['WIDGET_' . $lang . '_HIDE_WIDTH'], 0);
                $showMobile = get_option(Settings::HEUREKA['WIDGET_' . $lang . '_SHOW_MOBILE'], 0);
                $widgetId = get_option(Settings::HEUREKA['WIDGET_' . $lang . '_ID'], 0);

                if ($widgetId != '' && $widgetId !== 0) {
                    $langLower = strtolower($lang);
                    self::widgetCode($widgetId, $langLower, $marginTop, $position, $minWidth, $showMobile);
                }
            }
        }
    }

    public static function heurekaOrderConfirmation($orderId)
    {
        $CZtrackActive = get_option(Settings::HEUREKA['ACTIVE_TRACK_CZ']);
        $CZtrackCode = get_option(Settings::HEUREKA['TRACK_CODE_CZ']);
        $SKtrackActive = get_option(Settings::HEUREKA['ACTIVE_TRACK_SK']);
        $SKtrackCode = get_option(Settings::HEUREKA['TRACK_CODE_SK']);

        if ($CZtrackActive && $CZtrackCode) {
	        $products = self::getProducts($orderId, 'CZ');

	        if (count($products)) {
	            self::orderConfirmationCode($orderId, $CZtrackCode, $products, 'cz');
	        }
        }
        if ($SKtrackActive && $SKtrackCode) {
	        $products = self::getProducts($orderId, 'SK');
	        if (count($products)) {
                self::orderConfirmationCode($orderId, $SKtrackCode, $products, 'sk');
            }
        }
    }

    /**
     * Send data from backend to Heureka
     *
     * @param $orderId
     * @throws Exception
     */
    public static function heurekaVerify($orderId)
    {
        $confirmed = get_post_meta($orderId, 'heureka-verify-checkbox', true);

        if (empty($confirmed)) {
            $CZverifiedActive = get_option(Settings::HEUREKA['ACTIVE_CZ']);
            $CZverifiedCode = get_option(Settings::HEUREKA['VERIFIED_CZ']);
            $SKverifiedActive = get_option(Settings::HEUREKA['ACTIVE_SK']);
            $SKverifiedCode = get_option(Settings::HEUREKA['VERIFIED_SK']);

            if ($CZverifiedActive && $CZverifiedActive == 1) {
                $verifiedCzCode = get_option(Settings::HEUREKA['VERIFIED_CZ']);

                if ($verifiedCzCode && $verifiedCzCode !== '') {
                    $url = self::getRequestURL($CZverifiedCode, $orderId);
                    self::sendRequest($url);
                }
            }

            if ($SKverifiedActive && $SKverifiedActive == 1) {
                $verifiedSkCode = get_option(Settings::HEUREKA['VERIFIED_SK']);

                if ($verifiedSkCode && $verifiedSkCode !== '') {
                    $url = self::getRequestURL($SKverifiedCode, $orderId);
                    self::sendRequest($url);
                }
            }
        }
    }



    /*******************************************************************************************************************
     * GET
     *******************************************************************************************************************/

    private static function getProducts($orderId, $lang) {
        $order = wc_get_order($orderId);
        $heurekaWithVat = get_option(Settings::HEUREKA['CONVERSION_VAT_INCL_' . $lang], 1); // default true because heureka want that that way
        $products = [];

        foreach($order->get_items() as $item) {
	        if ($heurekaWithVat == 1) {
	        	$unitPrice = ($item->get_total() + $item->get_total_tax()) / $item->get_quantity();
	        } else {
	        	$unitPrice = $item->get_total() / $item->get_quantity();
	        }

            $product = [
                'name' => $item->get_name(),
                'qty' => $item->get_quantity(),
                'unitPrice' => $unitPrice,
            ];

            if ($item->get_data()['variation_id'] == 0) {
                $product['id'] = $item->get_data()['product_id'];
            } else {
                $product['id'] = $item->get_data()['product_id'] . '-' . $item->get_data()['variation_id'];
            }

            $products[] = $product;
        }

        return $products;
    }

    private static function getRequestURL($apiKey, $orderId)
    {
//        $lang = get_locale();
        $order = wc_get_order($orderId);

        $url = null;

        $currency = $order->get_currency();

        if ($currency == 'CZK') {
            $url = HeurekaClass::HEUREKA_URL;
        }

        if ($currency == 'EUR') {
            $url = HeurekaClass::HEUREKA_URL_SK;
        }

        $url .= '?id=' . $apiKey;
        $url .= '&email=' . urlencode($order->get_billing_email());

        $products = $order->get_items();

        foreach ($products as $product) {

            $exactName = $product->get_name();

            $url .= '&produkt[]=' . urlencode($exactName);
            if ($product->get_variation_id() == 0) {
                $url .= '&itemId[]=' . urlencode($product->get_data()['product_id']);
            } else {
                $url .= '&itemId[]=' . urlencode($product->get_data()['product_id']) . '-' . urlencode($product->get_variation_id());
            }
        }

        $url .= '&orderid=' . urlencode($orderId);

        return $url;
    }

    /*******************************************************************************************************************
     * JS CODES
     ******************************************************************************************************************
     * @param $conversionCode
     * @param $langLower
     * @param $marginTop
     * @param $position
     * @param $minWidth
     * @param $showMobile
     */

    private static function widgetCode($widgetId, $langLower, $marginTop, $position, $minWidth, $showMobile)
    {

        echo "<script type='text/javascript'>
//            var heureka_widget_enable_mobile = " . ($showMobile === '' ? 0 : $showMobile) . ";
//            var heureka_widget_hide_width = " . ($minWidth === '' ? 0 : $minWidth) . ";
            var heureka_widget_active = true;
            
            var widgetId = '" . $widgetId . "';
            var marginTop = '" . ($marginTop === '' ? 60 : $marginTop) . "';
            var position = '" . ($position === '' ? 21 : $position) . "';
            //<![CDATA[
            var _hwq = _hwq || [];
            _hwq.push(['setKey', widgetId]);
            _hwq.push(['setTopPos', marginTop]);
            _hwq.push(['showWidget', position]);
            (function () {
                var ho = document.createElement('script');
                ho.type = 'text/javascript';
                ho.async = true;
                ho.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.heureka." . $langLower . "/direct/i/gjs.php?n=wdgt&sak=' + widgetId;
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(ho, s);
            })();
            //]]>
        </script>";
    }

    private static function  orderConfirmationCode($orderId, $trackCode, $heurekaProducts, $iso_code)
    {
        echo "<script type='text/javascript'>
            var _hrq = _hrq || [];
            _hrq.push(['setKey', '" . $trackCode . "']);
            _hrq.push(['setOrderId'," . $orderId  . "]);
            ";

        foreach($heurekaProducts as $product) {
            echo "_hrq.push(['addProduct', '" . $product['name'] . "', '" . $product['unitPrice'] . "', '" . $product['qty'] . "', '" . $product['id'] . "']);";
        }

        if ($iso_code == 'cz') {
            $src = 'https://im9.cz/js/ext/2-roi-async.js';
        } else {
            $src = 'https://im9.cz/' . $iso_code . '/js/ext/2-roi-async.js';
        }

        echo "_hrq.push(['trackOrder']);
            (function () {
                    var ho = document.createElement('script');
                    ho.type = 'text/javascript';
                    ho.async = true;
                    ho.src = '" . $src . "';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(ho, s);
                }
            )();
    </script>";
    }
}
