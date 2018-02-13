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

use Cart;
use CartCore;
use CartRule;
use Configuration;
use Context;
use Tests\TestCase\IntegrationTestCase;
use Product;
use StockAvailable;

/**
 * these tests aim to check cart using mocks
 *
 * products and cartRules are inserted as fixtures
 */
abstract class AbstractCartTest extends IntegrationTestCase
{

    /**
     * @var CartCore
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

    protected $productFixtures = [
        1 => array('price' => 19.812),
        2 => array('price' => 32.388),
        3 => array('price' => 31.188),
        4 => array('price' => 35.567, 'outOfStock' => true),
    ];

    protected $cartRuleFixtures = [
        1  => array('priority' => 1, 'percent' => 50, 'amount' => 0),
        2  => array('priority' => 2, 'percent' => 50, 'amount' => 0),
        3  => array('priority' => 3, 'percent' => 10, 'amount' => 0),
        4  => array('priority' => 4, 'percent' => 0, 'amount' => 5),
        5  => array('priority' => 5, 'percent' => 0, 'amount' => 500),
        6  => array('priority' => 6, 'percent' => 0, 'amount' => 10),
        7  => array('priority' => 7, 'percent' => 50, 'amount' => 0),
        8  => array('priority' => 8, 'percent' => 0, 'amount' => 5, 'productRestrictionId' => 2),
        9  => array('priority' => 8, 'percent' => 0, 'amount' => 500, 'productRestrictionId' => 2),
        10 => array('priority' => 8, 'percent' => 50, 'amount' => 0, 'productRestrictionId' => 2),
        11 => array('priority' => 8, 'percent' => 10, 'amount' => 0, 'productRestrictionId' => 2),
        12 => array('priority' => 8, 'percent' => 10, 'amount' => 0, 'productGiftId' => 3),
        13 => array('priority' => 8, 'percent' => 10, 'amount' => 0, 'productGiftId' => 4),
    ];

    public function setUp()
    {
        parent::setUp();
        $this->cart              = new Cart();
        $this->cart->id_lang     = (int) Context::getContext()->language->id;
        $this->cart->id_currency = (int) Context::getContext()->currency->id;
        $this->cart->id_shop     = (int) Context::getContext()->shop->id;
        $this->cart->add(); // required, else we cannot get the content when calculation total
        Context::getContext()->cart = $this->cart;
        $this->resetCart();
        $this->insertProducts();
        $this->insertCartRules();
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

    protected function insertProducts()
    {
        foreach ($this->productFixtures as $k => $productFixture) {
            $product           = new Product;
            $product->price    = $productFixture['price'];
            $product->name     = 'product name';
            $product->quantity = 1000;
            if (!empty($productFixture['outOfStock'])) {
                $product->out_of_stock = 0;
                $product->quantity     = 0;
            }
            $product->add();
            if (!empty($productFixture['outOfStock'])) {
                StockAvailable::setProductOutOfStock((int) $product->id, 0);
            } else {
                StockAvailable::setQuantity((int) $product->id, 0, 1000);
            }
            $this->products[$k] = $product;
        }
    }

    protected function addProductsToCart($productData)
    {
        foreach ($productData as $id => $quantity) {
            $product = $this->getProductFromFixtureId($id);
            if ($product !== null) {
                $this->cart->updateQty($quantity, $product->id);
            }
        }
    }

    /**
     * @param int $id fixture product id
     *
     * @return Product|null
     */
    protected function getProductFromFixtureId($id)
    {
        if (isset($this->products[$id])) {
            return $this->products[$id];
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

    protected function insertCartRules()
    {
        foreach ($this->cartRuleFixtures as $k => $cartRuleData) {
            $cartRule                    = new CartRule;
            $cartRule->reduction_percent = $cartRuleData['percent'];
            $cartRule->reduction_amount  = $cartRuleData['amount'];
            $cartRule->name              = array(Configuration::get('PS_LANG_DEFAULT') => 'foo');
            $cartRule->code              = 'bar';
            $cartRule->priority          = $cartRuleData['priority'];
            $cartRule->quantity          = 1000;
            $cartRule->quantity_per_user = 1000;
            if (!empty($cartRuleData['productRestrictionId'])) {
                $product = $this->getProductFromFixtureId($cartRuleData['productRestrictionId']);
                if ($product === null) {
                    // if product does not exist, skip this rule
                    continue;
                }
                $cartRule->product_restriction = true;
                $cartRule->reduction_product   = $product->id;
            }
            if (!empty($cartRuleData['productGiftId'])) {
                $product = $this->getProductFromFixtureId($cartRuleData['productGiftId']);
                if ($product === null) {
                    // if product does not exist, skip this rule
                    continue;
                }
                $cartRule->gift_product = $product->id;
            }
            $now = new \DateTime();
            // sub 1s to avoid bad comparisons with strictly greater than
            $now->sub(new \DateInterval('PT1S'));
            $cartRule->date_from = $now->format('Y-m-d H:i:s');
            $now->add(new \DateInterval('P1Y'));
            $cartRule->date_to = $now->format('Y-m-d H:i:s');
            $cartRule->active  = 1;
            $cartRule->add();
            $this->cartRules[$k] = $cartRule;
        }
    }

    protected function addCartRulesToCart(array $cartRuleIds)
    {
        foreach ($cartRuleIds as $cartRuleId) {
            $cartRule = $this->getCartRuleFromFixtureId($cartRuleId);
            if ($cartRule !== null) {
                $this->cartRulesInCart[] = $cartRule;
                $this->cart->addCartRule($cartRule->id);
            }
        }
    }

}
