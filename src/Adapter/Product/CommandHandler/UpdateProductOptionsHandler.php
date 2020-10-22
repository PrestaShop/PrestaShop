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

use PrestaShop\PrestaShop\Adapter\Manufacturer\Repository\ManufacturerRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductOptionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductOptionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use Product;

/**
 * Handles @see UpdateProductOptionsCommand using legacy object models
 */
final class UpdateProductOptionsHandler implements UpdateProductOptionsHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ManufacturerRepository
     */
    private $manufacturerRepository;

    /**
     * @param ProductRepository $productRepository
     * @param ManufacturerRepository $manufacturerRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        ManufacturerRepository $manufacturerRepository
    ) {
        $this->productRepository = $productRepository;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductOptionsCommand $command): void
    {
        $product = $this->productRepository->get($command->getProductId());
        $updatableProperties = $this->fillUpdatableProperties($product, $command);

        $this->productRepository->partialUpdate($product, $updatableProperties, CannotUpdateProductException::FAILED_UPDATE_OPTIONS);
    }

    /**
     * @param Product $product
     * @param UpdateProductOptionsCommand $command
     *
     * @return string[]|array<string, int[]> updatable properties
     */
    private function fillUpdatableProperties(Product $product, UpdateProductOptionsCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getVisibility()) {
            $product->visibility = $command->getVisibility()->getValue();
            $updatableProperties[] = 'visibility';
        }

        if (null !== $command->isAvailableForOrder()) {
            $product->available_for_order = $command->isAvailableForOrder();
            $updatableProperties[] = 'available_for_order';
        }

        if (null !== $command->isOnlineOnly()) {
            $product->online_only = $command->isOnlineOnly();
            $updatableProperties[] = 'online_only';
        }

        if (null !== $command->showPrice()) {
            $product->show_price = $command->showPrice();
            $updatableProperties[] = 'show_price';
        }

        if (null !== $command->getCondition()) {
            $product->condition = $command->getCondition()->getValue();
            $updatableProperties[] = 'condition';
        }

        if (null !== $command->getEan13()) {
            $product->ean13 = $command->getEan13()->getValue();
            $updatableProperties[] = 'ean13';
        }

        if (null !== $command->getIsbn()) {
            $product->isbn = $command->getIsbn()->getValue();
            $updatableProperties[] = 'isbn';
        }

        if (null !== $command->getMpn()) {
            $product->mpn = $command->getMpn();
            $updatableProperties[] = 'mpn';
        }

        if (null !== $command->getReference()) {
            $product->reference = $command->getReference()->getValue();
            $updatableProperties[] = 'reference';
        }

        if (null !== $command->getUpc()) {
            $product->upc = $command->getUpc()->getValue();
            $updatableProperties[] = 'upc';
        }

        $manufacturerId = $command->getManufacturerId();
        if (null !== $manufacturerId) {
            $this->assertManufacturerExists($manufacturerId);
            $product->id_manufacturer = $manufacturerId->getValue();
            $updatableProperties[] = 'id_manufacturer';
        }

        return $updatableProperties;
    }

    /**
     * @param ManufacturerIdInterface $manufacturerId
     */
    private function assertManufacturerExists(ManufacturerIdInterface $manufacturerId): void
    {
        if ($manufacturerId instanceof ManufacturerId) {
            $this->manufacturerRepository->assertManufacturerExists($manufacturerId);
        }
    }
}
