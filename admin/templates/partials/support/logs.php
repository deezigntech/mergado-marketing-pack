<?php

use Mergado\Tools\Settings;

$settingsData = Settings::getInformationsForSupport();
$settingsDataJson = json_encode($settingsData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>

<h2><?php _e('Logs', 'mergado-marketing-pack'); ?></h2>
<p>
    <?php _e('Every action is logged into WooCommerce logs. In case of a problem, please send us the log report from the date and time the issue occurred.', 'mergado-marketing-pack'); ?>
</p>

<p>
    <?php _e('You can also delete all logs on WooCommerce logs page. These logs are only accessible by users with admin access rights.', 'mergado-marketing-pack'); ?>
</p>

<h4><?php _e('Include when contacting support:', 'mergado-marketing-pack'); ?></h4>
<table class="wp-list-table widefat striped">
    <thead>
        <tr>
            <th><?php _e('Report item', 'mergado-marketing-pack'); ?></th>
            <th><?php _e('Value', 'mergado-marketing-pack'); ?></th>
        </tr>
    </thead>

    <tbody>
    <?php foreach($settingsData['base'] as $item): ?>
        <tr>
            <td><?= $item['name'] ?></td>
            <td><?= $item['value'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="mmp_logs__buttons">
    <p>
        <a href="/wp-admin/admin.php?page=wc-status&tab=logs" class="button button-primary" title="<?php _e('Show WooCommerce logs', 'mergado-marketing-pack'); ?>"><?php _e('Show Woocommerce logs', 'mergado-marketing-pack'); ?></a>
    </p>

    <a class="mmp_feedBox__button mmp_btn__blue mmp_btn__blue--small mmp_feedBox__copy priceImport__copy"
       data-copy-stash="<?= htmlspecialchars($settingsDataJson) ?>"
       href="javascript:void(0);">

        <svg class="mmp_icon">
            <use xlink:href="<?= plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#copy' ?>"></use>
        </svg>
        <?= __('Copy cron URL') ?>
    </a>
</div>
