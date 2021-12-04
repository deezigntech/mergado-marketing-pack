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

namespace Mergado\Tools;

use AlertClass;
use DateTime;
use DOMDocument;
use Mergado_Marketing_Pack_Admin;
use XMLWriter;

class XMLStockFeed
{
    protected $language;
    protected $currency;
    protected $logger;
    protected $token;
    protected $lock;

    protected $currentBlogId;

    // Dirs
    public $tmpBlogDir;
    public $stockFeedTmpDir;
    public $outputBlogDir;
    public $stockFeedOutputDir;

    const FEED_SECTION = 'other';

    /*******************************************************************************************************************
     * XML GENERATORS
     *******************************************************************************************************************/
    /*******************************************************************************************************************
     * HEUREKA STOCK FEED
     *******************************************************************************************************************/

    public function __construct()
    {
        $this->logger = wc_get_logger();
        $this->token = Mergado_Marketing_Pack_Admin::getToken();
        $this->currentBlogId = Settings::getCurrentBlogId();
        $this->lock = get_option('stock_feed_' . $this->token);

        $this->tmpBlogDir = __MERGADO_TMP_DIR__ . $this->currentBlogId;
        $this->stockFeedTmpDir = __MERGADO_TMP_DIR__ . $this->currentBlogId . '/stockFeed/';
        $this->outputBlogDir =  __MERGADO_XML_DIR__ . $this->currentBlogId;
        $this->stockFeedOutputDir =  __MERGADO_XML_DIR__ . $this->currentBlogId . '/';
    }

    /**
     * @param $page
     * @param bool $redirect
     * @return bool
     */
    public function generateStockXML($page, $force = false, $redirect = false)
    {
        $now = new DateTime();
        $this->createNecessaryDirs();

        if($this->isFeedLocked($now) && !$force) {
            $this->logger->info('STOCK FEED LOCKED - generating process can\'t proceed');
            throw new CronRunningException();
        } else {
            $this->setFeedLocked($now);

            $productsPerStep = $this->getProductsPerStep();

            $currentFilesCount = $this->getCurrentTempFilesCount();
            $start = XMLClass::getStart($currentFilesCount);

            // If no temporary files, reset generating
            if ($start === 1) {
                $this->resetFeedGenerating();
            }

            $productsList = XMLClass::getProducts($start, $productsPerStep);

            // Step generating
            if ($this->isPartial($productsPerStep, $productsList)) {
                $file = $this->stockFeedTmpDir . ($currentFilesCount) . '.xml';

                $this->logger->info('Mergado log: Stock feed generator started - step ' . $currentFilesCount);
                $this->createXML($file, $start, $productsPerStep, $productsList);
                $this->logger->info('Mergado log: Stock feed generator ended - step ' . $currentFilesCount);
                $this->logger->info('Mergado log: Stock feed generator saved XML file - step ' . $currentFilesCount);

                $this->increaseIterator();
                $this->unlockFeed();

                if ($redirect) {
                    wp_redirect('admin.php?page=' . $page . '&flash=stepGenerated');
                    exit;
                }

                return 'stepGenerated';
            // Normal generating
            } else if ($this->isNormal($productsPerStep, $productsList)) {
                $file = $this->stockFeedOutputDir . 'stock_' . $this->token . '.xml';

                $this->logger->info('Mergado log: Stock feed generator started');
                $this->createXML($file);
                $this->logger->info('Mergado log: Stock feed generator ended');
                $this->logger->info('Mergado log: Stock feed generator saved XML file');

                $this->unlockFeed();

                if ($redirect) {
                    wp_redirect('admin.php?page=' . $page . '&flash=stockGenerated');
                    exit;
                }

                return 'fullGenerated';
            // Merge
            } else {
                $this->mergeTemporaryFiles();
                $this->unlockFeed();
                $this->updateFeedCount();

                if ($redirect) {
                    wp_redirect('admin.php?page=' . $page . '&flash=mergeGenerated');
                    exit;
                }

                return 'merged';
            }
        }
    }


