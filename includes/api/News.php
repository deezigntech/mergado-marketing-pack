<?php

use Mergado\Tools\NewsClass;

function ajax_news() {
    if(is_user_logged_in()) {
        $todo = isset($_POST['todo']) ? $_POST['todo'] : '';
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $token = isset($_POST['token']) ? $_POST['token'] : '';

        if ($todo !== '') {
            if ($token != Mergado_Marketing_Pack_Admin::getToken()) {
                wp_send_json_error(['error' => __('Invalid token', 'mergado-marketing-pack')]);
                exit;
            }

            switch ($todo) {
                case 'mmp-set-readed':
                    NewsClass::setArticlesShown([$id]);
                    exit;
            }
        }
    }
}

add_action( 'wp_ajax_ajax_news','ajax_news');