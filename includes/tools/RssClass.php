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

use DateTime;
use Exception;
use Mergado;
use SimpleXMLElement;


include_once WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';
include_once __MERGADO_DIR__ . 'autoload.php';

class RssClass
{
    const FEED_URLS = array(
        'en' => 'https://feeds.mergado.com/woo-en-4ef08984c05b3f6d9022f61f1cdedbfb.xml',
        'cs' => 'https://feeds.mergado.com/woo-cs-141299e041da8ca20e4378a5336e061b.xml',
        'sk' => 'https://feeds.mergado.com/woo-sk-a801ef028d651335df17fc8e463e5847.xml',
        'pl' => 'https://feeds.mergado.com/woo-pl-04dd4548ddc67a3892f3f148528b26f6.xml'
    );

    const LAST_RSS_FEED_UPDATE = 'mergado_last_rss_feed_download';
    const RSS_FEED_LOCK = 'unfinished_rss_downloads';

    protected $logger;

    public function __construct()
    {
        //Don't add $this->logger = wc_get_logger() .. Jetpack will shout on you
    }

    public function getFeed()
    {
        $now = new DateTime();
        $date = $now->format(NewsClass::DATE_FORMAT);

        try {

            $lastDownload = $this->getLastDownload();

            if ($lastDownload && $lastDownload !== '') {
                $dateTime = new DateTime($lastDownload);
                // Check every half day from last check

                if ($this->getDownloadLock() < count(self::FEED_URLS) * 3) {
                    $dateFormatted = $dateTime->modify('+5 minutes')->format(NewsClass::DATE_FORMAT);
                } else {
                    $dateFormatted = $dateTime->modify('+30 minutes')->format(NewsClass::DATE_FORMAT);
                }

                if ($dateFormatted <= $date) {
                    foreach (self::FEED_URLS as $item_lang => $val) {
                        $this->saveFeed($item_lang);
                    }

                    $this->nullDownloadLock();
                    $this->setLastDownload($date);
                }
            } else {
                foreach(self::FEED_URLS as $item_lang => $val) {
                    $this->saveFeed($item_lang);
                }

                $this->nullDownloadLock();
                $this->setLastDownload($date);
            }
        } catch (Exception $e) {
            wc_get_logger()->warning('Error getting RSS feed. - ' . $e->getMessage());
            $this->increaseDownloadLock();
            $this->setLastDownload($date);
        }
    }

    /**
     * Save new RSS feed articles to database
     *
     * @param $lang
     * @return void
     */
    private function saveFeed($lang)
    {
        try {
            $dbQuery = NewsClass::getNews($lang);
            $rssFeed = $this->downloadFeed($lang);

            foreach ($rssFeed as $item) {

                // Transform keys to lowercase
                $itemAr = (array)$item;
                $item = array_change_key_case($itemAr, CASE_LOWER);

                $itemDatetime = new DateTime((string)$item['pubdate']);
                $save = true;

                if (count($dbQuery) > 0) {
                    foreach ($dbQuery as $dbItem) {

                        // Fix different APIs ( one with time and second only date ) => Compare only based on date and title
                        $dbTime = new DateTime($dbItem->pubDate);
                        $dbTime = $dbTime->format(NewsClass::DATE_COMPARE_FORMAT);

                        if ($itemDatetime->format(NewsClass::DATE_COMPARE_FORMAT) === $dbTime && (string)$item['title'] === $dbItem->title) {
                            $save = false;
                            break;
                        }
                    }
                }

                if ($save) {
                    NewsClass::saveArticle($item, $itemDatetime, $lang);
                }
            }
        } catch (\ParseError $e) {
            wc_get_logger()->warning('Error parsing RSS feed. - ' . $e->getMessage());
        } catch (Exception $e) {
            wc_get_logger()->warning('Error getting RSS feed. - ' . $e->getMessage());
        }
    }

    /**
     * Download RSS feed
     *
     * @param $lang
     * @return array
     */
    private function downloadFeed($lang)
    {
        $lang = NewsClass::getMergadoNewsLanguage($lang);

        $feed = ToolsClass::fileGetContents(self::FEED_URLS[$lang]);

        try {
            $x = new SimpleXMLElement($feed, LIBXML_NOERROR);

            $data = array();
            foreach ($x->channel->item as $item) {
                $data[] = $item;
            }
            return $data;

        } catch (Exception $e) {
            throw new \ParseError($e);
        }
    }

    /**
     * Get last download based
     *
     */
    private function getLastDownload()
    {
        return get_option(self::LAST_RSS_FEED_UPDATE, '');
    }

    /**
     * Set last download based
     *
     * @param $now
     */
    private function setLastDownload($now)
    {
        update_option(self::LAST_RSS_FEED_UPDATE, $now);
    }

    /**
     * Set lock for few minutes, if feed is broken
     */
    private function increaseDownloadLock()
    {
        $value = $this->getDownloadLock();
        update_option(self::RSS_FEED_LOCK, $value + 1);
    }

    /**
     * Set download lock to null
     */
    private function nullDownloadLock()
    {
        update_option(self::RSS_FEED_LOCK, 0);
    }

    /**
     * Return current downlaod lock number
     * @return false|string|null
     */
    private function getDownloadLock()
    {
        return get_option(self::RSS_FEED_LOCK, '');
    }
}
