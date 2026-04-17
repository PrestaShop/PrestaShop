<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Cart;
use Context;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use RuntimeException;

class CartFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;

    /**
     * @Then there is no delivery options available for my cart
     */
    public function noDeliveryOptions()
    {
        if ($this->getCurrentCart() === null) {
            throw new RuntimeException('No current cart, cannot check available delivery options');
        }

        $deliveryOptions = $this->getCurrentCart()->getDeliveryOptionList();

        if (!empty($deliveryOptions)) {
            throw new RuntimeException('Expected no available delivery options, but there are some !');
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
            throw new RuntimeException('No current cart, cannot check available delivery options');
        }

        $deliveryOptions = $this->getCurrentCart()->getDeliveryOptionList();

        if (empty($deliveryOptions)) {
            throw new RuntimeException('Expected available delivery options, but there are none !');
        }
    }

    /**
     * @Given /^I have an empty default cart$/
     */
    public function iHaveAnEmptyDefaultCart()
    {
        $cart = new Cart();
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
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $productCount, count($currentCartProducts)));
        }
    }

    /**
     * @Then /^I should have (\d+) products in my cart$/
     */
    public function totalProductCountInMyCartShouldBe($productCount)
    {
        $currentCartProducts = Cart::getNbProducts($this->getCurrentCart()->id);
        if ($productCount != $currentCartProducts) {
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $productCount, $currentCartProducts));
        }
    }

    /**
     * @Then /^my cart total should be (precisely )?(\d+\.\d+) tax included$/
     */
    public function totalCartWithTaxShouldBe($precisely, $expectedTotal)
    {
        $this->expectsTotal($expectedTotal, true, !empty($precisely));
    }

    /**
     * @Then /^my cart total should be (precisely )?(\d+\.\d+) tax excluded$/
     */
    public function totalCartWithoutTaxShouldBe($precisely, $expectedTotal)
    {
        $this->expectsTotal($expectedTotal, false, !empty($precisely));
    }

    protected function expectsTotal($expectedTotal, $withTax = true, $precisely = false)
    {
        $cart = $this->getCurrentCart();
        $carrierId = (int) $cart->id_carrier <= 0 ? null : $cart->id_carrier;
        $total = $cart->getOrderTotal($withTax, Cart::BOTH, null, $carrierId);
        if (!$precisely) {
            // here we round values to avoid round issues : rounding modes are tested by specific tests
            $expectedTotal = round($expectedTotal, 1);
            $total = round($total, 1);
        }
        if ($expectedTotal != $total) {
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $expectedTotal, $total));
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
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $expectedTotal, $shippingFees));
        }
    }

    /**
     * @todo: check if possible to unify this step with calculateCartShippingFees() so that they produce the same result (maybe selecting currency and address is missing?)
     *
     * @Then /^my cart total shipping fees should be (\d+\.\d+) tax (excluded|included)?$/
     */
    public function assertTotalCartShipping(string $expectedShipping, bool $taxIncluded): void
    {
        $cart = $this->getCurrentCart();
        $expectedTotal = new DecimalNumber($expectedShipping);
        // using ONLY_SHIPPING will not reduce discounts, so this method is not suitable to assert free_shipping discount application
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
