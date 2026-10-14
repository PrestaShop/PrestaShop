<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter;

use Db;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Combination;
use Tests\Resources\DatabaseDump;

class CombinationTest extends TestCase
{
    private const FULLY_ASSOCIATED_SHOP_ID = 900001;
    private const PARTIALLY_ASSOCIATED_SHOP_ID = 900002;
    private const UNASSOCIATED_SHOP_ID = 900003;

    private const TABLES_TO_RESTORE = [
        'product_attribute',
        'product_attribute_shop',
        'product_attribute_combination',
        'attribute_shop',
    ];

    private static int $combinationId;

    private static int $productId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);

        $db = Db::getInstance();
        self::$productId = (int) $db->getValue('SELECT id_product FROM `' . _DB_PREFIX_ . 'product` ORDER BY id_product ASC');
        $attributeIds = array_map('intval', array_column(
            $db->executeS('SELECT id_attribute FROM `' . _DB_PREFIX_ . 'attribute` ORDER BY id_attribute ASC LIMIT 2'),
            'id_attribute'
        ));

        self::assertGreaterThan(0, self::$productId, 'Demo data must contain at least one product.');
        self::assertCount(2, $attributeIds, 'Demo data must contain at least two attributes.');

        $db->insert('product_attribute', ['id_product' => self::$productId]);
        self::$combinationId = (int) $db->Insert_ID();

        foreach ([self::FULLY_ASSOCIATED_SHOP_ID, self::PARTIALLY_ASSOCIATED_SHOP_ID] as $shopId) {
            $db->insert('product_attribute_shop', [
                'id_product' => self::$productId,
                'id_product_attribute' => self::$combinationId,
                'id_shop' => $shopId,
            ]);
        }

        foreach ($attributeIds as $attributeId) {
            $db->insert('product_attribute_combination', [
                'id_attribute' => $attributeId,
                'id_product_attribute' => self::$combinationId,
            ]);
            $db->insert('attribute_shop', [
                'id_attribute' => $attributeId,
                'id_shop' => self::FULLY_ASSOCIATED_SHOP_ID,
            ]);
        }

        $db->insert('attribute_shop', [
            'id_attribute' => $attributeIds[0],
            'id_shop' => self::PARTIALLY_ASSOCIATED_SHOP_ID,
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
    }

    public function testCombinationAndAllItsAttributesAreAssociatedToShop(): void
    {
        $adapter = new Combination();

        self::assertTrue($adapter->isCombinationAndAttributesAssociatedToShop(
            self::$combinationId,
            self::$productId,
            self::FULLY_ASSOCIATED_SHOP_ID
        ));
    }

    public function testCombinationIsInvalidWhenOneAttributeIsNotAssociatedToShop(): void
    {
        $adapter = new Combination();

        self::assertFalse($adapter->isCombinationAndAttributesAssociatedToShop(
            self::$combinationId,
            self::$productId,
            self::PARTIALLY_ASSOCIATED_SHOP_ID
        ));
    }

    public function testCombinationIsInvalidWhenItIsNotAssociatedToShop(): void
    {
        $adapter = new Combination();

        self::assertFalse($adapter->isCombinationAndAttributesAssociatedToShop(
            self::$combinationId,
            self::$productId,
            self::UNASSOCIATED_SHOP_ID
        ));
    }

    public function testCombinationIsInvalidWhenItBelongsToAnotherProduct(): void
    {
        $adapter = new Combination();

        self::assertFalse($adapter->isCombinationAndAttributesAssociatedToShop(
            self::$combinationId,
            self::$productId + 1,
            self::FULLY_ASSOCIATED_SHOP_ID
        ));
    }
}
