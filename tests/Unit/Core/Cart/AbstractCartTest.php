<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Cart;

use Cache;
use Cart;
use CartRule;
use Configuration;
use Context;
use DateInterval;
use DateTime;
use Db;
use Tests\TestCase\IntegrationTestCase;
use Product;
use Pack;
use StockAvailable;
use Tests\Unit\Core\Cart\Calculation\CartOld;

/**
 * these tests aim to check cart using mocks
 *
 * products and cartRules are inserted as fixtures
 */
abstract class AbstractCartTest extends IntegrationTestCase
{

    const DEFAULT_SHIPPING_FEE = 7;
    const DEFAULT_WRAPPING_FEE = 0;

    const PRODUCT_FIXTURES = [
        1 => array('price' => 19.812),
        2 => array('price' => 32.388),
        3 => array('price' => 31.188),
        4 => array('price' => 35.567, 'outOfStock' => true),
        5 => array('price' => 23.86, 'quantity' => 50),
        6 => array('price' => 12.34, 'quantity' => 10, 'is_pack' => true, 'pack_items' => array(array('id_product_fixture' => 5, 'quantity' => 10))),
    ];

    const CART_RULES_FIXTURES = [
        1  => array('priority' => 1, 'code' => 'foo1', 'percent' => 50, 'amount' => 0),
        2  => array('priority' => 2, 'code' => 'foo2', 'percent' => 50, 'amount' => 0),
        3  => array('priority' => 3, 'code' => 'foo3', 'percent' => 10, 'amount' => 0),
        4  => array('priority' => 4, 'code' => 'foo4', 'percent' => 0, 'amount' => 5),
        5  => array('priority' => 5, 'code' => 'foo5', 'percent' => 0, 'amount' => 500),
        6  => array('priority' => 6, 'code' => 'foo6', 'percent' => 0, 'amount' => 10),
        7  => array('priority' => 7, 'code' => 'foo7', 'percent' => 50, 'amount' => 0),
        8  => array('priority' => 8, 'code' => 'foo8', 'percent' => 0, 'amount' => 5, 'productRestrictionId' => 2),
        9  => array('priority' => 9, 'code' => 'foo9', 'percent' => 0, 'amount' => 500, 'productRestrictionId' => 2),
        10 => array('priority' => 10, 'code' => 'foo10', 'percent' => 50, 'amount' => 0, 'productRestrictionId' => 2),
        11 => array('priority' => 11, 'code' => 'foo11', 'percent' => 10, 'amount' => 0, 'productRestrictionId' => 2),
        12 => array('priority' => 12, 'code' => 'foo12', 'percent' => 10, 'amount' => 0, 'productGiftId' => 3),
        13 => array('priority' => 13, 'code' => 'foo13', 'percent' => 10, 'amount' => 0, 'productGiftId' => 4),
    ];

    /**
     * @var CartOld
     */
    protected $cart;

    /**
     * @var CartRule[]
     */
    protected $cartRulesInCart = array();

    /**
     * @var CartRule[]
     */
    protected $cartRules = array();

    /**
     * @var Product[]
     */
    protected $products = array();

    public function setUp()
    {
        parent::setUp();
        $this->cart              = new CartOld();
        $this->cart->id_lang     = (int) Context::getContext()->language->id;
        $this->cart->id_currency = (int) Context::getContext()->currency->id;
        $this->cart->id_shop     = (int) Context::getContext()->shop->id;
        $this->cart->add(); // required, else we cannot get the content when calculation total
        Context::getContext()->cart = $this->cart;
        $this->resetCart();
        $this->insertProductsFromFixtures();
        $this->insertCartRulesFromFixtures();
    }

    public function tearDown()
    {
        $this->resetCart();

        // delete cart rules from cart
        foreach ($this->cartRulesInCart as $cartRule) {
            $cartRule->delete();
        }

        // delete products
        foreach ($this->products as $product) {
            $product->delete();
        }

        // delete cart
        $this->cart->delete();

        // delete products
        foreach ($this->products as $product) {
            $product->delete();
        }

        // delete cart rules
        foreach ($this->cartRules as $cartRule) {
            $cartRule->delete();
        }

        parent::tearDown();
    }

    protected function resetCart()
    {
        $productData = $this->cart->getProducts(true);
        foreach ($productData as $data) {
            $this->cart->updateQty(0, $data['id_product']);
        }
        $carRuleData = $this->cart->getCartRules();
        foreach ($carRuleData as $data) {
            $this->cart->removeCartRule($data['id_cart_rule']);
        }
    }

