<?php

use Mergado\Tools\NewsClass;
use Mergado\Tools\Settings;

function ajax_cookie() {
    if(is_user_logged_in()) {
        $cookie = isset($_POST['cookie']) ? $_POST['cookie'] : '';
        $token = isset($_POST['token']) ? $_POST['token'] : '';

        if ($cookie !== '') {
            if ($token != Mergado_Marketing_Pack_Admin::getToken()) {
                wp_send_json_error(['error' => __('Invalid token', 'mergado-marketing-pack')]);
                exit;
            }

            switch ($cookie) {
                case 'mmp-cookie-rating':
                    $now = new DateTime();
                    update_option(Settings::COOKIE_RATING, $now->modify('+14 days')->format(NewsClass::DATE_FORMAT));
                    exit;

                case 'mmp-cookie-news':
                    $now = new DateTime();
                    update_option(Settings::COOKIE_NEWS, $now->modify('+14 days')->format(NewsClass::DATE_FORMAT));
                    exit;
            }
        }
    }
}

add_action( 'wp_ajax_ajax_cookie','ajax_cookie');