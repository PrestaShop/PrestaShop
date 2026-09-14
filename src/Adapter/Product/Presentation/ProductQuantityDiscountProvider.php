<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Presentation;

use Address;
use Combination;
use Configuration;
use Context;
use Customer;
use Group;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\ProductCalculatorInterface;
use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPrice;
use Product;
use SpecificPrice;
use SpecificPriceFormatter;
use Throwable;
use Tools;

final class ProductQuantityDiscountProvider implements ProductQuantityDiscountPreparationInterface
{
    /** @var array<string, array<int, array<string, mixed>>> */
    private array $quantityDiscountsCache = [];

    public function __construct(
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private readonly ProductCalculatorInterface $productCalculator,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getQuantityDiscounts(
        Product $product,
        Context $context,
        ?int $idProductAttribute,
    ): array {
        return $this->getQuantityDiscountsWithPreparation($product, $context, $idProductAttribute, $this);
    }

    /**
     * @internal
     *
     * @return array<int, array<string, mixed>>
     */
    public function getQuantityDiscountsWithPreparation(
        Product $product,
        Context $context,
        ?int $idProductAttribute,
        ProductQuantityDiscountPreparationInterface $preparation,
        ?float $taxRate = null,
    ): array {
        $idCustomer = isset($context->customer) ? (int) $context->customer->id : 0;
        $idCountry = $idCustomer ? (int) Customer::getCurrentCountry($idCustomer) : (int) Tools::getCountry();
        $idGroup = (int) Group::getCurrent()->id;

        // Reuse the tax rate when already available to avoid computing it twice.
        $taxRate ??= $this->getTaxRate($product, $context);

        $cacheKey = $this->getCacheKey($product, $context, $idProductAttribute, $idCountry, $idGroup, $idCustomer, $taxRate, $preparation);

        if (isset($this->quantityDiscountsCache[$cacheKey])) {
            return $this->quantityDiscountsCache[$cacheKey];
        }

        $specificPrices = SpecificPrice::getQuantityDiscounts(
            (int) $product->id,
            (int) $context->shop->id,
            (int) $context->currency->id,
            $idCountry,
            $idGroup,
            $idProductAttribute,
            false,
            $idCustomer
        );

        foreach ($specificPrices as &$specificPrice) {
            if ($specificPrice['id_product_attribute']) {
                $combination = new Combination((int) $specificPrice['id_product_attribute']);
                $attributes = $combination->getAttributesName((int) $context->language->id);
                foreach ($attributes as $attribute) {
                    $specificPrice['attributes'] = $attribute['name'] . ' - ';
                }
                $specificPrice['attributes'] = rtrim($specificPrice['attributes'], ' - ');
            }
            if ((int) $specificPrice['id_currency'] === 0 && $specificPrice['reduction_type'] === 'amount') {
                $specificPrice['reduction'] = Tools::convertPriceFull($specificPrice['reduction'], null, $context->currency);
            }
        }
        unset($specificPrice);

        $quantityDiscounts = $preparation->formatQuantityDiscounts(
            $specificPrices,
            $this->getProductPrice($product, $idProductAttribute, $preparation),
            $taxRate,
            (float) $product->ecotax,
            $context
        );

        return $this->quantityDiscountsCache[$cacheKey] = $quantityDiscounts;
    }

    public function getTaxRate(
        Product $product,
        Context $context,
    ): float {
        return (float) $product->getTaxesRate(new Address((int) $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
    }

    /**
     * @param array<int, array<string, mixed>> $specificPrices
     *
     * @return array<int, array<string, mixed>>
     */
    public function formatQuantityDiscounts(
        array $specificPrices,
        float $price,
        float $taxRate,
        float $ecotaxAmount,
        Context $context,
    ): array {
        $priceCalculationMethod = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        $isTaxIncluded = $priceCalculationMethod !== null && (int) $priceCalculationMethod === PS_TAX_INC;

        foreach ($specificPrices as $key => &$specificPrice) {
            $specificPriceFormatter = new SpecificPriceFormatter(
                $specificPrice,
                $isTaxIncluded,
                $context->currency,
                (bool) Configuration::get('PS_DISPLAY_DISCOUNT_PRICE')
            );
            $specificPrice = $specificPriceFormatter->formatSpecificPrice($price, $taxRate, $ecotaxAmount);
            $specificPrice['nextQuantity'] = isset($specificPrices[$key + 1]) ? (int) $specificPrices[$key + 1]['from_quantity'] : -1;
        }

        return $specificPrices;
    }

    private function getProductPrice(
        Product $product,
        ?int $idProductAttribute,
        ProductQuantityDiscountPreparationInterface $preparation,
    ): float {
        if ($preparation->isNewPricingEnabled()) {
            $productPrice = ProductPrice::create(
                (int) $product->id,
                (int) $idProductAttribute
            );

            $preparation->getProductCalculator()->compute($productPrice);

            return (float) (string) $productPrice
                ->getFinalPrice()
                ->getTaxExcluded();
        }

        return (float) $product->getPrice(
            Product::$_taxCalculationMethod == PS_TAX_INC,
            $idProductAttribute,
            6,
            null,
            false,
            false
        );
    }

    public function getProductCalculator(): ProductCalculatorInterface
    {
        return $this->productCalculator;
    }

    public function isNewPricingEnabled(): bool
    {
        try {
            return $this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_NEW_PRICING);
        } catch (Throwable) {
            return false;
        }
    }

    private function getCacheKey(
        Product $product,
        Context $context,
        ?int $idProductAttribute,
        int $idCountry,
        int $idGroup,
        int $idCustomer,
        float $taxRate,
        ProductQuantityDiscountPreparationInterface $preparation,
    ): string {
        return implode(':', [
            spl_object_id($preparation),
            $product->id,
            $idProductAttribute,
            $context->shop->id,
            $context->currency->id,
            $context->language->id,
            $idCustomer,
            $context->cart->id,
            $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')},
            $idCountry,
            $idGroup,
            $taxRate,
        ]);
    }
}
