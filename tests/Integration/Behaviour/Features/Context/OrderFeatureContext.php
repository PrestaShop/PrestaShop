<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Address;
use Attribute;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Carrier;
use Cart;
use CartRule;
use Configuration;
use Context;
use Currency;
use Customer;
use Exception;
use Hook;
use ImageManager;
use LegacyTests\Unit\Core\Cart\CartToOrder\PaymentModuleFake;
use Order;
use OrderCarrier;
use OrderCartRule;
use OrderDetail;
use OrderInvoice;
use OrderReturn;
use OrderSlip;
use Product;
use Shop;
use SpecificPrice;
use StockAvailable;
use Tools;
use Validate;
use Warehouse;
use WarehouseProductLocation;

class OrderFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;

    /**
     * @var Order[]
     */
    protected $orders = [];

    /**
     * @var ProductFeatureContext
     */
    protected $productFeatureContext;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        $this->productFeatureContext = $scope->getEnvironment()->getContext(ProductFeatureContext::class);
    }

    /**
     * @When /^I validate my cart using payment module (fake)$/
     */
    public function validateCartWithPaymentModule($paymentModuleName)
    {
        switch ($paymentModuleName) {
            case 'fake':
                $paymentModule = new PaymentModuleFake();
                break;
            default:
                throw new Exception(sprintf('Invalid payment module: %s' . $paymentModuleName));
        }

        // need to boot kernel for usage in $paymentModule->validateOrder()
        global $kernel;
        $previousKernel = $kernel;
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        // need to update secret_key in order to get payment working
        $cart = $this->getCurrentCart();
        $cart->secure_key = md5('xxx');
        $cart->update();
        $paymentModule->validateOrder(
            $cart->id,
            Configuration::get('PS_OS_CHEQUE'), // PS_OS_PAYMENT for payment-validated order
            0,
            'Unknown',
            null,
            [],
            null,
            false,
            $cart->secure_key
        );
        $order = Order::getByCartId($cart->id);
        $this->orders[] = $order;

        $kernel = $previousKernel;
    }

    /**
     * @When /^(\d+) items? of product "(.+)" are added in my cart order, with prices (\d+\.\d+) tax excluded and (\d+\.\d+) tax included$/
     */
    public function addProductInCartOrder($quantity, $productName, $priceTaxExcl, $priceTaxIncl)
    {
        $cart = $this->getCurrentCart();
        $order = $this->getCurrentCartOrder();
        $this->productFeatureContext->checkProductWithNameExists($productName);
        $product = $this->productFeatureContext->getProductWithName($productName);
        $this->addProductToOrder($order, [
            'product_id' => $product->id,
            'product_price_tax_excl' => $priceTaxExcl,
            'product_price_tax_incl' => $priceTaxIncl,
            'product_quantity' => $quantity,
        ]);
        // restore correct cart since previous method has overridden it
        Context::getContext()->cart = $cart;
    }

    /**
     * Duplicate from AdminOrderController::addProductToOrder
     *
     * @param Order $order
     * @param array $product_informations
     * @param array $invoice_informations
     * @param bool $warehouseId
     *
     * @return array
     */
    protected function addProductToOrder(Order $order, array $product_informations, array $invoice_informations = [], $warehouseId = false)
    {
        $old_cart_rules = Context::getContext()->cart->getCartRules();
        if ($order->hasBeenShipped()) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('You cannot add products to delivered orders.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        $product = new Product($product_informations['product_id'], false, $order->id_lang);
        if (!Validate::isLoadedObject($product)) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('The product object cannot be loaded.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        if (isset($product_informations['product_attribute_id']) && $product_informations['product_attribute_id']) {
            $combination = new Combination($product_informations['product_attribute_id']);
            if (!Validate::isLoadedObject($combination)) {
                die(json_encode(array(
                    'result' => false,
                    'error' => $this->trans('The combination object cannot be loaded.', array(), 'Admin.Orderscustomers.Notification'),
                )));
            }
        }

        // Total method
        $total_method = Cart::BOTH_WITHOUT_SHIPPING;

        // Create new cart
        $cart = new Cart();
        $cart->id_shop_group = $order->id_shop_group;
        $cart->id_shop = $order->id_shop;
        $cart->id_customer = $order->id_customer;
        $cart->id_carrier = $order->id_carrier;
        $cart->id_address_delivery = $order->id_address_delivery;
        $cart->id_address_invoice = $order->id_address_invoice;
        $cart->id_currency = $order->id_currency;
        $cart->id_lang = $order->id_lang;
        $cart->secure_key = $order->secure_key;

        // Save new cart
        $cart->add();

        // Save context (in order to apply cart rule)
        Context::getContext()->cart = $cart;
        Context::getContext()->customer = new Customer($order->id_customer);

        // always add taxes even if there are not displayed to the customer
        $use_taxes = true;

        $initial_product_price_tax_incl = Product::getPriceStatic(
            $product->id,
            $use_taxes,
            isset($combination) ? $combination->id : null,
            2,
            null,
            false,
            true,
            1,
            false,
            $order->id_customer,
            $cart->id,
            $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)}
        );

        // Creating specific price if needed
        if ($product_informations['product_price_tax_incl'] != $initial_product_price_tax_incl) {
            $specific_price = new SpecificPrice();
            $specific_price->id_shop = 0;
            $specific_price->id_shop_group = 0;
            $specific_price->id_currency = 0;
            $specific_price->id_country = 0;
            $specific_price->id_group = 0;
            $specific_price->id_customer = $order->id_customer;
            $specific_price->id_product = $product->id;
            if (isset($combination)) {
                $specific_price->id_product_attribute = $combination->id;
            } else {
                $specific_price->id_product_attribute = 0;
            }
            $specific_price->price = $product_informations['product_price_tax_excl'];
            $specific_price->from_quantity = 1;
            $specific_price->reduction = 0;
            $specific_price->reduction_type = 'amount';
            $specific_price->reduction_tax = 0;
            $specific_price->from = '0000-00-00 00:00:00';
            $specific_price->to = '0000-00-00 00:00:00';
            $specific_price->add();
        }

        // Add product to cart
        $update_quantity = $cart->updateQty(
            $product_informations['product_quantity'],
            $product->id,
            isset($product_informations['product_attribute_id']) ? $product_informations['product_attribute_id'] : null,
            isset($combination) ? $combination->id : null,
            'up',
            0,
            new Shop($cart->id_shop)
        );

        if ($update_quantity < 0) {
            // If product has attribute, minimal quantity is set with minimal quantity of attribute
            $minimal_quantity = ($product_informations['product_attribute_id']) ? Attribute::getAttributeMinimalQty($product_informations['product_attribute_id']) : $product->minimal_quantity;
            die(json_encode(array('error' => $this->trans('You must add %d minimum quantity', array('%d' => $minimal_quantity), 'Admin.Orderscustomers.Notification'))));
        } elseif (!$update_quantity) {
            die(json_encode(array('error' => $this->trans('You already have the maximum quantity available for this product.', array(), 'Admin.Orderscustomers.Notification'))));
        }

        // If order is valid, we can create a new invoice or edit an existing invoice
        if ($order->hasInvoice()) {
            $order_invoice = new OrderInvoice($product_informations['invoice']);
            // Create new invoice
            if ($order_invoice->id == 0) {
                // If we create a new invoice, we calculate shipping cost
                $total_method = Cart::BOTH;
                // Create Cart rule in order to make free shipping
                if (isset($invoice_informations['free_shipping']) && $invoice_informations['free_shipping']) {
                    $cart_rule = new CartRule();
                    $cart_rule->id_customer = $order->id_customer;
                    $cart_rule->name = array(
                        Configuration::get('PS_LANG_DEFAULT') => $this->trans('[Generated] CartRule for Free Shipping', array(), 'Admin.Orderscustomers.Notification'),
                    );
                    $cart_rule->date_from = date('Y-m-d H:i:s', time());
                    $cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 3600);
                    $cart_rule->quantity = 1;
                    $cart_rule->quantity_per_user = 1;
                    $cart_rule->minimum_amount_currency = $order->id_currency;
                    $cart_rule->reduction_currency = $order->id_currency;
                    $cart_rule->free_shipping = true;
                    $cart_rule->active = 1;
                    $cart_rule->add();

                    // Add cart rule to cart and in order
                    $cart->addCartRule($cart_rule->id);
                    $values = array(
                        'tax_incl' => $cart_rule->getContextualValue(true),
                        'tax_excl' => $cart_rule->getContextualValue(false),
                    );
                    $order->addCartRule($cart_rule->id, $cart_rule->name[Configuration::get('PS_LANG_DEFAULT')], $values);
                }

                $order_invoice->id_order = $order->id;
                if ($order_invoice->number) {
                    Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $order->id_shop);
                } else {
                    $order_invoice->number = Order::getLastInvoiceNumber() + 1;
                }

                $invoice_address = new Address((int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});
                $carrier = new Carrier((int) $order->id_carrier);
                $tax_calculator = $carrier->getTaxCalculator($invoice_address);

                $order_invoice->total_paid_tax_excl = Tools::ps_round((float) $cart->getOrderTotal(false, $total_method), 2);
                $order_invoice->total_paid_tax_incl = Tools::ps_round((float) $cart->getOrderTotal($use_taxes, $total_method), 2);
                $order_invoice->total_products = (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
                $order_invoice->total_products_wt = (float) $cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);
                $order_invoice->total_shipping_tax_excl = (float) $cart->getTotalShippingCost(null, false);
                $order_invoice->total_shipping_tax_incl = (float) $cart->getTotalShippingCost();

                $order_invoice->total_wrapping_tax_excl = abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
                $order_invoice->total_wrapping_tax_incl = abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
                $order_invoice->shipping_tax_computation_method = (int) $tax_calculator->computation_method;

                // Update current order field, only shipping because other field is updated later
                $order->total_shipping += $order_invoice->total_shipping_tax_incl;
                $order->total_shipping_tax_excl += $order_invoice->total_shipping_tax_excl;
                $order->total_shipping_tax_incl += ($use_taxes) ? $order_invoice->total_shipping_tax_incl : $order_invoice->total_shipping_tax_excl;

                $order->total_wrapping += abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
                $order->total_wrapping_tax_excl += abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
                $order->total_wrapping_tax_incl += abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
                $order_invoice->add();

                $order_invoice->saveCarrierTaxCalculator($tax_calculator->getTaxesAmount($order_invoice->total_shipping_tax_excl));

                $order_carrier = new OrderCarrier();
                $order_carrier->id_order = (int) $order->id;
                $order_carrier->id_carrier = (int) $order->id_carrier;
                $order_carrier->id_order_invoice = (int) $order_invoice->id;
                $order_carrier->weight = (float) $cart->getTotalWeight();
                $order_carrier->shipping_cost_tax_excl = (float) $order_invoice->total_shipping_tax_excl;
                $order_carrier->shipping_cost_tax_incl = ($use_taxes) ? (float) $order_invoice->total_shipping_tax_incl : (float) $order_invoice->total_shipping_tax_excl;
                $order_carrier->add();
            } else {
                // Update current invoice
                $order_invoice->total_paid_tax_excl += Tools::ps_round((float) ($cart->getOrderTotal(false, $total_method)), 2);
                $order_invoice->total_paid_tax_incl += Tools::ps_round((float) ($cart->getOrderTotal($use_taxes, $total_method)), 2);
                $order_invoice->total_products += (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
                $order_invoice->total_products_wt += (float) $cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);
                $order_invoice->update();
            }
        }

        // Create Order detail information
        $order_detail = new OrderDetail();
        $order_detail->createList($order, $cart, $order->getCurrentOrderState(), $cart->getProducts(), (isset($order_invoice) ? $order_invoice->id : 0), $use_taxes, (int) $warehouseId);

        // update totals amount of order
        $order->total_products += (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $order->total_products_wt += (float) $cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);

        $order->total_paid += Tools::ps_round((float) ($cart->getOrderTotal(true, $total_method)), 2);
        $order->total_paid_tax_excl += Tools::ps_round((float) ($cart->getOrderTotal(false, $total_method)), 2);
        $order->total_paid_tax_incl += Tools::ps_round((float) ($cart->getOrderTotal($use_taxes, $total_method)), 2);

        if (isset($order_invoice) && Validate::isLoadedObject($order_invoice)) {
            $order->total_shipping = $order_invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_incl = $order_invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_excl = $order_invoice->total_shipping_tax_excl;
        }
        // discount
        $order->total_discounts += (float) abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
        $order->total_discounts_tax_excl += (float) abs($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS));
        $order->total_discounts_tax_incl += (float) abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));

        // Save changes of order
        $order->update();

        StockAvailable::synchronize($product->id);

        // Update weight SUM
        $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float) $order->getTotalWeight();
            if ($order_carrier->update()) {
                $order->weight = sprintf('%.3f ' . Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }

        // Update Tax lines
        $order_detail->updateTaxAmount($order);

        // Delete specific price if exists
        if (isset($specific_price)) {
            $specific_price->delete();
        }

        // Replace $this->getProducts($order);
        $products = $order->getProducts();
        foreach ($products as &$product) {
            if ($product['image'] != null) {
                $name = 'product_mini_' . (int) $product['product_id'] . (isset($product['product_attribute_id']) ? '_' . (int) $product['product_attribute_id'] : '') . '.jpg';
                // generate image cache, only for back office
                $product['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $product['image']->getExistingImgPath() . '.jpg', $name, 45, 'jpg');
                if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                    $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                } else {
                    $product['image_size'] = false;
                }
            }
        }
        ksort($products);

        // Get the last product
        $product = end($products);
        $resume = OrderSlip::getProductSlipResume((int) $product['id_order_detail']);
        $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
        $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
        $product['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl']);
        $product['return_history'] = OrderReturn::getProductReturnDetail((int) $product['id_order_detail']);
        $product['refund_history'] = OrderSlip::getProductSlipDetail((int) $product['id_order_detail']);
        if ($product['id_warehouse'] != 0) {
            $warehouse = new Warehouse((int) $product['id_warehouse']);
            $product['warehouse_name'] = $warehouse->name;
            $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
            if (!empty($warehouse_location)) {
                $product['warehouse_location'] = $warehouse_location;
            } else {
                $product['warehouse_location'] = false;
            }
        } else {
            $product['warehouse_name'] = '--';
            $product['warehouse_location'] = false;
        }

        // Get invoices collection
        $invoice_collection = $order->getInvoicesCollection();

        $invoice_array = array();
        foreach ($invoice_collection as $invoice) {
            /* @var OrderInvoice $invoice */
            $invoice->name = $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int) $order->id_shop);
            $invoice_array[] = $invoice;
        }

        /** @var Order $order */
        $order = $order->refreshShippingCost();

        // Assign to smarty informations in order to show the new product line
        Context::getContext()->smarty->assign(array(
            'product' => $product,
            'order' => $order,
            'currency' => new Currency($order->id_currency),
            // 'can_edit' => $this->access('edit'),
            'invoices_collection' => $invoice_collection,
            'current_id_lang' => Context::getContext()->language->id,
            'link' => Context::getContext()->link,
            // 'current_index' => self::$currentIndex,
            'display_warehouse' => (int) Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
        ));

        // Replace $this->sendChangedNotification($order);
        if (null === $order) {
            $order = new Order(Tools::getValue('id_order'));
        }
        Hook::exec('actionOrderEdited', array('order' => $order));

        $new_cart_rules = Context::getContext()->cart->getCartRules();
        sort($old_cart_rules);
        sort($new_cart_rules);
        $result = array_diff($new_cart_rules, $old_cart_rules);
        $refresh = false;

        $res = true;
        foreach ($result as $cart_rule) {
            $refresh = true;
            // Create OrderCartRule
            $rule = new CartRule($cart_rule['id_cart_rule']);
            $values = array(
                'tax_incl' => $rule->getContextualValue(true),
                'tax_excl' => $rule->getContextualValue(false),
            );
            $order_cart_rule = new OrderCartRule();
            $order_cart_rule->id_order = $order->id;
            $order_cart_rule->id_cart_rule = $cart_rule['id_cart_rule'];
            if ($order->hasInvoice()) {
                $order_cart_rule->id_order_invoice = $order_invoice->id;
            }
            $order_cart_rule->name = $cart_rule['name'];
            $order_cart_rule->value = $values['tax_incl'];
            $order_cart_rule->value_tax_excl = $values['tax_excl'];
            $res &= $order_cart_rule->add();
        }

        // Update Order
        $res &= $order->update();

        return [
            'invoice_array' => $invoice_array,
            'refresh' => $refresh,
        ];
    }

    /**
     * @Then /^current cart order total for products should be (\d+\.\d+)( tax included| tax excluded)?$/
     */
    public function checkOrderProductTotal($expectedTotal, $taxes = null)
    {
        $order = $this->getCurrentCartOrder();
        $withTaxes = $taxes == ' tax excluded' ? false : true;
        $total = $withTaxes ? $order->total_products_wt : $order->total_products;
        if ((float) $expectedTotal != (float) $total) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $expectedTotal,
                    $total
                )
            );
        }
    }

    /**
     * @Then /^current cart order total discount should be (\d+\.\d+)( tax included| tax excluded)?$/
     */
    public function checkOrderTotalDiscount($expectedTotal, $taxes = null)
    {
        $order = $this->getCurrentCartOrder();
        $withTaxes = $taxes == ' tax excluded' ? false : true;
        $total = $withTaxes ? $order->total_discounts_tax_incl : $order->total_discounts_tax_excl;
        if ((float) $expectedTotal != (float) $total) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $expectedTotal,
                    $total
                )
            );
        }
    }

    /**
     * @Then /^current cart order shipping fees should be (\d+\.\d+)( tax included| tax excluded)?$/
     */
    public function checkOrderShippingFees($expectedTotal, $taxes = null)
    {
        $order = $this->getCurrentCartOrder();
        $withTaxes = $taxes == ' tax excluded' ? false : true;
        $total = $withTaxes ? $order->total_shipping_tax_incl : $order->total_shipping_tax_excl;
        if ((float) $expectedTotal != (float) $total) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $expectedTotal,
                    $total
                )
            );
        }
    }

    /**
     * @Then /^current cart order cart rules count should be (\d+)$/
     */
    public function checkOrderCartRulesCount($expectedCount)
    {
        $order = $this->getCurrentCartOrder();
        $count = count($order->getCartRules());
        if ($expectedCount != $count) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $expectedCount,
                    $count
                )
            );
        }
    }

    /**
     * @Then /^current cart order should have a discount in position (\d+) with an amount of (.+) tax included and (.+) tax excluded$/
     */
    public function checkOrderDiscount($position, $discountTaxIncluded, $discountTaxExcluded)
    {
        $order = $this->getCurrentCartOrder();
        $orderCartRulesData = $order->getCartRules();
        if (!isset($orderCartRulesData[$position - 1]['id_order_cart_rule'])) {
            throw new Exception(
                sprintf('Undefined order cart rule on position #%s', $position)
            );
        }
        $orderCartRule = new OrderCartRule($orderCartRulesData[$position - 1]['id_order_cart_rule']);
        if ((float) $discountTaxIncluded != (float) $orderCartRule->value) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $discountTaxIncluded,
                    $orderCartRule->value
                )
            );
        }
        if ((float) $discountTaxExcluded != (float) $orderCartRule->value_tax_excl) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $discountTaxIncluded,
                    $orderCartRule->value_tax_excl
                )
            );
        }
    }

    /**
     * @Then /^current cart order should have no discount$/
     */
    public function checkOrderNoDiscount()
    {
        $order = $this->getCurrentCartOrder();
        $orderCartRulesData = $order->getCartRules();
        if (!empty($orderCartRulesData)) {
            throw new Exception(
                sprintf('Order should have no cart rule')
            );
        }
    }

    /**
     * @Then order :reference should have :quantity products in total
     */
    public function assertOrderProductsQuantity($reference, $quantity)
    {
        $order = SharedStorage::getStorage()->get($reference);
        $orderProducts = $order->getProductsDetail();

        $totalQuantity = 0;

        foreach ($orderProducts as $orderProduct) {
            $totalQuantity += (int) $orderProduct['product_quantity'];
        }

        if ($totalQuantity !== (int) $quantity) {
            throw new Exception(sprintf(
                'Order should have "%d" products, but has "%d".',
                $totalQuantity,
                $quantity
            ));
        }
    }

    /**
     * @Given there is order with reference :orderReference
     */
    public function thereIsOrderWithReference($orderReference)
    {
        $orders = Order::getByReference($orderReference);

        if (0 === $orders->count()) {
            throw new \Exception(sprintf('Order with reference "%s" does not exist.', $orderReference));
        }
    }

    /**
     * @Then order :reference should have free shipping
     */
    public function createdOrderShouldHaveFreeShipping($reference)
    {
        $order = SharedStorage::getStorage()->get($reference);

        foreach ($order->getCartRules() as $cartRule) {
            if ($cartRule['free_shipping']) {
                return;
            }
        }

        throw new Exception('Order should have free shipping.');
    }

    /**
     * @Then order :reference should have :paymentModuleName payment method
     */
    public function createdOrderShouldHavePaymentMethod($reference, $paymentModuleName)
    {
        $order = SharedStorage::getStorage()->get($reference);

        if ($order->module !== $paymentModuleName) {
            throw new Exception(sprintf(
                'Order should have "%s" payment method, but has "%s" instead.',
                $paymentModuleName,
                $order->payment
            ));
        }
    }

    /**
     * @Given order with reference :orderReference does not contain product with reference :productReference
     */
    public function orderDoesNotContainProductWithReference($orderReference, $productReference)
    {
        $orders = Order::getByReference($orderReference);
        /** @var Order $order */
        $order = $orders->getFirst();

        $productId = Product::getIdByReference($productReference);

        if ($order->orderContainProduct($productId)) {
            throw new \RuntimeException(
                sprintf(
                    'Order with reference "%s" contains product with reference "%s".',
                    $orderReference,
                    $productReference
                )
            );
        }
    }

    /**
     * @Then order :orderReference should contain :quantity products with reference :productReference
     */
    public function orderContainsProductWithReference($orderReference, $quantity, $productReference)
    {
        $orders = Order::getByReference($orderReference);
        /** @var Order $order */
        $order = $orders->getFirst();

        $productId = (int) Product::getIdByReference($productReference);

        if (!$order->orderContainProduct($productId)) {
            throw new \RuntimeException(
                sprintf(
                    'Order with reference "%s" does not contain product with reference "%s".',
                    $orderReference,
                    $productReference
                )
            );
        }

        $orderDetails = $order->getOrderDetailList();

        foreach ($orderDetails as $orderDetail) {
            if ((int) $orderDetail['product_id'] === $productId &&
                (int) $orderDetail['product_quantity'] === (int) $quantity
            ) {
                return;
            }
        }

        throw new \RuntimeException(
            sprintf('Order was expected to have "%d" products "%s" in it.', $quantity, $productReference)
        );
    }

    /**
     * @Given order :orderReference does not have any invoices
     */
    public function orderDoesNotHaveAnyInvoices($orderReference)
    {
        $orders = Order::getByReference($orderReference);
        /** @var Order $order */
        $order = $orders->getFirst();

        if ($order->hasInvoice()) {
            throw new \RuntimeException('Order should not have any invoices');
        }
    }

    /**
     * @Then order :orderReference should have invoice
     */
    public function orderShouldHaveInvoice($orderReference)
    {
        $orders = Order::getByReference($orderReference);
        /** @var Order $order */
        $order = $orders->getFirst();

        if (false === $order->hasInvoice()) {
            throw new \RuntimeException(sprintf('Order "%s" should have invoice', $orderReference));
        }
    }

    protected function getCurrentCartOrder()
    {
        $cart = $this->getCurrentCart();
        if (null === $cart) {
            throw new Exception('Current cart was not initialized');
        }
        $order = Order::getByCartId($cart->id);

        return $order;
    }

    /**
     * @AfterScenario
     */
    public function cleanOrderFixtures()
    {
        foreach ($this->orders as $order) {
            $order->delete();
        }
        $this->orders = [];
    }
}
