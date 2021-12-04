<?php

use Mergado\Tools\CronRunningException;
use Mergado\Tools\ImportPricesClass;
use Mergado\Tools\XMLCategoryFeed;
use Mergado\Tools\XMLProductFeed;
use Mergado\Tools\XMLStockFeed;

function ajax_generate_feed() {
    if(is_user_logged_in()) {
        $feed = isset($_POST['feed']) ? $_POST['feed'] : '';
        $token = isset($_POST['token']) ? $_POST['token'] : '';
        $force = isset($_POST['force']) ? $_POST['force'] : false; // forced generating manual
        $firstRun = isset($_POST['firstRun']) ? $_POST['firstRun'] : false; // forced generating manual


        if ($feed !== '') {
            if ($token != Mergado_Marketing_Pack_Admin::getToken()) {
                wp_send_json_error(['error' => __('Invalid token', 'mergado-marketing-pack')]);
                exit;
            }

            switch ($feed) {
                case 'productCron':
                    try {
                        $xmlProductFeed = new XMLProductFeed();

                        if ($firstRun) {
                            $xmlProductFeed->deleteLoweredProductsPerStep();
                        }

                        if ($firstRun && $xmlProductFeed->hasFeedFailed()) {
                            $xmlProductFeed->setLowerProductsPerStep($xmlProductFeed->getDefaultProductsPerStep());
                        }

                        $result = $xmlProductFeed->cron('', $force);
                        $percentage = $xmlProductFeed->getFeedPercentage();

                        // Save lowered value as main if cron is ok without internal error
                        if ($xmlProductFeed->getLoweredProductsPerStep() !== 0) {
                            $xmlProductFeed->setLoweredProductsPerStepAsMain();
                        }

                        $alertClass = new AlertClass();
                        $alertClass->setErrorInactive('product', AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION']);

                        wp_send_json_success(["success" => __('Product feed generated', 'mergado-marketing-pack'), 'feedStatus' => $result, 'percentage' => $percentage]);
                    } catch (CronRunningException $e) {
                        wp_send_json_error(['error' => __('Product feed generating already running. Please wait a minute and try it again.', 'mergado-marketing-pack')], 412);
                    }
                    exit;

                case 'stockCron':
                    try {
                        $xmlStockFeed = new XMLStockFeed();

                        if ($firstRun) {
                            $xmlStockFeed->deleteLoweredProductsPerStep();
                        }

                        if ($firstRun && $xmlStockFeed->hasFeedFailed()) {
                            $xmlStockFeed->setLowerProductsPerStep($xmlStockFeed->getDefaultProductsPerStep());
                        }

                        $result = $xmlStockFeed->generateStockXML('', $force);
                        $percentage = $xmlStockFeed->getFeedPercentage();

                        // Save lowered value as main if cron is ok without internal error
                        if ($xmlStockFeed->getLoweredProductsPerStep() !== 0) {
                            $xmlStockFeed->setLoweredProductsPerStepAsMain();
                        }

                        $alertClass = new AlertClass();
                        $alertClass->setErrorInactive('stock', AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION']);

                        wp_send_json_success(['success' => __('Heureka availability feed generated', 'mergado-marketing-pack'), 'feedStatus' => $result, 'percentage' => $percentage]);
                    } catch (CronRunningException $e) {
                        wp_send_json_error(['error' => __('Heureka availability feed already running.', 'mergado-marketing-pack')],412);
                    }
                    exit;

                case 'categoryCron':
                    try {
                        $xmlCategoryFeed = new XMLCategoryFeed();

                        if ($firstRun) {
                            $xmlCategoryFeed->deleteLoweredProductsPerStep();
                        }

                        if ($firstRun && $xmlCategoryFeed->hasFeedFailed()) {
                            $xmlCategoryFeed->setLowerProductsPerStep($xmlCategoryFeed->getDefaultCategoriesPerStep());
                        }

                        $result = $xmlCategoryFeed->generateCategoryXML('', $force);
                        $percentage = $xmlCategoryFeed->getFeedPercentage();

                        // Save lowered value as main if cron is ok without internal error
                        if ($xmlCategoryFeed->getLoweredProductsPerStep() !== 0) {
                            $xmlCategoryFeed->setLoweredProductsPerStepAsMain();
                        }

                        $alertClass = new AlertClass();
                        $alertClass->setErrorInactive('category', AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION']);

                        wp_send_json_success(['success' => __('Category feed generated', 'mergado-marketing-pack'), 'feedStatus' => $result, 'percentage' => $percentage]);
                    } catch (CronRunningException $e) {
                        wp_send_json_error(['success' => __('Category feed already running.', 'mergado-marketing-pack')],412);
                    }
                    exit;

                case 'importPrices':
                    $importPrices = new ImportPricesClass();
                    $result = $importPrices->importPrices('');

                    // Save lowered value as main if cron is ok without internal error
                    if ($importPrices->getLoweredProductsPerStep() !== 0) {
                        $importPrices->setLoweredProductsPerStepAsMain();
                    }

                    if($result) {
                        wp_send_json_success(['success' => __('Mergado prices imported', 'mergado-marketing-pack'), 'feedStatus' => $result]);
                    } else {
                        wp_send_json_error(['error' => __('Error importing prices. Do you have correct URL in settings?', 'mergado-marketing-pack')], 424);
                    }
                    exit;
            }
        }
    }
}

add_action( 'wp_ajax_ajax_generate_feed','ajax_generate_feed');

function ajax_lower_cron_product_step() {
    if(is_user_logged_in()) {
        $feed = $_POST['feed'] ?? '';
        $token = $_POST['token'] ?? '';

        if ($feed !== '') {
            if ($token != Mergado_Marketing_Pack_Admin::getToken()) {
                wp_send_json_error(['error' => __('Invalid token', 'mergado-marketing-pack')]);
                exit;
            }

            switch ($feed) {
                case 'product':
                    $xmlProductFeed = new XMLProductFeed();
                    $xmlProductFeed->setFeedCount( 0);
                    $xmlProductFeed->deleteTemporaryFiles();

                    $loweredPerStep = $xmlProductFeed->lowerProductsPerStep();

                    if ($loweredPerStep) {
                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack'), "loweredCount" => $loweredPerStep], 200);
                    } else {
                        //Not prestashop so simple
	                    $alertClass = new AlertClass();
	                    $alertClass->setErrorActive($feed, AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION']);

                        wp_send_json_error(['error' => __('Something went wrong. Feed can\'t be generated.', 'mergado-marketing-pack')], 500);
                    }

                    exit;
                case 'stock':
                    $xmlStockFeed = new XMLStockFeed();
                    $xmlStockFeed->setFeedCount(0);
                    $xmlStockFeed->deleteTemporaryFiles();

                    if ($loweredPerStep = $xmlStockFeed->lowerProductsPerStep()) {
                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack'), "loweredCount" => $loweredPerStep]);
                    } else {
	                    //Not prestashop so simple
	                    $alertClass = new AlertClass();
	                    $alertClass->setErrorActive($feed, AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION']);

                        wp_send_json_error(['error' => __('Something went wrong. Feed can\'t be generated.', 'mergado-marketing-pack')], 500);
                    }

                    exit;
                case 'category':
                    $xmlCategoryFeed = new XMLCategoryFeed();
                    $xmlCategoryFeed->setFeedCount(0);
                    $xmlCategoryFeed->deleteTemporaryFiles();

                    if ($loweredPerStep = $xmlCategoryFeed->lowerProductsPerStep()) {
                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack'), "loweredCount" => $loweredPerStep]);
                    } else {

	                    //Not prestashop so simple
	                    $alertClass = new AlertClass();
	                    $alertClass->setErrorActive($feed, AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION']);
                        wp_send_json_error(['error' => __('Something went wrong. Feed can\'t be generated.', 'mergado-marketing-pack')], 500);
                    }

                    exit;
                case 'import':
                    $importPricesClass = new ImportPricesClass();

                    if ($loweredPerStep = $importPricesClass->lowerProductsPerStep()) {
                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack'), "loweredCount" => $loweredPerStep]);
                    } else {
                        wp_send_json_error(['error' => __('Something went wrong. Prices can\'t be imported.', 'mergado-marketing-pack')], 500);
                    }

                    exit;
            }
        }
    }
}

add_action ( 'wp_ajax_ajax_lower_cron_product_step', 'ajax_lower_cron_product_step');

function ajax_save_import_url() {
    if(is_user_logged_in()) {
        $token = $_POST['token'] ?? '';
        $url = $_POST['url'] ?? '';

        if ($token != Mergado_Marketing_Pack_Admin::getToken()) {
            wp_send_json_error(['error' => __('Invalid token', 'mergado-marketing-pack')]);
            exit;
        }

        $importPricesClass = new ImportPricesClass();

        $result = $importPricesClass->setImportUrl($url);

        if ($result) {
            wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack')]);
        } else {
            wp_send_json_error(['error' => __('Something went wrong. Import url can\'t be saved.', 'mergado-marketing-pack')], 500);
        }

        exit;
    }
}

add_action ( 'wp_ajax_ajax_save_import_url', 'ajax_save_import_url');
