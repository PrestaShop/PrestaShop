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

namespace Tests\Unit\Core\Localization\CLDR;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Currency;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleDataSource;
use PrestaShop\PrestaShop\Core\Localization\CLDR\NumberSymbolsData;
use PrestaShop\PrestaShop\Core\Localization\DataLayer\CldrLocaleDataLayerInterface;

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
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->frStubLocaleData = $this->getStubLocaleData('fr-FR');
        $this->enStubLocaleData = $this->getStubLocaleData('en-US');

        $layer = $this->createMock(CldrLocaleDataLayerInterface::class);
        $layer->method('read')
            ->willReturnMap([
                ['fr-FR', $this->frStubLocaleData],
                ['en-US', $this->enStubLocaleData],
            ]);

        $this->localeDataSource = new LocaleDataSource([$layer]);
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
        $localeData                         = new LocaleData();
        $localeData->numberingSystems       = ['latn'];
        $localeData->defaultNumberingSystem = 'latn';
        $localeData->minimumGroupingDigits  = 1;
        $localeData->decimalPatterns        = ['latn' => '#,##0.###'];
        $localeData->percentPatterns        = ['latn' => '#,##0.### %'];

        $stubSymbolsData                         = new NumberSymbolsData();
        $stubSymbolsData->list                   = ';';
        $stubSymbolsData->percentSign            = '%';
        $stubSymbolsData->minusSign              = '-';
        $stubSymbolsData->plusSign               = '+';
        $stubSymbolsData->exponential            = '^';
        $stubSymbolsData->superscriptingExponent = 'E';
        $stubSymbolsData->perMille               = '‰';
        $stubSymbolsData->infinity               = '∞';
        $stubSymbolsData->nan                    = 'NaN';
        $stubSymbolsData->timeSeparator          = ':';

        $stubCurrencyData                 = new CurrencyData();
        $stubCurrencyData->isoCode        = 'PCE';
        $stubCurrencyData->numericIsoCode = 333;
        $stubCurrencyData->decimalDigits  = 2;

        // Locale-specific data
        switch ($localeCode) {
            case 'fr-FR':
                $localeData->localeCode       = 'fr-FR';
                $localeData->currencyPatterns = ['latn' => '#,##0.00# ¤'];

                $stubSymbolsData->decimal       = ',';
                $stubSymbolsData->group         = ' ';

                $stubCurrencyData->displayNames = [
                    'default' => 'Paix PrestaShop',
                    'one'     => 'paix',
                    'other'   => 'paix',
                ];
                $stubCurrencyData->symbols      = [
                    Currency::SYMBOL_TYPE_DEFAULT => '☮PS',
                    Currency::SYMBOL_TYPE_NARROW  => '☮',
                ];
                break;

            case 'en-US':
                $localeData->localeCode = 'en-US';
                $localeData->currencyPatterns = ['latn' => '¤#,##0.00#'];

                $stubSymbolsData->decimal = '.';
                $stubSymbolsData->group   = ',';

                $stubCurrencyData->displayNames = [
                    'default' => 'PrestaShop Peace',
                    'one'     => 'peace',
                    'other'   => 'peaces',
                ];
                $stubCurrencyData->symbols      = [
                    Currency::SYMBOL_TYPE_DEFAULT => 'PS☮',
                    Currency::SYMBOL_TYPE_NARROW  => '☮',
                ];
                break;

            default:
                return null;
        }

        $localeData->numberSymbols = ['latn' => $stubSymbolsData];
        $localeData->currencies    = ['PCE' => $stubCurrencyData];

        return $localeData;
    }
}
