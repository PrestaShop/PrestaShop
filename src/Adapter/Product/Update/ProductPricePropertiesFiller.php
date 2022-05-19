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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use Product;

/**
 * Fills Product price properties which needs specific handling for update
 */
class ProductPricePropertiesFiller
{
    /**
     * @var NumberExtractor
     */
    private $numberExtractor;

    /**
     * @var bool
     */
    private $ecotaxEnabled;

    /**
     * @param NumberExtractor $numberExtractor
     * @param bool $ecotaxEnabled
     */
    public function __construct(
        NumberExtractor $numberExtractor,
        bool $ecotaxEnabled
    ) {
        $this->numberExtractor = $numberExtractor;
        $this->ecotaxEnabled = $ecotaxEnabled;
    }

    /**
     * Wraps following properties filling: price, unit_price, unit_price_ratio, wholesale_price
     * as most of them (price, unit_price, unit_price_ratio) are highly coupled & depends on each other
     *
     * @param Product $product
     * @param DecimalNumber|null $price
     * @param DecimalNumber|null $unitPrice
     * @param DecimalNumber|null $wholesalePrice
     *
     * @return string[] updatable properties
     */
    public function fillWithPrices(
        Product $product,
        ?DecimalNumber $price,
        ?DecimalNumber $unitPrice,
        ?DecimalNumber $wholesalePrice,
        ?DecimalNumber $ecotax
    ): array {
        $updatableProperties = [];
        if (null !== $wholesalePrice) {
            $product->wholesale_price = (float) (string) $wholesalePrice;
            $updatableProperties[] = 'wholesale_price';
        }

        $impactUnitPrice = false;
        if (null !== $price) {
            $product->price = (float) (string) $price;
            $updatableProperties[] = 'price';
            $impactUnitPrice = true;
        }

        if (null !== $ecotax) {
            $product->ecotax = (float) (string) $ecotax;
            $updatableProperties[] = 'ecotax';
            $impactUnitPrice = true;
        }

        $productPrice = null;
        if (null !== $unitPrice || $impactUnitPrice) {
            if (null === $price) {
                $price = $this->numberExtractor->extract($product, 'price');
            }

            // We need to add the ecotax part (when it's enabled)
            if (!$this->ecotaxEnabled) {
                $ecotax = new DecimalNumber('0');
            } elseif (null === $ecotax) {
                $ecotax = $this->numberExtractor->extract($product, 'ecotax');
            }

            // When product price is zero unit price must be 0 as well (we must not forget the ecotax part)
            $productPrice = $price->plus($ecotax);
            if ($productPrice->equalsZero()) {
                $unitPrice = new DecimalNumber('0');
            }

            $product->unit_price = (float) (string) $unitPrice;
            $updatableProperties[] = 'unit_price';
        }

        // When price or unit price is changed the ratio must be updated, but only the object field
        // we don't ask to update this property since it will be updated via an SQL query by the Product class
        if (null !== $unitPrice || null !== $price || null !== $productPrice) {
            $this->fillUnitPriceRatio($product, $productPrice ?: $price, $unitPrice);
        }

        return $updatableProperties;
    }

    /**
     * @param Product $product
     * @param DecimalNumber|null $price
     * @param DecimalNumber|null $unitPrice
     */
    private function fillUnitPriceRatio(Product $product, ?DecimalNumber $price, ?DecimalNumber $unitPrice): void
    {
        if (null === $price) {
            $price = $this->numberExtractor->extract($product, 'price');
        }

        if (null === $unitPrice) {
            $unitPrice = $this->numberExtractor->extract($product, 'unit_price');
        }

        // Reminder: regardless of what we compute here a final update is also performed in Product::updateUnitRatio
        // this part is more destined to keep the field consistent in the $product object
        $this->setUnitPriceRatio($product, $price, $unitPrice);
    }

    /**
     * @param Product $product
     * @param DecimalNumber $price
     * @param DecimalNumber $unitPrice
     */
    private function setUnitPriceRatio(Product $product, DecimalNumber $price, DecimalNumber $unitPrice): void
    {
        // If unit price or price is zero, then reset ratio to zero too
        if ($unitPrice->equalsZero() || $price->equalsZero()) {
            $ratio = new DecimalNumber('0');
        } else {
            $ratio = $price->dividedBy($unitPrice);
        }

        // Ratio is computed based on price and unit price, we update it so that the value is up-to-date in hooks
        $product->unit_price_ratio = (float) (string) $ratio;
    }
}
