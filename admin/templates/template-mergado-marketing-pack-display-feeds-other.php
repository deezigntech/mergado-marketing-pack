<?php

use Mergado\Tools\BannersClass;

include_once('partials/template-mergado-marketing-pack-header.php');

$tabsSettings = [
    'tabs' => [
        'category' => ['title' => __('Category feeds', 'mergado-marketing-pack'), 'active' => true, 'icon' => 'icons.svg#list'],
        'stock' => ['title' => __('Heureka<br>Availability feed', 'mergado-marketing-pack'), 'icon' => 'icons.svg#service-heureka'],
        'import' => ['title' => __('Import prices', 'mergado-marketing-pack'), 'icon' => 'icons.svg#import'],
        'settings' => ['title' => '', 'icon' => 'icons.svg#settings'],
    ],
    'tabContentPath' => wp_normalize_path(__DIR__ . '/partials/tabs-other-feeds/')
];
?>

<div class="wrap">
    <div class="rowmer">
        <div class="col-content">
            <?php
            include_once(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'notices/notices.php');
            include_once(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'tabs/tabs.php')
            ?>
        </div>
        <div class="col-side">
            <?= BannersClass::getSidebar() ?>
        </div>
    </div>
    <div class="merwide">
        <?= BannersClass::getWide() ?>
    </div>
</div>