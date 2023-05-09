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

namespace Tests\Integration\Behaviour\Features\Context;

use Cart;
use Context;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use Tests\Integration\Utility\CartOld;

class CartFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;

    /**
     * @var CartOld
     */
    protected $cart;

    /**
     * @Then there is no delivery options available for my cart
     */
    public function noDeliveryOptions()
    {
        if ($this->getCurrentCart() === null) {
            throw new \RuntimeException('No current cart, cannot check available delivery options');
        }

        $deliveryOptions = $this->getCurrentCart()->getDeliveryOptionList();

        if (!empty($deliveryOptions)) {
            throw new \RuntimeException('Expected no available delivery options, but there are some !');
        }
    }

    /**
     * @Then there are available delivery options for my cart
     *
     * @todo: improve this to assert the content of delivery options
     */
    public function deliveryOptionsAreAvailable()
    {
        if ($this->getCurrentCart() === null) {
            throw new \RuntimeException('No current cart, cannot check available delivery options');
        }

        $deliveryOptions = $this->getCurrentCart()->getDeliveryOptionList();

        if (empty($deliveryOptions)) {
            throw new \RuntimeException('Expected available delivery options, but there are none !');
        }
    }

    /**
     * @Given /^I have an empty default cart$/
     */
    public function iHaveAnEmptyDefaultCart()
    {
        $cart = new CartOld();
        $cart->id_lang = (int) Context::getContext()->language->id;
        $cart->id_currency = (int) Context::getContext()->currency->id;
        $cart->id_shop = (int) Context::getContext()->shop->id;
        $cart->add(); // required, else we cannot get the content when calculating total
        Context::getContext()->cart = $cart;
    }

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function emptyCartProducts()
    {
        if ($this->getCurrentCart() !== null) {
            // remove products from cart
            $productDatas = $this->getCurrentCart()->getProducts(true);
            foreach ($productDatas as $productData) {
                $this->getCurrentCart()->updateQty(0, $productData['id_product'], $productData['id_product_attribute']);
            }
            // delete cart
            $this->getCurrentCart()->delete();
        }
    }

    /**
     * @Then /^I should have (\d+) different products in my cart$/
     */
    public function productCountInMyCartShouldBe($productCount)
    {
        $currentCartProducts = $this->getCurrentCart()->getProducts(true);
        if ($productCount != count($currentCartProducts)) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $productCount, count($currentCartProducts)));
        }
    }

    /**
     * @Then /^I should have (\d+) products in my cart$/
     */
    public function totalProductCountInMyCartShouldBe($productCount)
    {
        $currentCartProducts = Cart::getNbProducts($this->getCurrentCart()->id);
        if ($productCount != $currentCartProducts) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $productCount, $currentCartProducts));
        }
    }

    /**
     * @Then /^my cart total should be (precisely )?(\d+\.\d+) tax included$/
     */
    public function totalCartWithTaxShouldBe($precisely, $expectedTotal)
    {
        $this->expectsTotal($expectedTotal, 'v2', true, !empty($precisely));
    }

    /**
     * @Then /^my cart total using previous calculation method should be (precisely )?(\d+\.\d+) tax included$/
     */
    public function totalCartWithTaxOnPreviousCaclculationMethodShouldBe($precisely, $expectedTotal)
    {
        $this->expectsTotal($expectedTotal, 'v1', true, !empty($precisely));
    }

    /**
     * @Then /^my cart total should be (precisely )?(\d+\.\d+) tax excluded$/
     */
    public function totalCartWithoutTaxShouldBe($precisely, $expectedTotal)
    {
        $this->expectsTotal($expectedTotal, 'v2', false, !empty($precisely));
    }

    /**
     * @Then /^my cart total using previous calculation method should be (precisely )?(\d+\.\d+) tax excluded$/
     */
    public function totalCartWithoutTaxOnPreviousCaclculationMethodShouldBe($precisely, $expectedTotal)
    {
        $this->expectsTotal($expectedTotal, 'v1', false, !empty($precisely));
    }

    protected function expectsTotal($expectedTotal, $method, $withTax = true, $precisely = false)
    {
        if ($method == 'v1') {
            /** @var CartOld $cart */
            $cart = $this->getCurrentCart();
            $carrierId = (int) $cart->id_carrier <= 0 ? null : $cart->id_carrier;
            $total = $cart->getOrderTotalV1($withTax, Cart::BOTH, null, $carrierId);
        } else {
            $cart = $this->getCurrentCart();
            $carrierId = (int) $cart->id_carrier <= 0 ? null : $cart->id_carrier;
            $total = $cart->getOrderTotal($withTax, Cart::BOTH, null, $carrierId);
        }
        if (!$precisely) {
            // here we round values to avoid round issues : rounding modes are tested by specific tests
            $expectedTotal = round($expectedTotal, 1);
            $total = round($total, 1);
        }
        if ($expectedTotal != $total) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $expectedTotal, $total));
        }
    }

    /**
     * @When /^I select gift wrapping$/
     */
    public function iSelectGiftWrapping()
    {
        $this->getCurrentCart()->gift = true;
    }

    /**
     * @Then /^cart shipping fees should be (\d+\.\d+)( tax excluded| tax included)?$/
     */
    public function calculateCartShippingFees($expectedShippingFees, $taxes = null)
    {
        $withTaxes = $taxes == ' tax excluded' ? false : true;
        $expectedTotal = round($expectedShippingFees, 1);
        $shippingFees = round($this->getCurrentCart()->getPackageShippingCost($this->getCurrentCart()->id_carrier, $withTaxes), 1);
        if ($expectedTotal != $shippingFees) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $expectedTotal, $shippingFees));
        }
    }

    /**
     * @Then /^total cart shipping fees should be (\d+\.\d+) tax (excluded|included)?$/
     */
    public function assertTotalCartShipping(string $expectedShipping, bool $taxIncluded): void
    {
        $cart = $this->getCurrentCart();
        $expectedTotal = new DecimalNumber($expectedShipping);
        $actualTotal = new DecimalNumber((string) $cart->getOrderTotal($taxIncluded, Cart::ONLY_SHIPPING));

        Assert::assertSame((string) $expectedTotal, (string) $actualTotal, 'Unexpected total cart shipping');
    }

    /**
     * @Then /^I should have a voucher named "(.+)" with (\d+\.\d+) of discount$/
     */
    public function cartVoucher($voucherName, $discountAmount)
    {
        $cartRules = $this->getCurrentCart()->getCartRules();
        Assert::assertEquals($cartRules[0]['code'], $voucherName);
        Assert::assertEquals((float) $cartRules[0]['value_real'], (float) $discountAmount);
    }
}
