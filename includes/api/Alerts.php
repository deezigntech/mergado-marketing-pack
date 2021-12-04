<?php
function ajax_disable_alert() {
	if(is_user_logged_in()) {
		$alertName = isset($_POST['name']) ? $_POST['name'] : '';
		$feedName = isset($_POST['feed']) ? $_POST['feed'] : '';
		$token = isset($_POST['token']) ? $_POST['token'] : '';


		if ($alertName !== '' && $feedName !== '') {
			if ($token != Mergado_Marketing_Pack_Admin::getToken()) {
				wp_send_json_error(['error' => __('Invalid token', 'mergado-marketing-pack')]);
				exit;
			}

			$alertClass = new AlertClass();
			$alertClass->setAlertDisabled($feedName, $alertName);
			exit;
		} else {
			exit;
		}
	}
}

add_action( 'wp_ajax_ajax_disable_alert','ajax_disable_alert');

function ajax_disable_section() {
	if(is_user_logged_in()) {
		$sectionName = isset($_POST['section']) ? $_POST['section'] : '';
		$token = isset($_POST['token']) ? $_POST['token'] : '';

		if ($sectionName !== '') {
			if ($token != Mergado_Marketing_Pack_Admin::getToken()) {
				wp_send_json_error(['error' => __('Invalid token', 'mergado-marketing-pack')]);
				exit;
			}

			$alertClass = new AlertClass();
			$alertClass->setSectionDisabled($sectionName);
			exit;
		} else {
			exit;
		}
	}
}

add_action( 'wp_ajax_ajax_disable_section','ajax_disable_section');

function ajax_add_alert() {
	if(is_user_logged_in()) {
		$alertName = isset($_POST['name']) ? $_POST['name'] : '';
		$feedName = isset($_POST['feed']) ? $_POST['feed'] : '';

		if ($alertName !== '' && $feedName !== '') {
			if ($token != Mergado_Marketing_Pack_Admin::getToken()) {
				wp_send_json_error(['error' => __('Invalid token', 'mergado-marketing-pack')]);
				exit;
			}

			$alertClass = new AlertClass();
			$alertClass->setErrorActive($feedName, $alertName);
			exit;
		} else {
			exit;
		}
	}
}

add_action( 'wp_ajax_ajax_add_alert','ajax_add_alert');