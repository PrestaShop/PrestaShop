<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cache;
use Carrier;
use Cart;
use Configuration;
use Context;
use Country;
use Currency;
use Db;
use Group;
use RangeWeight;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * Pins which carriers the native checkout lists, because CarrierFormDataHandler requires a range exactly
 * for the carriers this method can return: a module carrier with need_range off is never listed, with or
 * without a range, and every other carrier is listed only once it has one.
 */
class CarrierListedForOrderTest extends KernelTestCase
{
    private const TABLES = [
        'carrier',
        'carrier_group',
        'carrier_lang',
        'carrier_shop',
        'carrier_tax_rules_group_shop',
        'carrier_zone',
        'delivery',
        'module_carrier',
        'range_price',
        'range_weight',
    ];

    private ?Cart $previousCart = null;

    private ?Currency $previousCurrency = null;

    public static function setUpBeforeClass(): void
    {
        DatabaseDump::restoreTables(self::TABLES);
    }

    public static function tearDownAfterClass(): void
    {
        DatabaseDump::restoreTables(self::TABLES);
    }

    protected function setUp(): void
    {
        parent::setUp();
        // The shipping cost lookup resolves services through ContainerFinder, which reads the global kernel.
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        // getCarriers() and getAvailableCarrierList() memoise per SQL string, so without this every data set
        // would be answered with the carrier list of the first one.
        Cache::clean('Carrier::*');

        $context = Context::getContext();
        $this->previousCart = $context->cart;
        $this->previousCurrency = $context->currency;
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->cart = new Cart();
        $context->cart->id_currency = (int) $context->currency->id;
        $context->cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
    }

    protected function tearDown(): void
    {
        $context = Context::getContext();
        $context->cart = $this->previousCart;
        $context->currency = $this->previousCurrency;
        parent::tearDown();
    }

    /**
     * @dataProvider provideCarriers
     */
    public function testWhichCarriersTheCheckoutLists(bool $isModule, bool $needRange, bool $withRange, bool $expectedListed): void
    {
        $zoneId = (int) Country::getIdZone((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        $carrierId = $this->createCarrier($isModule, $needRange, $withRange, $zoneId);

        $errors = [];
        $listed = array_map(
            static fn (array $row): int => (int) $row['id_carrier'],
            Carrier::getCarriersForOrder($zoneId, null, Context::getContext()->cart, $errors)
        );

        $this->assertSame($expectedListed, in_array($carrierId, $listed, true));
    }

    public function provideCarriers(): iterable
    {
        yield 'shop carrier with a range' => [false, false, true, true];
        yield 'shop carrier without a range' => [false, false, false, false];
        yield 'module carrier keeping need_range, with a range' => [true, true, true, true];
        yield 'module carrier keeping need_range, without a range' => [true, true, false, false];
        yield 'module carrier with need_range off, with a range' => [true, false, true, false];
        yield 'module carrier with need_range off, without a range' => [true, false, false, false];
    }

    private function createCarrier(bool $isModule, bool $needRange, bool $withRange, int $zoneId): int
    {
        $languageId = (int) Configuration::get('PS_LANG_DEFAULT');
        $carrier = new Carrier();
        $carrier->name = 'CarrierListedForOrderTest';
        $carrier->active = true;
        $carrier->is_module = $isModule;
        // The filter under test reads is_module and need_range only. shipping_external stays off so the
        // price comes from the range and no module has to be installed to answer for it.
        $carrier->shipping_external = false;
        $carrier->need_range = $needRange;
        $carrier->is_free = false;
        $carrier->shipping_method = Carrier::SHIPPING_METHOD_WEIGHT;
        $carrier->range_behavior = false;
        $carrier->delay = [$languageId => 'test'];
        $carrier->add();
        $carrier->addZone($zoneId);

        $groups = [];
        foreach (Group::getGroups($languageId) as $group) {
            $groups[] = ['id_carrier' => (int) $carrier->id, 'id_group' => (int) $group['id_group']];
        }
        Db::getInstance()->insert('carrier_group', $groups);

        if ($withRange) {
            $range = new RangeWeight();
            $range->id_carrier = (int) $carrier->id;
            $range->delimiter1 = 0;
            $range->delimiter2 = 10000;
            $range->add();
            Db::getInstance()->insert('delivery', [
                'id_carrier' => (int) $carrier->id,
                'id_range_weight' => (int) $range->id,
                'id_range_price' => null,
                'id_zone' => $zoneId,
                'price' => 5,
            ], true);
        }

        return (int) $carrier->id;
    }
}
