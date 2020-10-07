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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Decimal\Exception\DivisionByZeroException;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductPricesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use Product;

/**
 * Updates product price information using legacy object models
 */
final class UpdateProductPricesHandler implements UpdateProductPricesHandlerInterface
{
    /**
     * @var NumberExtractor
     */
    private $numberExtractor;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param NumberExtractor $numberExtractor
     * @param ProductRepository $productRepository
     */
    public function __construct(
        NumberExtractor $numberExtractor,
        ProductRepository $productRepository
    ) {
        $this->numberExtractor = $numberExtractor;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductPricesCommand $command): void
    {
        $product = $this->productRepository->get($command->getProductId());
        $updatableProperties = $this->fillUpdatableProperties($product, $command);

        $this->productRepository->partialUpdate($product, $updatableProperties, CannotUpdateProductException::FAILED_UPDATE_PRICES);
    }

    /**
     * @param Product $product
     * @param UpdateProductPricesCommand $command
     *
     * @return string[] updatable properties
     *
     * @throws ProductConstraintException
     */
    private function fillUpdatableProperties(Product $product, UpdateProductPricesCommand $command): array
    {
        $updatableProperties = [];

        $price = $command->getPrice();
        if (null !== $price) {
            $product->price = (float) (string) $price;
            $updatableProperties[] = 'price';
        } else {
            $price = new DecimalNumber((string) $product->price);
        }

        $this->fillUnitPriceRatio($product, $price, $command->getUnitPrice());
        $updatableProperties[] = 'unit_price_ratio';

        if (null !== $command->getUnity()) {
            $product->unity = $command->getUnity();
            $updatableProperties[] = 'unity';
        }

        if (null !== $command->getEcotax()) {
            $product->ecotax = (float) (string) $command->getEcotax();
            $updatableProperties[] = 'ecotax';
        }

        $taxRulesGroupId = $command->getTaxRulesGroupId();

        if (null !== $taxRulesGroupId) {
            $product->id_tax_rules_group = $taxRulesGroupId;
            $updatableProperties[] = 'id_tax_rules_group';
        }

        if (null !== $command->isOnSale()) {
            $product->on_sale = $command->isOnSale();
            $updatableProperties[] = 'on_sale';
        }

        if (null !== $command->getWholesalePrice()) {
            $product->wholesale_price = (float) (string) $command->getWholesalePrice();
            $updatableProperties[] = 'wholesale_price';
        }

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
            $unitPrice = new DecimalNumber((string) $product->unit_price);
        }

        $this->setUnitPriceInfo($product, $unitPrice, $price);
    }

    /**
     * @param Product $product
     * @param DecimalNumber $price
     * @param DecimalNumber $unitPrice
     *
     * @throws ProductConstraintException
     * @throws DivisionByZeroException
     */
    private function setUnitPriceRatio(Product $product, DecimalNumber $price, DecimalNumber $unitPrice): void
    {
        $this->validateUnitPrice($unitPrice);

        // If unit price or price is zero, then reset ratio to zero too
        if ($unitPrice->equalsZero() || $price->equalsZero()) {
            $ratio = new DecimalNumber('0');
        } else {
            $ratio = $price->dividedBy($unitPrice);
        }

        // unit_price_ratio is calculated based on input price and unit_price & then is saved to database,
        // however - unit_price is not saved to database. When loading product it is calculated depending on price and unit_price_ratio
        // so there is no static values saved, that's why unit_price is always inaccurate
        $product->unit_price_ratio = (float) (string) $ratio;
    }

    /**
     * @param Product $product
     * @param DecimalNumber $unitPrice
     * @param DecimalNumber $price
     *
     * @throws ProductConstraintException
     */
    private function setUnitPriceInfo(Product $product, DecimalNumber $unitPrice, ?DecimalNumber $price): void
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

    /**
     * Unit price validation is not involved in legacy validation, so it is checked manually to have unsigned int value
     *
     * @param DecimalNumber $unitPrice
     *
     * @throws ProductConstraintException
     */
    private function validateUnitPrice(DecimalNumber $unitPrice): void
    {
        if ($unitPrice->isLowerThanZero()) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid product unit_price. Got "%s"',
                    $unitPrice
                ),
                ProductConstraintException::INVALID_UNIT_PRICE
            );
        }
    }
}
