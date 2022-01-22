<?php

namespace Mergado\Etarget;


use Mergado\Helpers\TemplateLoader;

class EtargetServiceIntegration {

	/**
	 * @var EtargetService
	 */
	private $etargetService;

	public function __construct() {
		$this->etargetService = new EtargetService();
	}

	public function etargetRetarget()
	{
		$active = $this->etargetService->isActive();
		$templatePath = __DIR__ . '/templates/retarget.php';

		$templateVariables = [
			'id' => $this->etargetService->getId(),
			'hash' => $this->etargetService->getHash(),
		];

		if ($active) {
			echo TemplateLoader::getTemplate($templatePath, $templateVariables);
		}
	}
}