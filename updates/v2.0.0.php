<?php

global $wpdb;

$table = $wpdb->prefix . 'mergado_news';

$query = "CREATE TABLE IF NOT EXISTS `" . $table . "` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `title` varchar(255) NOT NULL,
                  `description` text NOT NULL,
                  `category` varchar(255) NOT NULL,
                  `language` text NOT NULL,
                  `pubDate` datetime DEFAULT NULL,
                  `shown` tinyint(1) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$wpdb->query($query);