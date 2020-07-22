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
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
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
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryHandler\GetProductSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
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
     * @var GetProductSuppliersHandlerInterface
     */
    private $getProductSuppliersHandler;

    /**
     * @param AddProductSupplierHandlerInterface $addProductSupplierHandler
     * @param UpdateProductSupplierHandlerInterface $updateProductSupplierHandler
     * @param DeleteProductSupplierHandlerInterface $deleteProductSupplierHandler
     * @param GetProductSuppliersHandlerInterface $getProductSuppliersHandler
     */
    public function __construct(
        AddProductSupplierHandlerInterface $addProductSupplierHandler,
        UpdateProductSupplierHandlerInterface $updateProductSupplierHandler,
        DeleteProductSupplierHandlerInterface $deleteProductSupplierHandler,
        GetProductSuppliersHandlerInterface $getProductSuppliersHandler
    ) {
        $this->addProductSupplierHandler = $addProductSupplierHandler;
        $this->updateProductSupplierHandler = $updateProductSupplierHandler;
        $this->deleteProductSupplierHandler = $deleteProductSupplierHandler;
        $this->getProductSuppliersHandler = $getProductSuppliersHandler;
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
        if (null !== $command->getDefaultSupplierId()) {
            $this->updateDefaultSupplier($productId, $command->getDefaultSupplierId());
        }

        return $this->getProductSuppliersHandler->handle(new GetProductSuppliers($productId->getValue()));
    }

    /**
     * @param ProductId $productId
     * @param ProductSupplier[] $productSuppliers
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
     * @param SupplierId $defaultSupplierId
     *
     * @throws CannotUpdateProductException
     * @throws ProductException
     * @throws ProductNotFoundException
     */
    private function updateDefaultSupplier(ProductId $productId, SupplierId $defaultSupplierId): void
    {
        $product = $this->getProduct($productId);
        $product->id_supplier = $defaultSupplierId->getValue();
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
}
