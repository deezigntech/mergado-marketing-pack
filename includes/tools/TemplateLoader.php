<?php

namespace Mergado\Helpers;

class TemplateLoader {

	public static function getTemplate(string $path, array $variables)
	{
		extract($variables); // Extract variables for template

		ob_start();

		include_once $path;

		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}