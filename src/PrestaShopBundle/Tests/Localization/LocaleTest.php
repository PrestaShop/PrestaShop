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

namespace PrestaShopBundle\Tests\Localization;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Currency\CurrencyCollectionFactory;
use PrestaShopBundle\Currency\DataSource\Cache as CurrencyCacheDataSource;
use PrestaShopBundle\Currency\DataSource\CLDR as CurrencyCLDRDataSource;
use PrestaShopBundle\Currency\Manager as CurrencyManager;
use PrestaShopBundle\Currency\Repository as CurrencyRepository;
use PrestaShopBundle\Localization\CLDR\DataReader;
use PrestaShopBundle\Localization\DataSource\Cache as LocaleCacheDataSource;
use PrestaShopBundle\Localization\DataSource\CLDR as LocaleCLDRDataSource;
use PrestaShopBundle\Localization\Formatter\NumberFactory as NumberFormatterFactory;
use PrestaShopBundle\Localization\Locale;
use PrestaShopBundle\Localization\Manager as LocaleManager;
use PrestaShopBundle\Localization\Repository as LocaleRepository;

class LocaleTest extends TestCase
{
    /**
     * @var LocaleManager
     */
    protected $localeManager;

    public function setUp()
    {
        $currencyCacheData = new CurrencyCacheDataSource('fr-FR');
        $currencyCache     = new CurrencyRepository([$currencyCacheData]);

        $currencyCLDRData = new CurrencyCLDRDataSource('fr-FR', new DataReader());
        $currencyCLDR     = new CurrencyRepository([$currencyCLDRData]);

        $currencyManager = new CurrencyManager($currencyCache, $currencyCLDR);

        $localeCacheData = new LocaleCacheDataSource();
        $localeCache     = new LocaleRepository(
            [$localeCacheData],
            new NumberFormatterFactory(),
            new CurrencyCollectionFactory($currencyManager),
            2 // Half Up
        );

        $localeCLDRData = new LocaleCLDRDataSource(new DataReader());
        $localeCLDR     = new LocaleRepository(
            [$localeCLDRData],
            new NumberFormatterFactory(),
            new CurrencyCollectionFactory($currencyManager),
            2 // Half Up
        );

        $this->localeManager = new LocaleManager($localeCache, $localeCLDR);
    }

    /**
     * @param $floatNumber
     * @param $expectedFormats
     *
     * @dataProvider provideValidNumberFormats
     */
    public function testFormatNumber($floatNumber, $expectedFormats)
    {
        $cachedLocales = array();

        foreach ($expectedFormats as $localeCode => $format) {
            if (!isset($cachedLocales[$localeCode])) {
                $cachedLocales[$localeCode] = $this->localeManager->getLocaleByIsoCode($localeCode);
            }
            /** @var Locale $locale */
            $locale = $cachedLocales[$localeCode];
            $this->assertSame(
                $format,
                $locale->formatNumber($floatNumber),
                "For locale $localeCode."
            );
        }
    }

    /**
     * @param $floatNumber
     * @param $currencyCode
     * @param $expectedFormats
     *
     * @dataProvider provideValidCurrencyFormats
     */
    public function testFormatCurrency($floatNumber, $currencyCode, $expectedFormats)
    {
        $cachedLocales    = array();
        $cachedCurrencies = array();

        foreach ($expectedFormats as $localeCode => $format) {
            if (!isset($cachedLocales[$localeCode])) {
                $cachedLocales[$localeCode] = $this->localeManager->getLocaleByIsoCode($localeCode);
            }

            if (!isset($cachedCurrencies[$currencyCode])) {
                $cachedCurrencies[$currencyCode] = $cachedLocales[$localeCode]->getCurrency($currencyCode);
            }

            /** @var Locale $locale */
            $locale = $cachedLocales[$localeCode];
            $this->assertSame(
                $format,
                $locale->formatCurrency($floatNumber, $currencyCode),
                "For locale $localeCode."
            );
        }
    }

