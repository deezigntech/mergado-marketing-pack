<?php

namespace Mergado\Tools;

use Exception;

class ToolsClass
{
    public static function fileGetContents($url)
    {
        if (extension_loaded('curl')) {
            try {
                $c = curl_init();
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($c, CURLOPT_URL, $url);
                $contents = curl_exec($c);
                curl_close($c);

                if ($contents) {
                    return $contents;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        } else {
            return file_get_contents($url);
        }
    }
}