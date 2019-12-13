<?php

namespace PrestaShop\PrestaShop\Core\Util;

class BoolParser
{
    /**
     * @param string|int $value
     *
     * @return bool
     */
    public static function castToBool($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (bool) $value; // 0 => false; all other true
        }

        return strtolower($value) !== 'false';
    }
}
