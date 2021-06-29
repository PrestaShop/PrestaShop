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

namespace Tests\Integration\Core\Localization\Locale;

use Currency;
use PrestaShop\PrestaShop\Adapter\Entity\LocalizationPack;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;
use PrestaShopBundle\Cache\LocalizationWarmer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class RepositoryTest extends KernelTestCase
{
    private const SERVICE_LOCALE_REPOSITORY = 'prestashop.core.localization.locale.repository';
    private const SERVICE_CLDR_CACHE_ADAPTER = 'prestashop.core.localization.cldr.cache.adapter';

    /**
     * The Locale repository is the entry point to retrieve a given Locale object.
     * Then the Locale object is the entry point to formatting numbers and prices.
     *
     * @var LocaleRepository
     */
    protected $localeRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Init Symfony
        self::bootKernel();
        // Global var for SymfonyContainer
        global $kernel;
        $kernel = self::$kernel;

        $this->localeRepository = self::$kernel->getContainer()->get(self::SERVICE_LOCALE_REPOSITORY);
    }

    /**
     * Given a valid Locale object
     * When asking this Locale object to format a number according to its specific formatting rules
     * Then the correct formatted number should be retrieved
     *
     * @dataProvider provideLocalizedNumbers
     *
     * @param string $localeCode
     * @param float $rawNumber
     * @param string $formattedNumber
     *
     * @throws LocalizationException
     */
    public function testItShouldFormatNumbers(string $localeCode, float $rawNumber, string $formattedNumber): void
    {
        $this->setupLocalisationPack($localeCode);

        $locale = $this->localeRepository->getLocale($localeCode);

        $this->assertSame(
            $formattedNumber,
            $locale->formatNumber($rawNumber)
        );
    }

    private function setupLocalisationPack(string $localeCode): void
    {
        // Clear Cache
        /** @var FilesystemAdapter $cacheAdapter */
        $cacheAdapter = self::$kernel->getContainer()->get(self::SERVICE_CLDR_CACHE_ADAPTER);
        $cacheAdapter->clear();

        $cacheDir = _PS_CACHE_DIR_ . 'sandbox' . DIRECTORY_SEPARATOR;

        $localizationWarmer = new LocalizationWarmer(_PS_VERSION_, strtolower(substr($localeCode, 3, 2)));
        $xmlContent = $localizationWarmer->warmUp($cacheDir);

        $localizationPack = new LocalizationPack();
        $localizationPack->loadLocalisationPack($xmlContent, [], true);
        $localizationPack->loadLocalisationPack($xmlContent, ['languages'], true);
    }

    public function provideLocalizedNumbers(): array
    {
        return [
            'United States' => [
                'localeCode' => 'en-US',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '1,234,568.123',
            ],
            'Japan' => [
                'localeCode' => 'ja-JP',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '1,234,568.123',
            ],
            'United Kingdom' => [
                'localeCode' => 'en-GB',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '1,234,568.123',
            ],
            'Germany' => [
                'localeCode' => 'de-DE',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '1.234.568,123',
            ],
            'France' => [
                'localeCode' => 'fr-FR',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => "1\u{202f}234\u{202f}568,123",
            ],
            'India (Hindi)' => [
                'localeCode' => 'hi-IN',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '12,34,568.123',
            ],
            'India (English)' => [
                'localeCode' => 'en-IN',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '12,34,568.123',
            ],
            'India (Bengali)' => [
                'localeCode' => 'bn-IN',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '12,34,568.123',
            ],
            'Spain' => [
                'localeCode' => 'es-ES',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '1.234.568,123',
            ],
            'Canada (French)' => [
                'localeCode' => 'fr-CA',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => "1\u{a0}234\u{a0}568,123",
            ],
            'Canada (English)' => [
                'localeCode' => 'en-CA',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '1,234,568.123',
            ],
            'China' => [
                'localeCode' => 'zh-CN',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '1,234,568.123',
            ],
            'Australia' => [
                'localeCode' => 'en-AU',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '1,234,568.123',
            ],
            'Brazil' => [
                'localeCode' => 'pt-BR',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '1.234.568,123',
            ],
            'Mexico' => [
                'localeCode' => 'es-MX',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '1,234,568.123',
            ],
            'Russia' => [
                'localeCode' => 'ru-RU',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => "1\u{a0}234\u{a0}568,123",
            ],
            'Italy' => [
                'localeCode' => 'it-IT',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => '1.234.568,123',
            ],
            'Poland' => [
                'localeCode' => 'pl-PL',
                'rawNumber' => 1234568.12345,
                'formattedNumber' => "1\u{a0}234\u{a0}568,123",
            ],
            'Bulgaria' => [
                'localeCode' => 'bg-BG',
                'rawNumber' => 1234568.12345,
                'formattedPrice' => "1\u{a0}234\u{a0}568,123",
            ],
            'Azerbaijani' => [
                'localeCode' => 'az-AZ',
                'rawNumber' => 1234568.12345,
                'formattedPrice' => '1.234.568,123',
            ],
        ];
    }

    /**
     * Given a valid Locale object and a valid currency code
     * When asking the locale to format a price of the said currency
     * Then the expected formatted price should be retrieved
     *
     * @dataProvider provideFormattedPrices
     */
    public function testItShouldFormatPrices(string $localeCode, float $rawNumber, string $currencyCode, string $formattedPrice): void
    {
        $locale = $this->localeRepository->getLocale($localeCode);

        $this->assertSame(
            $formattedPrice,
            $locale->formatPrice($rawNumber, $currencyCode)
        );

        /*
         * Following could be used to test with the native intl NumberFormatter
         * it could result in different results depending on the server
        $numberFormatter = new \NumberFormatter($localeCode, \NumberFormatter::CURRENCY);
        // following is used when customizing fraction digits
        //$numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 3);
        $this->assertSame(
            $formattedPrice,
            $numberFormatter->formatCurrency($rawNumber, $currencyCode)
        );
        */
    }

    public function provideFormattedPrices(): array
    {
        return [
            'United States' => [
                'localeCode' => 'en-US',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'USD',
                'formattedPrice' => '$1,234,568.12',
            ],
            'Japan' => [
                'localeCode' => 'ja-JP',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'JPY',
                'formattedPrice' => '￥1,234,568',
            ],
            'United Kingdom' => [
                'localeCode' => 'en-GB',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'GBP',
                'formattedPrice' => '£1,234,568.12',
            ],
            'Germany' => [
                'localeCode' => 'de-DE',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'EUR',
                'formattedPrice' => "1.234.568,12\u{a0}€",
            ],
            'France' => [
                'localeCode' => 'fr-FR',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'EUR',
                'formattedPrice' => "1\u{202f}234\u{202f}568,12\u{a0}€",
            ],
            'India' => [
                'localeCode' => 'ta-IN',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'INR',
                'formattedPrice' => "₹\u{a0}12,34,568.12",
            ],
            'India (English)' => [
                'localeCode' => 'en-US',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'INR',
                'formattedPrice' => '₹1,234,568.12',
            ],
            'Spain' => [
                'localeCode' => 'es-ES',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'EUR',
                'formattedPrice' => "1.234.568,12\u{a0}€",
            ],
            'Canada (French)' => [
                'localeCode' => 'fr-CA',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'CAD',
                'formattedPrice' => "1\u{a0}234\u{a0}568,12\u{a0}\$",
            ],
            'China' => [
                'localeCode' => 'zh-CN',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'CNY',
                'formattedPrice' => '¥1,234,568.12',
                //'nativeFormattedPrice' => '￥1,234,568.12',
            ],
            'Australia' => [
                'localeCode' => 'en-US',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'AUD',
                'formattedPrice' => '$1,234,568.12',
            ],
            'Brazil' => [
                'localeCode' => 'pt-BR',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'BRL',
                'formattedPrice' => "R\$\u{a0}1.234.568,12",
            ],
            'Mexico' => [
                'localeCode' => 'es-MX',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'MXN',
                'formattedPrice' => '$1,234,568.12',
            ],
            'Russia' => [
                'localeCode' => 'ru-RU',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'RUB',
                'formattedPrice' => "1\u{a0}234\u{a0}568,12\u{a0}₽",
            ],
            'Italy' => [
                'localeCode' => 'it-IT',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'EUR',
                'formattedPrice' => "1.234.568,12\u{a0}€",
            ],
            'Poland' => [
                'localeCode' => 'pl-PL',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'PLN',
                'formattedPrice' => "1\u{a0}234\u{a0}568,12\u{a0}zł",
            ],
            'Bulgaria' => [
                'localeCode' => 'bg-BG',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'BGN',
                'formattedPrice' => "1234568,12\u{a0}лв.",
            ],
            // BGN does not have a symbol in en-US
            'United States BGN' => [
                'localeCode' => 'en-US',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'BGN',
                'formattedPrice' => 'BGN1,234,568.12',
            ],
            'Azerbaijani' => [
                'localeCode' => 'az-AZ',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'AZN',
                'formattedPrice' => "1.234.568,12\u{a0}₼",
            ],
            // BGN does not have a symbol in en-US
            'United States AZN' => [
                'localeCode' => 'en-US',
                'rawNumber' => 1234568.12345,
                'currencyCode' => 'AZN',
                'formattedPrice' => '₼1,234,568.12',
            ],
        ];
    }
}
