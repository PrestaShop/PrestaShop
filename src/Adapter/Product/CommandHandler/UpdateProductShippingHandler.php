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

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductShippingCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductShippingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Product;

/**
 * Handles @var UpdateProductShippingCommand using legacy object model
 */
final class UpdateProductShippingHandler implements UpdateProductShippingHandlerInterface
{
    /**
     * @var ProductMultiShopRepository
     */
    private $productMultiShopRepository;

    /**
     * @param ProductMultiShopRepository $productMultiShopRepository
     */
    public function __construct(
        ProductMultiShopRepository $productMultiShopRepository
    ) {
        $this->productMultiShopRepository = $productMultiShopRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductShippingCommand $command): void
    {
        $shopConstraint = $command->getShopConstraint();
        $product = $this->productMultiShopRepository->getByShopConstraint($command->getProductId(), $shopConstraint);
        $updatableProperties = $this->fillUpdatableProperties($product, $command);

        $this->productMultiShopRepository->partialUpdate(
            $product,
            $updatableProperties,
            $shopConstraint,
            CannotUpdateProductException::FAILED_UPDATE_SHIPPING_OPTIONS
        );

        if (null !== $command->getCarrierReferenceIds()) {
            $this->productMultiShopRepository->setCarrierReferences(
                new ProductId((int) $product->id),
                $command->getCarrierReferenceIds(),
                $shopConstraint
            );
        }
    }

    /**
     * @param Product $product
     * @param UpdateProductShippingCommand $command
     *
     * @return string[] updatable properties
     */
    private function fillUpdatableProperties(Product $product, UpdateProductShippingCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getWidth()) {
            $product->width = (string) $command->getWidth();
            $updatableProperties[] = 'width';
        }

        if (null !== $command->getHeight()) {
            $product->height = (string) $command->getHeight();
            $updatableProperties[] = 'height';
        }

        if (null !== $command->getDepth()) {
            $product->depth = (string) $command->getDepth();
            $updatableProperties[] = 'depth';
        }

        if (null !== $command->getWeight()) {
            $product->weight = (string) $command->getWeight();
            $updatableProperties[] = 'weight';
        }

        if (null !== $command->getAdditionalShippingCost()) {
            $product->additional_shipping_cost = (float) (string) $command->getAdditionalShippingCost();
            $updatableProperties[] = 'additional_shipping_cost';
        }

        if (null !== $command->getDeliveryTimeNoteType()) {
            $product->additional_delivery_times = $command->getDeliveryTimeNoteType()->getValue();
            $updatableProperties[] = 'additional_delivery_times';
        }

        if (null !== $command->getLocalizedDeliveryTimeInStockNotes()) {
            $product->delivery_in_stock = $command->getLocalizedDeliveryTimeInStockNotes();
            $updatableProperties['delivery_in_stock'] = array_keys($command->getLocalizedDeliveryTimeInStockNotes());
        }

        if (null !== $command->getLocalizedDeliveryTimeOutOfStockNotes()) {
            $product->delivery_out_stock = $command->getLocalizedDeliveryTimeOutOfStockNotes();
            $updatableProperties['delivery_out_stock'] = array_keys($command->getLocalizedDeliveryTimeOutOfStockNotes());
        }

        return $updatableProperties;
    }
}
