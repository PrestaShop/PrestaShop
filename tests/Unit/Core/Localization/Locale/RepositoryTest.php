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

namespace Tests\Unit\Core\Localization\Locale;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleInterface as CldrLocaleInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository as CldrLocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\CLDR\NumberSymbolsData;
use PrestaShop\PrestaShop\Core\Localization\Currency;
use PrestaShop\PrestaShop\Core\Localization\Currency\Repository as CurrencyRepository;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;

class RepositoryTest extends TestCase
{
    /**
     * An instance of the tested LocaleRepository class
     *
     * @var LocaleRepository
     */
    protected $localeRepository;

    protected function setUp(): void
    {
        /**
         * Mock the LocaleRepository dependencies :
         */
        $symbolsDataStub = new NumberSymbolsData();
        $symbolsDataStub->setDecimal(',');
        $symbolsDataStub->setGroup(' ');
        $symbolsDataStub->setList(';');
        $symbolsDataStub->setPercentSign('%');
        $symbolsDataStub->setMinusSign('-');
        $symbolsDataStub->setPlusSign('+');
        $symbolsDataStub->setExponential('E');
        $symbolsDataStub->setSuperscriptingExponent('^');
        $symbolsDataStub->setPerMille('‰');
        $symbolsDataStub->setInfinity('∞');
        $symbolsDataStub->setNan('NaN');

        /** CLDR Locale data object */
        $cldrLocale = $this->createMock(CldrLocaleInterface::class);
        $cldrLocale
            ->method('getAllNumberSymbols')
            ->willReturn(['latn' => $symbolsDataStub]);

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
        $currencyRepository->method('getAvailableCurrencies')
            ->willReturn([$currency]);
        $currencyRepository
            ->method('getAllInstalledCurrencies')
            ->willReturn([$currency]);

        /* @var CldrLocaleRepository $cldrLocaleRepository */
        /* @var CurrencyRepository $currencyRepository */
        $this->localeRepository = new LocaleRepository($cldrLocaleRepository, $currencyRepository, 'fr-FR');
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
     */
    public function testGetLocaleWithInvalidLocaleCode()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException::class);

        $this->localeRepository->getLocale('en-US');
    }
}
