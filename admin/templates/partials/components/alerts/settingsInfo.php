<?php
$alertDefaultData = [
	'alertName' => 'settingsInfo',
	'alertSection' => $alertData['alertSection'],
	'type' => 'warning',
	'text' => sprintf(__('These settings are <strong>valid for all %s feeds</strong>. After saving the changes, the temporary files will be deleted and the <strong>feed creation will start from the beginning with the new settings</strong>.', 'mergado-marketing-pack'), $alertData['alertSection']),
	'closable' => true,
	'closableAll' => true,
];

$alertClass = new AlertClass();
if (!$alertClass->isAlertDisabled($alertData['feedName'], $alertDefaultData['alertName']) && !$alertClass->isSectionDisabled($alertData['alertSection'])) {
	include __DIR__ . '/template/alert.php';
}
?>