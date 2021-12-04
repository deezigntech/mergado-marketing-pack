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
use Mergado_Marketing_Pack_Admin;
use SimpleXMLElement;

include_once __MERGADO_DIR__ . 'autoload.php';

class ImportPricesClass
{
    protected $logger;

    /**
     * @var int|string
     */
    public $currentBlogId;

    public $token;

    /**
     * @var string
     */
    private $TMP_DIR_FOLDER;

    const FILE_NAMES = array(
        'MAIN' => 'pricesImport.xml',
        'PROGRESS' => 'progressFile.xml',
    );

    const IMPORT_URL = 'import_product_prices_url';

    public function __construct()
    {
        $this->logger = wc_get_logger();

        $this->currentBlogId = Settings::getCurrentBlogId();
        $this->token = Mergado_Marketing_Pack_Admin::getToken();

        // Temporary folder for saving
        $this->TMP_DIR_FOLDER = __MERGADO_TMP_DIR__ . $this->currentBlogId . '/importPrices/';

        // Create neccessary folders
        $this->createTemporaryFolders();
    }

    /**
     * Download or get data, update product info and save progress XML. Delete or change name of progress XML if not empty.
     * @param $page
     * @param bool $redirect
     * @return bool
     */
    public function importPrices($page, $redirect = false)
    {
        $this->logger->info('-- Mergado import prices started --');
        $result = '';

        try {
            if($data = $this->downloadPrices()) {
                $loop = 1;

                $this->logger->info('Importing products.');

                $itemsToImport = (int) get_option(Settings::IMPORT['COUNT'], 0);
                while((array) $data->ITEM != []) {
                    if (($loop <= $itemsToImport) || $itemsToImport == 0) {
                        $this->updateProduct($data->ITEM);
                        unset($data->ITEM[0]);
                        $this->saveProgressFile($data);
                        $loop++;
                    } else {
                        $result = 'hitTheLimit';
                        break;
                    }
                }

                $this->logger->info('Products imported successfully.');

                if((array) $data->ITEM != []) {
                    unlink($this->TMP_DIR_FOLDER . self::FILE_NAMES['MAIN']);
                    rename($this->TMP_DIR_FOLDER . self::FILE_NAMES['PROGRESS'], $this->TMP_DIR_FOLDER . self::FILE_NAMES['MAIN']);
                } else {
                    unlink($this->TMP_DIR_FOLDER . self::FILE_NAMES['MAIN']);
                    unlink($this->TMP_DIR_FOLDER . self::FILE_NAMES['PROGRESS']);
                }
            }

            $this->logger->info('--- Mergado import prices ended ---');

        } catch (MissingUrlException $ex) {
            return false;
        } catch (\Exception $ex) {
            $this->logger->error('Error importing new product prices from Mergado feed.' . $ex->getMessage());
        }

        if ($redirect) {
            wp_redirect('admin.php?page=' . $page . '&flash=categoryGenerated');
            exit;
        }

        if ($result === 'hitTheLimit') {
            return 'stepGenerated';
        } else {
            return 'finished';
        }
    }


    /**
     * Download Prices or retrieve file from tmp folder
     *
     * @throws \Exception
     * @throws MissingUrlException
     */
    public function downloadPrices()
    {
        $this->logger->info('Downloading mergado prices feed');
        $importPriceUrl = $this->getImportUrl();

        if($importPriceUrl != '') {
            if(ToolsClass::fileGetContents($importPriceUrl)) {
                $feed = ToolsClass::fileGetContents($importPriceUrl);
                $x = new SimpleXMLElement($feed);

                $importFinished = $this->lastImportFinished();

                // File not exists && build dates in files are not same
                if ($importFinished && $this->isNewPriceFile($x->LAST_BUILD_DATE)) {
                    $this->saveTemporaryFile($x);
                    $this->setLastImportDate($x->LAST_BUILD_DATE);

                    return $x;
                // File exists
                } elseif (!$importFinished) {
                    $this->logger->info('Last import not finished. Old file will be used.');
                    $tempFile = $this->getTempFile();
                    $x = new SimpleXMLElement($tempFile);

                    return $x;
                }
            } else {
                $this->logger->error('No data returned');
                throw new MissingUrlException('Missing import prices feed URL');

            }
        } else {
            $this->logger->error('Missing import prices feed URL');
            throw new MissingUrlException('Missing import prices feed URL');
        }

        $this->logger->info('No new prices for import.');
        return false;
    }


    /**
     * Set date of last downlaoded and saved XML
     *
     * @param $date
     * @throws \Exception
     */
    public function setLastImportDate($date)
    {
        try {
            $date = new \DateTime($date);
            update_option(Settings::IMPORT['LAST_UPDATE'], $date->format(NewsClass::DATE_FORMAT));
        } catch (\Exception $e) {
            throw new \Exception('Feed contains incorrect Date format! Import failed.');
        }
    }


    /**
     * Save downloaded Mergado XML
     *
     * @param $data
     * @throws \Exception
     */
    public function saveTemporaryFile($data)
    {
        $filename = $this->TMP_DIR_FOLDER . self::FILE_NAMES['MAIN'];

        if ($this->lastImportFinished()) {
            file_put_contents($filename, $data->asXml());
        } else {
            throw new \Exception('Previous import not finished! File exists.');
        }
    }


    /**
     * Save xml with progress data
     *
     * @param $data
     * @throws \Exception
     */
    public function saveProgressFile($data)
    {
        try {
            $dirFolder = $this->TMP_DIR_FOLDER;
            $filename = $dirFolder . self::FILE_NAMES['PROGRESS'];

            file_put_contents($filename, $data->asXml());
        } catch (\Exception $ex) {
            $this->logger->error('Mergado log: error saving progress file' . $ex);
            throw new \Exception('Error saving progress file');
        }
    }


