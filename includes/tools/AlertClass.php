<?php

use Mergado\Tools\XMLCategoryFeed;
use Mergado\Tools\XMLProductFeed;
use Mergado\Tools\XMLStockFeed;

class AlertClass {
	const FEED_TO_SECTION = [
		'product' => XMLProductFeed::FEED_SECTION,
		'category' => XMLCategoryFeed::FEED_SECTION,
		'stock' => XMLStockFeed::FEED_SECTION
	];

	//SINGLE ALERT NAMES .. in prestashop function that add blogId
	const ALERT_NAMES = [
		'NO_FEED_UPDATE' => 'feed_not_updated',
		'ERROR_DURING_GENERATION' => 'generation_failed'
	];

	// DISABLED ALERT
	public function getDisabledName($feedName, $alertName) {
		return 'mmp_alert_disabled_' . $feedName . '_' . $alertName;
	}

	public function isAlertDisabled($feedName, $alertName) {
		$name = $this->getDisabledName($feedName, $alertName);

		return get_option($name, 0);
	}

	public function setAlertDisabled($feedName, $alertName) {
		$name = $this->getDisabledName($feedName, $alertName);

		return update_option($name, 1);
	}

	// DISABLED SECTION

	public function getDisabledSectionName($sectionName) {
		return 'mmp_alert_section_disabled' . '_' . $sectionName;
	}

	public function isSectionDisabled($sectionName) {
		$name = $this->getDisabledSectionName($sectionName);

		return get_option($name, 0);
	}

	public function setSectionDisabled($sectionName) {
		$name = $this->getDisabledSectionName($sectionName);

		return update_option($name, 1);
	}

	// ERRORS
	public function getErrorName($feedName, $sectionName, $alertName) {
		return 'mmp_alert_error_' . $feedName . '_' . $sectionName . '_' . $alertName;
	}

	public function getSectionByFeed($feedName)
	{
		return self::FEED_TO_SECTION[$feedName];
	}

	public function setErrorInactive($feedName, $alertName)
	{
		$sectionName = $this->getSectionByFeed($feedName);
		$name = $this->getErrorName($feedName, $sectionName, $alertName);

		return update_option($name, 0);
	}

	public function setErrorActive($feedName, $alertName)
	{
		$sectionName = $this->getSectionByFeed($feedName);
		$name = $this->getErrorName($feedName, $sectionName, $alertName);

		return update_option($name, 1);
	}

	public function getFeedErrors($feedName)
	{
		$sectionName = $this->getSectionByFeed($feedName);

		$activeErrors = [];

		foreach(self::ALERT_NAMES as $alert) {
			$alertName = $this->getErrorName($feedName, $sectionName, $alert);

			if(get_option($alertName, 0) == 1) {
				$isNotHidden = !$this->isAlertDisabled($feedName, $alert);

				// Error is not hidden by user
				if ($isNotHidden) {
					$activeErrors[] = $alert;
				}
			}
		}

		return $activeErrors;
	}

	// Theres a function that set these variables base on specific conditions
	public function getMergadoErrors()
	{
		$errors = ['total' => 0];

		foreach(self::FEED_TO_SECTION as $feedName => $sectionName) {
		    if (!isset($errors[$sectionName])) {
                $errors[$sectionName] = 0;
            }

			foreach(self::ALERT_NAMES as $alert) {
				$alertName = $this->getErrorName($feedName, $sectionName, $alert);

				$hasError = get_option($alertName, 0);

				// Is error active
				if ($hasError) {
					$isNotHidden = !$this->isAlertDisabled($feedName, $alert);

					// Error is not hidden by user
					if ($isNotHidden) {
						$errors['total']++;
						$errors[$sectionName]++;
					}
				}
			}
		}

		return $errors;
	}

	public function checkIfErrorsShouldBeActive()
	{
		// Adding error if feeds exist and not updated for 24 hours
		$this->checkIfFeedsUpdated();
	}

	public function checkIfFeedsUpdated()
	{
		$xmlProductFeed = new XMLProductFeed();
		$xmlCategoryFeed = new XMLCategoryFeed();
		$xmlStockFeed = new XMLStockFeed();

		if ($xmlProductFeed->isFeedExist() && $this->isTimestampOlderThan24hours($xmlProductFeed->getLastFeedChangeTimestamp())) {
			$this->setErrorActive('product', self::ALERT_NAMES['NO_FEED_UPDATE']);
		} else {
			$this->setErrorInactive('product', self::ALERT_NAMES['NO_FEED_UPDATE']);
		}

		if ($xmlCategoryFeed->isFeedExist() && $this->isTimestampOlderThan24hours($xmlCategoryFeed->getLastFeedChangeTimestamp())) {
			$this->setErrorActive('category', self::ALERT_NAMES['NO_FEED_UPDATE']);
		} else {
			$this->setErrorInactive('category', self::ALERT_NAMES['NO_FEED_UPDATE']);
		}

		if ($xmlStockFeed->isFeedExist() && $this->isTimestampOlderThan24hours($xmlStockFeed->getLastFeedChangeTimestamp())) {
			$this->setErrorActive('stock', self::ALERT_NAMES['NO_FEED_UPDATE']);
		} else {
			$this->setErrorInactive('stock', self::ALERT_NAMES['NO_FEED_UPDATE']);
		}
	}

	public function isTimestampOlderThan24hours($timestamp)
	{
		return strtotime('+1 day', $timestamp) < time();
	}
}