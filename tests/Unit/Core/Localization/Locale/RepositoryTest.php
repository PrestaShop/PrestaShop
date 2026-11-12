<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\Locale;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleInterface as CldrLocaleInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository as CldrLocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\CLDR\NumberSymbolsData;
use PrestaShop\PrestaShop\Core\Localization\Currency;
use PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer\CurrencyInstalled;
use PrestaShop\PrestaShop\Core\Localization\Currency\Repository as CurrencyRepository;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
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
        $cldrLocale
            ->method('getDecimalPattern')
            ->willReturn('');
        $cldrLocale
            ->method('getCurrencyPattern')
            ->willReturn('');

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
     */
    public function testGetLocaleWithInvalidLocaleCode()
    {
        $this->expectException(LocalizationException::class);

        $this->localeRepository->getLocale('en-US');
    }

    /**
     * Given installed currency codes are provided
     * When formatting a price
     * Then only the requested currency is built, and a currency that is not installed is rejected
     */
    public function testPriceSpecificationsAreBuiltOnFirstUse()
    {
        $cldrLocale = $this->createMock(CldrLocaleInterface::class);
        $symbols = new NumberSymbolsData();
        $symbols->setDecimal(',');
        $symbols->setGroup(' ');
        $symbols->setList(';');
        $symbols->setPercentSign('%');
        $symbols->setMinusSign('-');
        $symbols->setPlusSign('+');
        $symbols->setExponential('E');
        $symbols->setSuperscriptingExponent('^');
        $symbols->setPerMille('‰');
        $symbols->setInfinity('∞');
        $symbols->setNan('NaN');
        $cldrLocale->method('getAllNumberSymbols')->willReturn(['latn' => $symbols]);
        $cldrLocale->method('getDecimalPattern')->willReturn('#,##0.###');
        $cldrLocale->method('getCurrencyPattern')->willReturn('#,##0.00 ¤');
        $cldrLocaleRepository = $this->createMock(CldrLocaleRepository::class);
        $cldrLocaleRepository->method('getLocale')->willReturn($cldrLocale);

        $currency = $this->createMock(Currency::class);
        $currency->method('getSymbol')->willReturn('€');
        $currency->method('getIsoCode')->willReturn('EUR');
        $currency->method('getDecimalPrecision')->willReturn(2);

        $currencyRepository = $this->createMock(CurrencyRepository::class);
        $currencyRepository->expects($this->never())->method('getAllInstalledCurrencies');
        $currencyRepository->expects($this->once())
            ->method('getCurrency')
            ->with('EUR', 'fr-FR')
            ->willReturn($currency);

        $installedCurrencies = $this->createMock(CurrencyInstalled::class);
        $installedCurrencies->method('getAllInstalledCurrencyIsoCodes')->willReturn(['EUR']);

        $locale = (new LocaleRepository($cldrLocaleRepository, $currencyRepository, installedCurrencies: $installedCurrencies))
            ->getLocale('fr-FR');

        $this->assertSame($locale->getPriceSpecification('EUR'), $locale->getPriceSpecification('EUR'));

        $this->expectException(LocalizationException::class);
        $locale->formatPrice(1, 'USD');
    }
}
