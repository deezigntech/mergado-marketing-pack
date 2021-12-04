<?php

use Mergado\Tools\Settings;
use Mergado\Tools\XMLStockFeed;

$xmlStockFeed = new XMLStockFeed();
$isAlreadyFinished = $xmlStockFeed->isWizardFinished();
$wizardName = 'Heureka availability';

if ((isset($_GET['mmp-wizard']) && $_GET['mmp-wizard'] === 'stock') || (!$isAlreadyFinished || isset($_GET['step']))) {
    $wizardData = $xmlStockFeed->getWizardData();
	?>
    <script>
        if (typeof window.mmpWizardData === 'undefined') {
            window.mmpWizardData = {'stock': <?= json_encode($wizardData) ?>};
        } else {
            window.mmpWizardData['stock'] = (<?= json_encode($wizardData) ?>);
        }
    </script>
	<?php

    include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'wizard/main.php'));
} else {
    ?>
    <div class="card full">
        <h1 class="mmp_h1"><?= __('Heureka Availability feed', 'mergado-marketing-pack') ?></h1>

        <?php
        $feedBoxData = $xmlStockFeed->getDataForTemplates();

        include(wp_normalize_path(__MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'feedBox/feedBox.php'));
        ?>
    </div>
    <?php
}