    public function provideValidNumberFormats()
    {
        return [
            [
                'number'  => 1234560.1234,
                'formats' => [
                    'ar-IL' => '1,234,560.123', // Needs to be changed when numbering system is configurable
//                    'bn-IN' => '1,234,560.123', // Needs to be changed when numbering system is configurable
                    'de-CH' => '1’234’560.123',
                    'en-US' => '1,234,560.123',
                    'es-AR' => '1.234.560,123',
                    'fr-FR' => '1 234 560,123',
                ],
            ],
            [
                'number'  => 1234560.9876,
                'formats' => [
                    'ar-IL' => '1,234,560.988', // Needs to be changed when numbering system is configurable
//                    'bn-IN' => '1,234,560.988', // Needs to be changed when numbering system is configurable
                    'de-CH' => '1’234’560.988',
                    'en-US' => '1,234,560.988',
                    'es-AR' => '1.234.560,988',
                    'fr-FR' => '1 234 560,988',
                ],
            ],
            [
                'number'  => 0.7,
                'formats' => [
                    'ar-IL' => '0.7', // Needs to be changed when numbering system is configurable
//                    'bn-IN' => '0.7', // Needs to be changed when numbering system is configurable
                    'de-CH' => '0.7',
                    'en-US' => '0.7',
                    'es-AR' => '0,7',
                    'fr-FR' => '0,7',
                ],
            ],
            [
                'number'  => '0.7000',
                'formats' => [
                    'ar-IL' => '0.7', // Needs to be changed when numbering system is configurable
//                    'bn-IN' => '0.7', // Needs to be changed when numbering system is configurable
                    'de-CH' => '0.7',
                    'en-US' => '0.7',
                    'es-AR' => '0,7',
                    'fr-FR' => '0,7',
                ],
            ],
            [
                'number'  => '1234560.78389',
                'formats' => [
                    'ar-IL' => '1,234,560.784', // Needs to be changed when numbering system is configurable
//                    'bn-IN' => '1,234,560.784', // Needs to be changed when numbering system is configurable
                    'de-CH' => '1’234’560.784',
                    'en-US' => '1,234,560.784',
                    'es-AR' => '1.234.560,784',
                    'fr-FR' => '1 234 560,784',
                ],
            ],
            [
                'number'  => '1234560.7831111',
                'formats' => [
                    'ar-IL' => '1,234,560.783', // Needs to be changed when numbering system is configurable
//                    'bn-IN' => '1,234,560.783', // Needs to be changed when numbering system is configurable
                    'de-CH' => '1’234’560.783',
                    'en-US' => '1,234,560.783',
                    'es-AR' => '1.234.560,783',
                    'fr-FR' => '1 234 560,783',
                ],
            ],
        ];
    }

    public function provideValidCurrencyFormats()
    {
        return [
            [
                'number'   => 1234560.123,
                'currency' => 'INR',
                'formats'  => [
                    'ar-IL' => '1,234,560.12 ₹', // Needs to be changed when numbering system is configurable
//                    'bn-IN' => '1,234,560.12 ₹', // Needs to be changed when numbering system is configurable
                    'de-CH' => '₹ 1’234’560.12',
                    'en-US' => '₹1,234,560.12',
                    'es-AR' => '₹ 1.234.560,12',
                    'fr-FR' => '1 234 560,12 ₹',
                ],
            ],
            [
                'number'   => 1234560.789,
                'currency' => 'JPY',
                'formats'  => [
                    'ar-IL' => '1,234,560.79 ¥', // Needs to be changed when numbering system is configurable
//                    'bn-IN' => '1,234,560.79 ¥', // Needs to be changed when numbering system is configurable
                    'de-CH' => '¥ 1’234’560.79',
                    'en-US' => '¥1,234,560.79',
                    'es-AR' => '¥ 1.234.560,79',
                    'fr-FR' => '1 234 560,79 ¥',
                ],
            ],
            [
                'number'   => '0.7000',
                'currency' => 'GBP',
                'formats'  => [
                    'ar-IL' => '0.70 £', // Needs to be changed when numbering system is configurable
//                    'bn-IN' => '0.70 £', // Needs to be changed when numbering system is configurable
                    'de-CH' => '£ 0.70',
                    'en-US' => '£0.70',
                    'es-AR' => '£ 0,70',
                    'fr-FR' => '0,70 £',
                ],
            ],
            [
                'number'   => '1234560.789898989898123',
                'currency' => 'DEM',
                'formats'  => [
                    'ar-IL' => '1,234,560.79 DEM', // To be changed when numbering system is configurable
//                    'bn-IN' => '1,234,560.79 DEM', // To be changed when numbering system is configurable
                    'de-CH' => 'DEM 1’234’560.79',
                    'en-US' => 'DEM1,234,560.79',
                    'es-AR' => 'DEM 1.234.560,79',
                    'fr-FR' => '1 234 560,79 DEM',
                ],
            ],
            [
                'number'   => 1234560.789,
                'currency' => 'COP',
                'formats'  => [
                    'ar-IL' => '1,234,560.79 $', // Needs to be changed when numbering system is configurable
//                    'bn-IN' => '1,234,560.79 $', // Needs to be changed when numbering system is configurable
                    'de-CH' => '$ 1’234’560.79',
                    'en-US' => '$1,234,560.79',
                    'es-AR' => '$ 1.234.560,79',
                    'fr-FR' => '1 234 560,79 $',
                ],
            ],
        ];
    }
}
