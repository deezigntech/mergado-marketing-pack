<?php

namespace Mergado\Kelkoo;

use Mergado\Helpers\TemplateLoader;

class KelkooServiceIntegration {

	/**
	 * @var KelkooService
	 */
	private $kelkooService;

	public function __construct() {
		$this->kelkooService = new KelkooService();
	}

	public function kelkooPurchase($orderId)
	{
		$active = $this->kelkooService->isActive();
		$country = $this->kelkooService->getCountryActiveDomain();
		$id = $this->kelkooService->getId();
		$vatIncluded = $this->kelkooService->getConversionVatIncluded();

		if($active) {
			$order = wc_get_order($orderId);
			$products_tmp = $order->get_items();

			$products = [];

			//Set prices with or without vat
			if ($vatIncluded == 1) {
				$orderPrice = number_format( (float) $order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '' );
			} else {
				$orderPrice = number_format( (float) $order->get_total() - $order->get_total_tax() - $order->get_shipping_total(), wc_get_price_decimals(), '.', '' );
			}

			foreach ($products_tmp as $item) {
				if ($item->get_variation_id() == 0) {
					$prodId = $item->get_data()['product_id'];
				} else {
					$prodId = $item->get_data()['product_id'] . '-' . $item->get_variation_id();
				}

				$prodName = $item->get_name();

				//Set prices with or without vat
				if ($vatIncluded == 1) {
					$unitPrice = (float) ($item->get_total() + $item->get_total_tax());
				} else {
					$unitPrice = (float) $item->get_total();
				}

				$products[] = [
					'productname' => $prodName,
					'productid' => $prodId,
					'quantity' => $item->get_quantity(),
					'price' => $unitPrice,
				];
			}

			$products = json_encode($products);

			$templatePath = __DIR__ . '/templates/purchase.php';
			$templateVariables = [
				'id' => $id,
				'country' => $country,
				'orderPrice' => $orderPrice,
				'orderId' => $orderId,
				'basket' => $products
			];

			echo TemplateLoader::getTemplate($templatePath, $templateVariables);
		}
	}
}