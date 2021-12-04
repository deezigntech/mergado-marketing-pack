<?php

use Mergado\Tools\Settings;
use Mergado\Tools\XMLCategoryFeed;

$xmlCategoryFeed = new XMLCategoryFeed();
$isAlreadyFinished = $xmlCategoryFeed->isWizardFinished();
$wizardName = 'category';

if ((isset($_GET['mmp-wizard']) && $_GET['mmp-wizard'] === 'category') || (!$isAlreadyFinished || isset($_GET['step']))) {

    $wizardData = $xmlCategoryFeed->getWizardData();
?>
    <script>
        if (typeof window.mmpWizardData === 'undefined') {
            window.mmpWizardData = {'category': <?= json_encode($wizardData) ?>};
        } else {
            window.mmpWizardData['category'] = (<?= json_encode($wizardData) ?>);
        }
    </script>
<?php
    include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'wizard/main.php'));
} else {
    ?>
    <div class="card full">
        <h1 class="mmp_h1"><?= __('Category feeds', 'mergado-marketing-pack') ?></h1>

        <?php
        $feedBoxData = $xmlCategoryFeed->getDataForTemplates();

        include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'feedBox/feedBox.php'));
        ?>
    </div>
    <?php
}
