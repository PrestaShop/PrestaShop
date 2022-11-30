<?php

namespace PrestaShop\PrestaShop\Core\Util\Number;

class Math
{
    public const PS_ROUND_UP = 0;
    public const PS_ROUND_DOWN = 1;
    public const PS_ROUND_HALF_UP = 2;
    public const PS_ROUND_HALF_DOWN = 3;
    public const PS_ROUND_HALF_EVEN = 4;
    public const PS_ROUND_HALF_ODD = 5;

    /**
     * returns the rounded value of $value to specified precision, according to your configuration;.
     *
     * @param float $value
     * @param int $precision
     * @param int $round_mode
     *
     * @return float
     */
    public static function round(float $value, int $precision, int $round_mode): float
    {
        switch ($round_mode) {
            case self::PS_ROUND_UP:
                return self::ceilf($value, $precision);
            case self::PS_ROUND_DOWN:
                return self::floorf($value, $precision);
            case self::PS_ROUND_HALF_DOWN:
            case self::PS_ROUND_HALF_EVEN:
            case self::PS_ROUND_HALF_ODD:
                return self::math_round($value, $precision, $round_mode);
            case self::PS_ROUND_HALF_UP:
            default:
                return self::math_round($value, $precision);
        }
    }

    /**
     * @param int|float $value
     * @param int|float $places
     * @param int<2,5> $mode (PS_ROUND_HALF_UP|PS_ROUND_HALF_DOWN|PS_ROUND_HALF_EVEN|PS_ROUND_HALF_ODD)
     *
     * @return false|float
     */
    public static function math_round($value, $places, $mode = PS_ROUND_HALF_UP)
    {
        return round($value, $places, $mode - 1);
    }

    /**
     * Returns the rounded value up of $value to specified precision.
     *
     * @param float $value
     * @param int $precision
     *
     * @return float
     */
    public static function ceilf($value, $precision = 0)
    {
        $precision_factor = $precision == 0 ? 1 : 10 ** $precision;
        $tmp = $value * $precision_factor;
        $tmp2 = (string) $tmp;
        // If the current value has already the desired precision
        if (strpos($tmp2, '.') === false) {
            return $value;
        }
        if ($tmp2[strlen($tmp2) - 1] == 0) {
            return $value;
        }

        return ceil($tmp) / $precision_factor;
    }

    /**
     * Returns the rounded value down of $value to specified precision.
     *
     * @param float $value
     * @param int $precision
     *
     * @return float
     */
    public static function floorf($value, $precision = 0)
    {
        $precision_factor = $precision == 0 ? 1 : 10 ** $precision;
        $tmp = $value * $precision_factor;
        $tmp2 = (string) $tmp;
        // If the current value has already the desired precision
        if (strpos($tmp2, '.') === false) {
            return $value;
        }
        if ($tmp2[strlen($tmp2) - 1] == 0) {
            return $value;
        }

        return floor($tmp) / $precision_factor;
    }
}
