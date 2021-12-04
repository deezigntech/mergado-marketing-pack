<?php

use Mergado\Tools\Settings;

if ($wizardData == null) {
    exit;
}

if (isset($_GET['mmp-wizard'])) {
    $wizardType = $_GET['mmp-wizard'];
}

$wizardStep = isset($_GET['step']) ? $_GET['step']: false;
$wizardForced = isset($_GET['force']) ? true : false;
$thisFeedActive = (@$wizardType === $wizardData['feed']) ? 1 : 0;

?>

<div class="wizard" data-forced="<?= $wizardForced ?>" data-mmp-wizard="<?= $wizardData['feed'] ?>">
    <?php
    include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'wizard/step1.php'));
    include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'wizard/step2.php'));
    include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'wizard/step3.php'));
    include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'wizard/step4a.php'));
    include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'wizard/step4b.php'));
    ?>
</div>

<div class="mmp_wizardDialog">
    <div class="mmp_dialog"
         id="mmp_dialogAttention"
         data-mmp-content="<h1 class='mmp_dialog__title'><?= __('ATTENTION: Please do not leave or close the page until the entire feed is generated.', 'mergado-marketing-pack') ?><br><strong><?= __('This process may take a while depending on the number of products in your eshop.', 'mergado-marketing-pack') ?></strong></h1><p><?= __('Otherwise you will have to wait until the cron service generated the whole feed according to the specified frequency. It will take longer.', 'mergado-marketing-pack') ?></p>" data-mmp-yes="<?= __('Ok, I understand', 'mergado-marketing-pack') ?>" data-mmp-no="">
    </div>
</div>

<div class="mmp_dialog"
     id="mmp_dialogLeave"
     data-mmp-content="<h1><?= __('Are you sure you want to leave the page?', 'mergado-marketing-pack') ?></h1><p><?= __('This interrupts the current feed generation process.', 'mergado-marketing-pack') ?></p>"
     data-mmp-no="<?= __('Leave page', 'mergado-marketing-pack') ?>"
     data-mmp-yes="<?= __('Stay on page', 'mergado-marketing-pack') ?>">
</div>

<div class="mmp_dialog"
     id="mmp_dialogAlreadyRunning"
     data-mmp-content="<h1><?= __('We are sorry', 'mergado-marketing-pack') ?></h1><p><?= __('It seems, that cron is currently running. Try it again after few minutes.', 'mergado-marketing-pack') ?></p>"
     data-mmp-no=""
     data-mmp-yes="Continue"></div>
<div class="mmp_dialog" id="mmp_dialogError" data-mmp-content="<h1><?= __('Something went wrong. Feed can\'t be generated.', 'mergado-marketing-pack') ?></h1><p><?= __('Try to change number of products generated in one cron run or contact our support.', 'mergado-marketing-pack') ?></p>" data-mmp-no="" data-mmp-yes="<?= __('Continue', 'mergado-marketing-pack') ?>"></div>