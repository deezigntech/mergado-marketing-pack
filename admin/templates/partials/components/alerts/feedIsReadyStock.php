<?php
$alertDefaultData = [
	'alertName' => 'feedIsReady',
	'alertSection' => $alertData['alertSection'],
	'type' => 'success',
	'text' => sprintf(__('<strong>The %s feed is ready</strong>. You can now go to the List of feeds and activate Availability feed in your Heureka account.', 'mergado-marketing-pack'), $wizardName),
	'closable' => false,
	'closableAll' => false,
];

$alertClass = new AlertClass();

if (!$alertClass->isAlertDisabled($alertData['feedName'], $alertDefaultData['alertName']) && !$alertClass->isSectionDisabled($alertData['alertSection'])) {
	include __DIR__ . '/template/alert.php';
}
?>