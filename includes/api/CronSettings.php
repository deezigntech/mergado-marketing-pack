<?php

use Mergado\Tools\Settings;

function ajax_save_wp_cron() {

    if(is_user_logged_in()) {
        $feed = isset($_POST['feed']) ? $_POST['feed'] : '';
        $token = isset($_POST['token']) ? $_POST['token'] : '';

        if ($feed !== '') {
            if ($token != Mergado_Marketing_Pack_Admin::getToken()) {
                wp_send_json_error(['error' => __('Invalid token', 'mergado-marketing-pack')]);
                exit;
            }

            switch ($feed) {
                case 'product':
                    try {
                        Settings::saveOptions($_POST, [
                            Settings::CRONS['ACTIVE_PRODUCT_FEED'],
                        ], [
                            Settings::CRONS['SCHEDULE_PRODUCT_FEED'],
                            Settings::CRONS['START_PRODUCT_FEED'],
                        ]);

                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack')]);
                    } catch (Exception $e) {
                        wp_send_json_error(['error' => __('Something went wrong during save.', 'mergado-marketing-pack')]);
                    }
                    exit;

                case 'stock':
                    try {
                        Settings::saveOptions($_POST, [
                            Settings::CRONS['ACTIVE_STOCK_FEED'],
                        ], [
                            Settings::CRONS['SCHEDULE_STOCK_FEED'],
                            Settings::CRONS['START_STOCK_FEED'],
                        ]);

                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack')]);
                    } catch (Exception $e) {
                        wp_send_json_error(['error' => __('Something went wrong during save.', 'mergado-marketing-pack')]);
                    }
                    exit;

                case 'category':
                    try {
                        Settings::saveOptions($_POST, [
                            Settings::CRONS['ACTIVE_CATEGORY_FEED'],
                        ], [
                            Settings::CRONS['SCHEDULE_CATEGORY_FEED'],
                            Settings::CRONS['START_CATEGORY_FEED'],
                        ]);

                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack')]);
                    } catch (Exception $e) {
                        wp_send_json_error(['error' => __('Something went wrong during save.', 'mergado-marketing-pack')]);
                    }
                    exit;
                case 'import':
                    try {
                        Settings::saveOptions($_POST, [
                            Settings::CRONS['ACTIVE_IMPORT_FEED'],
                        ], [
                            Settings::CRONS['SCHEDULE_IMPORT_FEED'],
                            Settings::CRONS['START_IMPORT_FEED'],
                        ]);

                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack')]);
                    } catch (Exception $e) {
                        wp_send_json_error(['error' => __('Something went wrong during save.', 'mergado-marketing-pack')]);
                    }
                    exit;
            }
        }
    }
}

add_action( 'wp_ajax_ajax_save_wp_cron','ajax_save_wp_cron');