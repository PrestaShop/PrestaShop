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
use PrestaShopBundle\Currency\DataSource\Cache as CurrencyCacheDataSource;
use PrestaShopBundle\Currency\DataSource\CLDR as CurrencyCLDRDataSource;
use PrestaShopBundle\Currency\Manager as CurrencyManager;
use PrestaShopBundle\Currency\Repository as CurrencyRepository;
use PrestaShopBundle\Localization\CLDR\DataReader;
use PrestaShopBundle\Localization\Manager as LocaleManager;

class LocaleTest extends TestCase
{
    /**
     * @var LocaleManager
     */
    protected $localeManager;

    public function setUp()
    {
        $currencyCacheData = new CurrencyCacheDataSource('fr-FR');
        $currencyCache = new CurrencyRepository([$currencyCacheData]);

        $currencyCLDRData = new CurrencyCLDRDataSource('fr-FR', new DataReader());
        $currencyCLDR = new CurrencyRepository([$currencyCLDRData]);

        $currencyManager = new CurrencyManager($currencyCache, $currencyCLDR);
        $this->localeManager = new LocaleManager($currencyManager);
    }

    /**
     * @param $floatNumber
     * @param $expectedFormats
     *
     * @dataProvider provideValidNumberFormatsFromFloat
     */
    public function testFormatNumberWithFloat($floatNumber, $expectedFormats)
    {
        $cachedLocales = array();

        foreach ($expectedFormats as $localeCode => $format) {
            if (!isset($cachedLocales[$localeCode])) {
                $cachedLocales[$localeCode] = $this->localeManager->getLocale($localeCode);
            }
            $locale = $cachedLocales[$localeCode];
            $this->assertSame($format, $locale->formatNumber($floatNumber));
        }
    }

    public function provideValidNumberFormatsFromFloat()
    {
        return [
            [
                'number'  => 1234560.789,
                'formats' => [
                    'en-GB' => '1,234,560.789',
                    'en-US' => '1,234,560.789',
                    'fr-FR' => '1 234 560,789',
                ],
            ],
        ];
    }

    /**
     * @param $floatNumber
     * @param $expectedFormats
     *
     * @dataProvider provideValidNumberFormatsFromString
     */
    public function testFormatNumberWithString($floatNumber, $expectedFormats)
    {
        // TODO
    }
}
