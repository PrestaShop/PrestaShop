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

namespace Tests\Unit\Core\Language\Pack\Loader;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Language\Pack\Loader\RemoteLanguagePackLoader;

final class RemoteLanguagePackLoaderTest extends TestCase
{
    public function testGetLanguagePackUrl()
    {
        $version = '8.0.0';
        $basePath = 'http://i18n.mekey.com';
        $packLoader = new RemoteLanguagePackLoader($version, $basePath);

        $locale = 'fr-FR';
        $languagePackLangLocaleUrl = $packLoader->getLanguagePackUrl($locale);

        $this->assertStringStartsWith($basePath, $languagePackLangLocaleUrl);
        $this->assertStringEndsWith("{$version}/{$locale}/{$locale}.zip", $languagePackLangLocaleUrl);
        $this->assertTrue(filter_var($languagePackLangLocaleUrl, FILTER_VALIDATE_URL) !== false);
    }

    public function testGetLanguagePackListUrl()
    {
        $version = '8.0.0';
        $basePath = 'http://i18n.mekey.com';
        $packLoader = new RemoteLanguagePackLoader($version, $basePath);

        $languagePackLangListUrl = $packLoader->getLanguagePackListUrl();

        $this->assertStringEndsWith("{$version}/available_languages.json", $languagePackLangListUrl);
        $this->assertStringStartsWith($basePath, $languagePackLangListUrl);
        $this->assertTrue(filter_var($languagePackLangListUrl, FILTER_VALIDATE_URL) !== false, "Invalid URL found for {$languagePackLangListUrl}");
    }

    public function testGetLanguagePackUrlWithoutBasePath()
    {
        $version = '8.0.0';
        $packLoader = new RemoteLanguagePackLoader($version);

        $languagePackLangListUrl = $packLoader->getLanguagePackListUrl();

        $this->assertStringEndsWith("{$version}/available_languages.json", $languagePackLangListUrl);
        $this->assertTrue(filter_var($languagePackLangListUrl, FILTER_VALIDATE_URL) !== false, "Invalid URL found for {$languagePackLangListUrl}");
    }
}
