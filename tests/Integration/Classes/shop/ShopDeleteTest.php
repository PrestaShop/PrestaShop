<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\shop;

use Configuration;
use Db;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use Shop;
use Tests\Resources\DatabaseDump;

class ShopDeleteTest extends TestCase
{
    private const TABLES_TO_RESTORE = [
        'cart_rule_shop',
        'configuration',
        'configuration_lang',
        'product_carrier',
        'shop',
        'specific_price',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Configuration::resetStaticCache();
        Shop::resetStaticCache();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Configuration::resetStaticCache();
        Shop::resetStaticCache();
    }

    public function testDeleteRemovesShopScopedData(): void
    {
        $shop = new Shop();
        $shop->name = 'test_shop_delete';
        $shop->id_shop_group = 1;
        $shop->id_category = 2;
        $shop->theme_name = Theme::getDefaultTheme();
        $this->assertTrue($shop->add());
        $shopId = (int) $shop->id;

        $db = Db::getInstance();
        $db->insert('configuration', [
            'id_shop_group' => 1,
            'id_shop' => $shopId,
            'name' => 'TEST_SHOP_DELETE',
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
        ]);
        $configurationId = (int) $db->Insert_ID();
        $db->insert('configuration_lang', [
            'id_configuration' => $configurationId,
            'id_lang' => (int) Configuration::get('PS_LANG_DEFAULT'),
            'value' => 'value',
        ]);

        $db->insert('specific_price', [
            'id_specific_price_rule' => 0,
            'id_cart' => 0,
            'id_product' => 1,
            'id_shop' => $shopId,
            'id_shop_group' => 0,
            'id_currency' => 0,
            'id_country' => 0,
            'id_group' => 0,
            'id_customer' => 0,
            'id_product_attribute' => 0,
            'price' => -1,
            'from_quantity' => 1,
            'reduction' => 0.1,
            'reduction_type' => 'percentage',
            'from' => '0000-00-00 00:00:00',
            'to' => '0000-00-00 00:00:00',
        ]);
        $db->insert('product_carrier', ['id_product' => 1, 'id_carrier_reference' => 1, 'id_shop' => $shopId]);
        $db->insert('cart_rule_shop', ['id_cart_rule' => 1, 'id_shop' => $shopId]);

        $this->assertTrue($shop->delete());

        foreach (['configuration', 'specific_price', 'product_carrier', 'cart_rule_shop'] as $table) {
            $this->assertSame(
                0,
                (int) $db->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . $table . '` WHERE `id_shop` = ' . $shopId),
                $table . ' rows of the deleted shop must be removed'
            );
        }
        $this->assertSame(
            0,
            (int) $db->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'configuration_lang` WHERE `id_configuration` = ' . $configurationId)
        );
    }
}
