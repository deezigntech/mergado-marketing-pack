<?php
$alertDefaultData = [
	'alertName' => AlertClass::ALERT_NAMES['ERROR_DURING_GENERATION'],
	'alertSection' => $alertData['alertSection'],
	'type' => 'danger',
	'text' => __('The last generation of feed failed.<br>Please start the generation again with the CREATE XML FEED button.', 'mergado-marketing-pack'),
	'closable' => true,
	'closableAll' => false,
];

$alertClass = new AlertClass();
if (!$alertClass->isAlertDisabled($alertData['feedName'], $alertDefaultData['alertName'])) {
	include __DIR__ . '/template/alert.php';
}
?>