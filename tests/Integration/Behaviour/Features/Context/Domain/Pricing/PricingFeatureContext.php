<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Pricing;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Cart\Calculator\CartCalculator;
use PrestaShop\PrestaShop\Core\Pricing\Cart\CartPrice;
use PrestaShop\PrestaShop\Core\Pricing\Cart\CartPriceInterface;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\ProductCalculator;
use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPrice;
use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPriceInterface;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePriceInterface;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;

/**
 * Behat context for testing the new pricing calculator pipeline.
 * Each step computes and asserts in a single call to avoid shared state side effects.
 */
class PricingFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I compute the product price for product :productReference with quantity :quantity I should get:
     */
    public function iComputeProductPriceAndAssert(string $productReference, int $quantity, TableNode $table): void
    {
        $productId = $this->referenceToId($productReference);
        $productPrice = ProductPrice::create($productId, 0, $quantity);

        $this->getCartProductCalculator()->compute($productPrice);
        $this->assertProductPrice($productPrice, $table);
    }

    /**
     * @When I compute the product price for product :productReference with combination :combinationReference and quantity :quantity I should get:
     */
    public function iComputeProductPriceWithCombinationAndAssert(string $productReference, string $combinationReference, int $quantity, TableNode $table): void
    {
        $productId = $this->referenceToId($productReference);
        $combinationId = $this->referenceToId($combinationReference);
        $productPrice = ProductPrice::create($productId, $combinationId, $quantity);

        $this->getCartProductCalculator()->compute($productPrice);
        $this->assertProductPrice($productPrice, $table);
    }

    protected function assertProductPrice(ProductPriceInterface $productPrice, TableNode $table): void
    {
        $data = $table->getRowsHash();

        $this->assertPriceField($productPrice->getOriginalPrice(), 'original_price', $data);
        $this->assertPriceField($productPrice->getUnitPrice(), 'unit_price', $data);
        $this->assertPriceField($productPrice->getDiscountPrice(), 'discount_price', $data);
        $this->assertPriceField($productPrice->getFinalPrice(), 'final_price', $data);
    }

    protected function assertPriceField(TaxablePriceInterface $price, string $fieldName, array $data): void
    {
        $taxExclKey = $fieldName . '_tax_excluded';
        $taxInclKey = $fieldName . '_tax_included';

        if (isset($data[$taxExclKey])) {
            Assert::assertTrue(
                $price->getTaxExcluded()->equals(new DecimalNumber($data[$taxExclKey])),
                sprintf(
                    'Expected %s %s, got %s',
                    $taxExclKey,
                    $data[$taxExclKey],
                    (string) $price->getTaxExcluded()
                )
            );
        }

        if (isset($data[$taxInclKey])) {
            Assert::assertTrue(
                $price->getTaxIncluded()->equals(new DecimalNumber($data[$taxInclKey])),
                sprintf(
                    'Expected %s %s, got %s',
                    $taxInclKey,
                    $data[$taxInclKey],
                    (string) $price->getTaxIncluded()
                )
            );
        }
    }

    /**
     * @When I compute the cart price for cart :cartReference I should get:
     */
    public function iComputeCartPriceAndAssert(string $cartReference, TableNode $table): void
    {
        $cartId = (int) $this->getSharedStorage()->get($cartReference);
        $cartPrice = CartPrice::create($cartId);

        $this->getCartCalculator()->compute($cartPrice);
        $this->assertCartPrice($cartPrice, $table);
    }

    protected function assertCartPrice(CartPriceInterface $cartPrice, TableNode $table): void
    {
        $data = $table->getRowsHash();

        if (isset($data['product_count'])) {
            Assert::assertCount(
                (int) $data['product_count'],
                $cartPrice->getProductPrices(),
                sprintf(
                    'Expected %d products, got %d',
                    (int) $data['product_count'],
                    count($cartPrice->getProductPrices())
                )
            );
        }

        $this->assertPriceField($cartPrice->getProductTotal(), 'product_total', $data);
        $this->assertPriceField($cartPrice->getShippingTotal(), 'shipping_total', $data);
        $this->assertPriceField($cartPrice->getWrappingTotal(), 'wrapping_total', $data);
        $this->assertPriceField($cartPrice->getDiscountTotal(), 'discount_total', $data);
        $this->assertPriceField($cartPrice->getCartTotal(), 'cart_total', $data);
    }

    protected function getCartProductCalculator(): ProductCalculator
    {
        return $this->getContainer()->get('prestashop.pricing.cart.product_calculator');
    }

    protected function getCartCalculator(): CartCalculator
    {
        return $this->getContainer()->get('prestashop.pricing.cart.cart_calculator');
    }
}
