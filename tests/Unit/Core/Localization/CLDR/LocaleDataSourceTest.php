<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\CLDR;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleDataLayerInterface as CldrLocaleDataLayerInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleDataSource;
use PrestaShop\PrestaShop\Core\Localization\CLDR\NumberSymbolsData;

class LocaleDataSourceTest extends TestCase
{
    /**
     * An instance of the tested CLDR LocaleDataSource class
     *
     * This LocaleDataSource instance has been populated with known dependencies / data.
     *
     * @var LocaleDataSource
     */
    protected $localeDataSource;

    /**
     * A stub LocaleData object with FR data
     *
     * @var LocaleData
     */
    protected $frStubLocaleData;

    /**
     * A stub LocaleData object with EN data
     *
     * @var LocaleData
     */
    protected $enStubLocaleData;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->frStubLocaleData = $this->getStubLocaleData('fr-FR');
        $this->enStubLocaleData = $this->getStubLocaleData('en-US');

        $layer = $this->createMock(CldrLocaleDataLayerInterface::class);
        $layer->method('read')
            ->willReturnMap([
                ['fr-FR', $this->frStubLocaleData],
                ['en-US', $this->enStubLocaleData],
            ]);

        $this->localeDataSource = new LocaleDataSource($layer);
    }

    /**
     * Given a valid CLDR LocaleDataSource object and a valid locale code
     * When asking a specific locale data
     * Then the expected CLDR LocaleData object should be returned, or null if the locale code was not found
     */
    public function testGetLocaleData()
    {
        $this->assertSame($this->frStubLocaleData, $this->localeDataSource->getLocaleData('fr-FR'));
        $this->assertSame($this->enStubLocaleData, $this->localeDataSource->getLocaleData('en-US'));
        $this->assertNull($this->localeDataSource->getLocaleData('foobar'));
    }

    protected function getStubLocaleData($localeCode)
    {
        // Common data
        $localeData = new LocaleData();
        $localeData->setNumberingSystems(['latn']);
        $localeData->setDefaultNumberingSystem('latn');
        $localeData->setMinimumGroupingDigits(1);
        $localeData->setDecimalPatterns(['latn' => '#,##0.###']);
        $localeData->setPercentPatterns(['latn' => '#,##0.### %']);

        $stubSymbolsData = new NumberSymbolsData();
        $stubSymbolsData->setList(';');
        $stubSymbolsData->setPercentSign('%');
        $stubSymbolsData->setMinusSign('-');
        $stubSymbolsData->setPlusSign('+');
        $stubSymbolsData->setExponential('^');
        $stubSymbolsData->setSuperscriptingExponent('E');
        $stubSymbolsData->setPerMille('‰');
        $stubSymbolsData->setInfinity('∞');
        $stubSymbolsData->setNan('NaN');
        $stubSymbolsData->setTimeSeparator(':');

        $stubCurrencyData = new CurrencyData();
        $stubCurrencyData->setIsoCode('PCE');
        $stubCurrencyData->setNumericIsoCode('333');
        $stubCurrencyData->setDecimalDigits(2);

        // Locale-specific data
        switch ($localeCode) {
            case 'fr-FR':
                $localeData->setLocaleCode('fr-FR');
                $localeData->setCurrencyPatterns(['latn' => '#,##0.00# ¤']);

                $stubSymbolsData->setDecimal(',');
                $stubSymbolsData->setGroup(' ');

                $stubCurrencyData->setDisplayNames([
                    'default' => 'Paix PrestaShop',
                    'one' => 'paix',
                    'other' => 'paix',
                ]);
                $stubCurrencyData->setSymbols([
                    CurrencyInterface::SYMBOL_TYPE_DEFAULT => '☮PS',
                    CurrencyInterface::SYMBOL_TYPE_NARROW => '☮',
                ]);
                break;

            case 'en-US':
                $localeData->setLocaleCode('en-US');
                $localeData->setCurrencyPatterns(['latn' => '¤#,##0.00#']);

                $stubSymbolsData->setDecimal('.');
                $stubSymbolsData->setGroup(',');

                $stubCurrencyData->setDisplayNames([
                    'default' => 'PrestaShop Peace',
                    'one' => 'peace',
                    'other' => 'peaces',
                ]);
                $stubCurrencyData->setSymbols([
                    CurrencyInterface::SYMBOL_TYPE_DEFAULT => 'PS☮',
                    CurrencyInterface::SYMBOL_TYPE_NARROW => '☮',
                ]);
                break;

            default:
                return null;
        }

        $localeData->setNumberSymbols(['latn' => $stubSymbolsData]);
        $localeData->setCurrencies(['PCE' => $stubCurrencyData]);

        return $localeData;
    }
}
