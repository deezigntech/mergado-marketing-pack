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

use Exception;
use Megrado_export;
use XMLWriter;

class XMLClass
{
    protected $language;
    protected $currency;

    const FEED_ITERATORS = [
        'PRODUCT' => 'mergado-feed-iterator',
        'STOCK' => 'mergado-stock-feed-iterator',
        'CATEGORY' => 'mergado-category-feed-iterator',
    ];

    const FEED_COUNT = [
        'PRODUCT' => 'mergado-feed-count',
        'STOCK' => 'mergado-stock-feed-count',
        'CATEGORY' => 'mergado-category-feed-count',
    ];

    const FEED_PRODUCTS_USER = [
        'PRODUCT' => 'mergado-feed-form-products-user',
        'STOCK' => 'mergado-feed-form-stock-user',
        'CATEGORY' => 'mergado-feed-form-category-user',
        'IMPORT' => 'mergado-feed-form-import-user',
    ];

    const FEEDS = [
    	'product', 'stock', 'category'
    ];

    const DEFAULT_ITEMS_STEP = [
        'PRODUCT_FEED' => 'mergado-feed-products-default-step',
        'STOCK_FEED' => 'mergado-feed-stock-default-step',
        'CATEGORY_FEED' => 'mergado-feed-category-default-step',
        'IMPORT_FEED' => 'mergado-feed-import-default-step',
    ];

    /*******************************************************************************************************************
     * XML GENERATORS
    *******************************************************************************************************************/
    /*******************************************************************************************************************
     * CREATE XML
     *******************************************************************************************************************/

    public static function getProducts($start = 0, $limit = 999999999)
    {
        $exporter = new Megrado_export();

        $products = [];

        foreach ($exporter->generate_data($start, $limit) as $k => $v) {
            $parentId = wc_get_product( $v['id'] )->get_parent_id();
            $product = wc_get_product( $v['id'] );
            $productPublished = $product->get_status() === 'publish';

            if($parentId != 0) {
                $parentProduct = wc_get_product( $parentId );
                $parentPublished = $parentProduct->get_status() === 'publish';
            } else {
                $parentProduct = false;
                $parentPublished = true;
            }

            // Check if parent product exist (woocommerce made error - deleted main product but not variations)
            // Variation is for VARIATION, VARIABLE is for main VARIATION product
            if ($product->is_type('variation') && $parentProduct == false) {
                $parentExists = false;
            } else {
                $parentExists = true;
            }

            // Check if not password protected and if parent no password protected (for variants)
            if(!post_password_required($v['id']) && !post_password_required($parentId) && $parentPublished && $productPublished && $parentExists) {
                if ($v['type'] != 'grouped' && $v['published'] === 1) { // preskoceni slozenych produktu
                    $products[$v['id']] = $v;
                }
            }
        }

        return $products;
    }


    /*******************************************************************************************************************
     * DOWNLOAD
     *******************************************************************************************************************/

    public static function download($page, $file) {
        $logger = wc_get_logger();
        if (file_exists($file)) {
            $logger->info('Mergado log: XML product file was downloaded');
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            echo readfile($file);
            exit;
        } else {
            wp_redirect('admin.php?page=' . $page);
        }
    }


    /*******************************************************************************************************************
     * FIND
     *******************************************************************************************************************/

    protected static function findParams($product, $values, $parent, $parentValues) {
        $usedParams = []; // For checking if already added
        $params = [];

        //Default product attributes
        foreach ($values as $attrName => $attrValue) {
            $item = [];

            if($attrValue instanceof \WC_Product_Attribute) {
//                $item['name'] = wc_attribute_label($attrValue->get_name()); //Case sensitive
                $item['name'] = wc_attribute_taxonomy_slug($attrName); //Lowercase // cant get simple products

                if(substr( $attrValue->get_name(), 0, 3 ) === "pa_") {
                    $item['value'] = $product->get_attribute( $attrValue->get_name() );
                } else {
                    $item['value'] = implode(', ', $attrValue->get_options());
                }
            } else { 
                $item['name'] = wc_attribute_label($attrName) . " (Variation level)";
                $term = \get_term_by('slug', $attrValue, $attrName);

                if ( $term ){
                    $item['value'] = $term->name;
                }else{
                    $item['value'] = $attrValue;
                }
            }

            if($item['value'] !== '') {
                $params[] = $item;
                $usedParams[] = $item['name'];
            }
        }

        //Other parameters/attributes not used in variation
        if($parentValues) {
            foreach ($parentValues as $attrName => $attrValue) {
                $item = [];

                if ($attrValue instanceof \WC_Product_Attribute) {
                    $item['name'] = wc_attribute_label($attrValue->get_name()); //Case //sensitive
                    //$item['name'] = wc_attribute_taxonomy_slug($attrName); //Lowercase // cant get simple products

                    if(substr( $attrValue->get_name(), 0, 3 ) === "pa_") {
                        $item['value'] = $parent->get_attribute( $attrValue->get_name() );
                    } else {
                        $item['value'] = implode(', ', $attrValue->get_options());
                    }

                } else { 
                    $item['name'] = wc_attribute_label($attrName);
                    $term = \get_term_by('slug', $attrValue, $attrName);
    
                    if ( $term ){
                        $item['value'] = $term->name;
                    }else{
                        $item['value'] = $attrValue;
                    }
                }


                if (!in_array($item['name'], $usedParams)) {
                    $params[] = $item;
                }
            }
        }

        return $params;
    }

    /*******************************************************************************************************************
     * GET FILES COUNT
     *******************************************************************************************************************/

    /**
     * @param $dir
     * @return int
     */
    public static function getCurrentTempFilesCount($dir)
    {
        if (glob($dir . '*.xml') != false) {
            return count(glob($dir . '*.xml'));
        } else {
            return 0;
        }
    }

