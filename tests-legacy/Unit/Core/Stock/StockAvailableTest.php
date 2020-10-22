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

namespace LegacyTests\Unit\Core\Stock;

use LegacyTests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Stock\StockManager;

class StockAvailableTest extends UnitTestCase
{
    private function setStockType($packStockType)
    {
        $this->setConfiguration(array('PS_PACK_STOCK_TYPE' => $packStockType));
    }

    public function get_update_pack_quantity_provider()
    {
        return array(
            array( // nominal case
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(50), 1, 3),
                    array(new FakeProduct4759(20), 2, 1),
                ),
                'delta' => -3,
                'expected' => array(7, 41, 17),
            ),
            array( // out of stock case
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(50), 1, 5),
                    array(new FakeProduct4759(20), 2, 2),
                ),
                'delta' => -13,
                'expected' => array(-3, -15, -6),
            ),
            array( // default stock type linked case
                'default_stock_type' => 2, // linked stock mode (Decrement both)
                'pack' => new FakeProduct4759(10, 3), // 3: default stock type
                'products' => array(
                    array(new FakeProduct4759(50), 1, 5),
                    array(new FakeProduct4759(20), 2, 2),
                ),
                'delta' => -1,
                'expected' => array(9, 45, 18),
            ),
            array( // no link stock type case
                'default_stock_type' => 0, // not linked stock mode
                'pack' => new FakeProduct4759(5, 3), // 3: default stock type
                'products' => array(
                    array(new FakeProduct4759(50), 1, 5),
                    array(new FakeProduct4759(20), 2, 2),
                ),
                'delta' => -5,
                'expected' => array(0, 50, 20),
            ),
            array( // half link stock type case
                'default_stock_type' => 1, // not linked stock mode
                'pack' => new FakeProduct4759(5, 3), // 3: default stock type
                'products' => array(
                    array(new FakeProduct4759(50), 1, 5),
                    array(new FakeProduct4759(20), 2, 2),
                ),
                'delta' => -5,
                'expected' => array(0, 25, 10),
            ),
            array( // increment case, in linked stock mode
                'default_stock_type' => 2, // linked stock mode (Decrement both)
                'pack' => new FakeProduct4759(5, 3), // 3: default stock type
                'products' => array(
                    array(new FakeProduct4759(50), 1, 5),
                    array(new FakeProduct4759(20), 2, 2),
                ),
                'delta' => 1,
                'expected' => array(6, 55, 22),
            ),
        );
    }

    /**
     * @dataProvider get_update_pack_quantity_provider
     */
    public function testUpdatePackQuantity($default_stock_type, FakeProduct4759 $pack, $products, $delta, $expected)
    {
        $this->setStockType($default_stock_type);
        $this->packItemsManager = new FakePackItemsManager4759();
        foreach ($products as $product) {
            $this->packItemsManager->addProduct($pack, $product[0], $product[1], $product[2]);
        }
        $this->container->bind('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PackItemsManager', $this->packItemsManager);
        $this->container->bind('\\PrestaShop\\PrestaShop\\Adapter\\StockManager', $this->packItemsManager);

        $stockManager = new StockManager();
        $stockManager->updatePackQuantity($pack, $pack->stock_available, $delta);

        $this->assertEquals($expected[0], $pack->stock_available->quantity);
        foreach ($products as $k => $product) {
            $this->assertEquals($expected[$k+1], $product[0]->stock_available->quantity);
        }
    }

    public function get_update_product_quantity_provider()
    {
        return array(
            array( // nominal case: pack not decreased
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'delta_first_product' => -3,
                'expected' => array(10, 27, 10),
            ),
            array( // nominal case: pack decreased
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(20), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'delta_first_product' => -1,
                'expected' => array(9, 19, 10),
            ),
            array( // nominal case: pack decreased
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(20), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'delta_first_product' => -2,
                'expected' => array(9, 18, 10),
            ),
            array( // increase case: not modification on pack
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(20), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'delta_first_product' => 2,
                'expected' => array(10, 22, 10),
            ),
            array( // not linked case
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 1), // 1: not linked stock mode
                'products' => array(
                    array(new FakeProduct4759(20), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'delta_first_product' => -2,
                'expected' => array(10, 18, 10),
            ),
            array( // out of stock case
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(20), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'delta_first_product' => -22,
                'expected' => array(0, -2, 10),
            ),
        );
    }

    /**
     * @dataProvider get_update_product_quantity_provider
     */
    public function testUpdateProductQuantity($default_stock_type, FakeProduct4759 $pack, $products, $delta, $expected)
    {
        $this->setStockType($default_stock_type);
        $this->packItemsManager = new FakePackItemsManager4759();
        foreach ($products as $product) {
            $this->packItemsManager->addProduct($pack, $product[0], $product[1], $product[2]);
        }
        $this->container->bind('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PackItemsManager', $this->packItemsManager);
        $this->container->bind('\\PrestaShop\\PrestaShop\\Adapter\\StockManager', $this->packItemsManager);

        $stockManager = new StockManager();
        // we will update first product quantity only, others will remain inchanged (excepting pack on needed cases)
        $stockAvailable = $products[0][0]->stock_available;
        $stockAvailable->quantity = $stockAvailable->quantity + $delta;
        $stockAvailable->update();
        $stockManager->updatePacksQuantityContainingProduct($products[0][0], $products[0][1], $stockAvailable);

        $this->assertEquals($expected[0], $pack->stock_available->quantity);
        foreach ($products as $k => $product) {
            $this->assertEquals($expected[$k+1], $product[0]->stock_available->quantity);
        }
    }

    public function get_update_quantity_provider()
    {
        return array(
            array( // nominal case: pack decreased with sub products
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'product_to_update' => 0, // 0 for pack, 1..n for an item in products
                'delta' => -3,
                'expected' => array(7, 24, 7),
            ),
            array( // nominal case: product will decrease pack
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'product_to_update' => 1, // 0 for pack, 1..n for an item in products
                'delta' => -11,
                'expected' => array(9, 19, 10),
            ),
            array( // product won't decrease pack (sufficient stocks)
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'product_to_update' => 1, // 0 for pack, 1..n for an item in products
                'delta' => -10,
                'expected' => array(10, 20, 10),
            ),
            array( // out of stock for pack decrease
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'product_to_update' => 0, // 0 for pack, 1..n for an item in products
                'delta' => -12,
                'expected' => array(-2, 6, -2),
            ),
            array( // not linked stock mode
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 1), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'product_to_update' => 1, // 0 for pack, 1..n for an item in products
                'delta' => -12,
                'expected' => array(10, 18, 10),
            ),
            array( // not linked stock mode
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 3), // 3: not linked stock mode
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'product_to_update' => 0, // 0 for pack, 1..n for an item in products
                'delta' => -8,
                'expected' => array(2, 30, 10),
            ),
            array( // half linked stock mode
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 1), // 1: half linked stock mode (pack decrease will decrease products)
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1),
                ),
                'product_to_update' => 0, // 0 for pack, 1..n for an item in products
                'delta' => -8,
                'expected' => array(2, 14, 2),
            ),
        );
    }

    /**
     * @dataProvider get_update_quantity_provider
     */
    public function testUpdateQuantity($default_stock_type, FakeProduct4759 $pack, $products, $product_to_update, $delta, $expected)
    {
        $this->setStockType($default_stock_type);
        $this->packItemsManager = new FakePackItemsManager4759();
        foreach ($products as $product) {
            $this->packItemsManager->addProduct($pack, $product[0], $product[1], $product[2]);
        }
        $this->container->bind('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PackItemsManager', $this->packItemsManager);
        $this->container->bind('\\PrestaShop\\PrestaShop\\Adapter\\StockManager', $this->packItemsManager);

        $productToUpdate = ($product_to_update === 0)? $pack : $products[$product_to_update-1][0];
        $productAttributeToUpdate = ($product_to_update === 0)? null : $products[$product_to_update-1][1];

        $stockManager = new StockManager();
        $stockManager->updateQuantity($productToUpdate, $productAttributeToUpdate, $delta);

        $this->assertEquals($expected[0], $pack->stock_available->quantity);
        foreach ($products as $k => $product) {
            $this->assertEquals($expected[$k+1], $product[0]->stock_available->quantity);
        }
    }
}
