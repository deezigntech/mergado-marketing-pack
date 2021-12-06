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

include_once __MERGADO_DIR__ . 'autoload.php';

use AlertClass;
use DateTime;
use DOMDocument;
use Exception;
use Mergado\Tools\XML\EanClass;
use Mergado_Marketing_Pack_Admin;
use XMLWriter;

class XMLProductFeed
{
    protected $language;
    protected $currency;
    protected $logger;
    protected $token;

    protected $currentBlogId;
    protected $lock;

    // Dirs
    private $tmpBlogDir;
    public $productFeedTmpDir;
    private $outputBlogDir;
    private $productFeedOutputDir;

    const FEED_SECTION = 'product';

    public function __construct()
    {
        $this->logger = wc_get_logger();
        $this->token = Mergado_Marketing_Pack_Admin::getToken();
        $this->currentBlogId = Settings::getCurrentBlogId();
        $this->lock = get_option('product_feed_' . $this->token);

        $this->tmpBlogDir = __MERGADO_TMP_DIR__ . $this->currentBlogId;
        $this->productFeedTmpDir = __MERGADO_TMP_DIR__ . $this->currentBlogId . '/productFeed/';
        $this->outputBlogDir = __MERGADO_XML_DIR__ . $this->currentBlogId;
        $this->productFeedOutputDir = __MERGADO_XML_DIR__ . $this->currentBlogId . '/';
    }

