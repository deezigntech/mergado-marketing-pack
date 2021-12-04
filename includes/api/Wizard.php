<?php


use Mergado\Tools\Settings;
use Mergado\Tools\XMLCategoryFeed;
use Mergado\Tools\XMLProductFeed;
use Mergado\Tools\XMLStockFeed;

function ajax_set_wizard_complete() {
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
                        Settings::saveOptions(
                            [Settings::WIZARD['FINISHED_PRODUCT'] => 'on'],
                            [Settings::WIZARD['FINISHED_PRODUCT']],
                            []
                        );

                        $xmlProductFeed = new XMLProductFeed();
                        $isAlreadyFinished = $xmlProductFeed->isWizardFinished();

                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack')]);
                    } catch (Exception $e) {
                        wp_send_json_error(['error' => __('Something went wrong during save.', 'mergado-marketing-pack')]);
                    }
                    exit;

                case 'stock':
                    try {
                        Settings::saveOptions(
                            [Settings::WIZARD['FINISHED_STOCK'] => 'on'],
                            [Settings::WIZARD['FINISHED_STOCK']],
                            []);

                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack')]);
                    } catch (Exception $e) {
                        wp_send_json_error(['error' => __('Something went wrong during save.', 'mergado-marketing-pack')]);
                    }
                    exit;

                case 'category':
                    try {
                        Settings::saveOptions(
                            [Settings::WIZARD['FINISHED_CATEGORY'] => 'on'],
                            [Settings::WIZARD['FINISHED_CATEGORY']],
                            []);

                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack')]);
                    } catch (Exception $e) {
                        wp_send_json_error(['error' => __('Something went wrong during save.', 'mergado-marketing-pack')]);
                    }
                    exit;
                case 'import':
                    try {
                        Settings::saveOptions([Settings::WIZARD['FINISHED_IMPORT'] => 'on'], [
                            [Settings::WIZARD['FINISHED_IMPORT']],
                        ], []);

                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack')]);
                    } catch (Exception $e) {
                        wp_send_json_error(['error' => __('Something went wrong during save.', 'mergado-marketing-pack')]);
                    }
                    exit;
            }
        }
    }
}

add_action( 'wp_ajax_ajax_set_wizard_complete','ajax_set_wizard_complete');