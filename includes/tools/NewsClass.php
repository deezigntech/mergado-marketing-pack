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
use Mergado;
use Mergado_Marketing_Pack;

class NewsClass
{
    const DATE_FORMAT = 'Y-m-d H:i:s';
    const DATE_COMPARE_FORMAT = 'Y-m-d';
    const DATE_OUTPUT_FORMAT = 'd.m.Y';

    /*******************************************************************************************************************
     * GET
     *******************************************************************************************************************/

    /**
     * Return news from DB by lang and limit (ifset)
     *
     * @param $lang
     * @param null $limit
     * @return array|false|\mysqli_result|\PDOStatement|resource|null
     */
    public static function getNews($lang, $limit = null)
    {
        global $wpdb;

        $query = self::getNewsBase($lang, $limit);

        return $wpdb->get_results($query, OBJECT);
    }

    /**
     * Return news with formated date
     *
     * @param $lang
     * @param null $limit
     * @return array|false|\mysqli_result|\PDOStatement|resource|null
     * @throws \Exception
     */
    public static function getNewsWithFormatedDate($lang, $limit = null)
    {
        global $wpdb;

        $query = self::getNewsBase($lang, $limit);
        $return = $wpdb->get_results($query, OBJECT);

        foreach($return as $item => $val) {
            $date = new DateTime($return[$item]->pubDate);
            $formatted = $date->format('d.m.Y H:m:s');

            $return[$item]->pubDate = $formatted;
        }

        return $return;
    }


    /**
     * Base query for returning news
     *
     * @param $lang
     * @param null $limit
     * @return string
     */
    private static function getNewsBase($lang, $limit = null)
    {
        global $wpdb;

        $lang = self::getMergadoNewsLanguage($lang);

        $query = 'SELECT * FROM ';
        $query .= $wpdb->prefix . Mergado_Marketing_Pack::TABLE_NEWS_NAME;
        $query .= ' WHERE `language`="' . $lang . '"';
        $query .= ' ORDER BY `id` DESC';

        if($limit) {
            $query .= ' LIMIT ' . $limit;
        }

        return $query;
    }


    /**
     * Return shown/new news from DB
     *
     * @param $shown
     * @param $lang
     * @param $category
     * @param null $limit
     * @param bool $excludeTop
     * @param bool $order
     * @return array|false|\mysqli_result|\PDOStatement|resource|null
     */
    public static function getNewsByStatusAndLanguageAndCategory($shown, $lang, $category = null, $limit = null, $excludeTop = false, $order = false)
    {
        global $wpdb;

        $lang = self::getMergadoNewsLanguage($lang);

        $query = 'SELECT * FROM ';
        $query .= $wpdb->prefix . Mergado_Marketing_Pack::TABLE_NEWS_NAME;
        $query .= ' WHERE `language`="' . $lang . '"';


        if($shown) {
            $query .= ' AND `shown`="' . 1 . '"';
        } else {
            $query .= ' AND `shown`="' . 0 . '"';
        }

        if (($category || $category != '') && $category !== null) {
            $query .= ' AND `category`="' . $category . '"';
        }

        if ($excludeTop) {
            $query .= ' AND `category`!="top"';
        }

        if($order) {
            $query .= ' ORDER BY `pubDate` ' . $order . '';
        } else {
            $query .= ' ORDER BY `pubDate`';
        }

        if($limit) {
            $query .= ' LIMIT '. $limit;
        }

        return $wpdb->get_results($query, OBJECT);
    }

    /**
     * @param $lang
     * @return string|string
     */
    public static function getMergadoNewsLanguage($lang)
    {
        $lang = substr($lang, 0, 2);

        // Set default English news if language not available
        if(!in_array($lang, Mergado_Marketing_Pack::LANG_AVAILABLE)) {
            $lang = Mergado_Marketing_Pack::LANG_EN;
        }

        return $lang;
    }

    public static function getFormattedDate($date)
    {
        $date = new DateTime($date);
        $date = $date->format(NewsClass::DATE_OUTPUT_FORMAT);
        return $date;
    }

    /*******************************************************************************************************************
     * SET
     *******************************************************************************************************************/

    /**
     * Save article to DB
     *
     * @param array $item
     * @param DateTime $date
     * @param $lang
     */
    public static function saveArticle(array $item, DateTime $date, $lang)
    {
        global $wpdb;

        $lang = self::getMergadoNewsLanguage($lang);

        $data = [
            'title' => (string) $item['title'],
            // Preg replace not worked .. others solutions either .. so .. sorry code ..
            'description' => (string) str_replace(']]>','', str_replace('<![CDATA[', '', $item['description'])),
            'pubDate' => $date->format(self::DATE_FORMAT),
            'category' => (string) $item['category'],
            'language' => $lang,
            'shown' => 0];

        $wpdb->insert($wpdb->prefix . Mergado_Marketing_Pack::TABLE_NEWS_NAME, $data);
    }

    /**
     * Set Article shown by user
     *
     * @param array|null $ids
     */
    public static function setArticlesShown(array $ids = null, $all = false)
    {
        global $wpdb;

        if($all) {
                $wpdb->update($wpdb->prefix . Mergado_Marketing_Pack::TABLE_NEWS_NAME, ['shown' => 1], ['shown' => 0]);
        } elseif($ids) {
            foreach($ids as $id) {
                $wpdb->update($wpdb->prefix . Mergado_Marketing_Pack::TABLE_NEWS_NAME, ['shown' => 1], ['id' => $id]);
            }
        }

    }
}
