<?php

use Mergado\Tools\XMLCategoryFeed;
use Mergado\Tools\XMLProductFeed;
use Mergado\Tools\XMLStockFeed;

function ajax_get_schedule_estimate() {
	if(is_user_logged_in()) {
		$feed = isset($_POST['feed']) ? $_POST['feed'] : '';
		$schedule = isset($_POST['schedule']) ? $_POST['schedule'] : '';
		$token = isset($_POST['token']) ? $_POST['token'] : '';

		if ($feed !== '' && $schedule !== '') {
			if ($token != Mergado_Marketing_Pack_Admin::getToken()) {
				wp_send_json_error(['error' => __('Invalid token', 'mergado-marketing-pack')]);
				exit;
			}

			switch ($feed) {
				case 'product':
					$xmlProductFeed = new XMLProductFeed();
					$estimate = $xmlProductFeed->getFeedEstimate($schedule);

					wp_send_json_success(["success" => __('Estimate ready', 'mergado-marketing-pack'), 'estimate' => $estimate]);
					exit;
				case 'stock':
					$xmlStockFeed = new XMLStockFeed();
					$estimate = $xmlStockFeed->getFeedEstimate($schedule);

					wp_send_json_success(["success" => __('Estimate ready', 'mergado-marketing-pack'), 'estimate' => $estimate]);
					exit;
				case 'category':
					$xmlCategoryFeed = new XMLCategoryFeed();
					$estimate = $xmlCategoryFeed->getFeedEstimate($schedule);

					wp_send_json_success(["success" => __('Estimate ready', 'mergado-marketing-pack'), 'estimate' => $estimate]);
					exit;
			}
		}
	}
}

add_action( 'wp_ajax_ajax_get_schedule_estimate','ajax_get_schedule_estimate');