    /*******************************************************************************************************************
     * XML GENERATORS
     *******************************************************************************************************************/
    public function cron($page, $force = false, $redirect = false)
    {
        $now = new DateTime();

        $this->createNecessaryDirs();

        if ($this->isFeedLocked($now) && !$force) {
            $this->logger->info('PRODUCT FEED LOCKED - generating process can\'t proceed');
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
                $file = $this->productFeedTmpDir . ($currentFilesCount) . '.xml';

                $this->logger->info('Mergado log: Product feed generator started - step ' . $currentFilesCount);
                $xml = $this->createXML($start, $productsPerStep, $productsList);
                $this->logger->info('Mergado log: Product feed generator ended - step ' . $currentFilesCount);
                file_put_contents($file, $xml->saveXML());
                $this->logger->info('Mergado log: Product feed generator saved XML file - step ' . $currentFilesCount);

                $this->increaseIterator();
                $this->unlockFeed();

                if ($redirect) {
                    wp_redirect('admin.php?page=' . $page . '&flash=stepGenerated');
                    exit;
                }

				wp_schedule_single_event(time(), 'wp-cron-product-feed-hook');
				
                return 'stepGenerated';
            // Common generating
            } elseif ($this->isNormal($productsPerStep, $productsList)) {
                $file = $this->productFeedOutputDir . 'products_' . $this->token . '.xml';

                $this->logger->info('Mergado log: Product feed generator started');
                $xml = $this->createXML();
                $this->logger->info('Mergado log: Product feed generator ended');
                file_put_contents($file, $xml->saveXML());
                $this->logger->info('Mergado log: Product feed generator saved XML file');

                $this->unlockFeed();

                if ($redirect) {
                    wp_redirect('admin.php?page=' . $page . '&flash=cronGenerated');
                    exit;
                }
                return 'fullGenerated';
            // Merge temporary files
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
     * @param $start
     * @param $limit
     * @param null $products
     * @return DOMDocument
     */
    public function createXML($start = null, $limit = null, $products = null)
    {
        global $wpdb;

        $xml = new DOMDocument('1.0', 'UTF-8');
        $channel = $xml->createElement('CHANNEL');
        $channel->setAttribute('xmlns', 'http://www.mergado.com/ns/1.10');
        $channel->appendChild($xml->createElement('link', get_home_url(get_current_blog_id())));
        $channel->appendChild($xml->createElement('generator', 'mergado.woocommerce.marketingpack.' . str_replace('.', '_', PLUGIN_VERSION)));

        $currency = $wpdb->get_col($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s", 'woocommerce_currency'));
        $defaults['currency'] = array_pop($currency);
        $defaults['tax_calc'] = Settings::isTaxCalculated();
        $defaults['tax_inc'] = Settings::isTaxIncluded();

        //Used units
        $weightUnit = get_option('woocommerce_weight_unit');
        $sizeUnit = get_option('woocommerce_dimension_unit');

        if ($products === null) {
            $products = XMLClass::getProducts($start, $limit);
        }

        $eanClass = new EanClass();

        foreach ($products as $k => $v) {
            $productObject = wc_get_product($v['id']);
            $parentId = wc_get_product($v['id'])->get_parent_id();

            $posts = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID=%s", $v['id']));
            if (isset($posts[0])) {
                $post = $posts[0];
            } else {
                continue;
            }

            $item = $xml->createElement('ITEM');

            $item->appendChild($xml->createElement('ITEM_ID', $v['id']));

            $product_visibility = $productObject->get_catalog_visibility();
            $item->appendChild( $xml->createElement('VISIBILITY', $product_visibility ) );

            $stock = $this->getStockStatus($v['stock_status']);

            $item->appendChild($xml->createElement('AVAILABILITY', $stock));
            // adresa kde je product
            $url = get_the_permalink($v['id']);
            $item->appendChild($xml->createElement('URL', htmlspecialchars($url)));

            $item->appendChild($xml->createElement('CURRENCY', $defaults['currency']));

            if( $this->is_product_type($v['type'], 'variation') ){
                $v['name'] = $productObject->get_name();
            }

            $nameExact = $xml->createElement('NAME_EXACT');
            $nameExact->appendChild($xml->createCDATASection($v['name']));
            $item->appendChild($nameExact);


            if( ! $this->is_product_type($v['type'], 'variable') ){
                //PRODUCT PRICES
                $productTaxClass = $productObject->get_tax_class();
                $mergadoFeedTaxRate = Settings::getTaxRatesForCountry(get_option(Settings::VAT), $productTaxClass); // Fill in country code from settings
                $taxRateCoefficient = ($mergadoFeedTaxRate / 100) + 1;

                if (wc_tax_enabled()) { // taxes counting enabled
                    $wc_priceWithoutTax = wc_get_price_excluding_tax($productObject); // price that count with discounts
                    $wc_priceRegularWithoutTax = wc_get_price_excluding_tax($productObject, ['qty' => 1, 'price' => $productObject->get_regular_price()]);

                    $priceNoVat = $wc_priceRegularWithoutTax; // price without discounts
                    $priceVat = round($wc_priceRegularWithoutTax * $taxRateCoefficient, 2); // default price with VAT
                    $discountPriceNoVat = $wc_priceWithoutTax; // discounted price without VAT
                    $discountPriceVat = round($wc_priceWithoutTax * $taxRateCoefficient, 2); // discounted price with VAT

                    $item->appendChild($xml->createElement('VAT', $mergadoFeedTaxRate));
                    $item->appendChild($xml->createElement('PRICE_VAT', $priceVat));
                    $item->appendChild($xml->createElement('PRICE', $priceNoVat));

                    // Only if is discounted
                    if ($priceNoVat != $discountPriceNoVat) {
                        $item->appendChild($xml->createElement('PRICE_DISCOUNT', $discountPriceNoVat));
                        $item->appendChild($xml->createElement('PRICE_DISCOUNT_VAT', $discountPriceVat));
                    }
                } else { // taxes not counting
                    $priceVat = $productObject->get_regular_price();
                    $discountPriceVat = $productObject->get_price();

                    $item->appendChild($xml->createElement('PRICE_VAT', $priceVat)); // price or sale price

                    if ($priceVat != $discountPriceVat) {
                        $item->appendChild($xml->createElement('PRICE_DISCOUNT_VAT', $discountPriceVat));
                    }
                }

                $salePriceEffectiveDate = $this->getSaleDateInterval($productObject);

                if ($salePriceEffectiveDate) {
                    $item->appendChild($xml->createElement('SALE_PRICE_EFFECTIVE_DATE', $salePriceEffectiveDate));
                }
            }

            //PRODUCT IMAGES
            $images = null;
            $hasMainImage = false;

            //Take images from product if exist and assign them
            if ($v['images'] != "") {
                $images = $this->findImages($v['images']);
                $item->appendChild($xml->createElement('IMAGE', $images['main']));
                $hasMainImage = true;

                // Alt for normal
                if ($images['alt']) {
                    foreach ($images['alt'] as $img) {
                        $item->appendChild($xml->createElement('IMAGE_ALTERNATIVE', htmlspecialchars($img)));
                    }
                }
            }

            // If parent product exists, assign images from him to variable (IMAGE_ALTERNATIVE tag if IMAGE already exist)
            if ($parentId) {
                $parentImages = $this->getParentImages($parentId);

                if ($parentImages != []) {
                    if (!$hasMainImage) {
                        $item->appendChild($xml->createElement('IMAGE', $parentImages[0]));
                        $hasMainImage = true;
                    } else {
                        $item->appendChild($xml->createElement('IMAGE_ALTERNATIVE', $parentImages[0]));
                    }

                    unset($parentImages[0]);

                    if (count($parentImages) > 0) {
                        foreach ($parentImages as $img) {
                            $item->appendChild($xml->createElement('IMAGE_ALTERNATIVE', htmlspecialchars($img)));
                        }
                    }
                }
            }

            // If not has main image then add placeholder
            if (!$hasMainImage) {
                $item->appendChild($xml->createElement('IMAGE', htmlspecialchars(wc_placeholder_img_src('woocommerce_single'))));
            }

            //Product parameters
            $productAttributes = $productObject->get_attributes();

            if (isset($products[$parentId])) {
                $productParentObject = wc_get_product($parentId);
                $parentParams = wc_get_product($parentId)->get_attributes();
            } else {
                $productParentObject = null;
                $parentParams = null;
            }

            $params = $this->findParams($productObject, $productAttributes, $productParentObject, $parentParams);

            $params = apply_filters('product_feed_params', $params, $productObject, $productParentObject );

            if ($params !== null) {
                foreach ($params as $paramKey => $paramValue) {
                    $xmlParam = $xml->createElement('PARAM');

                    $paramName = $xml->createElement('NAME');
                    $paramName->appendChild($xml->createCDATASection($paramValue['name']));
                    $xmlParam->appendChild($paramName);

                    $paramNameValue = $xml->createElement('VALUE');
                    $paramNameValue->appendChild($xml->createCDATASection($paramValue['value']));
                    $xmlParam->appendChild($paramNameValue);

                    $item->appendChild($xmlParam);
                }
            }

            if ($v['stock'] !== '') {
                $item->appendChild($xml->createElement('STOCK_QUANTITY', sprintf('%s', $v["stock"])));
            }

            if ( ! $this->is_product_type($v['type'], 'variation') ) {
                $productNo = $xml->createElement('PRODUCTNO');
                $productNo->appendChild($xml->createCDATASection($v['sku']));
                $item->appendChild($productNo);

                $description = $xml->createElement('DESCRIPTION');
                $description->appendChild($xml->createCDATASection($v['description']));
                $item->appendChild($description);

                $categories = $this->findCategory($v['category_ids']);
                $category = $xml->createElement('CATEGORY');
                $category->appendChild($xml->createCDATASection($categories));
                $item->appendChild($category);

                $shortDescription = $xml->createElement('DESCRIPTION_SHORT');
                $shortDescription->appendChild($xml->createCDATASection($v['short_description']));
                $item->appendChild($shortDescription);

                // SET EAN IF EXIST AND SELECTED PLUGIN ACTIVE
                $eanCode = $eanClass->getEan($v, false,'simple');

                if ($eanCode && $eanCode !== '') {
                    $ean = $xml->createElement('EAN', $eanCode);
                    $item->appendChild($ean);
                }
            } else {
                $parentId = wc_get_product($v['id'])->get_parent_id();
//                    $parentId = wc_get_product_id_by_sku($v['parent_id']);
                $item->appendChild($xml->createElement('ITEMGROUP_ID', $parentId));

                $productNo = $xml->createElement('PRODUCTNO');
                $productNo->appendChild($xml->createCDATASection(($v['sku'] != '') ? $v['sku'] : (isset($products[$parentId]['sku']) ? $products[$parentId]['sku'] : '')));
                $item->appendChild($productNo);

                if (isset($products[$parentId])) {
                    $categories = $this->findCategory($products[$parentId]['category_ids']);
                } else {
                    $categories = '';
                }

                $category = $xml->createElement('CATEGORY');
                $category->appendChild($xml->createCDATASection($categories));
                $item->appendChild($category);

                // Short description
                $shortDescription = $xml->createElement('DESCRIPTION_SHORT');

                if (isset($products[$parentId])) {
                    $shortDescription->appendChild($xml->createCDATASection($products[$parentId]['short_description']));
                } else {
                    $shortDescription->appendChild($xml->createCDATASection(''));
                }

                $item->appendChild($shortDescription);

                // Description
                $description = $xml->createElement('DESCRIPTION');
                $description->appendChild($xml->createCDATASection(sprintf('%s %s', isset($products[$parentId]['description']) ? $products[$parentId]['description'] : '', $v['description'])));

                $item->appendChild($description);

                // Variant description
                $variantDescription = $xml->createElement('VARIANT_DESCRIPTION');
                $variantDescription->appendChild($xml->createCDATASection($v['description']));

                $item->appendChild($variantDescription);

                // SET EAN IF EXIST AND SELECTED PLUGIN ACTIVE
                $eanCode = $eanClass->getEan($v, $parentId, 'variation');

                if ($eanCode && $eanCode !== '') {
                    $ean = $xml->createElement('EAN', $eanCode);
                    $item->appendChild($ean);
                }

                $parent = array_key_exists($parentId, $products) ? $products[$parentId] : null;

                // Variant administration in woocomerce showing MAIN PRODUCT attributes as placeholder ..
                // so assume that customer will fill only the one he wants to change
            }

            if ( $productObject->get_length() != 0){
                $item->appendChild($xml->createElement('SHIPPING_LENGTH', sprintf('%s %s', $productObject->get_length(), $sizeUnit)));
            }

            if( $productObject->get_width() != 0 ){
                $item->appendChild($xml->createElement('SHIPPING_WIDTH', sprintf('%s %s', $productObject->get_width(), $sizeUnit)));
            }

            if( $productObject->get_height() != 0) {
                $item->appendChild($xml->createElement('SHIPPING_HEIGHT', sprintf('%s %s', $productObject->get_height(), $sizeUnit)));
            }

            if ( $productObject->get_weight() != 0 ) {
                $item->appendChild($xml->createElement('SHIPPING_WEIGHT', sprintf('%s %s', $productObject->get_weight(), $weightUnit)));
            }


            $channel->appendChild($item);
        }

        $xml->appendChild($channel);
        return $xml;
    }

    /*******************************************************************************************************************
     * MERGE XML
     *******************************************************************************************************************/

    /**
     * Merge xml files to final file
     *
     * @param $storage
     * @param $tmpShopDir
     * @return bool
     */
    public function mergeXmlFile($storage, $tmpShopDir)
    {
        $logger = wc_get_logger();
        $logger->info('Merging Xml files of product feed.');
        $loop = 0;

        $xmlstr = '<CHANNEL xmlns="http://www.mergado.com/ns/1.10">';

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

        $logger->info('Product feed merged. XML created.');

        return true;
    }

    public function getSaleDateInterval($productObject)
    {

        $from = $productObject->get_date_on_sale_from();
        $to = $productObject->get_date_on_sale_to();

        if (!is_null($from)) {
            $from = $from->format(DateTime::ATOM);
        }

        if (!is_null($to)) {
            $to = $to->format(DateTime::ATOM);
        }

        if (is_null($from) && is_null($to)) {
            return false;
        }

        return implode('/', [$from, $to]);
    }

    /*******************************************************************************************************************
     * FIND
     *******************************************************************************************************************/

    protected function findParams($product, $values, $parent, $parentValues)
    {
        $usedParams = []; // For checking if already added
        $params = [];

        //Default product attributes
        foreach ($values as $attrName => $attrValue) {
            $item = [];

            if ($attrValue instanceof \WC_Product_Attribute) {
                $item['name'] = wc_attribute_label($attrValue->get_name()); //Case sensitive
//                $item['name'] = wc_attribute_taxonomy_slug($attrName); //Lowercase // cant get simple products

                if (substr($attrValue->get_name(), 0, 3) === "pa_") {
                    $item['value'] = $product->get_attribute($attrValue->get_name());
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

            if ($item['value'] !== '') {
                $params[] = $item;
                $usedParams[] = $item['name'];
            }
        }

        //Other parameters/attributes not used in variation
        if ($parentValues) {
            foreach ($parentValues as $attrName => $attrValue) {
                $item = [];

                if ($attrValue instanceof \WC_Product_Attribute) {
                    $item['name'] = wc_attribute_label($attrValue->get_name()); //Case //sensitive
//                    $item['name'] = wc_attribute_taxonomy_slug($attrName); //Lowercase // cant get simple products

                    if (substr($attrValue->get_name(), 0, 3) === "pa_") {
                        $item['value'] = $parent->get_attribute($attrValue->get_name());
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

    protected function findImages($values)
    {
        $images = [];
        $exploded = explode(", ", $values);
        $images['main'] = isset($exploded[0]) ? $exploded[0] : '';
        unset($exploded[0]);
        $images['alt'] = array_values($exploded);
        return $images;
    }

    protected function findCategory($categoryIds)
    {
        $cateroryGroups = explode(',', $categoryIds);
        foreach ($cateroryGroups as $group) {
            $categoryTrees[] = explode(" > ", $group);
        }
        $counts = array_map('count', $categoryTrees);
        $key = array_flip($counts)[max($counts)];

        return implode(' / ', $categoryTrees[$key]);
    }

    public function is_product_type($typeString, $type)
    {
        if (strpos($typeString, $type) !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function getParentImages($parentId)
    {
        $parentProductObject = wc_get_product($parentId);

        $parentImageIds = [];

        $mainImage = $parentProductObject->get_image_id();
        $galleryImages = $parentProductObject->get_gallery_image_ids();

        if ($mainImage !== '') {
            $parentImageIds[] = $mainImage;
        }

        if ($galleryImages != []) {
            $parentImageIds = array_merge($parentImageIds, $galleryImages);
        }

        $parentImageUrls = [];

        if ($parentImageIds !== []) {
            foreach ($parentImageIds as $k => $attachment_id) {
                $parentImageUrls[] = wp_get_attachment_url($attachment_id);
            }
        }

        return $parentImageUrls;
    }

    public function getStockStatus($stock)
    {
        if ($stock === 'backorder') {
            $stock = 'preorder';
        } else if ($stock) {
            $stock = 'in stock';
        } else {
            $stock = 'out of stock';
        }

        return $stock;
    }

    /*******************************************************************************************************************
     * FEED OPTIONS
     *******************************************************************************************************************/

    /**
     * Merge files, create XML and delete temporary files
     */
    private function mergeTemporaryFiles()
    {
        $storage = $this->productFeedOutputDir . 'products_' . $this->token . '.xml';
        $tmpShopDir = $this->productFeedTmpDir;

        $this->logger->info('Merging XML files of product feed.');
        $loop = 0;

        $xmlstr = '<CHANNEL xmlns="http://www.mergado.com/ns/1.10">';

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

        $this->logger->info('Product feed merged. XML created.');

        XMLClass::deleteTemporaryFiles($this->productFeedTmpDir);

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
        XMLClass::resetFeedGenerating(XMLClass::FEED_ITERATORS['PRODUCT'], $this->productFeedTmpDir);
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
        XMLClass::setFeedLocked('product_feed_' . $this->token, $now);
        $this->logger->info('PRODUCT FEED - locking');
    }

    /**
     * Unlock feed
     */
    private function unlockFeed() {
        XMLClass::unlockFeed('product_feed_' . $this->token);
        $this->logger->info('PRODUCT FEED - unlocking');
    }

    /*******************************************************************************************************************
     * FEED PRODUCT COUNT
     *******************************************************************************************************************/

    /**
     * Save the feed count to database for next runs
     */
    private function updateFeedCount()
    {
        return XMLClass::updateFeedCount(XMLClass::FEED_COUNT['PRODUCT'], XMLClass::FEED_ITERATORS['PRODUCT']);
    }

    private function increaseIterator()
    {
        return XMLClass::increaseIterator(XMLClass::FEED_ITERATORS['PRODUCT']);
    }

    /**
     * Return value of product per step
     * @return int
     */
    public function getProductsPerStep()
    {
        $loweredProductsPerStep = $this->getLoweredProductsPerStep();

        return XMLClass::getItemsPerStep(Settings::OPTIMIZATION['PRODUCT_FEED'], $loweredProductsPerStep);
    }

    public function getDefaultProductsPerStep()
    {
        return XMLClass::getDefaultProductsPerStep(XMLClass::DEFAULT_ITEMS_STEP['PRODUCT_FEED']);
    }

    public function setProductsPerStep($value)
    {
        return XMLClass::setItemsPerStep(Settings::OPTIMIZATION['PRODUCT_FEED'], $value);
    }

    /**
     * Return value of lowered product step (repetetive call if 500 error timeout)
     */
    public function getLoweredProductsPerStep()
    {
        return XMLClass::getLoweredProductPerStep(XMLClass::FEED_PRODUCTS_USER['PRODUCT']);
    }

    public function setLoweredProductsPerStepAsMain()
    {
        $productsPerStep = $this->getLoweredProductsPerStep();
        $this->setProductsPerStep($productsPerStep);
        $this->deleteLoweredProductsPerStep();
    }

    public function deleteLoweredProductsPerStep()
    {
        return XMLClass::setLoweredProductsPerStep(XMLClass::FEED_PRODUCTS_USER['PRODUCT'], 0);
    }

    public function lowerProductsPerStep()
    {
        $productsPerStep = $this->getProductsPerStep();

        $response = XMLClass::lowerProductsPerStep(XMLClass::FEED_PRODUCTS_USER['PRODUCT'], $productsPerStep);

        if ($response === false) {
            $this->deleteLoweredProductsPerStep();
        }

        return $response;
    }

    public function setLowerProductsPerStep($value)
    {
        return XMLClass::setLoweredProductsPerStep(XMLClass::FEED_PRODUCTS_USER['PRODUCT'], $value);
    }

    public function setFeedCount($value) {
        return XMLClass::setFeedCount(XMLClass::FEED_ITERATORS['PRODUCT'], $value);
    }

    public function getLastRunFeedCount()
    {
        return XMLClass::getLastRunFeedCount(XMLClass::FEED_COUNT['PRODUCT']);
    }

    /**
     * @return int
     */
    public function getCurrentTempFilesCount()
    {
        $dir = $this->productFeedTmpDir;

        return XMLClass::getCurrentTempFilesCount($dir);
    }

    /**
     * Delete all files from TMP folder
     */
    public function deleteTemporaryFiles()
    {
        XMLClass::deleteTemporaryFiles($this->productFeedTmpDir);
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
        Mergado_Marketing_Pack_Admin::createDir($this->productFeedTmpDir);
        Mergado_Marketing_Pack_Admin::createDir($this->outputBlogDir);
        Mergado_Marketing_Pack_Admin::createDir($this->productFeedOutputDir);
    }

    /*******************************************************************************************************************
     * DATA FOR TEMPLATES
     *******************************************************************************************************************/

    public function getDataForTemplates()
    {
    	$feedName = 'product';
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
            'cronGenerateUrl' =>  get_site_url() . '/mergado/?action=productCron&token=' . $this->token,
//            'wizardUrl' => '/wp-admin/admin.php?page=mergado-feeds-product&mmp-wizard=product&step=1' . '&mmp-tab=product',
            'wizardUrl' => '/wp-admin/admin.php?page=mergado-feeds-product&mmp-wizard=product' . '&mmp-tab=product',
            'deleteUrl' => '/wp-admin/admin.php?page=mergado-config&action=deleteFeed&feed=product&token=' . $this->token . '&mmp-tab=product',
            'downloadUrl' => '/wp-admin/admin.php?page=mergado-config&action=downloadFeed&feed=product&token=' . $this->token . '&mmp-tab=product',
            'generateUrl' => '/wp-admin/admin.php?page=mergado-feeds-product&mmp-wizard=product&step=3&force=true' . '&mmp-tab=product',
            'cronSetUpUrl' => '/wp-admin/admin.php?page=mergado-feeds-product&mmp-wizard=product&step=4a&force=true' . '&mmp-tab=product',
            'createExportInMergadoUrl' => 'https://app.mergado.com/new-project/prefill/?url=' . $feedUrl . '&inputFormat=mergado.cz',
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
            'feed' => 'product',
            'cronAction' => 'productCron',
            'ajaxGenerateAction' => 'ajax_generate_feed',
            'feedListLink' => '/wp-admin/admin.php?page=mergado-feeds-product' . '&mmp-tab=product',
            'wpCronActive' => Settings::CRONS['ACTIVE_PRODUCT_FEED'],
            'wpCronSchedule' => Settings::CRONS['SCHEDULE_PRODUCT_FEED'],
            'wpCronFirst' => Settings::CRONS['START_PRODUCT_FEED'],
            'cronUrl' =>  $this->getCronUrl(),
            'feedUrl' =>  $this->getFeedUrl(),
	        'settingsUrl' => '/wp-admin/admin.php?page=mergado-feeds-product&mmp-tab=settings',
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
	    return file_exists( __MERGADO_XML_DIR__ . $this->currentBlogId . '/' . 'products_' . $this->token . '.xml');
    }

    public function getFeedPercentage()
    {
        $productsPerRun = $this->getProductsPerStep();
        $lastRunIterationCount = $this->getLastRunFeedCount();
        $currentStep = $this->getCurrentTempFilesCount();

        return XMLClass::getFeedPercentage($productsPerRun, $currentStep, $lastRunIterationCount);
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

    public function getFeedUrl()
    {
        return __MERGADO_XML_URL__ . $this->currentBlogId . '/products_' . $this->token . '.xml';
    }

    public function getCronUrl()
    {
        return get_site_url() . '/mergado/?action=productCron&token=' . $this->token;
    }

    public function getFeedPath()
    {
        return realpath(wp_normalize_path(__MERGADO_XML_DIR__ . $this->currentBlogId . '/products_' . $this->token . '.xml'));
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
        return get_option(Settings::CRONS['ACTIVE_PRODUCT_FEED'], 0);
    }

    public function getCronSchedule()
    {
        return get_option(Settings::CRONS['SCHEDULE_PRODUCT_FEED'], 0);
    }

    public function isWizardFinished()
    {
    	return (bool)get_option(Settings::WIZARD['FINISHED_PRODUCT'], 0);
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
        $errors = $alertClass->getFeedErrors('product');
        return in_array(AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION'], $errors);
    }
}