    /**
     * Return if price file is updated or already imported before
     *
     * @param $date
     * @return bool
     * @throws \Exception
     */
    public function isNewPriceFile($date)
    {
        try {
            $date = new \DateTime($date);
            $dbDate = new \DateTime(get_option(Settings::IMPORT['LAST_UPDATE']), new \DateTimeZone('+00:00'));

            if ($date == $dbDate) {
                return false;
            } else {
                return true;
            }

        } catch (\Exception $ex) {
            $this->logger->error("Mergado DateTime error in isNewPriceFile function.\n" . $ex->getMessage());
            return false;
        }
    }


    /**
     * Returns if last import is finished
     *
     * @return bool
     */
    public function lastImportFinished()
    {
        $dir = $this->TMP_DIR_FOLDER . self::FILE_NAMES['MAIN'];

        return !file_exists($dir);
    }


    /**
     * Get temporary file
     *
     * @return false|string
     * @throws \Exception
     */
    public function getTempFile()
    {
        try {
            return ToolsClass::fileGetContents($this->TMP_DIR_FOLDER . self::FILE_NAMES['MAIN']);
        } catch (\Exception $ex) {
            $this->logger->warning('XML File deleted');
            throw new \Exception('XML File deleted.');
        }
    }

    public function getImportUrl()
    {
        return get_option(self::IMPORT_URL, '');
    }

    public function setImportUrl($url)
    {
        if ($this->getImportUrl() === $url) {
            return true;
        } else {
            return update_option(self::IMPORT_URL, $url);
        }
    }

    public function lowerProductsPerStep()
    {
        $productsPerStep = $this->getProductsPerStep();

        $response = XMLClass::lowerProductsPerStep(XMLClass::FEED_PRODUCTS_USER['IMPORT'], $productsPerStep);

        if ($response === false) {
            $this->deleteLoweredProductsPerStep();
        }

        return $response;
    }


    /**
     * Update product properties by XML data
     *
     * @param $item
     */
    private function updateProduct($item)
    {
        $exploded = explode('-', $item->ITEM_ID);
        if(isset($exploded[1])) {
            $combID = $exploded[1];
        }

        if(isset($combID)) {
            $subID = $combID;
            $product = wc_get_product((int) $subID);
            if($product && $product->exists()) {
                $product->set_regular_price((float)$item->PRICE);
                $product->save();
            }
        } else {
            $id = $item->ITEM_ID;
            $product = wc_get_product((int) $id);
            if($product && $product->exists()) {
                $product->set_regular_price((float) $item->PRICE);
                $product->save();
            }
        }
    }

    /**
     * Return value of lowered product step (repetetive call if 500 error timeout)
     */
    public function getLoweredProductsPerStep()
    {
        return XMLClass::getLoweredProductPerStep(XMLClass::FEED_PRODUCTS_USER['IMPORT']);
    }

    public function setLoweredProductsPerStepAsMain()
    {
        $productsPerStep = $this->getLoweredProductsPerStep();
        $this->setProductsPerStep($productsPerStep);
        $this->deleteLoweredProductsPerStep();
    }

    /**
     * Return value of product per step
     * @return int
     */
    public function getProductsPerStep()
    {
        $loweredProductsPerStep = $this->getLoweredProductsPerStep();

        return XMLClass::getItemsPerStep(Settings::OPTIMIZATION['IMPORT_FEED'], $loweredProductsPerStep);
    }

    public function setProductsPerStep($value)
    {
        return XMLClass::setItemsPerStep(Settings::OPTIMIZATION['IMPORT_FEED'], $value);
    }

    public function deleteLoweredProductsPerStep()
    {
        return XMLClass::setLoweredProductsPerStep(XMLClass::FEED_PRODUCTS_USER['IMPORT'], 0);
    }

    /**
     * Create neccessary folders for importPrices
     */
    private function createTemporaryFolders()
    {
        Mergado_Marketing_Pack_Admin::createDir(__MERGADO_TMP_DIR__);
        Mergado_Marketing_Pack_Admin::createDir(__MERGADO_TMP_DIR__ . $this->currentBlogId);
        Mergado_Marketing_Pack_Admin::createDir($this->TMP_DIR_FOLDER);
    }

    public function getWizardData()
    {

        return [
            'token' => $this->token,
            'feed' => 'import',
            'cronAction' => 'importPrices',
            'ajaxGenerateAction' => 'ajax_generate_feed',
            'wpCronActive' => Settings::CRONS['ACTIVE_IMPORT_FEED'],
            'wpCronSchedule' => Settings::CRONS['SCHEDULE_IMPORT_FEED'],
            'wpCronFirst' => Settings::CRONS['START_IMPORT_FEED'],
            'cronUrl' =>  $this->getCronUrl(),
            'importUrl' => $this->getImportUrl(),
            'productsPerStep' => $this->getProductsPerStep(),
            'settingsUrl' => '/wp-admin/admin.php?page=mergado-feeds-other&mmp-tab=settings',
            'percentage' => 'percentageToBeFilledFromSimpleXMl count'
        ];
    }

    public function getCronUrl()
    {
        return get_site_url() . '/mergado/?action=importPrices&token=' . $this->token;
    }

    public function isWpCronActive()
    {
        return get_option(Settings::CRONS['ACTIVE_IMPORT_FEED'], 0);
    }

    public function getCronSchedule()
    {
        return get_option(Settings::CRONS['SCHEDULE_IMPORT_FEED'], 0);
    }
}

/**
 * Thrown when an service returns an exception
 */
class MissingUrlException extends Exception
{
};