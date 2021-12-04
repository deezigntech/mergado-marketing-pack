<?php

use Mergado\Tools\BannersClass;
use Mergado\Tools\NewsClass;

$news = NewsClass::getNewsWithFormatedDate(\Mergado\Tools\Languages::getLocale(), 15);

if (isset($_GET) && $_GET) {
    NewsClass::setArticlesShown(null, true);
}

include_once( 'partials/template-mergado-marketing-pack-header.php' ); ?>

<div class="wrap">
    <div class="card full">
        <h2><?php _e('Mergado news', 'mergado-marketing-pack') ?></h2>
    </div>
    <div class="rowmer">
        <div class="col-content">
            <?php
            foreach ($news as $item) { ?>
                <div class="card full <?= $item->category ?>">
                    <div class="card-header">
                        <h3><?php echo $item->title ?></h3>
                        <p><?php echo $item->pubDate ?></p>
                    </div>
                    <div class="mergado-pb-10"><?php echo $item->description ?></div>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="col-side col-side-extra">
            <?= BannersClass::getSidebar() ?>
        </div>
    </div>
    <div class="merwide">
        <?= BannersClass::getWide() ?>
    </div>
</div>