    private function createXML($file, $start = null, $limit = null, $products = null)
    {
        if ($products === null) {
            $products = XMLClass::getProducts($start, $limit);
        }

        $xml_new = new XMLWriter();
        $xml_new->openUri($file);
        $xml_new->startDocument('1.0', 'UTF-8');
        $xml_new->startElement('item_list');

        foreach ($products as $i) {
            $product = wc_get_product($i['id']);

            if ($product->is_type('simple')) {
                $qty = $product->get_stock_quantity();
                $stockStatus = ($product->get_stock_status() == 'instock' ? true : false);

                if ($qty <= 0 && !$stockStatus) {
                    continue;
                } elseif ($stockStatus) {
					if ($qty <= 0) {
						$qty = 1; // If product doesn't have stock managment NULL is returned
					}

                    $xml_new->startElement('item');
                    $xml_new->writeAttribute('id', $product->get_id());

                    $xml_new->startElement('stock_quantity');
                    $xml_new->text($qty);
                    $xml_new->endElement();

                    $xml_new->endElement();
                }

            } elseif ($product->is_type('variable')) {
                $variations = $product->get_available_variations();

                if ($variations != []) {
                    foreach ($variations as $variation) {
                        $qty = max($variation['max_qty'], $variation['min_qty']);
                        $stockStatus = $variation['is_in_stock'];

                        if ($qty <= 0 && !$stockStatus) {
                            continue;
                        } elseif ($stockStatus) {
                            $xml_new->startElement('item');
                            $xml_new->writeAttribute('id', $variation['variation_id']);
                            $xml_new->startElement('stock_quantity');
                            $xml_new->text($qty);
                            $xml_new->endElement();

                            $xml_new->endElement();
                        }
                    }
                }
            }
        }

        $xml_new->endElement();
        $xml_new->endDocument();
        $xml_new->flush();
        unset($xml_new);
    }

    /*******************************************************************************************************************
     * FEED OPTIONS
     *******************************************************************************************************************/

    /**
     * Merge files, create XML and delete temporary files
     */
    private function mergeTemporaryFiles()
    {
        $storage = $this->stockFeedOutputDir . 'stock_' . $this->token . '.xml';
        $tmpShopDir = $this->stockFeedTmpDir;

        $this->logger->info('Merging XML files of stock feed.');
        $loop = 0;

        $xmlstr = '<item_list>';

        foreach (glob($tmpShopDir . '*.xml') as $file) {
            $xml = simplexml_load_file($file);
            foreach ($xml as $item) {
                $xmlstr .= $item->asXml();
            }

            $loop++;
        }

        $xmlstr .= '</item_list>';

        $xml_new = new XMLWriter();

        $xml_new->openURI($storage);
        $xml_new->startDocument('1.0', 'UTF-8');
        $xml_new->writeRaw($xmlstr);
        $xml_new->endDocument();

        $this->logger->info('Stock feed merged. XML created.');


        XMLClass::deleteTemporaryFiles($this->stockFeedTmpDir);

        return true;
    }

    private function isPartial($stepProducts, $productsList)
    {
        return XMLClass::isPartial($stepProducts, $productsList);
    }

    private function isNormal($stepProducts, $productsList)
    {
        return XMLClass::isNormal($stepProducts, $productsList);
    }

    /**
     * Reset feed and delete all TMP files
     */
    private function resetFeedGenerating()
    {
        XMLClass::resetFeedGenerating(XMLClass::FEED_ITERATORS['STOCK'], $this->stockFeedTmpDir);
    }

    /*******************************************************************************************************************
     * FEED LOCKING
     *******************************************************************************************************************/

    /**
     * Return if feed is currently locked
     *
     * @param $now
     * @return bool
     */
    private function isFeedLocked($now)
    {
        return XMLClass::isFeedLocked($this->lock, $now);
    }

    /**
     * Lock feed
     * @param $now
     */
    private function setFeedLocked( $now)
    {
        XMLClass::setFeedLocked('stock_feed_' . $this->token, $now);
        $this->logger->info('STOCK FEED - locking');
    }

    /**
     * Unlock feed
     */
    private function unlockFeed() {
        XMLClass::unlockFeed('stock_feed_' . $this->token);
        $this->logger->info('STOCK FEED - unlocking');
    }

    /*******************************************************************************************************************
     * FEED PRODUCT COUNT
     *******************************************************************************************************************/

    /**
     * Save the feed count to database for next runs
     */
    private function updateFeedCount()
    {
        XMLClass::updateFeedCount(XMLClass::FEED_COUNT['STOCK'], XMLClass::FEED_ITERATORS['STOCK']);
    }

    private function increaseIterator()
    {
        XMLClass::increaseIterator(XMLClass::FEED_ITERATORS['STOCK']);
    }

    /**
     * Return value of product per step
     * @return int
     */
    public function getProductsPerStep()
    {
        $loweredProductsPerStep = $this->getLoweredProductsPerStep();

        return XMLClass::getItemsPerStep(Settings::OPTIMIZATION['STOCK_FEED'], $loweredProductsPerStep);
    }

