<?php
//    feedBoxData should be set in parent template
//    Functions like:
//    XMLProductFeed::getDataForTemplates();
//    XMLStockFeed::getDataForTemplates();
//    XMLCategoryFeed::getDataForTemplates();
?>

<?php
if ( count( $feedBoxData['feedErrors'] ) === 0 ) {
    // No errors during generation
    if ($feedBoxData['wizardCompleted']) {
        if ( $feedBoxData['feedStatus'] === 'warning' && $feedBoxData['lastUpdate'] === false ) {
            // Not created yet
            $alertData = [ 'alertSection' => $feedBoxData['feedSection'], 'feedName' => $feedBoxData['feedName'] ];

            if ($feedBoxData['feedName'] === 'stock') {
                include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/congratulationWaitingStock.php';
            } else {
                include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/congratulationWaiting.php';
            }
        } else if ($feedBoxData['productFeedExist'] && $feedBoxData['feedStatus'] === 'success') {
            // Feed created already
            $alertData = [ 'alertSection' => $feedBoxData['feedSection'], 'feedName' => $feedBoxData['feedName'] ];

            if ($feedBoxData['feedName'] === 'stock') {
                include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/congratulationStock.php';
            } else {
                include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/congratulation.php';
            }
        }
    }
} else {
    if (in_array(AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION'], $feedBoxData['feedErrors'])) {
        if ($feedBoxData['feedStatus'] === 'danger') {
            // Error thrown during generation
            $alertData = [ 'alertSection' => $feedBoxData['feedSection'], 'feedName' => $feedBoxData['feedName'] ];
            include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/feedFailedBeforeFirstGeneration.php';
        } else {
            // Error thrown during generation
            $alertData = [ 'alertSection' => $feedBoxData['feedSection'], 'feedName' => $feedBoxData['feedName'] ];
            include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/feedFailed.php';
        }
    }

    if (in_array(AlertClass::ALERT_NAMES['NO_FEED_UPDATE'], $feedBoxData['feedErrors'])) {
        // Error thrown during generation
        $alertData = [ 'alertSection' => $feedBoxData['feedSection'], 'feedName' => $feedBoxData['feedName'] ];
        include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/feedNotUpdated.php';
    }
}

if(in_array(AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION'], $feedBoxData['feedErrors'])) {
    $feedStatusClass = 'mmp_feedBox__feedStatus--danger';
} else {
    $feedStatusClass = 'mmp_feedBox__feedStatus--' . $feedBoxData['feedStatus'];
}
?>

<?php if ( $feedBoxData['feedStatus'] === 'danger' ): ?>
    <div class="mmp_feedBox">
        <div class="mmp_feedBox__top">
            <div class="mmp_feedBox__status">
                <div class="mmp_feedBox__feedStatus <?= $feedStatusClass ?>"></div>
                <p class="mmp_feedBox__date"><?= __( 'Waiting for first generation', 'mergado-marketing-pack' ) ?></p>
            </div>
            <div class="mmp_feedBox__actions">
                <a class="mmp_feedBox__button mmp_feedBox__button--square mmp_feedBox__createXmlFeed"
                   href="<?= $feedBoxData['wizardUrl'] ?>">
					<?= __( 'Create xml feed', 'mergado-marketing-pack' ) ?>
                    <svg class="mmp_icon">
                        <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg' ) . 'mmp_icons.svg#plus' ?>"></use>
                    </svg>
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="mmp_feedBox">
        <div class="mmp_feedBox__top">
            <div class="mmp_feedBox__status">
				<?php if ( $feedBoxData['feedStatus'] === 'success' ): ?>
                    <div class="mmp_feedBox__feedStatus <?= $feedStatusClass ?>"></div>
                    <p class="mmp_feedBox__date"><?= __( 'Last update: ', 'mergado-marketing-pack' ) ?> <?= $feedBoxData['lastUpdate'] ?></p>
				<?php elseif ( $feedBoxData['feedStatus'] === 'warning' ): ?>
                    <div class="mmp_feedBox__feedStatus <?= $feedStatusClass ?>"></div>
                    <p class="mmp_feedBox__date"><?= $feedBoxData['percentageStep'] ?> %
                        - <?= __( 'Waiting for next cron start', 'mergado-marketing-pack' ) ?></p>
				<?php endif; ?>
            </div>
            <div class="mmp_feedBox__actions">
				<?php if ( $feedBoxData['feedStatus'] === 'warning' || in_array(AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION'], $feedBoxData['feedErrors'])): ?>
                    <a class="mmp_feedBox__button mmp_feedBox__finishManually""
                    href="<?= $feedBoxData['generateUrl'] ?>"
                    data-tippy-content="<?= __( 'Manually finish feed creating.', 'mergado-marketing-pack' ) ?>"
                    >
                    <svg class="mmp_icon">
                        <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#turn-on' ?>"></use>
                    </svg>
					<?= __( 'Finish manually', 'mergado-marketing-pack' ) ?>
                    </a>
				<?php elseif ( $feedBoxData['feedStatus'] === 'success' ): ?>
                    <a class="mmp_feedBox__button mmp_feedBox__button--square mmp_feedBox__openXmlFeed"
                       data-tippy-content="<?= __( 'Open XML feed in new window.', 'mergado-marketing-pack' ) ?>"
                       href="<?= $feedBoxData['feedUrl'] ?>" target="_blank">
                        <svg class="mmp_icon">
                            <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#open' ?>"></use>
                        </svg>
                    </a>
				<?php endif; ?>

                <?php if($feedBoxData['createExportInMergadoUrl'] === false): ?>
                    <a class="mmp_feedBox__button mmp_feedBox__copyUrl" href="javascript:void(0);" data-copy-stash='<?= $feedBoxData['feedUrl'] ?>'
                            data-tippy-content="<?= __( 'Copy feed URL address to clipboard. <br><br> Activate the Availability feed in Heureka administration in the Settings > Availability XML file page.', 'mergado-marketing-pack' ) ?>"
                            >
                        <svg class="mmp_icon">
                            <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#copy' ?>"></use>
                        </svg>
                        <?= __( 'Copy feed URL', 'mergado-marketing-pack' ) ?>
                    </a>
                <?php else: ?>
                    <a class="mmp_feedBox__export
                        <?php if ( $feedBoxData['feedStatus'] !== 'success' ): ?>
                                disabled" href="javascript:void(0);">
                        <?php else: ?>
                            " target="_blank" href="<?= $feedBoxData['createExportInMergadoUrl'] ?>"
                            data-tippy-content="<?= __( 'Click to redirect to Mergado where you can start creating exports for hundereds of different channels. <br><br> Mergado App will open in a new window.', 'mergado-marketing-pack' ) ?>"
                            >
                        <?php endif ?>
                        <p class="mmp_feedBox__button mmp_feedBox__mergadoExport"><?= __( 'Create export in Mergado', 'mergado-marketing-pack' ) ?></p>
                        <svg class="mmp_icon">
                            <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#service-mergado' ?>"></use>
                        </svg>
                    </a>
                <?php endif; ?>
                <a class="mmp_feedBox__toggler" href="javascript:void(0);">
                    <svg class="mmp_icon">
                        <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg' ) . 'mmp_icons.svg#chevron-down' ?>"></use>
                    </svg>
                </a>
            </div>
        </div>
        <div class="mmp_feedBox__bottom">
            <div class="mmp_feedBox__line">
                <div class="mmp_feedBox__line--left">
                    <p class="mmp_feedBox__name"><?= __( 'Feed URL', 'mergado-marketing-pack' ) ?></p>
                    <input type="text" class="mmp_feedBox__url" readonly value="<?= $feedBoxData['feedUrl'] ?>" />
                </div>
                <a class="mmp_feedBox__button mmp_feedBox__button--square mmp_feedBox__copy
                            <?php if ( $feedBoxData['feedStatus'] !== 'success' ): ?>
                               disabled"
				<?php else: ?>
                    " data-copy-stash='<?= $feedBoxData['feedUrl'] ?>'
                    data-tippy-content="<?= __( 'Copy feed URL address to clipboard.', 'mergado-marketing-pack' ) ?>"
				<?php endif; ?>
                href="javascript:void(0);">

                <svg class="mmp_icon">
                    <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#copy' ?>"></use>
                </svg>
                </a>
            </div>
            <div class="mmp_feedBox__line">
                <div class="mmp_feedBox__line--left">
                    <p class="mmp_feedBox__name"><?= __( 'Cron URL', 'mergado-marketing-pack' ) ?></p>
                    <input type="text" class="mmp_feedBox__url" readonly value="<?= $feedBoxData['cronGenerateUrl'] ?>">
                </div>
                <a class="mmp_feedBox__button mmp_feedBox__button--square mmp_feedBox__copy
                    " data-copy-stash='<?= $feedBoxData['cronGenerateUrl'] ?>'
                    data-tippy-content="<?= __( 'Copy cron URL address to clipboard.', 'mergado-marketing-pack' ) ?>"
                href="javascript:void(0);">

                <svg class="mmp_icon">
                    <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#copy' ?>"></use>
                </svg>
                </a>
            </div>
            <div class="mmp_feedBox__actionsBottom">
                <a class="mmp_feedBox__button mmp_feedBox__cronSetup"
                   href="<?= $feedBoxData['cronSetUpUrl'] ?>"
                   data-tippy-content="<?= __( 'Schedule when and how often is your feed going to be updated.', 'mergado-marketing-pack' ) ?>"
                >
                    <svg class="mmp_icon">
                        <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#in-progress' ?>"></use>
                    </svg>
					<?= __( 'Cron set up', 'mergado-marketing-pack' ) ?>
                </a>
                <a class="mmp_feedBox__button mmp_feedBox__generate"
                   href="<?= $feedBoxData['generateUrl'] ?>"
                   data-tippy-content="<?= __( 'Manually start feed creating.', 'mergado-marketing-pack' ) ?>"
                >
                    <svg class="mmp_icon">
                        <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#turn-on' ?>"></use>
                    </svg>
					<?= __( 'Generate manually', 'mergado-marketing-pack' ) ?></a>
                <a class="mmp_feedBox__button mmp_feedBox__button--square mmp_feedBox__download
                            <?php if ( $feedBoxData['feedStatus'] !== 'success' ): ?>
                               disabled" href="javascript:void(0);">
					<?php else: ?>
                        " href="<?= $feedBoxData['downloadUrl'] ?>"
                        data-tippy-content="<?= __( 'Download the feed to your computer.', 'mergado-marketing-pack' ) ?>"
                        >
					<?php endif; ?>

                    <svg class="mmp_icon">
                        <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#download' ?>"></use>
                    </svg>
                </a>
                <a class="mmp_feedBox__button mmp_feedBox__button--square mmp_feedBox__button--danger mmp_feedBox__delete
                    <?php if ( $feedBoxData['feedStatus'] === 'danger'): ?>
                       disabled" href="javascript:void(0)">
					<?php else: ?>
                        " href="<?= $feedBoxData['deleteUrl'] ?>"
                        data-tippy-content="<?= __( 'Deletes the product feed and all links.', 'mergado-marketing-pack' ) ?>"
                        >
					<?php endif ?>
                    <svg class="mmp_icon">
                        <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#delete' ?>"></use>
                    </svg>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>