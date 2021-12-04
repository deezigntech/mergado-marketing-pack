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
use Mergado_Marketing_Pack_Admin;
use WP_Query;
use XMLWriter;

class XMLCategoryFeed
{
    protected $language;
    protected $currency;
    protected $logger;
    protected $token;
    protected $lock;

    protected $currentBlogId;

    // Dirs
    public $tmpBlogDir;
    public $categoryFeedTmpDir;
    public $outputBlogDir;
    public $categoryFeedOutputDir;

    const FEED_SECTION = 'other';

    /*******************************************************************************************************************
     * XML GENERATORS
    *******************************************************************************************************************/
    /*******************************************************************************************************************
     * CATEGORY FEED
     *******************************************************************************************************************/

    public function __construct()
    {
        $this->logger = wc_get_logger();
        $this->token = Mergado_Marketing_Pack_Admin::getToken();
        $this->currentBlogId = Settings::getCurrentBlogId();
        $this->lock = get_option('category_feed_' . $this->token);

        $this->tmpBlogDir = __MERGADO_TMP_DIR__ . $this->currentBlogId;
        $this->categoryFeedTmpDir = __MERGADO_TMP_DIR__ . $this->currentBlogId . '/categoryFeed/';
        $this->outputBlogDir =  __MERGADO_XML_DIR__ . $this->currentBlogId;
        $this->categoryFeedOutputDir =  __MERGADO_XML_DIR__ . $this->currentBlogId . '/';
    }

