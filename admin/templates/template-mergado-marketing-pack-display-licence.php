<?php

use Mergado\Tools\BannersClass;

include_once( 'partials/template-mergado-marketing-pack-header.php' ); ?>

<div class="wrap">
    <div class="rowmer">
        <div class="col-content">
            <div class="card full">
                <h2><?php _e('Licence', 'mergado-marketing-pack'); ?></h2>
                <p>
                    <?php _e('<strong>Using the module Mergado pack is at your own risk.</strong> The creator of module, the company Mergado technologies, LLC, is not liable for any losses or damages in any form. Installing the module into your store, you agree to these terms.', 'mergado-marketing-pack'); ?>
                </p>

                <p>
                    <?php _e('The module source code cannot be changed and modified otherwise than the user settings in the administration.', 'mergado-marketing-pack'); ?>
                </p>

                <p>
                    <?php _e('Using the module Mergado pack within Wordpress & Woocommerce is free. Supported versions of Wordpress are starting 4.5.0 above and Woocommerce 3.1 above.', 'mergado-marketing-pack'); ?>
                </p>
            </div>
        </div>
        <div class="col-side col-side-extra">
            <?= BannersClass::getSidebar() ?>
        </div>
    </div>
    <div class="merwide">
        <?= BannersClass::getWide() ?>
    </div>
</div>
