<?php
include_once __MERGADO_DIR__ . 'autoload.php';

use Mergado\Tools\ImportPricesClass;
use Mergado\Tools\Settings;

/**
 * Product feed form settings
 */


if (isset($_POST['submit-settings-form'])) {
    //Feed optimization
    Settings::saveOptions($_POST, [], [Settings::OPTIMIZATION['STOCK_FEED']]);
    Settings::saveOptions($_POST, [], [Settings::OPTIMIZATION['CATEGORY_FEED']]);

    //Import settings
    Settings::saveOptions($_POST, [], [Settings::OPTIMIZATION['IMPORT_FEED']]);
    Settings::saveOptions($_POST, [], [ImportPricesClass::IMPORT_URL]);

	// WP CRON FORCED options - TAKE CARE ON TWO LOCATIONS !!
	Settings::saveOptions($_POST, [Settings::WP_CRON_FORCED]);

    //Remove wizard params from URL
    wp_redirect('admin.php?page=' . $_GET['page'] . '&mmp-tab=' . $_GET['mmp-tab'] . '&flash=settingsSaved');
}
?>


<div class="card full">
    <form method="post" id="settings-form" action="">
        <h1 class="mmp_h1"><?= __('Global settings for all other feeds', 'mergado-marketing-pack') ?></h1>

        <?php
            $alertData = ['alertSection' => 'other', 'feedName' => 'other'];

            include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/settingsInfo.php';
        ?>

        <div class="mmp_advancedSettingsBox">
            <div class="mmp_advancedSettingsBox__header">
                <p><?php _e('Advanced settings', 'mergado-marketing-pack') ?></p>
                <div class="mmp_advancedSettingsBox__toggler">
                    <svg>
                        <use xlink:href="<?= plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#chevron-down' ?>"></use>
                    </svg>
                </div>
            </div>
            <div class="mmp_advancedSettingsBox__content">

                <div class="mmp_settingsBox">
                    <div class="mmp_settingsBox__top">
			            <?= __('Batch export Category feeds', 'mergado-marketing-pack') ?>
                    </div>

                    <table>
                        <tbody>
                        <tr>
                            <th>
                                <label for="feed-form-category"
                                       data-tippy-content="<?= __('Changing the batch size could seriously effect the performance of your website. We advice against changing the batch size if you are unsure about its effects!<br><br>Default number is set to 3000 items per batch step.', 'mergado-marketing-pack') ?>">
						            <?php _e('Change the number of products per batch (Change only if advised by our support team)', 'mergado-marketing-pack') ?>
                                </label>
                            </th>
                            <td class="text-align: left;"><input type="number" min="1" id="feed-form-category" name="feed-form-category"
                                                                 placeholder="<?php _e('Insert number of products', 'mergado-marketing-pack') ?>"
                                                                 value="<?php echo get_option('feed-form-category', ''); ?>">
                                <br>
                                <small><?php _e('Leave blank to generate the entire XML feed at once.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mmp_settingsBox">
                    <div class="mmp_settingsBox__top">
			            <?= __('Batch export Heureka Availability feeds', 'mergado-marketing-pack') ?>
                    </div>
                    <table>
                        <tbody>
                        <tr>
                            <th>
                                <label for="feed-form-stock"
                                       data-tippy-content="<?= __('Changing the batch size could seriously effect the performance of your website. We advice against changing the batch size if you are unsure about its effects!<br><br>Default number is set to 5000 items per batch step.', 'mergado-marketing-pack') ?>">
						            <?php _e('Change the number of products per batch (Change only if advised by our support team)', 'mergado-marketing-pack') ?>
                                </label>
                            </th>
                            <td class="text-align: left;"><input type="number" min="1" id="feed-form-stock" name="feed-form-stock"
                                                                 placeholder="<?php _e('Insert number of products', 'mergado-marketing-pack') ?>"
                                                                 value="<?php echo get_option('feed-form-stock', ''); ?>">
                                <br>
                                <small><?php _e('Leave blank to generate the entire XML feed at once.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mmp_settingsBox">
                    <div class="mmp_settingsBox__top">
			            <?= __('Batch Import prices feeds', 'mergado-marketing-pack') ?>
                    </div>
                    <table>
                        <tbody>
                        <tr>
                            <th>
                                <label for="import-form-products"
                                       data-tippy-content="<?= __('Changing the batch size could seriously effect the performance of your website. We advice against changing the batch size if you are unsure about its effects!<br><br>Default number is set to 3000 items per batch step.', 'mergado-marketing-pack') ?>">
						            <?php _e('Change the number of products per batch (Change only if advised by our support team)', 'mergado-marketing-pack') ?>
                                </label>
                            </th>
                            <td class="text-align: left;">
                                <input type="number" min="1" id="import-form-products" name="import-form-products"
                                       placeholder="<?php _e('Insert number of steps', 'mergado-marketing-pack') ?>"
                                       value="<?php echo get_option('import-form-products', ''); ?>">
                                <br>
                                <small><?php _e('Leave blank to generate the entire XML feed at once.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mmp_settingsBox">
                    <table>
                        <tbody>
                        <tr>
                            <th>
                                <label for="<?= Settings::WP_CRON_FORCED ?>"
                                       data-tippy-content="<?= __('Checking this checkbox will unlock the WP cron wizard even with:<br><br> <strong>DISABLED_WP_CRON = true in wp-config.php</strong><br><br> This feature is intended to be used with externaly enabled WP CRON script.', 'mergado-marketing-pack') ?>"
                                ><?php _e('Unlock WP cron forms', 'mergado-marketing-pack') ?></label>
                            </th>
                            <td class="text-align: left;">
                                <input type="checkbox"
                                       id="<?= Settings::WP_CRON_FORCED ?>"
                                       name="<?= Settings::WP_CRON_FORCED ?>"
							           <?php if (get_option(Settings::WP_CRON_FORCED, 0) == 1){ ?>checked="checked"<?php } ?>>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="mmp_btnHolder mmp_btnHolder--right">
            <input type="submit" class="button mmp_btn__blue mmp_btn--wide"
                   value="<?php _e('Save', 'mergado-marketing-pack') ?>" name="submit-settings-form">
        </div>
    </form>
</div>