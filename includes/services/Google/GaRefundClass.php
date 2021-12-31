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

namespace Mergado\Google;

use Mergado;

class GaRefundClass
{
    const ACTIVE = 'ga_refund_active';
    const CODE = 'ga_refund_code';
    const STATUS = 'ga_refund_status';

    private $active;
    private $code;

    public function isActive()
    {
        $active = $this->getActive();
        $code = $this->getCode();

        if ($active == '1' && $code && $code !== '') {
            return true;
        } else {
            return false;
        }
    }

    public function isStatusActive($statusKey)
    {
        $active = $this->getStatus($statusKey);

        if ($active == '1') {
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
    public function getCode()
    {
        if (!is_null($this->code)) {
            return $this->code;
        }

        $code = get_option(self::CODE, 0);

        if (substr( $code, 0, 3 ) !== "UA-") {
            $this->code = 'UA-' . $code;
        } else {
            $this->code = $code;
        }

        return $this->code;
    }

    /**
     * @param string $statusKey
     * @return false|string|null
     */
    public function getStatus($statusKey)
    {
        // Default set to true
        if ($statusKey === 'wc-refunded' || $statusKey == 'cancelled') {
            $result = get_option(self::STATUS . $statusKey, 1);
        } else {
            $result = get_option(self::STATUS . $statusKey, 0);
        }

	    return $result;
    }

    public function sendRefundCode($products, $orderId, $partial = false)
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
            CURLOPT_URL => $this->createRefundUrl($products, $orderId, $partial),
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return true;
        } else {
            $decoded_response = json_decode($response, true);

            if ((int)($decoded_response["status"] / 100) === 2) {
                return true;
            }
        }
    }

    private function createRefundUrl($products, $orderId, $partial = false)
    {
        $data = array(
            'v' => '1', // Version.
            'tid' => $this->getCode(), // Tracking ID / Property ID.
            'cid' => '35009a79-1a05-49d7-b876-2b884d0f825b', // Anonymous Client ID
            't' => 'event', // Event hit type.
            'ec' => 'Ecommerce', // Event Category. Required.
            'ea' => 'Refund', // Event Action. Required.
            'ni' => '1', // Non-interaction parameter.
            'ti' => $orderId, // Transaction ID,
            'pa' => 'refund',
        );

        if ($partial) {
            $counter = 1;
            foreach($products as $id => $quantity) {
                $data['pr' . $counter . 'id'] = $id;
                $data['pr' . $counter . 'qt'] = $quantity;
                $counter++;
            }
        }

//        $url = 'https://www.google-analytics.com/debug/collect?';
        $url = 'https://www.google-analytics.com/collect?';
        $url .= http_build_query($data);

        return $url;
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

	/**
	 * @param $post
	 */
	public static function saveFields($post)
	{
	    $checkbox = array(self::ACTIVE);

        foreach (wc_get_order_statuses() as $key => $data) {
            $checkbox[] = self::STATUS . $key;
        }

		Mergado\Tools\Settings::saveOptions($post,
            $checkbox
		, [
			self::CODE
		]);
	}
}