    protected function insertProductsFromFixtures()
    {
        foreach (static::PRODUCT_FIXTURES as $k => $productFixture) {
            $product           = new Product;
            $product->price    = $productFixture['price'];
            $product->name     = 'product name';
            $product->quantity = !empty($productFixture['quantity']) ? $productFixture['quantity'] : 1000;

            if (!empty($productFixture['outOfStock'])) {
                $product->out_of_stock = 0;
                $product->quantity     = 0;
            }
            if (!empty($productFixture['taxRuleGroupId'])) {
                $product->id_tax_rules_group = $productFixture['taxRuleGroupId'];
            }
            $product->add();

            if (isset($productFixture['is_pack'])
                && $productFixture['is_pack'] === true
            ) {
                foreach ($productFixture['pack_items'] as $packItem) {
                    Pack::addItem(
                        $product->id,
                        $this->products[$packItem['id_product_fixture']]->id,
                        $packItem['quantity']
                    );
                }
            }

            if (!empty($productFixture['outOfStock'])) {
                StockAvailable::setProductOutOfStock((int) $product->id, 0);
            } else {
                StockAvailable::setQuantity((int) $product->id, 0, $product->quantity);
            }
            $this->products[$k] = $product;
        }

        // Fix issue pack cache is set when adding products.
        Pack::resetStaticCache();
    }

    protected function addProductsToCart($productsData)
    {
        foreach ($productsData as $productFixtureId => $quantity) {
            $this->addProductToCart($productFixtureId, $quantity);
        }
    }

    protected function addProductToCart($productFixtureId, $quantity)
    {
        $product = $this->getProductFromFixtureId($productFixtureId);
        if ($product !== null) {
            $this->cart->updateQty($quantity, $product->id);
        }
    }

    /**
     * @param int $productFixtureId fixture product id
     *
     * @return Product|null
     */
    protected function getProductFromFixtureId($productFixtureId)
    {
        if (isset($this->products[$productFixtureId])) {
            return $this->products[$productFixtureId];
        }

        return null;
    }

    /**
     * @param int $id fixture cart rule id
     *
     * @return CartRule|null
     */
    protected function getCartRuleFromFixtureId($id)
    {
        if (isset($this->cartRules[$id])) {
            return $this->cartRules[$id];
        }

        return null;
    }

    protected function insertCartRulesFromFixtures()
    {
        foreach (static::CART_RULES_FIXTURES as $k => $cartRuleData) {
            $this->insertCartRule($k, $cartRuleData);
        }
    }

    protected function insertCartRule($cartRuleFixtureId, $cartRuleData)
    {
        $cartRule                    = new CartRule;
        $cartRule->reduction_percent = $cartRuleData['percent'];
        $cartRule->reduction_amount  = $cartRuleData['amount'];
        $cartRule->name              = array(Configuration::get('PS_LANG_DEFAULT') => 'foo');
        if (!empty($cartRuleData['code'])) {
            $cartRule->code = $cartRuleData['code'];
        }
        $cartRule->priority          = $cartRuleData['priority'];
        $cartRule->quantity          = 1000;
        $cartRule->quantity_per_user = 1000;
        if (!empty($cartRuleData['productRestrictionId'])) {
            $product = $this->getProductFromFixtureId($cartRuleData['productRestrictionId']);
            if ($product === null) {
                // if product does not exist, skip this rule
                return;
            }
            $cartRule->product_restriction = true;
            $cartRule->reduction_product   = $product->id;
        }
        if (!empty($cartRuleData['productGiftId'])) {
            $product = $this->getProductFromFixtureId($cartRuleData['productGiftId']);
            if ($product === null) {
                // if product does not exist, skip this rule
                return;
            }
            $cartRule->gift_product = $product->id;
        }
        $now = new DateTime();
        // sub 1s to avoid bad comparisons with strictly greater than
        $now->sub(new DateInterval('PT1S'));
        $cartRule->date_from = $now->format('Y-m-d H:i:s');
        $now->add(new DateInterval('P1Y'));
        $cartRule->date_to = $now->format('Y-m-d H:i:s');
        $cartRule->active  = 1;
        if (!empty($cartRuleData['carrierRestrictionIds'])) {
            $cartRule->carrier_restriction = 1;
        }
        $cartRule->add();
        $this->cartRules[$cartRuleFixtureId] = $cartRule;
    }

    protected function addCartRulesToCart(array $cartRuleFixtureIds)
    {
        $allAdded = true;
        foreach ($cartRuleFixtureIds as $cartRuleFixtureId) {
            $cartRule = $this->getCartRuleFromFixtureId($cartRuleFixtureId);
            if ($cartRule === null) {
                $allAdded = false;
            } else {
                $this->cartRulesInCart[] = $cartRule;
                if (!$this->cart->addCartRule($cartRule->id)) {
                    $allAdded = false;
                }
            }
        }

        return $allAdded;
    }

    protected function addCartRuleToCart($cartRuleFixtureId)
    {
        $cartRule = $this->getCartRuleFromFixtureId($cartRuleFixtureId);
        if ($cartRule === null) {
            return false;
        }
        $this->cartRulesInCart[] = $cartRule;

        return $this->cart->addCartRule($cartRule->id);
    }
}
