<?php
include_once __MERGADO_DIR__ . 'autoload.php';

use Mergado\Tools\Crons;
use Mergado\Tools\Settings;

?>

<?php if (@$wizardType === $wizardData['feed'] && $wizardStep === '4a'): ?>
    <div class="mmp_wizard active" data-mmp-wizard-step="4a" data-mmp-wizard-type="<?= $wizardData['feed'] ?>">
<?php else: ?>
    <div class="mmp_wizard" data-mmp-wizard-step="4a" data-mmp-wizard-type="<?= $wizardData['feed'] ?>">
<?php endif ?>

    <div class="card full">
        <div class="mmp_wizard__content">
            <h1 class="mmp_wizard__heading"><?= __('Set up refresh interval - CRON', 'mergado-marketing-pack'); ?></h1>

            <?php
                $alertData = ['alertSection' => $wizardData['feedSection'], 'feedName' => $wizardData['feed']];

                include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/cronInfo.php';
            ?>

            <div class="mmp_wizard__content_body">

	            <?php
                    if ( !defined( 'DISABLE_WP_CRON' ) || DISABLE_WP_CRON == false || get_option(Settings::WP_CRON_FORCED, 0) == 1):
                ?>

                    <div class="mmp_wizard__content_heading">
                        <?= __('Set up a feed refresh using the WordPress cron task scheduler.', 'mergado-marketing-pack'); ?>
                    </div>

                    <form class="mmp_wizard__wp_cron">
                        <div>
                            <div class="mmp_wizard__wp_cron_heading">
                                <svg>
                                    <use xlink:href="<?= plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg') . 'mmp_icons.svg#wp-logo' ?>"></use>
                                </svg>
                                <h3>
                                    <?= __('Activate WP cron', 'mergado-marketing-pack') ?></h3></div>
                            <div>
                                <input type="checkbox" id="<?= $wizardData['wpCronActive'] ?>" name="<?= $wizardData['wpCronActive'] ?>"
                                       <?php if (get_option($wizardData['wpCronActive'], 0) == 1){ ?>checked="checked"<?php } ?>
                                >
                            </div>
                        </div>
                        <div>
                            <div class="mmp_wizard__wp_cron_heading">
                                <svg>
                                    <use xlink:href="<?= plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#refresh' ?>"></use>
                                </svg>
                                <h3><?php _e('Cron schedule', 'mergado-marketing-pack') ?></h3>
                            </div>
                            <div class="mmp_cron__feed_schedule">
                                <select class="wp-schedule-input" name="<?= $wizardData['wpCronSchedule'] ?>" id="<?= $wizardData['wpCronSchedule'] ?>" data-mmp-wizard-type="<?= $wizardData['feed']?>">

                                    <?php foreach(Crons::getScheduleTasks() as $key => $item) : ?>

                                        <option value="<?php echo $key ?>"
                                            <?php if($key === get_option($wizardData['wpCronSchedule'], 0)): ?> selected <?php endif ?>
                                        >
                                            <?php echo $item ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <span class="mmp_cron__feed_estimate">
                                    <?= __('The entire feed will be generated in approximately ', 'mergado-marketing-pack') . '<strong data-pps-output=""></strong>' .  '.'?>
                                </span>
                            </div>
                        </div>
<!--                        <div>-->
<!--                            <div class="mmp_wizard__wp_cron_heading">-->
<!--                                <svg  style="height: 21px;">-->
<!--                                    <use xlink:href="--><?//= plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#in-progress' ?><!--"></use>-->
<!--                                </svg>-->
<!--                                <h3>--><?php //_e('First start', 'mergado-marketing-pack') ?><!--</h3></div>-->
<!--                            <div><input type="time" name="--><?//= $wizardData['wpCronFirst'] ?><!--" id="--><?//= $wizardData['wpCronFirst'] ?><!--" value="--><?//= get_option($wizardData['wpCronFirst'], 0) ?><!--" pattern="(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]" title='"XX:XX"'/></div>-->
<!--                        </div>-->
                        <input type="hidden" name="token" id="token" value="<?= $wizardData['token'] ?>" />
                        <input type="hidden" name="feed" id="feed" value="<?= $wizardData['feed']  ?>" />
                        <input type="hidden" name="action" id="action" value="ajax_save_wp_cron" />
                    </form>


                    <div class="mmp_wizard__divider">
                        <?php _e('OR', 'mergado-marketing-pack'); ?>
                    </div>
                    <div class="mmp_wizard__bottom">
                        <a href="javascript:void(0);" class="mmp_btn__white mmp_btn__white--lowercase"
                           data-mmp-wizard-go="4b"><?php _e('Set up an external cron service. Click here.', 'mergado-marketing-pack'); ?></a>
                    </div>


                <?php else: ?>
                    <div class="mmp_wizard__wpCronDisabled">
                        <h3 class="mmp_wizard__wpCronDisabled_title">
                            <svg>
                                <use xlink:href="<?= plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg') . 'mmp_icons.svg#wp-logo' ?>"></use>
                            </svg>

                            <?= __('Your wp cron (wp-cron.php) is disabled.', 'mergado-marketing-pack'); ?></h3>
                        <p class="mmp_wizard__wpCronDisabled_text"><?= __('You must use an external cron service or enable<br> the WP cron function in the wp-config.php file.', 'mergado-marketing-pack') ?><br>
                        <a href="<?= $wizardData['settingsUrl'] ?>"><small>For developers: If you use external service to start WP CRON, click here to activate</small></a></p>
                        <hr>
                        <div class="mmp_wizard__wpCronDisabled_link">
                            <a href="javascript:void(0);" class="mmp_btn__white mmp_btn__white--lowercase"
                               data-mmp-wizard-go="4b"><?php _e('Set up an external cron service. Click here.', 'mergado-marketing-pack'); ?></a>
                        </div>

                    </div>
	            <?php endif; ?>
            </div>

            <?php if($wizardForced): ?>
                <div class="mmp_wizard__buttons mmp_justify_end">
                    <a href="javascript:void(0);" class="mmp_btn__blue"
                       data-mmp-wizard-go="4a" data-mmp-wizard-do-before="mmpSaveWpCronAndGo" data-go-to-link="<?= $wizardData['feedListLink'] ?>"><?php _e('Save and go to list of feeds', 'mergado-marketing-pack'); ?></a>
                </div>
            <?php else: ?>
                <div class="mmp_wizard__buttons mmp_justify_end">
<!--                    <a href="javascript:void(0);" class="mmp_btn__white"-->
<!--                       data-mmp-wizard-go="3" data-mmp-wizard-do-before="mmpStartWizardGenerating">--><?php //_e('Back', 'mergado-marketing-pack'); ?><!--</a>-->
                    <a href="javascript:void(0);" class="mmp_btn__blue"
                       data-mmp-wizard-go="4a" data-mmp-wizard-do-before="mmpSaveWpCronAndGo" data-go-to-link="<?= $wizardData['feedListLink'] ?>"><?php _e('Save and go to list of feeds', 'mergado-marketing-pack'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
