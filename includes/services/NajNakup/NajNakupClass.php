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

namespace Mergado\NajNakup;

use Exception;
use Mergado\Tools\Settings;

class NajNakupClass
{
    /**
     * Products
     *
     * @var array $products
     */
    private $products = array();

    /**
     * Add product to products variable
     *
     * @param string $productCode
     */
    public function addProduct($productCode)
    {
        $this->products[] = $productCode;
    }

    /**
     * Send new order to najnakup.sk
     *
     * @param int $shopId
     * @param string $email
     * @param int $shopOrderId
     * @return string
     * @throws Exception
     */
    public function sendNewOrder($shopId, $email, $shopOrderId)
    {
        $url = 'http://www.najnakup.sk/dz_neworder.aspx' . '?w=' . $shopId;
        $url .= '&e=' . urlencode($email);
        $url .= '&i=' . urlencode($shopOrderId);

        foreach ($this->products as $product) {
            $url .= '&p=' . urlencode($product);
        }

        $contents = self::sendRequest($url, "www.najnakup.sk", "80");

        if ($contents === false) {
            throw new Exception('Neznama chyba');
        } elseif ($contents !== '') {
            return $contents;
        } else {
            throw new Exception($contents);
        }
    }

    /**
     * Sends request to najnakup.sk
     *
     * @param string $url
     * @param string $host
     * @param string $port
     * @return string
     * @throws Exception
     */
    private static function sendRequest($url, $host, $port)
    {
        $fp = fsockopen($host, $port, $errno, $errstr, 6);

        if (!$fp) {
            throw new Exception($errstr . ' (' . $errno . ')');
        } else {
            $return = '';
            $out = "GET " . $url;
            $out .= " HTTP/1.1\r\n";
            $out .= "Host: " . $host . "\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);

            while (!feof($fp)) {
                $return .= fgets($fp, 128);
            }

            fclose($fp);
            $rp1 = explode("\r\n\r\n", $return);
            return $rp1[sizeof($rp1) - 1] == '0' ? '' : $rp1[sizeof($rp1) - 1];
        }
    }

    /**
     * Send data from backend to NajNakup
     *
     * @param $orderId
     * @return string
     */
    public static function sendNajnakupValuation($orderId)
    {
        $active = get_option(Settings::NAJNAKUP['ACTIVE']);
        $code = get_option(Settings::NAJNAKUP['ID']);

        if ($active == 1 && $code != '') {
            try {
                $najNakup = new NajNakupClass();

                $order = wc_get_order($orderId);
                $email = $order->get_billing_email();
                $products = $order->get_items();

                foreach ($products as $product) {
                    if($product->get_variation_id() == 0) {

                        $najNakup->addProduct($product->get_data()['product_id']);
                    } else {
                        $najNakup->addProduct($product->get_data()['product_id'] . '-' . $product->get_variation_id());
                    }
                }

                return $najNakup->sendNewOrder($code, $email, $orderId);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        return false;
    }
}
