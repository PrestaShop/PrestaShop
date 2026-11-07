<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Db;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreConfig;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\Resetter\ShopResetter;
use Zone;

/**
 * Deleting a zone from some shops only keeps it in the others, with its carriers, delivery prices, countries
 * and states. Deleting it from its last shop detaches them.
 */
class ZoneMultistoreDeleteTest extends KernelTestCase
{
    private const DEFAULT_SHOP_ID = 1;

    private static int $secondShopId;

    private int $zoneId;

    private int $carrierId;

    private int $countryId;

    private int $stateId;

    /** @var array{country: int, state: int} */
    private array $originalZones;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        ShopResetter::resetShops();
        Configuration::updateGlobalValue(MultistoreConfig::FEATURE_STATUS, 1);
        $shop = new Shop();
        $shop->name = 'Zone test shop 2';
        $shop->id_shop_group = (int) Shop::getGroupFromShop(self::DEFAULT_SHOP_ID, true);
        $shop->id_category = 2;
        $shop->active = true;
        $shop->save();
        self::$secondShopId = (int) $shop->id;
        Shop::resetStaticCache();
    }

    public static function tearDownAfterClass(): void
    {
        ShopResetter::resetShops();
        Shop::resetStaticCache();
        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();
        Shop::resetContext();
        Shop::setContext(Shop::CONTEXT_ALL);

        $zone = new Zone();
        $zone->name = 'Multistore zone';
        $zone->id_shop_list = [self::DEFAULT_SHOP_ID, self::$secondShopId];
        $zone->add();
        $this->zoneId = (int) $zone->id;

        $db = Db::getInstance();
        $this->carrierId = (int) $db->getValue('SELECT id_carrier FROM ' . _DB_PREFIX_ . 'carrier ORDER BY id_carrier');
        $this->countryId = (int) $db->getValue('SELECT id_country FROM ' . _DB_PREFIX_ . 'country ORDER BY id_country');
        $this->stateId = (int) $db->getValue('SELECT id_state FROM ' . _DB_PREFIX_ . 'state ORDER BY id_state');
        $this->originalZones = [
            'country' => (int) $db->getValue('SELECT id_zone FROM ' . _DB_PREFIX_ . 'country WHERE id_country = ' . $this->countryId),
            'state' => (int) $db->getValue('SELECT id_zone FROM ' . _DB_PREFIX_ . 'state WHERE id_state = ' . $this->stateId),
        ];

        $db->insert('carrier_zone', ['id_carrier' => $this->carrierId, 'id_zone' => $this->zoneId]);
        $db->insert('delivery', ['id_carrier' => $this->carrierId, 'id_zone' => $this->zoneId, 'price' => 5]);
        $db->update('country', ['id_zone' => $this->zoneId], 'id_country = ' . $this->countryId);
        $db->update('state', ['id_zone' => $this->zoneId], 'id_state = ' . $this->stateId);
    }

    protected function tearDown(): void
    {
        $db = Db::getInstance();
        foreach (['carrier_zone', 'delivery', 'zone_shop', 'zone'] as $table) {
            $db->delete($table, 'id_zone = ' . $this->zoneId);
        }
        $db->update('country', ['id_zone' => $this->originalZones['country']], 'id_country = ' . $this->countryId);
        $db->update('state', ['id_zone' => $this->originalZones['state']], 'id_state = ' . $this->stateId);
        parent::tearDown();
    }

    public function testDeleteFromOneShopKeepsTheZoneAndItsRelations(): void
    {
        $zone = new Zone($this->zoneId);
        $zone->id_shop_list = [self::$secondShopId];
        $this->assertTrue($zone->delete());

        $this->assertSame(1, $this->countRows('zone'));
        $this->assertSame([self::DEFAULT_SHOP_ID], array_map('intval', array_column(
            Db::getInstance()->executeS('SELECT id_shop FROM ' . _DB_PREFIX_ . 'zone_shop WHERE id_zone = ' . $this->zoneId),
            'id_shop'
        )));
        $this->assertSame(1, $this->countRows('carrier_zone'));
        $this->assertSame(1, $this->countRows('delivery'));
        $this->assertSame($this->zoneId, $this->zoneOf('country', 'id_country', $this->countryId));
        $this->assertSame($this->zoneId, $this->zoneOf('state', 'id_state', $this->stateId));
    }

    public function testDeleteFromEveryShopDetachesTheRelations(): void
    {
        $zone = new Zone($this->zoneId);
        $zone->id_shop_list = [self::DEFAULT_SHOP_ID, self::$secondShopId];
        $this->assertTrue($zone->delete());

        $this->assertSame(0, $this->countRows('zone'));
        $this->assertSame(0, $this->countRows('zone_shop'));
        $this->assertSame(0, $this->countRows('carrier_zone'));
        $this->assertSame(0, $this->countRows('delivery'));
        $this->assertSame(0, $this->zoneOf('country', 'id_country', $this->countryId));
        $this->assertSame(0, $this->zoneOf('state', 'id_state', $this->stateId));
    }

    private function countRows(string $table): int
    {
        return (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . $table . ' WHERE id_zone = ' . $this->zoneId);
    }

    private function zoneOf(string $table, string $primary, int $id): int
    {
        return (int) Db::getInstance()->getValue('SELECT id_zone FROM ' . _DB_PREFIX_ . $table . ' WHERE ' . $primary . ' = ' . $id);
    }
}