    public function getDefaultProductsPerStep()
    {
        return XMLClass::getDefaultProductsPerStep(XMLClass::DEFAULT_ITEMS_STEP['STOCK_FEED']);
    }

    public function setProductsPerStep($value)
    {
        return XMLClass::setItemsPerStep(Settings::OPTIMIZATION['STOCK_FEED'], $value);
    }

    public function setLoweredProductsPerStepAsMain()
    {
        $productsPerStep = $this->getLoweredProductsPerStep();
        self::setProductsPerStep($productsPerStep);
        self::deleteLoweredProductsPerStep();
    }

    public function setLowerProductsPerStep($value)
    {
        return XMLClass::setLoweredProductsPerStep(XMLClass::FEED_PRODUCTS_USER['STOCK'], $value);
    }

    public function deleteLoweredProductsPerStep()
    {
        return XMLClass::setLoweredProductsPerStep(XMLClass::FEED_PRODUCTS_USER['STOCK'], 0);
    }

    /**
     * Return value of lowered product step (repetetive call if 500 error timeout)
     */
    public function getLoweredProductsPerStep()
    {
        return XMLClass::getLoweredProductPerStep(XMLClass::FEED_PRODUCTS_USER['STOCK']);
    }

    public function lowerProductsPerStep()
    {
        $productsPerStep = $this->getProductsPerStep();
        
        $response =  XMLClass::lowerProductsPerStep(XMLClass::FEED_PRODUCTS_USER['STOCK'], $productsPerStep);

        if ($response === false) {
            self::deleteLoweredProductsPerStep();
        }

        return $response;
    }


    public function setFeedCount($value) {
        return XMLClass::setFeedCount(XMLClass::FEED_ITERATORS['STOCK'], $value);
    }

    /**
     * Delete all files from TMP folder
     */
    public function deleteTemporaryFiles()
    {
        XMLClass::deleteTemporaryFiles($this->stockFeedTmpDir);
    }

    public function getLastRunFeedCount()
    {
        return XMLClass::getLastRunFeedCount(XMLClass::FEED_COUNT['STOCK']);
    }

    /*******************************************************************************************************************
     * FEED FOLDER MANIPULATION
     *******************************************************************************************************************/

    /**
     * Check and create necessary directories for this cron
     */
    private function createNecessaryDirs()
    {
        Mergado_Marketing_Pack_Admin::checkAndCreateTmpDataDir();
        Mergado_Marketing_Pack_Admin::createDir($this->tmpBlogDir);
        Mergado_Marketing_Pack_Admin::createDir($this->stockFeedTmpDir);
        Mergado_Marketing_Pack_Admin::createDir($this->outputBlogDir);
        Mergado_Marketing_Pack_Admin::createDir($this->stockFeedOutputDir);
    }

    /**
     * @return int
     */
    public function getCurrentTempFilesCount()
    {
        $dir = $this->stockFeedTmpDir;

        return XMLClass::getCurrentTempFilesCount($dir);
    }

    /*******************************************************************************************************************
     * DATA FOR TEMPLATES
     *******************************************************************************************************************/

    public function getDataForTemplates()
    {
    	$feedName = 'stock';
        $feedUrl = $this->getFeedUrl();

	    $alertClass = new AlertClass();
	    $errors = $alertClass->getFeedErrors($feedName);

	    $productFeedExist = $this->isFeedExist();
        $percentage = $this->getFeedPercentage();

        if (!$productFeedExist && !$percentage) {
            $feedStatus = 'danger';
        } else if ($productFeedExist) {
            $feedStatus = 'success';
        } else {
            $feedStatus = 'warning';
        }

        return [
        	'feedSection' => self::FEED_SECTION,
        	'feedName' => $feedName,
            'feedStatus' => $feedStatus,
            'productFeedExist' => $productFeedExist,
            'percentageStep' => $percentage,
            'feedUrl' => $feedUrl,
            'cronGenerateUrl' =>  get_site_url() . '/mergado/?action=stockCron&token=' . $this->token,
            'feedPath' => $this->getFeedPath(),
            'wizardUrl' => '/wp-admin/admin.php?page=mergado-feeds-other&mmp-wizard=stock' . '&mmp-tab=stock',
//            'wizardUrl' => '/wp-admin/admin.php?page=mergado-feeds-other&mmp-wizard=stock&step=1' . '&mmp-tab=stock',
            'deleteUrl' => '/wp-admin/admin.php?page=mergado-config&action=deleteFeed&feed=stock&token=' . $this->token . '&mmp-tab=stock',
            'downloadUrl' => '/wp-admin/admin.php?page=mergado-config&action=downloadFeed&feed=stock&token=' . $this->token . '&mmp-tab=stock',
            'generateUrl' => '/wp-admin/admin.php?page=mergado-feeds-other&mmp-wizard=stock&step=3&force=true' . '&mmp-tab=stock',
            'cronSetUpUrl' => '/wp-admin/admin.php?page=mergado-feeds-other&mmp-wizard=stock&step=4a&force=true' . '&mmp-tab=stock',
            'createExportInMergadoUrl' => false,
            'lastUpdate' => $this->getLastFeedChange(),
	        'feedErrors' => $errors,
	        'wizardCompleted' => $this->isWizardFinished()
        ];
    }

