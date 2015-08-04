<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Business\Stock;

use Exception;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Core_Business_Stock_StockManager;

class FakeStockAvailable4759
{
    public $quantity = 0;
    public function __construct($quantity)
    {
        $this->quantity = $quantity;
    }
    public function update()
    {
    }
}
class FakeProduct4759
{
    private static $LAST_ID = 0;
    public $id;
    public $pack_stock_type;
    public $stock_available;
    public function __construct($stock_available, $pack_stock_type = false)
    {
        $this->id = ++FakeProduct4759::$LAST_ID;
        $this->pack_stock_type = $pack_stock_type ? $pack_stock_type : 0;
        $this->stock_available = new FakeStockAvailable4759($stock_available);
    }
}
class FakePackItemsManager4759
{
    private $packs = array();
    private $items = array();
    private $stockAvailables = array();
    public function addProduct(FakeProduct4759 $pack, FakeProduct4759 $product, $product_attribute_id, $quantity)
    {
        $entry = array(
            'productObj' => $product,
            'id' => $product->id,
            'id_pack_product_attribute' => $product_attribute_id,
            'pack_quantity' => $quantity
        );
        $this->packs[$pack->id][] = (object) $entry;
        $entry = array(
            'packObj' => $pack,
            'id' => $pack->id,
            'pack_item_quantity' => $quantity,
            'pack_stock_type' => $pack->pack_stock_type
            
        );
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
    public function getStockAvailableByProduct($product, $id_product_attribute = null, $id_shop = null)
    {
        $id_product_attribute = $id_product_attribute?$id_product_attribute:0;
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
                    array(new FakeProduct4759(20), 2, 1)
                ),
                'delta' => -3,
                'expected' => array(7, 41, 17)
            ),
            array( // out of stock case
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(50), 1, 5),
                    array(new FakeProduct4759(20), 2, 2)
                ),
                'delta' => -13,
                'expected' => array(-3, -15, -6)
            ),
            array( // default stock type linked case
                'default_stock_type' => 2, // linked stock mode (Decrement both)
                'pack' => new FakeProduct4759(10, 3), // 3: default stock type
                'products' => array(
                    array(new FakeProduct4759(50), 1, 5),
                    array(new FakeProduct4759(20), 2, 2)
                ),
                'delta' => -1,
                'expected' => array(9, 45, 18)
            ),
            array( // no link stock type case
                'default_stock_type' => 0, // not linked stock mode
                'pack' => new FakeProduct4759(5, 3), // 3: default stock type
                'products' => array(
                    array(new FakeProduct4759(50), 1, 5),
                    array(new FakeProduct4759(20), 2, 2)
                ),
                'delta' => -5,
                'expected' => array(0, 50, 20)
            ),
            array( // half link stock type case
                'default_stock_type' => 1, // not linked stock mode
                'pack' => new FakeProduct4759(5, 3), // 3: default stock type
                'products' => array(
                    array(new FakeProduct4759(50), 1, 5),
                    array(new FakeProduct4759(20), 2, 2)
                ),
                'delta' => -5,
                'expected' => array(0, 25, 10)
            ),
            array( // increment case, in linked stock mode
                'default_stock_type' => 2, // linked stock mode (Decrement both)
                'pack' => new FakeProduct4759(5, 3), // 3: default stock type
                'products' => array(
                    array(new FakeProduct4759(50), 1, 5),
                    array(new FakeProduct4759(20), 2, 2)
                ),
                'delta' => 1,
                'expected' => array(6, 55, 22)
            )
        );
    }

    /**
     * @dataProvider get_update_pack_quantity_provider
     */
    public function test_update_pack_quantity($default_stock_type, FakeProduct4759 $pack, $products, $delta, $expected)
    {
        $this->setStockType($default_stock_type);
        $this->packItemsManager = new FakePackItemsManager4759();
        foreach($products as $product) {
            $this->packItemsManager->addProduct($pack, $product[0], $product[1], $product[2]);
        }
        $this->container->bind('Adapter_PackItemsManager', $this->packItemsManager);
        $this->container->bind('Adapter_StockManager', $this->packItemsManager);
        
        $stockManager = new Core_Business_Stock_StockManager();
        $stockManager->updatePackQuantity($pack, $pack->stock_available, $delta);
        
        $this->assertEquals($expected[0], $pack->stock_available->quantity);
        foreach($products as $k => $product) {
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
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'delta_first_product' => -3,
                'expected' => array(10, 27, 10)
            ),
            array( // nominal case: pack decreased
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(20), 1, 2),
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'delta_first_product' => -1,
                'expected' => array(9, 19, 10)
            ),
            array( // nominal case: pack decreased
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(20), 1, 2),
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'delta_first_product' => -2,
                'expected' => array(9, 18, 10)
            ),
            array( // increase case: not modification on pack
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(20), 1, 2),
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'delta_first_product' => 2,
                'expected' => array(10, 22, 10)
            ),
            array( // not linked case
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 1), // 1: not linked stock mode
                'products' => array(
                    array(new FakeProduct4759(20), 1, 2),
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'delta_first_product' => -2,
                'expected' => array(10, 18, 10)
            ),
            array( // out of stock case
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(20), 1, 2),
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'delta_first_product' => -22,
                'expected' => array(0, -2, 10)
            ),
        );
    }
    
    /**
     * @dataProvider get_update_product_quantity_provider
     */
    public function test_update_product_quantity($default_stock_type, FakeProduct4759 $pack, $products, $delta, $expected)
    {
        $this->setStockType($default_stock_type);
        $this->packItemsManager = new FakePackItemsManager4759();
        foreach($products as $product) {
            $this->packItemsManager->addProduct($pack, $product[0], $product[1], $product[2]);
        }
        $this->container->bind('Adapter_PackItemsManager', $this->packItemsManager);
        $this->container->bind('Adapter_StockManager', $this->packItemsManager);
        
        $stockManager = new Core_Business_Stock_StockManager();
        // we will update first product quantity only, others will remain inchanged (excepting pack on needed cases)
        $stockAvailable = $products[0][0]->stock_available;
        $stockAvailable->quantity = $stockAvailable->quantity + $delta;
        $stockAvailable->update();
        $stockManager->updatePacksQuantityContainingProduct($products[0][0], $products[0][1], $stockAvailable);
        
        $this->assertEquals($expected[0], $pack->stock_available->quantity);
        foreach($products as $k => $product) {
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
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'product_to_update' => 0, // 0 for pack, 1..n for an item in products
                'delta' => -3,
                'expected' => array(7, 24, 7)
            ),
            array( // nominal case: product will decrease pack
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'product_to_update' => 1, // 0 for pack, 1..n for an item in products
                'delta' => -11,
                'expected' => array(9, 19, 10)
            ),
            array( // product won't decrease pack (sufficient stocks)
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'product_to_update' => 1, // 0 for pack, 1..n for an item in products
                'delta' => -10,
                'expected' => array(10, 20, 10)
            ),
            array( // out of stock for pack decrease
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 2), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'product_to_update' => 0, // 0 for pack, 1..n for an item in products
                'delta' => -12,
                'expected' => array(-2, 6, -2)
            ),
            array( // not linked stock mode
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 1), // 2: linked stock mode (Decrement both)
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'product_to_update' => 1, // 0 for pack, 1..n for an item in products
                'delta' => -12,
                'expected' => array(10, 18, 10)
            ),
            array( // not linked stock mode
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 3), // 3: not linked stock mode
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'product_to_update' => 0, // 0 for pack, 1..n for an item in products
                'delta' => -8,
                'expected' => array(2, 30, 10)
            ),
            array( // half linked stock mode
                'default_stock_type' => 0, // does not matter for this test
                'pack' => new FakeProduct4759(10, 1), // 1: half linked stock mode (pack decrease will decrease products)
                'products' => array(
                    array(new FakeProduct4759(30), 1, 2),
                    array(new FakeProduct4759(10), 2, 1)
                ),
                'product_to_update' => 0, // 0 for pack, 1..n for an item in products
                'delta' => -8,
                'expected' => array(2, 14, 2)
            ),
        );
    }
    
    /**
     * @dataProvider get_update_quantity_provider
     */
    public function test_update_quantity($default_stock_type, FakeProduct4759 $pack, $products, $product_to_update, $delta, $expected)
    {
        $this->setStockType($default_stock_type);
        $this->packItemsManager = new FakePackItemsManager4759();
        foreach($products as $product) {
            $this->packItemsManager->addProduct($pack, $product[0], $product[1], $product[2]);
        }
        $this->container->bind('Adapter_PackItemsManager', $this->packItemsManager);
        $this->container->bind('Adapter_StockManager', $this->packItemsManager);
        
        
        $productToUpdate = ($product_to_update === 0)? $pack : $products[$product_to_update-1][0];
        $productAttributeToUpdate = ($product_to_update === 0)? null : $products[$product_to_update-1][1];
        
        $stockManager = new \Core_Business_Stock_StockManager();
        $stockManager->updateQuantity($productToUpdate, $productAttributeToUpdate, $delta);
        
        $this->assertEquals($expected[0], $pack->stock_available->quantity);
        foreach($products as $k => $product) {
            $this->assertEquals($expected[$k+1], $product[0]->stock_available->quantity);
        }
    }
    
}
