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

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\AddProductSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\DeleteProductSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\AddProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\DeleteProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\UpdateProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\UpdateProductSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotDeleteProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplier;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use ProductSupplier as ProductSupplierEntity;

/**
 * Handles @var UpdateProductSuppliersCommand using legacy object model
 */
final class UpdateProductSuppliersHandler extends AbstractProductHandler implements UpdateProductSuppliersHandlerInterface
{
    /**
     * @var AddProductSupplierHandlerInterface
     */
    private $addProductSupplierHandler;

    /**
     * @var UpdateProductSupplierHandlerInterface
     */
    private $updateProductSupplierHandler;

    /**
     * @var DeleteProductSupplierHandler
     */
    private $deleteProductSupplierHandler;

    /**
     * @param AddProductSupplierHandlerInterface $addProductSupplierHandler
     * @param UpdateProductSupplierHandlerInterface $updateProductSupplierHandler
     * @param DeleteProductSupplierHandlerInterface $deleteProductSupplierHandler
     */
    public function __construct(
        AddProductSupplierHandlerInterface $addProductSupplierHandler,
        UpdateProductSupplierHandlerInterface $updateProductSupplierHandler,
        DeleteProductSupplierHandlerInterface $deleteProductSupplierHandler
    ) {
        $this->addProductSupplierHandler = $addProductSupplierHandler;
        $this->updateProductSupplierHandler = $updateProductSupplierHandler;
        $this->deleteProductSupplierHandler = $deleteProductSupplierHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductSuppliersCommand $command): array
    {
        $productId = $command->getProductId();

        if (null !== $command->getProductSuppliers()) {
            $this->updateProductSuppliers($productId, $command->getProductSuppliers());
        }

        $this->handleDefaultSupplierUpdate($command);

        return $this->getProductSupplierIds($productId);
    }

    /**
     * @param UpdateProductSuppliersCommand $command
     *
     * @throws CannotUpdateProductException
     * @throws ProductException
     */
    private function handleDefaultSupplierUpdate(UpdateProductSuppliersCommand $command): void
    {
        $productId = $command->getProductId();
        $deletedAllProductSuppliers = null !== $command->getProductSuppliers() && empty($command->getProductSuppliers());
        $defaultSupplierIsNotProvided = null === $command->getDefaultSupplierId() && null !== $command->getProductSuppliers();
        $defaultSupplierIsProvided = null !== $command->getDefaultSupplierId();

        if ($deletedAllProductSuppliers) {
            $this->removeDefaultSupplier($productId);

            return;
        }

        if ($defaultSupplierIsProvided) {
            $this->updateDefaultSupplier($productId, $command->getDefaultSupplierId()->getValue());

            return;
        }

        if ($defaultSupplierIsNotProvided) {
            $firstSupplier = $command->getProductSuppliers()[0];
            $this->updateDefaultSupplier($productId, $firstSupplier->getSupplierId());
        }
    }

    /**
     * @param ProductId $productId
     * @param ProductSupplier[] $productSuppliers
     *
     * @throws CannotDeleteProductSupplierException
     * @throws CombinationConstraintException
     * @throws CurrencyException
     * @throws ProductSupplierException
     * @throws SupplierException
     */
    private function updateProductSuppliers(ProductId $productId, array $productSuppliers): void
    {
        $deletableProductSupplierIds = $this->getDeletableProductSupplierIds($productId, $productSuppliers);

        foreach ($productSuppliers as $productSupplier) {
            if ($productSupplier->getProductSupplierId()) {
                $this->updateProductSupplier($productSupplier);
            } else {
                $this->addProductSupplier($productId, $productSupplier);
            }
        }

        $this->deleteProductSuppliers($deletableProductSupplierIds);
    }

    /**
     * @param ProductId $productId
     * @param ProductSupplier $productSupplier
     *
     * @return ProductSupplierId
     *
     * @throws CurrencyException
     * @throws CombinationConstraintException
     * @throws SupplierException
     */
    private function addProductSupplier(ProductId $productId, ProductSupplier $productSupplier): ProductSupplierId
    {
        $combinationId = $productSupplier->getCombinationId();

        if ($combinationId === CombinationId::NO_COMBINATION) {
            $combinationId = null;
        }

        $command = new AddProductSupplierCommand(
            $productId->getValue(),
            $productSupplier->getSupplierId(),
            $productSupplier->getCurrencyId(),
            $productSupplier->getReference(),
            $productSupplier->getPriceTaxExcluded(),
            $combinationId
        );

        return $this->addProductSupplierHandler->handle($command);
    }

    /**
     * @param ProductSupplier $productSupplier
     */
    private function updateProductSupplier(ProductSupplier $productSupplier): void
    {
        //@todo: check if i need to set supplier reference for product attribute manually (it has such field in db)
        $command = new UpdateProductSupplierCommand($productSupplier->getProductSupplierId());
        $command->setCurrencyId($productSupplier->getCurrencyId())
            ->setReference($productSupplier->getReference())
            ->setPriceTaxExcluded($productSupplier->getPriceTaxExcluded())
            ->setCombinationId($productSupplier->getCombinationId())
        ;

        $this->updateProductSupplierHandler->handle($command);
    }

    /**
     * @param ProductId $productId
     *
     * @throws CannotUpdateProductException
     * @throws ProductException
     */
    private function removeDefaultSupplier(ProductId $productId): void
    {
        $this->updateDefaultSupplier($productId, 0);
    }

    /**
     * @param ProductId $productId
     * @param int $defaultSupplierId
     *
     * @throws CannotUpdateProductException
     * @throws ProductException
     */
    private function updateDefaultSupplier(ProductId $productId, int $defaultSupplierId): void
    {
        $product = $this->getProduct($productId);
        $product->id_supplier = $defaultSupplierId;
        $this->fieldsToUpdate['id_supplier'] = true;

        $this->performUpdate($product, CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER);
    }

    /**
     * @param ProductId $productId
     * @param array $providedProductSuppliers
     *
     * @return int[]
     */
    private function getDeletableProductSupplierIds(ProductId $productId, array $providedProductSuppliers): array
    {
        $productSuppliers = ProductSupplierEntity::getSupplierCollection($productId->getValue());
        $existingProductSupplierIds = [];
        $providedProductSupplierIds = [];

        /** @var ProductSupplierEntity $currentSupplier */
        foreach ($productSuppliers as $currentSupplier) {
            $existingProductSupplierIds[] = (int) $currentSupplier->id;
        }

        foreach ($providedProductSuppliers as $productSupplier) {
            $providedProductSupplierIds[] = $productSupplier->getProductSupplierId();
        }

        return array_diff($existingProductSupplierIds, $providedProductSupplierIds);
    }

    /**
     * @param array $productSupplierIds
     *
     * @throws CannotDeleteProductSupplierException
     * @throws ProductSupplierException
     */
    private function deleteProductSuppliers(array $productSupplierIds): void
    {
        foreach ($productSupplierIds as $productSupplierId) {
            $this->deleteProductSupplierHandler->handle(new DeleteProductSupplierCommand($productSupplierId));
        }
    }

    /**
     * @param ProductId $productId
     *
     * @return ProductSupplierId[]
     */
    private function getProductSupplierIds(ProductId $productId): array
    {
        $productSupplierIds = [];

        /** @var ProductSupplierEntity $productSupplierEntity */
        foreach (ProductSupplierEntity::getSupplierCollection($productId->getValue()) as $productSupplierEntity) {
            $productSupplierIds[] = new ProductSupplierId((int) $productSupplierEntity->id);
        }

        return $productSupplierIds;
    }
}