    /**
     * @param $page
     * @param bool $redirect
     * @return bool
     */
    public function generateCategoryXML($page, $force = false, $redirect = false)
    {
        $now = new DateTime();
        $this->createNecessaryDirs();

        if($this->isFeedLocked($now) && !$force) {
            $this->logger->info('CATEGORY FEED LOCKED - generating process can\'t proceed');
            throw new CronRunningException();
        } else {
            $this->setFeedLocked($now);
            $categoriesPerStep = $this->getCategoriesPerStep();

            $currentFilesCount = $this->getCurrentTempFilesCount();
            $start = XMLClass::getStart($currentFilesCount);

            // If no temporary files, reset generating
            if ($start === 1) {
                $this->resetFeedGenerating();
            }

            $categoryList = $this->getCategories($start, $categoriesPerStep);

            // Step generating
            if ($this->isPartial($categoriesPerStep, $categoryList)) {
                $file = $this->categoryFeedTmpDir . ($currentFilesCount) . '.xml';

                $this->logger->info('Mergado log: Category feed generator started - step ' . $currentFilesCount);
                $this->createXML($file, $start, $categoriesPerStep, $categoryList);
                $this->logger->info('Mergado log: Category feed generator ended - step ' . $currentFilesCount);
                $this->logger->info('Mergado log: Category feed generator saved XML file - step ' . $currentFilesCount);

                $this->increaseIterator();
                $this->unlockFeed();

                if ($redirect) {
                    wp_redirect('admin.php?page=' . $page . '&flash=stepGenerated');
                    exit;
                }
                return 'stepGenerated';
            // Normal generating
            } else if ($this->isNormal($categoriesPerStep, $categoryList)) {
                $file = $this->categoryFeedOutputDir . 'category_' . $this->token . '.xml';

                $this->logger->info('Mergado log: Category feed generator started');
                $this->createXML($file);
                $this->logger->info('Mergado log: Category feed generator ended');
                $this->logger->info('Mergado log: Category feed generator saved XML file');

                $this->unlockFeed();

                if ($redirect) {
                    wp_redirect('admin.php?page=' . $page . '&flash=categoryGenerated');
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


    /**
     * @param $file
     * @param null $start
     * @param null $limit
     * @param null $categories
     * @return bool
     */
    private function createXML($file, $start = null, $limit = null, $categories = null)
    {
        if ($categories === null) {
            $categories = $this->getCategories($start, $limit);
        }

        $xml_new = new XMLWriter();
        $xml_new->openURI($file);
        $xml_new->startDocument('1.0', 'UTF-8');
        $xml_new->startElement('CHANNEL');
        $xml_new->writeAttribute('xmlns', 'http://www.mergado.com/ns/category/1.7');

        $xml_new->startElement('LINK');
        $xml_new->text(get_home_url(get_current_blog_id()));
        $xml_new->endElement();

        $xml_new->startElement('GENERATOR');
        $xml_new->text('mergado.woocommerce.marketingpack.' . str_replace('.', '_', PLUGIN_VERSION));
        $xml_new->endElement();

        foreach ($categories as $cat) {
            $minPrice = $this->getCategoryPrice($cat->slug, 'ASC');
            $maxPrice = $this->getCategoryPrice($cat->slug, 'DESC');

            $taxRate = Settings::getTaxRatesForCountry(get_option(Settings::VAT), '');

            if(Settings::isTaxCalculated()) {
                if(Settings::isTaxIncluded()) {
                    $catMinPrice = $minPrice;
                    $catMaxPrice = $maxPrice;
                } else {
                    $catMinPrice = round($minPrice * (1 + ($taxRate / 100)), 2);
                    $catMaxPrice = round($maxPrice * (1 + ($taxRate / 100)), 2);
                }
            } else {
                $catMinPrice = $minPrice;
                $catMaxPrice = $maxPrice;
            }

            if($cat->parent !== 0) {
                $breadcrumbs = $this->getBreadcrumbs($cat->parent, $cat->name);
            } else {
                $breadcrumbs = $cat->name;
            }

            // START ITEM
            $xml_new->startElement('ITEM');

            $xml_new->startElement('CATEGORY_NAME');
            $xml_new->text('<![CDATA[' . $cat->name . ']]>');
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY');
            $xml_new->text('<![CDATA[' . $breadcrumbs . ']]>');
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY_ID');
            $xml_new->text($cat->term_id);
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY_URL');
            $xml_new->text(get_category_link($cat));
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY_QUANTITY');
            $xml_new->text($cat->count);
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY_DESCRIPTION');
            $xml_new->text('<![CDATA[' . $cat->description . ']]>');
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY_MIN_PRICE_VAT');
            $xml_new->text($catMinPrice);
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY_MAX_PRICE_VAT');
            $xml_new->text($catMaxPrice);
            $xml_new->endElement();

            // END ITEM
            $xml_new->endElement();
        }

        $xml_new->endElement();
        $xml_new->endDocument();
        $xml_new->flush();
        unset($xml_new);

        return true;
    }

    /**
     * Return max or min price in category
     *
     * SORT -> 'DESC' for MAX value
     * SORT -> 'ASC' for MIN value
     *
     * @param $slug
     * @param $sort
     * @return mixed
     */
    private function getCategoryPrice($slug, $sort)
    {
        $args = array(
            'posts_per_page' => 1,
            'post_type' => 'product',
            'orderby' => 'meta_value_num',
            'order' => $sort,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $slug,
                    'operator' => 'IN'
                )
            ),
            'meta_query' => array(
                array(
                    'key' => '_price',
                )
            )
        );

        $loop = new WP_Query($args);

        return get_post_meta($loop->posts[0]->ID, '_price', true);
    }

    /**
     * Return breadcrumbs for category feed
     *
     * @param $id
     * @param $name
     * @return string
     */
    private function getBreadcrumbs($id, $name)
    {
        $term = get_term_by( 'id', $id, 'product_cat' );

        if($term->parent != 0) {
            $newName = $term->name . ' | ' . $name;
            return $this->getBreadcrumbs($term->parent, $newName);
        }

        return $term->name . ' | ' . $name;
    }

    /*******************************************************************************************************************
     * FEED OPTIONS
     *******************************************************************************************************************/

    /**
     * Merge files, create XML and delete temporary files
     */
    private function mergeTemporaryFiles()
    {
        $storage = $this->categoryFeedOutputDir . 'category_' . $this->token . '.xml';
        $tmpShopDir = $this->categoryFeedTmpDir;

        $logger = wc_get_logger();
        $logger->info('Merging Xml files of category feed.');
        $loop = 0;

        $xmlstr = '<CHANNEL xmlns="http://www.mergado.com/ns/category/1.7">';

        foreach (glob($tmpShopDir . '*.xml') as $file) {
            $xml = simplexml_load_file($file);
            $innerLoop = 0;
            foreach ($xml as $item) {
                if ($loop != 0 && (preg_match('/^mergado.woocommerce/', $item[0]) || ($innerLoop == 0 || $innerLoop == 1))) {
                    $innerLoop++;
                    continue;
                } else {
                    $innerLoop++;
                    $xmlstr .= $item->asXml();
                }
            }

            $loop++;
        }

        $xmlstr .= '</CHANNEL>';

        $xml_new = new XMLWriter();

        $xml_new->openURI($storage);
        $xml_new->startDocument('1.0', 'UTF-8');
        $xml_new->writeRaw($xmlstr);
        $xml_new->endDocument();

        $this->logger->info('Category feed merged. XML created.');


        XMLClass::deleteTemporaryFiles($this->categoryFeedTmpDir);

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
        XMLClass::resetFeedGenerating(XMLClass::FEED_ITERATORS['CATEGORY'], $this->categoryFeedTmpDir);
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
        XMLClass::setFeedLocked('category_feed_' . $this->token, $now);
        $this->logger->info('CATEGORY FEED - locking');
    }

    /**
     * Unlock feed
     */
    private function unlockFeed() {
        XMLClass::unlockFeed('category_feed_' . $this->token);
        $this->logger->info('CATEGORY FEED - unlocking');
    }

    /*******************************************************************************************************************
     * FEED PRODUCT COUNT
     *******************************************************************************************************************/

    /**
     * Save the feed count to database for next runs
     */
    private function updateFeedCount()
    {
        return XMLClass::updateFeedCount(XMLClass::FEED_COUNT['CATEGORY'], XMLClass::FEED_ITERATORS['CATEGORY']);
    }

    private function increaseIterator()
    {
        return XMLClass::increaseIterator(XMLClass::FEED_ITERATORS['CATEGORY']);
    }

    /**
     * Return value of product per step
     * @return int
     */
    public function getCategoriesPerStep()
    {
        $loweredProductsPerStep = $this->getLoweredProductsPerStep();

        return XMLClass::getItemsPerStep(Settings::OPTIMIZATION['CATEGORY_FEED'], $loweredProductsPerStep);
    }

    public function getDefaultCategoriesPerStep()
    {
        return XMLClass::getDefaultProductsPerStep(XMLClass::DEFAULT_ITEMS_STEP['CATEGORY_FEED']);
    }

    public function setCategoriesPerStep($value)
    {
        return XMLClass::setItemsPerStep(Settings::OPTIMIZATION['CATEGORY_FEED'], $value);
    }

    public function setLoweredProductsPerStepAsMain()
    {
        $productsPerStep = $this->getLoweredProductsPerStep();
        $this->setCategoriesPerStep($productsPerStep);
        $this->deleteLoweredProductsPerStep();
    }

    public function setLowerProductsPerStep($value)
    {
        return XMLClass::setLoweredProductsPerStep(XMLClass::FEED_PRODUCTS_USER['CATEGORY'], $value);
    }

    public function deleteLoweredProductsPerStep()
    {
        return XMLClass::setLoweredProductsPerStep(XMLClass::FEED_PRODUCTS_USER['CATEGORY'], 0);
    }

    /**
     * Return value of lowered product step (repetetive call if 500 error timeout)
     */
    public function getLoweredProductsPerStep()
    {
        return XMLClass::getLoweredProductPerStep(XMLClass::FEED_PRODUCTS_USER['CATEGORY']);
    }

    public function lowerProductsPerStep()
    {
        $productsPerStep = $this->getCategoriesPerStep();

        $response = XMLClass::lowerProductsPerStep(XMLClass::FEED_PRODUCTS_USER['CATEGORY'], $productsPerStep);

        if ($response === false) {
            $this->deleteLoweredProductsPerStep();
        }

        return $response;
    }


    public function setFeedCount($value) {
        return XMLClass::setFeedCount(XMLClass::FEED_ITERATORS['CATEGORY'], $value);
    }

    public function getLastRunFeedCount()
    {
        return XMLClass::getLastRunFeedCount(XMLClass::FEED_COUNT['CATEGORY']);
    }

    /**
     * @return int
     */
    public function getCurrentTempFilesCount()
    {
        $dir = $this->categoryFeedTmpDir;

        return XMLClass::getCurrentTempFilesCount($dir);
    }

    /**
     * Delete all files from TMP folder
     */
    public function deleteTemporaryFiles()
    {
        XMLClass::deleteTemporaryFiles($this->categoryFeedTmpDir);
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
        Mergado_Marketing_Pack_Admin::createDir($this->categoryFeedTmpDir);
        Mergado_Marketing_Pack_Admin::createDir($this->outputBlogDir);
        Mergado_Marketing_Pack_Admin::createDir($this->categoryFeedOutputDir);
    }

    /*******************************************************************************************************************
     * GET CATEGORIES
     *******************************************************************************************************************/

    /**
     * @param $start
     * @param $stepProducts
     */
    public function getCategories($start, $stepProducts)
    {
        return get_terms(
            [
                'taxonomy' => 'product_cat',
                'offset' => ($start - 1) * $stepProducts,
                'number' => $stepProducts,
                'hide_empty' => true
            ]
        );
    }

    public function getTotalCategories() {
        return count(get_terms(
            [
                'taxonomy' => 'product_cat',
                'hide_empty' => true
            ]
        ));
    }

    /*******************************************************************************************************************
     * DATA FOR TEMPLATES
     *******************************************************************************************************************/

    public function getDataForTemplates()
    {
    	$feedName = 'category';
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
            'cronGenerateUrl' =>  get_site_url() . '/mergado/?action=categoryCron&token=' . $this->token,
//            'wizardUrl' => '/wp-admin/admin.php?page=mergado-feeds-other&mmp-wizard=category&step=1' . '&mmp-tab=category',
            'wizardUrl' => '/wp-admin/admin.php?page=mergado-feeds-other&mmp-wizard=category' . '&mmp-tab=category',
            'deleteUrl' => '/wp-admin/admin.php?page=mergado-config&action=deleteFeed&feed=category&token=' . $this->token . '&mmp-tab=category',
            'downloadUrl' => '/wp-admin/admin.php?page=mergado-config&action=downloadFeed&feed=category&token=' . $this->token . '&mmp-tab=category',
            'generateUrl' => '/wp-admin/admin.php?page=mergado-feeds-other&mmp-wizard=category&step=3&force=true' . '&mmp-tab=category',
            'cronSetUpUrl' => '/wp-admin/admin.php?page=mergado-feeds-other&mmp-wizard=category&step=4a&force=true' . '&mmp-tab=category',
            'createExportInMergadoUrl' => 'https://app.mergado.com/new-project/prefill/?url=' . $feedUrl . '&inputFormat=mergado.cz.category',
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
            'feed' => 'category',
            'cronAction' => 'categoryCron',
            'ajaxGenerateAction' => 'ajax_generate_feed',
            'feedListLink' => '/wp-admin/admin.php?page=mergado-feeds-other' . '&mmp-tab=category',
            'wpCronActive' => Settings::CRONS['ACTIVE_CATEGORY_FEED'],
            'wpCronSchedule' => Settings::CRONS['SCHEDULE_CATEGORY_FEED'],
            'wpCronFirst' => Settings::CRONS['START_CATEGORY_FEED'],
            'cronUrl' =>  $this->getCronUrl(),
            'feedUrl' =>  $this->getFeedUrl(),
	        'settingsUrl' => '/wp-admin/admin.php?page=mergado-feeds-other&mmp-tab=settings',
            'productsPerStep' => $this->getCategoriesPerStep(),
            'percentage' => $this->getFeedPercentage(),
            'frontendData' => [
	            'productsPerStep' => $this->getCategoriesPerStep(),
	            'feedRunning' => false,
	            'feedFinished' => false,
            ]
        ];
    }

    public function isFeedExist()
    {
	    return file_exists(__MERGADO_XML_DIR__ . $this->currentBlogId . '/' . 'category_' . $this->token . '.xml');
    }

    public function getFeedPercentage()
    {
        $xmlCategoryFeed = new XMLCategoryFeed();

        $productsPerRun = $xmlCategoryFeed->getCategoriesPerStep();
        $currentStep = $xmlCategoryFeed->getCurrentTempFilesCount();
        $lastRunIterationCount = $xmlCategoryFeed->getLastRunFeedCount();

        return XMLClass::getFeedPercentage($productsPerRun, $currentStep, $lastRunIterationCount);
    }

    public function getFeedUrl()
    {
        return __MERGADO_XML_URL__ . $this->currentBlogId . '/category_' . $this->token . '.xml';
    }

    public function getCronUrl()
    {
        return get_site_url() . '/mergado/?action=categoryCron&token=' . $this->token;
    }

    public function getFeedPath()
    {
        return realpath(wp_normalize_path(__MERGADO_XML_DIR__ . $this->currentBlogId . '/category_' . $this->token . '.xml'));
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
        return get_option(Settings::CRONS['ACTIVE_CATEGORY_FEED'], 0);
    }

    public function getCronSchedule()
    {
        return get_option(Settings::CRONS['SCHEDULE_CATEGORY_FEED'], 0);
    }

    public function isWizardFinished()
    {
    	return (bool)get_option(Settings::WIZARD['FINISHED_CATEGORY'], 0);
    }

	public function getFeedEstimate($schedule)
	{
        $productsPerStep = $this->getCategoriesPerStep();
        $productTotal = $this->getTotalCategories();

        return Settings::getScheduleEstimate($productsPerStep, $productTotal, $schedule);
	}

    public function hasFeedFailed()
    {
        $alertClass = new AlertClass();
        $errors = $alertClass->getFeedErrors('category');
        return in_array(AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION'], $errors);
    }
}
