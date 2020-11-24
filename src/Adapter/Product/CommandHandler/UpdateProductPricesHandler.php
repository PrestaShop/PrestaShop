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

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductPricePropertiesFiller;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductPricesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use Product;

/**
 * Updates product price information using legacy object models
 */
final class UpdateProductPricesHandler implements UpdateProductPricesHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductPricePropertiesFiller
     */
    private $productPricePropertiesFiller;

    /**
     * @param ProductRepository $productRepository
     * @param ProductPricePropertiesFiller $productPricePropertiesFiller
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductPricePropertiesFiller $productPricePropertiesFiller
    ) {
        $this->productRepository = $productRepository;
        $this->productPricePropertiesFiller = $productPricePropertiesFiller;
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
        $updatableProperties = $this->productPricePropertiesFiller->fillWithPrices(
            $product,
            $command->getPrice(),
            $command->getUnitPrice(),
            $command->getWholesalePrice()
        );

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

        return $updatableProperties;
    }
}
