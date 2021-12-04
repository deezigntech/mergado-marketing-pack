<?php
   if(($isAlreadyFinished && !$wizardStep) || ($wizardStep == 3 && $wizardForced)):
?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
          var $ = jQuery;

          if ($('[data-mmp-tab-button="<?= $wizardData['feed'] ?>"]').closest('li').hasClass('active')) {
              window.mmpWizard.startFeedGenerating('<?= $wizardData['feed'] ?>');
          }
        });
    </script>
<?php
    endif;
?>

<?php if ((@$wizardType === $wizardData['feed'] && $wizardStep === '3') || ($isAlreadyFinished && !$wizardStep)): ?>
<div class="mmp_wizard active" data-mmp-wizard-step="3" data-mmp-wizard-type="<?= $wizardData['feed'] ?>">
    <?php else: ?>
    <div class="mmp_wizard" data-mmp-wizard-step="3" data-mmp-wizard-type="<?= $wizardData['feed'] ?>">
        <?php endif ?>
        <div class="card full">
            <div class="mmp_wizard__content">

                <?php if ($wizardForced): ?>
                    <h1 class="mmp_wizard__heading" data-feed-finished="false"><?= sprintf(__('Wait until your %s feed is created', 'mergado-marketing-pack'), $wizardName); ?></h1>
                    <h1 class="mmp_wizard__heading" data-feed-finished="true"><?= sprintf(__('Your %s feed is ready', 'mergado-marketing-pack'), $wizardName); ?></h1>
                <?php else: ?>
                    <h1 class="mmp_wizard__heading" data-feed-finished="false"><?= sprintf(__('Wait until your first %s feed is created', 'mergado-marketing-pack'), $wizardName); ?></h1>
                    <h1 class="mmp_wizard__heading" data-feed-finished="true"><?= sprintf(__('Your first %s feed is ready. Now please continue to the Cron settings to set up automatic feed updates.', 'mergado-marketing-pack'), $wizardName); ?></h1>
                <?php endif; ?>

                <div data-feed-finished="false">
                    <?php
                    $alertData = ['alertSection' => $wizardData['feedSection'], 'feedName' => $wizardData['feed']];

                    include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/longTime.php';
                    ?>
                </div>

                <div data-feed-finished="true">
                    <?php
                    $alertData = ['alertSection' => $wizardData['feedSection'], 'feedName' => $wizardData['feed']];

                    if ($wizardData['feed'] === 'stock') {
                        include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/feedIsReadyStock.php';
                    } else {
                        include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/feedIsReady.php';
                    }
                    ?>
                </div>

                <div class="mmp_wizard__content">
                    <div class="mmp_wizard__content_body">
                        <div class="mmp_wizard__generate">
                            <form>
                                <div class="mmp_wizard__generating" data-status="inactive">
                                    <div class="mmp_wizard__generating_status">

                                    </div>

                                    <div style="position: relative;"
                                         class="rangeSlider rangeSlider-<?= $wizardData['feed'] ?>"
                                         data-range-index="<?= $wizardData['feed'] ?>">
                                        <span class="rangeSliderPercentage"
                                        <?php if ($wizardData['percentage'] > 52): ?>
                                            style="color: white;"
                                        <?php endif; ?>
                                        ><?= $wizardData['percentage'] ?>%</span>
                                        <span class="rangeSliderBg" style="width: <?= $wizardData['percentage'] ?>%;"></span>
                                    </div>

                                    <svg class="mmp_wizard__generating_svg">
                                        <use xlink:href="<?= plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#refresh' ?>"></use>
                                    </svg>

                                    <svg class="mmp_wizard__generating_done_svg">
                                        <use xlink:href="<?= plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#check-inv' ?>"></use>
                                    </svg>
                                </div>

                                <input type="hidden" name="token" id="token" value="<?= $wizardData['token'] ?>"/>
                                <input type="hidden" name="feed" id="feed" value="<?= $wizardData['cronAction'] ?>"/>
                                <input type="hidden" name="feedName" id="feedName" value="<?= $wizardData['feed'] ?>"/>
                                <input type="hidden" name="action" id="action"
                                       value="<?= $wizardData['ajaxGenerateAction'] ?>"/>
                            </form>
                        </div>

                    </div>

                    <?php if ($wizardForced): ?>
                        <div class="mmp_wizard__buttons mmp_justify_end">
                            <a href="javascript:void(0);" class="mmp_btn__blue"
                               data-3-generate="start"
                               data-mmp-wizard-go="3"
                               data-mmp-wizard-do-before="mmpStartWizardGenerating"
                               data-go-to-link="<?= $wizardData['feedListLink'] ?>"><?= __('Start feed generation', 'mergado-marketing-tab') ?></a>
                            <a href="javascript:void(0);" class="mmp_btn__blue" data-3-generate="skip"
                               style="display: none;" data-mmp-wizard-go="3"
                               data-mmp-wizard-do-before="mmpSkipWizard"
                               data-go-to-link="<?= $wizardData['feedListLink'] ?>"><?= __('Skip to list of feeds', 'mergado-marketing-tab') ?></a>
                            <a href="javascript:void(0);" data-go-to-link="<?= $wizardData['feedListLink'] ?>" class="mmp_btn__blue" style="display: none;"
                               data-3-generate="done"
                               data-mmp-wizard-go="3"
                               data-mmp-wizard-do-before="mmpGoToLink"
                            >
                                <?= __('Continue to list of feeds', 'mergado-marketing-tab') ?></a>
                        </div>
                    <?php else: ?>
                        <div class="mmp_wizard__buttons mmp_justify_end">
<!--                            <a href="javascript:void(0);" class="mmp_btn__white" data-mmp-wizard-go="1"-->
<!--                               data-mmp-wizard-do-before="mmpStopProgress">--><?//= __('Back', 'mergado-marketing-tab') ?><!--</a>-->
                            <a href="javascript:void(0);" class="mmp_btn__blue"
                               data-mmp-wizard-go="4a"
                               data-mmp-wizard-do-before="mmpSkipWizard"
                               data-3-generate="skip"><?= __('Skip to cron settings', 'mergado-marketing-tab') ?></a>
                            <a href="javascript:void(0);" class="mmp_btn__blue" style="display: none;"
                               data-mmp-wizard-do-before="setWizardCompleted"
                               data-mmp-wizard-go="4a"
                               data-3-generate="done">
                                <?= __('Continue to cron settings', 'mergado-marketing-tab') ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
