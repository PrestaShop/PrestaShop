<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Carrier;
use Configuration as LegacyConfiguration;
use Db;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class CarrierReversionPerShopDefaultTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'shop_group', 'carrier', 'carrier_shop', 'carrier_lang', 'configuration'];

    private static int $secondShopId;
    private static int $oldCarrierId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();

        $configuration = self::$kernel->getContainer()->get('prestashop.adapter.legacy.configuration');
        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        $db = Db::getInstance();
        $db->insert('shop_group', [
            'name' => 'test_group_carrier', 'color' => '', 'share_customer' => 0,
            'share_order' => 0, 'share_stock' => 0, 'active' => 1, 'deleted' => 0,
        ]);
        $secondGroupId = (int) $db->Insert_ID();
        $db->insert('shop', [
            'id_shop_group' => $secondGroupId, 'name' => 'test_shop_carrier', 'color' => '',
            'id_category' => 2, 'theme_name' => 'classic', 'active' => 1, 'deleted' => 0,
        ]);
        self::$secondShopId = (int) $db->Insert_ID();
        LegacyShop::resetStaticCache();

        self::$oldCarrierId = self::insertCarrier('Old carrier');

        // Global default stays carrier 1; the second shop overrides it to the carrier we will re-version.
        LegacyConfiguration::updateValue('PS_CARRIER_DEFAULT', 1);
        LegacyConfiguration::updateValue('PS_CARRIER_DEFAULT', self::$oldCarrierId, false, null, self::$secondShopId);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    /**
     * Re-versioning a carrier must move every scope's PS_CARRIER_DEFAULT to the new carrier id.
     * Only the current context's default was updated, so a per-shop override was left pointing at
     * the old carrier id, which no longer exists after the re-version.
     */
    public function testReversionReassignsPerShopDefaultCarrier(): void
    {
        self::bootKernel();
        LegacyConfiguration::resetStaticCache();

        // Sanity: the second shop's default is the old carrier before the re-version.
        self::assertSame(
            self::$oldCarrierId,
            (int) LegacyConfiguration::get('PS_CARRIER_DEFAULT', null, null, self::$secondShopId)
        );

        $newCarrierId = self::insertCarrier('New carrier');
        $newCarrier = new Carrier($newCarrierId);
        $newCarrier->copyCarrierData(self::$oldCarrierId);

        LegacyConfiguration::resetStaticCache();
        self::assertSame(
            $newCarrierId,
            (int) LegacyConfiguration::get('PS_CARRIER_DEFAULT', null, null, self::$secondShopId),
            'the second shop default carrier must follow the re-version to the new id'
        );
    }

    private static function insertCarrier(string $name): int
    {
        $db = Db::getInstance();
        $db->insert('carrier', [
            'id_reference' => 0, 'name' => $name, 'active' => 1, 'deleted' => 0,
            'is_module' => 0, 'need_range' => 0, 'shipping_external' => 0, 'external_module_name' => '',
            'shipping_handling' => 1, 'range_behavior' => 0, 'shipping_method' => 0,
            'max_width' => 0, 'max_height' => 0, 'max_depth' => 0, 'max_weight' => 0, 'grade' => 0,
            'url' => '', 'position' => 0,
        ]);
        $carrierId = (int) $db->Insert_ID();
        $db->execute('UPDATE `' . _DB_PREFIX_ . 'carrier` SET id_reference = ' . $carrierId . ' WHERE id_carrier = ' . $carrierId);
        $db->insert('carrier_shop', ['id_carrier' => $carrierId, 'id_shop' => 1]);
        $db->insert('carrier_shop', ['id_carrier' => $carrierId, 'id_shop' => self::$secondShopId]);

        return $carrierId;
    }
}
