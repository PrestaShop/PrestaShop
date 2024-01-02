<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\PrestaShop\Adapter\File\HtaccessFileGenerator;
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
     *
     * @return string
     */
    public function linkRewrite($str)
    {
        return LegacyTools::str2url($str);
    }

    /**
     * @param string $html
     * @param string|null $uri_unescape
     * @param bool $allow_style
     *
     * @return string
     */
    public function purifyHTML($html, $uri_unescape = null, $allow_style = false)
    {
        return LegacyTools::purifyHTML($html, $uri_unescape, $allow_style);
    }

    /**
     * @see LegacyTools::refreshCACertFile()
     */
    public function refreshCaCertFile()
    {
        LegacyTools::refreshCACertFile();
    }

    /**
     * @see LegacyTools::generateHtaccess()
     *
     * @return bool
     */
    public function generateHtaccess()
    {
        return LegacyTools::generateHtaccess();
    }

    /**
     * @see HtaccessFileGenerator::generateFile()
     *
     * @return bool
     */
    public function generateHtaccessWithMultiViews()
    {
        return LegacyTools::generateHtaccess(
            null,
            null,
            null,
            '',
            true
        );
    }

    /**
     * @see HtaccessFileGenerator::generateFile()
     *
     * @return bool
     */
    public function generateHtaccessWithoutMultiViews()
    {
        return LegacyTools::generateHtaccess(
            null,
            null,
            null,
            '',
            false
        );
    }

    /**
     * returns the rounded value of $value to specified precision, according to your configuration;.
     *
     * @note : PHP 5.3.0 introduce a 3rd parameter mode in round function
     *
     * @param float $value
     * @param int $precision
     *
     * @return float
     */
    public function round($value, $precision = 0, $round_mode = null)
    {
        return LegacyTools::ps_round($value, $precision, $round_mode);
    }

    /**
     * Return domain name according to configuration and depending on ssl activation.
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

    /**
     * Checks if apache mod exists for mod_rewrite or the server has HTTP_MOD_REWRITE enabled.
     *
     * @return bool
     */
    public function isModRewriteActive()
    {
        return LegacyTools::modRewriteActive();
    }

    /**
     * Copy content.
     *
     * @param string $source
     * @param string $destination
     * @param resource|null $streamContext
     *
     * @return bool|int
     */
    public function copy($source, $destination, $streamContext = null)
    {
        return LegacyTools::copy($source, $destination, $streamContext);
    }

    /**
     * Sanitize a string.
     *
     * @param string $value
     * @param bool $allowHtml
     *
     * @return string
     */
    public function sanitize($value, $allowHtml = false)
    {
        return LegacyTools::safeOutput($value, $allowHtml);
    }

    /**
     * Get a valid image URL to use from BackOffice.
     *
     * @param string $fileName image file name
     * @param bool $escapeHtmlEntities if true - escape html entities on file name argument
     *
     * @return string image URL
     */
    public function getAdminImageUrl($fileName, $escapeHtmlEntities = false)
    {
        return LegacyTools::getAdminImageUrl($fileName, $escapeHtmlEntities);
    }

    /**
     * @see LegacyTools::displayDate()
     *
     * @return string
     *
     * @throws \PrestaShopException
     */
    public function displayDate($date, $full = false)
    {
        return LegacyTools::displayDate($date, $full);
    }

    /**
     * @see LegacyTools::truncateString()
     *
     * @return bool|string
     */
    public function truncateString($text, $length = 120, $options = [])
    {
        return LegacyTools::truncateString($text, $length, $options);
    }
}
