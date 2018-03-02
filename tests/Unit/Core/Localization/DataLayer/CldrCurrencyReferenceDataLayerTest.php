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
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData as CldrCurrencyData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Locale as CldrLocale;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository as CldrLocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\DataLayer\CldrCurrencyReferenceDataLayer;

class CldrCurrencyReferenceDataLayerTest extends TestCase
{
    /**
     * The tested data layer
     *
     * @var CldrCurrencyReferenceDataLayer
     */
    protected $layer;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $stubCurrencyData      = new CurrencyData();
        $stubCurrencyData->foo = ['bar', 'baz'];

        // This fake CLDR Locale will be returned by the fake CLDR LocaleRepository
        $fakeCldrLocale = $this->getMockBuilder(CldrLocale::class)
            ->disableOriginalConstructor()
            ->getMock();
        $fakeCldrLocale->method('getCurrencyData')
            ->willReturnMap([
                ['PCE', $stubCurrencyData],
                ['FOO', null], // Simulates an unknown currency request
            ]);

        // This fake CLDR LocaleRepository will be passed to our tested layer class as a dependency
        $cldrLocaleRepository = $this->getMockBuilder(CldrLocaleRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cldrLocaleRepository
            ->method('getLocale')
            ->with('fr-FR')
            ->willReturn($fakeCldrLocale);

        /** @var CldrLocaleRepository $cldrLocaleRepository */
        $this->layer = new CldrCurrencyReferenceDataLayer($cldrLocaleRepository, 'fr-FR');
    }

    /**
     * Given a valid CldrCurrencyReferenceDataLayer object
     * When asking it for a given currency's data
     * Then the expected CLDR CurrencyData object should be retrieved, of null if currency is unknown.
     */
    public function testRead()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $cldrCurrencyData = $this->layer->read('PCE');
        /** @noinspection end */

        $this->assertInstanceOf(
            CldrCurrencyData::class,
            $cldrCurrencyData
        );

        $this->assertSame(
            ['bar', 'baz'],
            $cldrCurrencyData->foo
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $cldrCurrencyData = $this->layer->read('FOO');
        /** @noinspection end */

        $this->assertNull($cldrCurrencyData);
    }
}
