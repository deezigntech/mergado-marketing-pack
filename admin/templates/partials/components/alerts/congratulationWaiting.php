<?php
$alertDefaultData = [
	'alertName' => 'congratulationWaiting',
	'alertSection' => $alertData['alertSection'],
	'type' => 'success',
	'text' => sprintf(__('<strong>Congratulations!</strong> You have just created your first feed in Mergado Pack. Once the feed is ready (the indicator will be green), you can <strong>create an export in the Mergado App</strong> by pressing the <strong>button "CREATE EXPORT IN MERGADO"</strong>', 'mergado-marketing-pack'), $feedBoxData['feedName']),
	'closable' => true,
	'closableAll' => true,
];

$alertClass = new AlertClass();
if (!$alertClass->isAlertDisabled($alertData['feedName'], $alertDefaultData['alertName']) && !$alertClass->isSectionDisabled($alertData['alertSection'])) {
	include __DIR__ . '/template/alert.php';
}
?>