    /*******************************************************************************************************************
     * PRODUCT COUNT
     *******************************************************************************************************************/

    /**
     * @param $feedName
     * @return int
     */
    public static function getItemsPerStep($feedName, $loweredProductsPerStep)
    {
        if ($loweredProductsPerStep != 0 && $loweredProductsPerStep !== '') {
            return $loweredProductsPerStep;
        } else {
           return (int)get_option($feedName, 0);
        }
    }

    public static function getDefaultProductsPerStep($ppsName)
    {
        return (int)get_option($ppsName, 0);
    }

    public static function setItemsPerStep($feedName, $value)
    {
        return update_option($feedName, $value);
    }

    public static function getLoweredProductPerStep($loweredProductPerStepName)
    {
        return (int)get_option($loweredProductPerStepName, 0);
    }

    public static function setLoweredProductsPerStep($loweredProductPerStepName, $value)
    {

        return update_option($loweredProductPerStepName, $value);
    }

    public static function lowerProductsPerStep($loweredProductPerStepName, $productsPerStep)
    {
        $loweredValue = round($productsPerStep / 2);

        if ($loweredValue < 10 && $loweredValue != 0) {
            return false;
        }

        if (self::setLoweredProductsPerStep($loweredProductPerStepName, $loweredValue)) {
            return $loweredValue;
        } else {
            return false;
        }
    }

    /**
     * @param $currentFilesCount
     * @return int
     */
    public static function getStart($currentFilesCount)
    {
        return $currentFilesCount === 0 ? 1 : $currentFilesCount + 1;
    }

    public static function isPartial($stepProducts, $productsList)
    {
        return $stepProducts !== 0 && $productsList !== [];
    }

    public static function isNormal($stepProducts, $productsList)
    {
        return $stepProducts === 0 || $stepProducts === false || ($productsList && ($stepProducts >= count($productsList)));
    }

    /**
     * Reset feed and delete all TMP files
     * @param $iteratorName
     * @param $tmpDir
     */
    public static function resetFeedGenerating($iteratorName, $tmpDir)
    {
        self::resetFeedCount($iteratorName);
        self::deleteTemporaryFiles($tmpDir);
    }

    /**
     * Delete all files from TMP folder
     * @param $tmpDir
     */
    public static function deleteTemporaryFiles($tmpDir)
    {
        $files = glob($tmpDir . '*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Return if feed is currently locked
     *
     * @param $lock
     * @param $now
     * @return bool
     */
    public static function isFeedLocked($lock, $now)
    {
        if($lock && $lock !== 0 && $lock >= $now) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lock feed
     * @param $feedName
     * @param $now
     */
    public static function setFeedLocked($feedName, $now)
    {
        return update_option($feedName, $now->modify( "+1 minute +30 seconds" ));
    }

    /**
     * Save the feed count to database for next runs
     */
    public static function updateFeedCount($feedCountName, $feedIterator)
    {
        return update_option($feedCountName, (int)get_option($feedIterator));
    }

    public static function increaseIterator($feedIterator)
    {
        return update_option($feedIterator, (int)get_option($feedIterator) + 1);
    }

    public static function getLastRunFeedCount($feedCountName)
    {
        return (int)get_option($feedCountName);
    }

    /**
     * Unlock feed
     * @param $feedName
     */
    public static function unlockFeed($feedName) {
        return update_option($feedName, 0);
    }

    public static function setFeedCount($feedIteratorName, $value) {
        return update_option($feedIteratorName, $value);
    }

    /**
     * Reset feed count
     * @param $feedIteratorName
     */
    public static function resetFeedCount($feedIteratorName)
    {
        return update_option($feedIteratorName, 1);
    }

    public static function getFeedPercentage($productsPerRun, $currentStep, $lastRunIterationCount)
    {
        $totalFiles = XMLClass::getTotalFiles($productsPerRun, $lastRunIterationCount);

        if ($totalFiles === 0) {
            return 0;
        }

        return intval(round(($currentStep / ($totalFiles)) * 100));
    }

    public static function getTotalFiles($productsPerRun, $lastRunIterationCount)
    {
	    if($productsPerRun === 0) {
		    $totalFiles = 0;
	    } else {
		    $publishedProductsCount = (int) wp_count_posts( 'product' )->publish;

		    $specialCoeficient = ( $lastRunIterationCount * $productsPerRun ) * 1.2; // Last run products + 20%;

		    // If first run of cron or someone added more than 20% of products
		    if ( $lastRunIterationCount === 0 || $specialCoeficient < $publishedProductsCount ) {
			    $totalGenerationRuns = $publishedProductsCount / $productsPerRun;
		    } else {
			    $totalGenerationRuns = $lastRunIterationCount;
		    }

		    $totalFiles = ceil( $totalGenerationRuns );
	    }

	    return $totalFiles;
    }

    public static function getTotalProducts($productsPerRun, $lastRunIterationCount)
    {
        return self::getTotalFiles($productsPerRun, $lastRunIterationCount) * $productsPerRun;
    }

    public static function getLastFeedChangeTimestamp($path)
    {
    	$lastUpdate = filemtime($path);

    	if ($lastUpdate) {
    		return $lastUpdate;
	    } else {
    		return false;
	    }
    }

    public static function getLastFeedChange($path)
    {
    	$lastUpdate = XMLClass::getLastFeedChangeTimestamp($path);

        if ($lastUpdate) {
            $lastUpdate = date(__('Y-m-d H:i', 'mergado-marketing-pack'), filemtime($path));
        } else {
            $lastUpdate = false;
        }

        return $lastUpdate;
    }
}

/**
 * Thrown when an service returns an exception
 */
class CronRunningException extends Exception
{

};
