<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

use Tools as LegacyTools;

/**
 * This adapter will complete the new architecture Tools.
 *
 * Please put only wrappers and equivalents from Legacy \Tools class.
 * (only for this purpose, this is not here to put new utils functions).
 */
class Tools
{
    /**
     * Return the friendly url from the provided string.
     *
     * @param string $str
     * @param bool   $utf8_decode (deprecated)
     *
     * @return string
     */
    public function link_rewrite($str, $utf8_decode = null)
    {
        if ($utf8_decode !== null) {
            LegacyTools::displayParameterAsDeprecated('utf8_decode');
        }

        return LegacyTools::str2url($str);
    }

    /**
     * Polyfill for bcadd if BC Math extension is not installed.
     */
    public function bcadd($left_operand, $right_operand, $scale = null)
    {
        if (function_exists('bcadd')) {
            return bcadd($left_operand, $right_operand, $scale);
        }

        // from http://php.net/manual/en/function.bcadd.php#92252
        // check if they're valid positive numbers, extract the whole numbers and decimals
        if (!preg_match("/^\+?(\d+)(\.\d+)?$/", $left_operand, $Tmp1) ||
            !preg_match("/^\+?(\d+)(\.\d+)?$/", $right_operand, $Tmp2)) {
            return '0';
        }

        // this is where the result is stored
        $Output = array();

        // remove ending zeroes from decimals and remove point
        $Dec1 = isset($Tmp1[2]) ? rtrim(substr($Tmp1[2], 1), '0') : '';
        $Dec2 = isset($Tmp2[2]) ? rtrim(substr($Tmp2[2], 1), '0') : '';

        // calculate the longest length of decimals
        $DLen = max(strlen($Dec1), strlen($Dec2));

        // if $Scale is null, automatically set it to the amount of decimal places for accuracy
        if ($scale == null) {
            $Scale = $DLen;
        }

        // remove leading zeroes and reverse the whole numbers, then append padded decimals on the end
        $Num1 = strrev(ltrim($Tmp1[1], '0').str_pad($Dec1, $DLen, '0'));
        $Num2 = strrev(ltrim($Tmp2[1], '0').str_pad($Dec2, $DLen, '0'));

        // calculate the longest length we need to process
        $MLen = max(strlen($Num1), strlen($Num2));

        // pad the two numbers so they are of equal length (both equal to $MLen)
        $Num1 = str_pad($Num1, $MLen, '0');
        $Num2 = str_pad($Num2, $MLen, '0');

        // process each digit, keep the ones, carry the tens (remainders)
        for ($i = 0;$i < $MLen;++$i) {
            $Sum = ((int) $Num1{$i} + (int) $Num2{$i});
            if (isset($Output[$i])) {
                $Sum += $Output[$i];
            }
            $Output[$i] = $Sum % 10;
            if ($Sum > 9) {
                $Output[$i + 1] = 1;
            }
        }

        // convert the array to string and reverse it
        $Output = strrev(implode($Output));

        // substring the decimal digits from the result, pad if necessary (if $Scale > amount of actual decimals)
        // next, since actual zero values can cause a problem with the substring values, if so, just simply give '0'
        // next, append the decimal value, if $Scale is defined, and return result
        $Decimal = str_pad(substr($Output, -$DLen, $scale), $scale, '0');
        $Output = (($MLen - $DLen < 1) ? '0' : substr($Output, 0, -$DLen));
        $Output .= (($scale > 0) ? ".{$Decimal}" : '');

        return $Output;
    }

    public function purifyHTML($html, $uri_unescape = null, $allow_style = false)
    {
        return LegacyTools::purifyHTML($html, $uri_unescape, $allow_style);
    }

    public function refreshCaCertFile()
    {
        LegacyTools::refreshCaCertFile();
    }
}
