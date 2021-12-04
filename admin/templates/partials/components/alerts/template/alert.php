<?php
//    $alertDefaultData = [
//        'type' => 'success',
//        'text' => 'Some amazing text',
//        'closable' => true,
//        'closableAll' => true,
//         'alertName' => 'congratulation'
//        'alertSection' => 'product'
//    ]
?>

<div class="mmp_alert__wrapper mmp_alert__wrapper--<?= $alertDefaultData['type'] ?>"
     data-mmp-alert="<?= htmlspecialchars( json_encode(
	     [
		     'section' => $alertDefaultData['alertSection'],
		     'feed'    => $alertData['feedName'],
		     'name'    => $alertDefaultData['alertName'],
		     'token'   => $token
	     ]
     ) )
     ?>">
    <div class="mmp_alert mmp_alert--<?= $alertDefaultData['type'] ?>">
		<?php if ( $alertDefaultData['closable'] ): ?>
            <a class="mmp_alert__closer" href="javascript:void(0);"
               data-mmp-hide-alert="<?= $alertDefaultData['alertName'] ?>">✖</a>
		<?php endif; ?>

        <svg class="mmp_alert__icon">
			<?php if ( $alertDefaultData['type'] === 'success' ): ?>
                <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#check-inv' ?>"></use>
			<?php elseif ( $alertDefaultData['type'] === 'warning' ): ?>
                <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#info' ?>"></use>
			<?php elseif ( $alertDefaultData['type'] === 'danger' ): ?>
                <use xlink:href="<?= plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#error' ?>"></use>
			<?php endif ?>
        </svg>

        <span><?= $alertDefaultData['text'] ?></span>
    </div>

	<?php if ( $alertDefaultData['closableAll'] ): ?>
        <div class="mmp_alert__closerFull">
            <a href="javascript:void(0);" data-mmp-disable-all-notifications="">
				<?= __( 'Don\'t show me any more tips.', 'mergado-marketing-pack' ) ?>
            </a>
        </div>
	<?php endif; ?>
</div>
