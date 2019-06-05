<?php

/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Loader;

use PrestaShop\PrestaShop\Core\Translation\Locale\Converter;
use PrestaShopBundle\Translation\Exception\UnsupportedLocaleException;

/**
 * Reads legacy locale files
 */
class LegacyFileReader
{
    /**
     * @var Converter Converts IETF language tags into two-letter language code
     */
    private $localeConverter;

    public function __construct(Converter $converter)
    {
        $this->localeConverter = $converter;
    }

    /**
     * Loads legacy translations from a file
     *
     * @param string $path Path where the locale file should be looked up
     * @param string $locale IETF language tag
     *
     * @return array Translation tokens
     */
    public function load($path, $locale)
    {
        // Each legacy file declare this variable to store the translations
        $_MODULE = [];

        $shopLocale = $this->localeConverter->toLegacyLocale($locale);

        $filePath = $path . "$shopLocale.php";

        if (!file_exists($filePath)) {
            throw UnsupportedLocaleException::fileNotFound($filePath, $locale);
        }

        // Load a global array $_MODULE
        include_once $filePath;

        return $_MODULE;
    }
}
