<?php

namespace Mergado\Tools;

class BannersClass
{
    public static function getWide()
    {
        return ToolsClass::fileGetContents('https://platforms.mergado.com/woocommerce/wide');
    }

    public static function getSidebar()
    {
        return ToolsClass::fileGetContents('https://platforms.mergado.com/woocommerce/sidebar');
    }
}