    public function getWizardData()
    {
        return [
	        'feedSection' => self::FEED_SECTION,
            'token' => $this->token,
            'feed' => 'stock',
            'cronAction' => 'stockCron',
            'ajaxGenerateAction' => 'ajax_generate_feed',
            'feedListLink' => '/wp-admin/admin.php?page=mergado-feeds-other' . '&mmp-tab=stock',
            'wpCronActive' => Settings::CRONS['ACTIVE_STOCK_FEED'],
            'wpCronSchedule' => Settings::CRONS['SCHEDULE_STOCK_FEED'],
            'wpCronFirst' => Settings::CRONS['START_STOCK_FEED'],
            'cronUrl' =>  $this->getCronUrl(),
            'feedUrl' =>  $this->getFeedUrl(),
	        'settingsUrl' => '/wp-admin/admin.php?page=mergado-feeds-other&mmp-tab=settings',
            'productsPerStep' => $this->getProductsPerStep(),
            'percentage' => $this->getFeedPercentage(),
            'frontendData' => [
	            'productsPerStep' => $this->getProductsPerStep(),
	            'feedRunning' => false,
	            'feedFinished' => false,
            ]
        ];
    }

    public function isFeedExist()
    {
	    return file_exists(__MERGADO_XML_DIR__ . $this->currentBlogId . '/' . 'stock_' . $this->token . '.xml');
    }

    public function getFeedPercentage()
    {
        $productsPerRun = $this->getProductsPerStep();
        $currentStep = $this->getCurrentTempFilesCount();
        $lastRunIterationCount = $this->getLastRunFeedCount();

        return XMLClass::getFeedPercentage($productsPerRun, $currentStep, $lastRunIterationCount);
    }

    public function getFeedUrl()
    {
        return __MERGADO_XML_URL__ . $this->currentBlogId . '/stock_' . $this->token . '.xml';
    }

    public function getCronUrl()
    {
        return get_site_url() . '/mergado/?action=categoryCron&token=' . $this->token;
    }

    public function getFeedPath()
    {
        return realpath(__MERGADO_XML_DIR__ . $this->currentBlogId . '/stock_' . $this->token . '.xml');
    }

	public function getLastFeedChangeTimestamp()
	{
		return XMLClass::getLastFeedChangeTimestamp($this->getFeedPath());
	}

    public function getLastFeedChange()
    {
        $path = $this->getFeedPath();

        return XMLClass::getLastFeedChange($path);
    }

    public function isWpCronActive()
    {
        return get_option(Settings::CRONS['ACTIVE_STOCK_FEED'], 0);
    }

    public function getCronSchedule()
    {
        return get_option(Settings::CRONS['SCHEDULE_STOCK_FEED'], 0);
    }

    public function isWizardFinished()
    {
    	return (bool)get_option(Settings::WIZARD['FINISHED_STOCK'], 0);
    }

	public function getTotalProducts()
	{
		$productsPerRun = $this->getProductsPerStep();
		$lastRunIterationCount = $this->getLastRunFeedCount();

        $totalProducts = XMLClass::getTotalProducts($productsPerRun, $lastRunIterationCount);

        if ($totalProducts == 0) {
            $totalProducts = (int) wp_count_posts( 'product' )->publish;
        }

        return $totalProducts;
	}

	public function getFeedEstimate($schedule)
	{
		$productsPerStep = $this->getProductsPerStep();
		$productTotal = $this->getTotalProducts();

		return Settings::getScheduleEstimate($productsPerStep, $productTotal, $schedule);
	}

    public function hasFeedFailed()
    {
        $alertClass = new AlertClass();
        $errors = $alertClass->getFeedErrors('stock');
        return in_array(AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION'], $errors);
    }
}
