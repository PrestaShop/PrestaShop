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
     * @param NumberExtractor $numberExtractor
     */
    public function __construct(
        NumberExtractor $numberExtractor
    ) {
        $this->numberExtractor = $numberExtractor;
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
    public function fillWithPrices(Product $product, ?DecimalNumber $price, ?DecimalNumber $unitPrice, ?DecimalNumber $wholesalePrice): array
    {
        if (null !== $wholesalePrice) {
            $product->wholesale_price = (string) $wholesalePrice;
            $updatableProperties[] = 'wholesale_price';
        }

        if (null !== $price) {
            $product->price = (float) (string) $price;
            $updatableProperties[] = 'price';
        } else {
            $price = $this->numberExtractor->extract($product, 'price');
        }

        $this->fillUnitPriceRatio($product, $price, $unitPrice);
        $updatableProperties[] = 'unit_price_ratio';

        return $updatableProperties;
    }

    /**
     * @param Product $product
     * @param DecimalNumber $price
     * @param DecimalNumber|null $unitPrice
     */
    private function fillUnitPriceRatio(Product $product, DecimalNumber $price, ?DecimalNumber $unitPrice): void
    {
        // if price was reset then also reset unit_price_ratio
        if ($price->equalsZero()) {
            $this->setUnitPriceRatio($product, $price, $price);

            return;
        }

        if (null === $unitPrice) {
            $unitPrice = $this->numberExtractor->extract($product, 'unit_price');
        }

        // if price was not reset then allow setting new unit_price and unit_price_ratio
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
        // unit_price_ratio is calculated based on input price and unit_price & then is saved to database,
        // however - unit_price is not saved to database. When loading product it is calculated depending on price and unit_price_ratio
        // so there is no static values saved, that's why unit_price is inaccurate
        $product->unit_price_ratio = (float) (string) $ratio;
        //set unit_price to go through validation
        $product->unit_price = (float) (string) $unitPrice;
    }
}
