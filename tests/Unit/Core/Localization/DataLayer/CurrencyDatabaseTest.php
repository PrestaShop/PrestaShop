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

namespace Tests\Unit\Core\Localization\DataLayer;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Adapter\Entity\Currency;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\Currency\LocalizedCurrencyId;
use PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer\CurrencyDatabase as CurrencyDatabaseDataLayer;
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
     * @var CurrencyDataProvider
     */
    protected $fakeDataProvider;

    protected function setUp()
    {
        $this->fakeFrEuro                   = $this->createMock(Currency::class);
        $this->fakeFrEuro->iso_code         = 'EUR';
        $this->fakeFrEuro->numeric_iso_code = '978';
        $this->fakeFrEuro->symbol           = '€';
        $this->fakeFrEuro->name             = 'euro';
        $this->fakeFrEuro->precision        = 2;

        $this->fakeDataProvider = $this->createMock(CurrencyDataProvider::class);
        $this->fakeDataProvider->method('getCurrencyByIsoCode')
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
    public function testRead()
    {
        /** @var CurrencyData $currencyData */
        /** @noinspection PhpUnhandledExceptionInspection */
        $currencyData = $this->layer->read(new LocalizedCurrencyId('EUR', 'fr-FR'));
        /** @noinspection end */

        $this->assertSame(
            $this->fakeFrEuro->iso_code,
            $currencyData->isoCode
        );

        $this->assertSame(
            $this->fakeFrEuro->name,
            $currencyData->names['fr-FR']
        );

        $this->assertSame(
            $this->fakeFrEuro->symbol,
            $currencyData->symbols['fr-FR']
        );

        // FOO is unknown
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertNull($this->layer->read(new LocalizedCurrencyId('FOO', 'fr-FR')));
        /** @noinspection end */
    }

    /**
     * Given a valid CurrencyDatabase layer object
     * When asking it to write Currency data
     * Then it should call the expected write method on its data provider
     *
     * @throws LocalizationException
     */
    public function testWrite()
    {
        $someCurrencyData = new CurrencyData();

        $this->fakeDataProvider->expects($this->once())
            ->method('saveCurrency')
            ->with($this->isInstanceOf(Currency::class));

        $writableLayer = new CurrencyDatabaseDataLayer($this->fakeDataProvider, 'fr-FR');
        $writableLayer->write(
            new LocalizedCurrencyId('FOO', 'fr-FR'),
            $someCurrencyData
        ); // Should trigger saveCurrency() on data provider
    }
}
