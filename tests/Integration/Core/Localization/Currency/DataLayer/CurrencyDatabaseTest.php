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

namespace Tests\Integration\Core\Localization\Currency\DataLayer;

use Currency;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer\CurrencyDatabase as CurrencyDatabaseDataLayer;
use PrestaShop\PrestaShop\Core\Localization\Currency\LocalizedCurrencyId;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

class CurrencyDatabaseTest extends TestCase
{
    /**
     * The tested data layer
     *
     * @var CurrencyDatabaseDataLayer
     */
    protected $layer;

    /**
     * Fake PrestaShop Currency entity with french flavor EUR data
     *
     * @var Currency
     */
    protected $fakeFrEuro;

    /**
     * @var CurrencyDataProvider|MockObject
     */
    protected $fakeDataProvider;

    protected function setUp(): void
    {
        $this->fakeFrEuro = $this->createMock(Currency::class);
        $this->fakeFrEuro->iso_code = 'EUR';
        $this->fakeFrEuro->numeric_iso_code = '978';
        $this->fakeFrEuro->symbol = '€';
        $this->fakeFrEuro->name = 'euro';
        $this->fakeFrEuro->precision = 2;

        $this->fakeDataProvider = $this->createMock(CurrencyDataProvider::class);
        $this->fakeDataProvider->method('getCurrencyByIsoCodeAndLocale')
            ->willReturnMap([
                ['EUR', 'fr-FR', $this->fakeFrEuro],
            ]);
        $this->fakeDataProvider->method('getCurrencyByIsoCodeOrCreate')
            ->willReturnMap([
                ['FOO', 'fr-FR', $this->createMock(Currency::class)],
            ]);

        $this->layer = new CurrencyDatabaseDataLayer($this->fakeDataProvider);
    }

    /**
     * Given a valid CurrencyDatabase data layer
     * When asking this layer for data of a given currency
     * Then the expected CurrencyData object should be retrieved, or null if unknown.
     */
    public function testRead(): void
    {
        /** @var CurrencyData $currencyData */
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @phpstan-ignore-next-line */
        $currencyData = $this->layer->read(new LocalizedCurrencyId('EUR', 'fr-FR'));
        /* @noinspection end */

        $this->assertSame(
            $this->fakeFrEuro->iso_code,
            $currencyData->getIsoCode()
        );

        $this->assertSame(
            $this->fakeFrEuro->name,
            $currencyData->getNames()['fr-FR']
        );

        $this->assertSame(
            $this->fakeFrEuro->symbol,
            $currencyData->getSymbols()['fr-FR']
        );

        // FOO is unknown
        /* @noinspection PhpUnhandledExceptionInspection */
        /* @phpstan-ignore-next-line */
        $this->assertNull($this->layer->read(new LocalizedCurrencyId('FOO', 'fr-FR')));
        /* @noinspection end */
    }

    /**
     * This layer is not writable, it should not call any persistence methods
     *
     * @throws LocalizationException
     */
    public function testWrite(): void
    {
        $someCurrencyData = new CurrencyData();

        $this->fakeDataProvider->expects($this->never())
            ->method('saveCurrency')
            ->with($this->isInstanceOf(Currency::class));

        $writableLayer = new CurrencyDatabaseDataLayer($this->fakeDataProvider);
        $writableLayer->write(
            new LocalizedCurrencyId('FOO', 'fr-FR'),
            $someCurrencyData
        ); // Should trigger saveCurrency() on data provider
    }
}
