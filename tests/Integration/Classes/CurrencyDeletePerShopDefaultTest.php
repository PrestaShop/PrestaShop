<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration as LegacyConfiguration;
use Currency;
use Db;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class CurrencyDeletePerShopDefaultTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'shop_group', 'currency', 'currency_shop', 'currency_lang', 'configuration'];

    private static int $secondShopId;
    private static int $extraCurrencyId;
    private static int $activeShopCurrencyId;

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
            'name' => 'test_group_cur', 'color' => '', 'share_customer' => 0,
            'share_order' => 0, 'share_stock' => 0, 'active' => 1, 'deleted' => 0,
        ]);
        $secondGroupId = (int) $db->Insert_ID();
        $db->insert('shop', [
            'id_shop_group' => $secondGroupId, 'name' => 'test_shop_cur', 'color' => '',
            'id_category' => 2, 'theme_name' => 'classic', 'active' => 1, 'deleted' => 0,
        ]);
        self::$secondShopId = (int) $db->Insert_ID();
        LegacyShop::resetStaticCache();

        // The second shop holds three currencies: its default, an inactive one, and an active one created last
        self::$extraCurrencyId = self::insertCurrency('TST', true, [self::$secondShopId]);
        self::insertCurrency('TIN', false, [self::$secondShopId]);
        self::$activeShopCurrencyId = self::insertCurrency('TAC', true, [self::$secondShopId]);

        LegacyConfiguration::updateValue('PS_CURRENCY_DEFAULT', self::$extraCurrencyId, false, null, self::$secondShopId);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    public function testDeleteReassignsPerShopDefaultCurrency(): void
    {
        self::bootKernel();

        // Sanity: the second shop's default is the extra currency before deletion.
        LegacyConfiguration::resetStaticCache();
        self::assertSame(
            self::$extraCurrencyId,
            (int) LegacyConfiguration::get('PS_CURRENCY_DEFAULT', null, null, self::$secondShopId)
        );

        $currency = new Currency(self::$extraCurrencyId);
        self::assertTrue((bool) $currency->delete());

        LegacyConfiguration::resetStaticCache();
        self::assertSame(
            self::$activeShopCurrencyId,
            (int) LegacyConfiguration::get('PS_CURRENCY_DEFAULT', null, null, self::$secondShopId),
            'the shop default must move to an active currency of that shop'
        );
        self::assertSame(1, (int) LegacyConfiguration::get('PS_CURRENCY_DEFAULT', null, 0, 0), 'the global default is untouched');
    }

    public function testDeletingTheGlobalDefaultInAShopContextMovesTheGlobalDefault(): void
    {
        self::bootKernel();
        $globalCurrencyId = self::insertCurrency('TGL', true, [1]);
        LegacyConfiguration::updateGlobalValue('PS_CURRENCY_DEFAULT', $globalCurrencyId);
        LegacyConfiguration::resetStaticCache();
        $previousContext = LegacyShop::getContext();
        $previousShopId = LegacyShop::getContextShopID(true);
        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, 1);

        try {
            self::assertTrue((bool) (new Currency($globalCurrencyId))->delete());
        } finally {
            LegacyShop::setContext($previousContext, $previousShopId);
        }

        LegacyConfiguration::resetStaticCache();
        $newGlobalDefault = (int) LegacyConfiguration::get('PS_CURRENCY_DEFAULT', null, 0, 0);
        self::assertNotSame($globalCurrencyId, $newGlobalDefault, 'the global default must move');
        self::assertSame(0, (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'configuration`
             WHERE `name` = "PS_CURRENCY_DEFAULT" AND `value` = ' . $newGlobalDefault . ' AND `id_shop` IS NOT NULL'
        ), 'no shop override is created');
    }

    /**
     * @param int[] $shopIds
     */
    private static function insertCurrency(string $isoCode, bool $active, array $shopIds): int
    {
        $db = Db::getInstance();
        $db->insert('currency', [
            'name' => $isoCode, 'iso_code' => $isoCode, 'numeric_iso_code' => '997',
            'precision' => 2, 'conversion_rate' => 1.0, 'deleted' => 0, 'active' => (int) $active,
            'unofficial' => 0, 'modified' => 0,
        ]);
        $currencyId = (int) $db->Insert_ID();
        foreach ($shopIds as $shopId) {
            $db->insert('currency_shop', ['id_currency' => $currencyId, 'id_shop' => $shopId, 'conversion_rate' => 1.0]);
        }
        foreach ($db->executeS('SELECT id_lang FROM `' . _DB_PREFIX_ . 'lang`') as $lang) {
            $db->insert('currency_lang', [
                'id_currency' => $currencyId, 'id_lang' => (int) $lang['id_lang'],
                'name' => $isoCode, 'symbol' => $isoCode, 'pattern' => '',
            ]);
        }

        return $currencyId;
    }
}
