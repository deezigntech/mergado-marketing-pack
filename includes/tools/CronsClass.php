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

class Crons
{
    const CRON_NAMES = [
        'wp-cron-product-feed-hook',
        'wp-cron-stock-feed-hook',
        'wp-cron-category-feed-hook',
        'wp-cron-import-feed-hook',
    ];

    /*******************************************************************************************************************
     * ADD SCHEDULE TASKS
     *******************************************************************************************************************/

    public static function addTask($task, $schedule, $start)
    {
        $hook = $task . '-hook';

        if (wp_next_scheduled($hook)) {
            // First remove old task if exist
            Crons::removeTask($task);
        }

        if($start !== '') {
            $time = date_create($start)->getTimestamp();
        } else {
            $time = strtotime('+30 minutes');
        }

        wp_schedule_event( $time, $schedule, $hook);
    }

    public static function addAllTasks()
    {
        foreach(self::CRON_NAMES as $hook) {
            $name = explode('-hook', $hook)[0];

            $start = get_option($name . '-start');
            $schedule = get_option($name . '-schedule');

            if($start !== '') {
                $time = date_create($start . 'GMT+1')->getTimestamp();
            } else {
                $time = time();
            }

            wp_schedule_event( $time, $schedule, $hook);
        }
    }

    /*******************************************************************************************************************
     * REMOVE TASKS
     *******************************************************************************************************************/

    public static function removeTask($task)
    {
        wp_clear_scheduled_hook($task . '-hook');
    }

    public static function removeAllTasks()
    {
        foreach(self::CRON_NAMES as $item) {
            wp_clear_scheduled_hook($item);
        }
    }

    public static function getScheduleTasks()
    {
        return [
            'quarterhour' => __('Every 15 minutes', 'mergado-marketing-pack'),
            'hourly' => __('Every hour', 'mergado-marketing-pack'),
            'twicedaily' => __('Twice a day', 'mergado-marketing-pack'),
            'daily' => __('Daily', 'mergado-marketing-pack')
        ];
    }

    public static function getScheduleInSeconds($schedule)
    {
    	if ($schedule === 'quarterhour') {
    		return 900;
	    } else if ($schedule === 'hourly') {
			return 3600;
	    } else if ($schedule === 'twicedaily') {
    		return 43200;
	    } else if ($schedule === 'daily') {
    		return 86400;
	    }
    }

    public static function getTaskByVariable($task)
    {
    	if ($task !== 0) {
	        return self::getScheduleTasks()[$task];
	    } else {
    		return '--';
	    }
    }
}
