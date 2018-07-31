<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\Decimal\Number;
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
     * @deprecated use linkRewrite.
     *
     * @param string $str
     * @param bool   $utf8Decode (deprecated)
     *
     * @return string
     */
    public function link_rewrite($str, $utf8Decode = null)
    {
        return $this->linkRewrite($str, $utf8Decode);
    }

    /**
     * Return the friendly url from the provided string.
     *
     * @param string $str
     * @param bool   $utf8Decode (deprecated)
     *
     * @return string
     */
    public function linkRewrite($str, $utf8Decode = null)
    {
        if ($utf8Decode !== null) {
            LegacyTools::displayParameterAsDeprecated('utf8_decode');
        }

        return LegacyTools::str2url($str);
    }

    /**
     * Polyfill for bcadd if BC Math extension is not installed.
     *
     * @deprecated since 1.7.2.2 - Use PrestaShop\Decimal\Number instead
     */
    public function bcadd($left_operand, $right_operand, $scale = null)
    {
        $result = (new Number((string) $left_operand))
            ->plus(new Number((string) $right_operand));

        if (null === $scale) {
            return (string) $result;
        }

        return (string) $result->toPrecision($scale);
    }

    public function purifyHTML($html, $uri_unescape = null, $allow_style = false)
    {
        return LegacyTools::purifyHTML($html, $uri_unescape, $allow_style);
    }

    public function refreshCaCertFile()
    {
        LegacyTools::refreshCACertFile();
    }

    public function generateHtaccess()
    {
        return LegacyTools::generateHtaccess();
    }

    /**
     * returns the rounded value of $value to specified precision, according to your configuration;
     *
     * @note : PHP 5.3.0 introduce a 3rd parameter mode in round function
     *
     * @param float $value
     * @param int $precision
     * @return float
     */
    public function round($value, $precision = 0, $round_mode = null)
    {
        return LegacyTools::ps_round($value, $precision, $round_mode);
    }

    /**
     * Return domain name according to configuration and depending on ssl activation
     *
     * @param bool $http if true, return domain name with protocol
     * @param bool $entities if true, convert special chars to HTML entities
     *
     * @return string domain
     */
    public function getShopDomainSsl($http = false, $entities = false)
    {
        return LegacyTools::getShopDomainSsl($http, $entities);
    }
}
