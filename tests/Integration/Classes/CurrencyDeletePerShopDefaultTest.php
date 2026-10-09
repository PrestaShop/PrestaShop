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

        // A second currency, set as the second shop's default (a per-shop override).
        $db->insert('currency', [
            'name' => 'TestCoin', 'iso_code' => 'TST', 'numeric_iso_code' => '997',
            'precision' => 2, 'conversion_rate' => 1.0, 'deleted' => 0, 'active' => 1,
            'unofficial' => 0, 'modified' => 0,
        ]);
        self::$extraCurrencyId = (int) $db->Insert_ID();
        $db->insert('currency_shop', [
            'id_currency' => self::$extraCurrencyId, 'id_shop' => self::$secondShopId, 'conversion_rate' => 1.0,
        ]);
        foreach (Db::getInstance()->executeS('SELECT id_lang FROM `' . _DB_PREFIX_ . 'lang`') as $lang) {
            $db->insert('currency_lang', [
                'id_currency' => self::$extraCurrencyId, 'id_lang' => (int) $lang['id_lang'],
                'name' => 'TestCoin', 'symbol' => 'T', 'pattern' => '',
            ]);
        }

        // Global default stays currency 1; the second shop overrides it to the extra currency.
        LegacyConfiguration::updateValue('PS_CURRENCY_DEFAULT', self::$extraCurrencyId, false, null, self::$secondShopId);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    /**
     * Deleting a currency must not leave any shop's PS_CURRENCY_DEFAULT pointing at it. The delete
     * only repaired the current context's default, so a per-shop override pointing at the deleted
     * currency was left dangling.
     */
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
        self::assertNotSame(
            self::$extraCurrencyId,
            (int) LegacyConfiguration::get('PS_CURRENCY_DEFAULT', null, null, self::$secondShopId),
            'a deleted currency must not remain a shop default'
        );
    }
}
