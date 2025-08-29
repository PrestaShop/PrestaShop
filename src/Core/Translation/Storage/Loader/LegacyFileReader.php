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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Loader;

use PrestaShop\PrestaShop\Core\Translation\Exception\UnsupportedLocaleException;
use PrestaShop\PrestaShop\Core\Translation\Locale\Converter;

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
    public function load(string $path, string $locale): array
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
