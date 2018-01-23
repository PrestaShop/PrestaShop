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
use PrestaShop\PrestaShop\Core\Localization\CLDR\Number as CldrNumber;
use PrestaShop\PrestaShop\Core\Localization\CLDR\NumberRepository as CldrNumberRepository;
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
        /** CLDR Number data bag */
        $cldrNumber = $this->getMockBuilder(CldrNumber::class)
            ->setMethods([
                'getPositivePattern',
                'getNegativePattern',
                'getSymbols',
                'getMaxFractionDigits',
                'getMinFractionDigits',
                'getGroupingUsed',
                'getPrimaryGroupSize',
                'getSecondaryGroupSize',
            ])
            ->getMock();
        $cldrNumber->method('getPositivePattern')->willReturn('');
        $cldrNumber->method('getNegativePattern')->willReturn('');
        $cldrNumber->method('getSymbols')->willReturn([]);
        $cldrNumber->method('getMaxFractionDigits')->willReturn(3);
        $cldrNumber->method('getMinFractionDigits')->willReturn(0);
        $cldrNumber->method('getGroupingUsed')->willReturn(true);
        $cldrNumber->method('getPrimaryGroupSize')->willReturn(3);
        $cldrNumber->method('getSecondaryGroupSize')->willReturn(3);

        /** CLDR NumberRepository (returning the data bag) */
        $cldrNumberRepository = $this->getMockBuilder(CldrNumberRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cldrNumberRepository->method('getNumber')
            ->willReturnMap([
                ['fr-FR', $cldrNumber],
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

        /** @var CldrNumberRepository $cldrNumberRepository */
        /** @var CurrencyRepository $currencyRepository */
        $this->localeRepository = new LocaleRepository($cldrNumberRepository, $currencyRepository);
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
        $locale = $this->localeRepository->getLocale('en-US');

        $this->assertInstanceOf(Locale::class, $locale);
    }
}
