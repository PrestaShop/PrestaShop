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

namespace Tests\Integration\Core\Stock;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\Product\PackItemsManager;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Core\Stock\StockManager;
use Product;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StockManagerTest extends KernelTestCase
{
    /**
     * @var ConfigurationInterface|MockObject
     */
    private $configuration;
    /**
     * @var Container
     */
    private $testContainer;
    /**
     * @var Container
     */
    private $savedContainer;
    /**
     * @var PackItemsManager
     */
    private $packItemsManager;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->configuration = $this->createMock(ConfigurationInterface::class);
        $this->savedContainer = ServiceLocator::getContainer();

        $this->testContainer = new Container();
        $this->testContainer->bind(
            '\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface',
            $this->configuration
        );
        ServiceLocator::setServiceContainerInstance($this->testContainer);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        ServiceLocator::setServiceContainerInstance($this->savedContainer);
    }

    /**
     * @dataProvider dataProviderUpdatePackQuantity
     */
    public function testUpdatePackQuantity(
        int $default_stock_type,
        FakeProduct4759 $pack,
        array $products,
        int $delta,
        array $expected
    ): void {
        $this->configuration->method('get')->willReturn($default_stock_type);
        $packItemsManager = new FakePackItemsManager4759();
        foreach ($products as $product) {
            $packItemsManager->addProduct($pack, $product[0], $product[1], $product[2]);
        }
        $this->testContainer->bind('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PackItemsManager', $packItemsManager);
        $this->testContainer->bind('\\PrestaShop\\PrestaShop\\Adapter\\StockManager', $packItemsManager);

        $stockManager = new StockManager();
        $stockManager->updatePackQuantity($pack, $pack->stock_available, $delta);

        $this->assertEquals($expected[0], $pack->stock_available->quantity);
        foreach ($products as $k => $product) {
            $this->assertEquals($expected[$k + 1], $product[0]->stock_available->quantity);
        }
    }

    public function dataProviderUpdatePackQuantity(): array
    {
        return [
            [ // nominal case
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => [
                    [new FakeProduct4759(50), 1, 3],
                    [new FakeProduct4759(20), 2, 1],
                ],
                'delta' => -3,
                'expected' => [7, 41, 17],
            ],
            [ // out of stock case
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => [
                    [new FakeProduct4759(50), 1, 5],
                    [new FakeProduct4759(20), 2, 2],
                ],
                'delta' => -13,
                'expected' => [-3, -15, -6],
            ],
            [ // default stock type linked case
                'default_stock_type' => PackStockType::STOCK_TYPE_BOTH, // linked stock mode (Decrement both)
                'pack' => new FakeProduct4759(10, 3), // 3: default stock type
                'products' => [
                    [new FakeProduct4759(50), 1, 5],
                    [new FakeProduct4759(20), 2, 2],
                ],
                'delta' => -1,
                'expected' => [9, 45, 18],
            ],
            [ // no link stock type case
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // not linked stock mode
                'pack' => new FakeProduct4759(5, PackStockType::STOCK_TYPE_DEFAULT), // 3: default stock type
                'products' => [
                    [new FakeProduct4759(50), 1, 5],
                    [new FakeProduct4759(20), 2, 2],
                ],
                'delta' => -5,
                'expected' => [0, 50, 20],
            ],
            [ // half link stock type case
                'default_stock_type' => PackStockType::STOCK_TYPE_PRODUCTS_ONLY, // not linked stock mode
                'pack' => new FakeProduct4759(5, 3), // 3: default stock type
                'products' => [
                    [new FakeProduct4759(50), 1, 5],
                    [new FakeProduct4759(20), 2, 2],
                ],
                'delta' => -5,
                'expected' => [0, 25, 10],
            ],
            [ // increment case, in linked stock mode
                'default_stock_type' => PackStockType::STOCK_TYPE_BOTH, // linked stock mode (Decrement both)
                'pack' => new FakeProduct4759(5, 3), // 3: default stock type
                'products' => [
                    [new FakeProduct4759(50), 1, 5],
                    [new FakeProduct4759(20), 2, 2],
                ],
                'delta' => 1,
                'expected' => [6, 55, 22],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderUpdateProductQuantity
     */
    public function testUpdateProductQuantity(
        int $default_stock_type,
        FakeProduct4759 $pack,
        array $products,
        int $delta,
        array $expected
    ): void {
        $this->configuration->method('get')->willReturn($default_stock_type);
        $this->packItemsManager = new FakePackItemsManager4759();
        foreach ($products as $product) {
            $this->packItemsManager->addProduct($pack, $product[0], $product[1], $product[2]);
        }
        $this->testContainer->bind('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PackItemsManager', $this->packItemsManager);
        $this->testContainer->bind('\\PrestaShop\\PrestaShop\\Adapter\\StockManager', $this->packItemsManager);

        $stockManager = new StockManager();
        // we will update first product quantity only, others will remain inchanged (excepting pack on needed cases)
        $stockAvailable = $products[0][0]->stock_available;
        $stockAvailable->quantity = $stockAvailable->quantity + $delta;
        $stockAvailable->update();
        $stockManager->updatePacksQuantityContainingProduct($products[0][0], $products[0][1], $stockAvailable);

        $this->assertEquals($expected[0], $pack->stock_available->quantity);
        foreach ($products as $k => $product) {
            $this->assertEquals($expected[$k + 1], $product[0]->stock_available->quantity);
        }
    }

    public function dataProviderUpdateProductQuantity(): array
    {
        return [
            [ // nominal case: pack not decreased
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => [
                    [new FakeProduct4759(30), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'delta_first_product' => -3,
                'expected' => [10, 27, 10],
            ],
            [ // nominal case: pack decreased
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => [
                    [new FakeProduct4759(20), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'delta_first_product' => -1,
                'expected' => [9, 19, 10],
            ],
            [ // nominal case: pack decreased
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => [
                    [new FakeProduct4759(20), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'delta_first_product' => -2,
                'expected' => [9, 18, 10],
            ],
            [ // increase case: not modification on pack
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => [
                    [new FakeProduct4759(20), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'delta_first_product' => 2,
                'expected' => [10, 22, 10],
            ],
            [ // not linked case
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 1), // 1: not linked stock mode
                'products' => [
                    [new FakeProduct4759(20), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'delta_first_product' => -2,
                'expected' => [10, 18, 10],
            ],
            [ // out of stock case
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => [
                    [new FakeProduct4759(20), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'delta_first_product' => -22,
                'expected' => [0, -2, 10],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderUpdateQuantity
     */
    public function testUpdateQuantity(
        int $default_stock_type,
        FakeProduct4759 $pack,
        array $products,
        int $product_to_update,
        int $delta,
        array $expected
    ): void {
        $this->configuration->method('get')->willReturn($default_stock_type);
        $this->packItemsManager = new FakePackItemsManager4759();
        foreach ($products as $product) {
            $this->packItemsManager->addProduct($pack, $product[0], $product[1], $product[2]);
        }
        $this->testContainer->bind('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PackItemsManager', $this->packItemsManager);
        $this->testContainer->bind('\\PrestaShop\\PrestaShop\\Adapter\\StockManager', $this->packItemsManager);

        $productToUpdate = ($product_to_update === 0) ? $pack : $products[$product_to_update - 1][0];
        $productAttributeToUpdate = ($product_to_update === 0) ? null : $products[$product_to_update - 1][1];

        $stockManager = new StockManager();
        $stockManager->updateQuantity($productToUpdate, $productAttributeToUpdate, $delta);

        $this->assertEquals($expected[0], $pack->stock_available->quantity);
        foreach ($products as $k => $product) {
            $this->assertEquals($expected[$k + 1], $product[0]->stock_available->quantity);
        }
    }

    public function dataProviderUpdateQuantity(): array
    {
        return [
            [ // nominal case: pack decreased with sub products
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => [
                    [new FakeProduct4759(30), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'product_to_update' => 0, // 0 for pack, 1..n for an item in products
                'delta' => -3,
                'expected' => [7, 24, 7],
            ],
            [ // nominal case: product will decrease pack
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => [
                    [new FakeProduct4759(30), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'product_to_update' => 1, // 0 for pack, 1..n for an item in products
                'delta' => -11,
                'expected' => [9, 19, 10],
            ],
            [ // product won't decrease pack (sufficient stocks)
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => [
                    [new FakeProduct4759(30), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'product_to_update' => 1, // 0 for pack, 1..n for an item in products
                'delta' => -10,
                'expected' => [10, 20, 10],
            ],
            [ // out of stock for pack decrease
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => [
                    [new FakeProduct4759(30), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'product_to_update' => 0, // 0 for pack, 1..n for an item in products
                'delta' => -12,
                'expected' => [-2, 6, -2],
            ],
            [ // not linked stock mode
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 1), // 2: linked stock mode (Decrement both)
                'products' => [
                    [new FakeProduct4759(30), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'product_to_update' => 1, // 0 for pack, 1..n for an item in products
                'delta' => -12,
                'expected' => [10, 18, 10],
            ],
            [ // not linked stock mode
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 3), // 3: not linked stock mode
                'products' => [
                    [new FakeProduct4759(30), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'product_to_update' => 0, // 0 for pack, 1..n for an item in products
                'delta' => -8,
                'expected' => [2, 30, 10],
            ],
            [ // half linked stock mode
                'default_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY, // does not matter for this test
                'pack' => new FakeProduct4759(10, 1), // 1: half linked stock mode (pack decrease will decrease products)
                'products' => [
                    [new FakeProduct4759(30), 1, 2],
                    [new FakeProduct4759(10), 2, 1],
                ],
                'product_to_update' => 0, // 0 for pack, 1..n for an item in products
                'delta' => -8,
                'expected' => [2, 14, 2],
            ],
        ];
    }
}

class FakeProduct4759 extends Product
{
    private static $LAST_ID = 0;
    public $stock_available;

    public function __construct($stock_available, int $pack_stock_type = PackStockType::STOCK_TYPE_PACK_ONLY)
    {
        $this->id = ++self::$LAST_ID;
        $this->pack_stock_type = $pack_stock_type;
        $this->stock_available = new FakeStockAvailable4759($stock_available);
    }

    /**
     * Check if product has attributes combinations.
     *
     * @return int Attributes combinations number
     */
    public function hasAttributes()
    {
        return 0;
    }
}

class FakePackItemsManager4759 extends PackItemsManager
{
    private $packs = [];
    private $items = [];
    private $stockAvailables = [];

    public function addProduct(FakeProduct4759 $pack, FakeProduct4759 $product, $product_attribute_id, $quantity)
    {
        $entry = [
            'productObj' => $product,
            'id' => $product->id,
            'id_pack_product_attribute' => $product_attribute_id,
            'pack_quantity' => $quantity,
        ];
        $this->packs[$pack->id][] = (object) $entry;
        $entry = [
            'packObj' => $pack,
            'id' => $pack->id,
            'pack_item_quantity' => $quantity,
            'pack_stock_type' => $pack->pack_stock_type,
        ];
        $this->items[$product->id][$product_attribute_id][$pack->id] = (object) $entry;
        $this->stockAvailables[$pack->id][0] = $pack->stock_available;
        $this->stockAvailables[$product->id][$product_attribute_id] = $product->stock_available;
    }

    public function getPackItems($pack, $id_lang = false)
    {
        return $this->packs[$pack->id];
    }

    public function getPacksContainingItem($item, $item_attribute_id, $id_lang = false)
    {
        return $this->items[$item->id][$item_attribute_id];
    }

    public function getStockAvailableByProduct($product, int $id_product_attribute = null, $id_shop = null)
    {
        $id_product_attribute = $id_product_attribute ? $id_product_attribute : 0;

        return $this->stockAvailables[$product->id][$id_product_attribute];
    }

    public function isPack($product)
    {
        return isset($this->packs[$product->id]);
    }

    public function isPacked($product, $id_product_attribute = false)
    {
        return isset($this->items[$product->id][$id_product_attribute]);
    }
}

class FakeStockAvailable4759 extends StockAvailable
{
    public function __construct($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @param $null_values
     *
     * @return bool|int|string|void
     */
    public function update($null_values = false)
    {
    }
}
