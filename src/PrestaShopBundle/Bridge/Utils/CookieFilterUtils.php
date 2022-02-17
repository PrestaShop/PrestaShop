<?php

namespace PrestaShopBundle\Bridge\Utils;

use Tools;

class CookieFilterUtils
{
    public static function getCookieByPrefix(string $className)
    {
        return str_replace(['admin', 'controller'], '', Tools::strtolower($className));
    }
}
