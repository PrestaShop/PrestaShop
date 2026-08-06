<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Cart\Calculator;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Cart\CartPriceInterface;
use PrestaShop\PrestaShop\Core\Pricing\Cart\Provider\CartProductProviderInterface;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\ProductCalculatorInterface;
use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

/**
 * First calculator in the cart pipeline: fetches cart products, computes each product's
 * price via the ProductCalculator, then sums all product totals into productTotal.
 *
 * @experimental
 */
class ProductTotalCalculator implements CartCalculatorInterface
{
    public function __construct(
        protected readonly CartProductProviderInterface $cartProductProvider,
        protected readonly ProductCalculatorInterface $productCalculator,
    ) {
    }

    public function compute(CartPriceInterface $cartPrice): void
    {
        $cartProducts = $this->cartProductProvider->getCartProducts($cartPrice->getCartId());

        $productPrices = [];
        $totalTaxExcluded = new DecimalNumber('0');
        $totalTaxIncluded = new DecimalNumber('0');

        foreach ($cartProducts as $cartProduct) {
            $productPrice = ProductPrice::create(
                $cartProduct->getProductId(),
                $cartProduct->getCombinationId(),
                $cartProduct->getQuantity()
            );

            $this->productCalculator->compute($productPrice);

            $quantity = new DecimalNumber((string) $cartProduct->getQuantity());
            $totalTaxExcluded = $totalTaxExcluded->plus(
                $productPrice->getFinalPrice()->getTaxExcluded()->times($quantity)
            );
            $totalTaxIncluded = $totalTaxIncluded->plus(
                $productPrice->getFinalPrice()->getTaxIncluded()->times($quantity)
            );

            $productPrices[] = $productPrice;
        }

        $cartPrice->setProductPrices($productPrices);
        $cartPrice->setProductTotal(TaxablePrice::fromTaxExcluded($totalTaxExcluded, TaxRate::zero()));
        $cartPrice->setCartTotal(TaxablePrice::fromTaxExcluded($totalTaxExcluded, TaxRate::zero()));
    }
}
