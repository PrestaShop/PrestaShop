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

namespace Tests\Unit\Core\Localization\Locale;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Locale as CldrLocale;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository as CldrLocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Currency;
use PrestaShop\PrestaShop\Core\Localization\Currency\Repository as CurrencyRepository;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;

class RepositoryTest extends TestCase
{
    /**
     * @var LocaleRepository
     */
    protected $localeRepository;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        /**
         * Mock the LocaleRepository dependencies :
         */
        /** CLDR Locale data object */
        $cldrLocale = $this->getMockBuilder(CldrLocale::class)
            ->setMethods([
                'getNumberPositivePattern',
                'getNumberNegativePattern',
                'getNumberSymbols',
                'getNumberMaxFractionDigits',
                'getNumberMinFractionDigits',
                'getNumberGroupingUsed',
                'getNumberPrimaryGroupSize',
                'getNumberSecondaryGroupSize',
            ])
            ->getMock();
        $cldrLocale->method('getNumberPositivePattern')->willReturn('');
        $cldrLocale->method('getNumberNegativePattern')->willReturn('');
        $cldrLocale->method('getNumberSymbols')->willReturn([]);
        $cldrLocale->method('getNumberMaxFractionDigits')->willReturn(3);
        $cldrLocale->method('getNumberMinFractionDigits')->willReturn(0);
        $cldrLocale->method('getNumberGroupingUsed')->willReturn(true);
        $cldrLocale->method('getNumberPrimaryGroupSize')->willReturn(3);
        $cldrLocale->method('getNumberSecondaryGroupSize')->willReturn(3);

        /** CLDR LocaleRepository (returning the data object) */
        $cldrLocaleRepository = $this->getMockBuilder(CldrLocaleRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cldrLocaleRepository->method('getLocale')
            ->willReturnMap([
                ['fr-FR', $cldrLocale],
                ['en-US', null],
            ]);

        /** Currency */
        $currency = $this->getMockBuilder(Currency::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency->method('getSymbol')
            ->willReturn('€');
        $currency->method('getIsoCode')
            ->willReturn('EUR');

        /** CurrencyRepository (returning Currencies ) */
        $currencyRepository = $this->getMockBuilder(CurrencyRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currencyRepository->method('getInstalledCurrencies')
            ->willReturn([$currency]);

        /** @var CldrLocaleRepository $cldrLocaleRepository */
        /** @var CurrencyRepository $currencyRepository */
        $this->localeRepository = new LocaleRepository($cldrLocaleRepository, $currencyRepository);
    }

    /**
     * Given a valid locale code
     * When asking the repository for the corresponding locale
     * Then the expected Locale instance should be retrieved
     */
    public function testGetLocale()
    {
        $locale = $this->localeRepository->getLocale('fr-FR');

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertSame('fr-FR', $locale->getCode());
    }

    /**
     * Given an invalid locale code
     * When asking the repository for the corresponding locale
     * Then an exception should be raised
     *
     * @expectedException \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testGetLocaleWithInvalidLocaleCode()
    {
        $this->localeRepository->getLocale('en-US');
    }
}
