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
use PrestaShop\PrestaShop\Adapter\Product\ProductProvider;
use PrestaShop\PrestaShop\Adapter\Product\ProductSupplierPersister;
use PrestaShop\PrestaShop\Adapter\Product\ProductUpdater;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\AddProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\DeleteProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\SetProductSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\UpdateProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplier as ProductSupplierDTO;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplierDeleterInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use ProductSupplier;

/**
 * Handles @var SetProductSuppliersCommand using legacy object model
 */
final class SetProductSuppliersHandler extends AbstractProductHandler implements SetProductSuppliersHandlerInterface
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
     * @var ProductUpdater
     */
    private $productUpdater;

    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @var ProductSupplierDeleterInterface
     */
    private $productSupplierDeleter;

    /**
     * @var ProductSupplierPersister
     */
    private $productSupplierPersister;

    /**
     * @param AddProductSupplierHandlerInterface $addProductSupplierHandler
     * @param UpdateProductSupplierHandlerInterface $updateProductSupplierHandler
     * @param DeleteProductSupplierHandlerInterface $deleteProductSupplierHandler
     * @param ProductUpdater $productUpdater
     * @param ProductProvider $productProvider
     * @param ProductSupplierDeleterInterface $productSupplierDeleter
     * @param ProductSupplierPersister $productSupplierPersister
     */
    public function __construct(
        AddProductSupplierHandlerInterface $addProductSupplierHandler,
        UpdateProductSupplierHandlerInterface $updateProductSupplierHandler,
        DeleteProductSupplierHandlerInterface $deleteProductSupplierHandler,
        ProductUpdater $productUpdater,
        ProductProvider $productProvider,
        ProductSupplierDeleterInterface $productSupplierDeleter,
        ProductSupplierPersister $productSupplierPersister
    ) {
        $this->addProductSupplierHandler = $addProductSupplierHandler;
        $this->updateProductSupplierHandler = $updateProductSupplierHandler;
        $this->deleteProductSupplierHandler = $deleteProductSupplierHandler;
        $this->productUpdater = $productUpdater;
        $this->productProvider = $productProvider;
        $this->productSupplierDeleter = $productSupplierDeleter;
        $this->productSupplierPersister = $productSupplierPersister;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SetProductSuppliersCommand $command): array
    {
        $productId = $command->getProductId();
        $product = $this->productProvider->get($productId);
        $defaultSupplierIdValue = $command->getDefaultSupplierId()->getValue();
        $this->assertDefaultSupplierIsOneOfProvidedSuppliers($command);

        $this->setProductSuppliers($productId, $command->getProductSuppliers());
        $this->productUpdater->updateProductDefaultSupplier($product, $defaultSupplierIdValue);

        return $this->getProductSupplierIds($productId);
    }

    /**
     * @param SetProductSuppliersCommand $command
     *
     * @throws CannotUpdateProductException
     */
    private function assertDefaultSupplierIsOneOfProvidedSuppliers(SetProductSuppliersCommand $command): void
    {
        $defaultSupplierId = $command->getDefaultSupplierId()->getValue();

        foreach ($command->getProductSuppliers() as $productSupplier) {
            if ($productSupplier->getSupplierId() === $defaultSupplierId) {
                return;
            }
        }

        throw new CannotUpdateProductException(
            sprintf(
                'Cannot update product #%s default supplier #%s, it is not one of provided product suppliers',
                $command->getProductId()->getValue(),
                $defaultSupplierId
            ),
            CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER
        );
    }

    /**
     * @param ProductId $productId
     * @param ProductSupplierDTO[] $productSuppliers
     */
    private function setProductSuppliers(ProductId $productId, array $productSuppliers): void
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
     * @param ProductSupplierDTO $productSupplierDTO
     *
     * @return ProductSupplierId
     *
     * @throws CurrencyException
     * @throws CombinationConstraintException
     * @throws SupplierException
     */
    private function addProductSupplier(ProductId $productId, ProductSupplierDTO $productSupplierDTO): ProductSupplierId
    {
        $productSupplier = new ProductSupplier();
        $productSupplier->id_product = $productId->getValue();
        $productSupplier->id_supplier = $productSupplierDTO->getProductSupplierId();
        $productSupplier->id_currency = $productSupplierDTO->getCurrencyId();
        $productSupplier->product_supplier_reference = $productSupplierDTO->getReference();
        $productSupplier->product_supplier_price_te = $productSupplierDTO->getPriceTaxExcluded();
        $productSupplier->id_product_attribute = $productSupplierDTO->getCombinationId();

        return $this->productSupplierPersister->add($productSupplier);
    }

    /**
     * @param ProductSupplierDTO $productSupplierDTO
     */
    private function updateProductSupplier(ProductSupplierDTO $productSupplierDTO): void
    {
        $command = new UpdateProductSupplierCommand($productSupplierDTO->getProductSupplierId());
        $command->setCurrencyId($productSupplierDTO->getCurrencyId())
            ->setReference($productSupplierDTO->getReference())
            ->setPriceTaxExcluded($productSupplierDTO->getPriceTaxExcluded())
        ;

        if ($productSupplierDTO->getCombinationId()) {
            $command->setCombinationId($productSupplierDTO->getCombinationId());
        }

        $this->updateProductSupplierHandler->handle($command);
    }

    /**
     * @param ProductId $productId
     * @param ProductSupplierDTO[] $providedProductSuppliers
     *
     * @return ProductSupplierId[]
     */
    private function getDeletableProductSupplierIds(ProductId $productId, array $providedProductSuppliers): array
    {
        $existingProductSuppliers = ProductSupplier::getSupplierCollection($productId->getValue());
        $idsForDeletion = [];

        /** @var ProductSupplier $currentSupplier */
        foreach ($existingProductSuppliers as $currentSupplier) {
            $currentId = (int) $currentSupplier->id;
            $idsForDeletion[$currentId] = new ProductSupplierId($currentId);
        }

        foreach ($providedProductSuppliers as $productSupplier) {
            if (isset($idsForDeletion[$productSupplier->getProductSupplierId()])) {
                unset($idsForDeletion[$productSupplier->getProductSupplierId()]);
            }
        }

        return $idsForDeletion;
    }

    /**
     * @param ProductSupplierId[] $productSupplierIds
     */
    private function deleteProductSuppliers(array $productSupplierIds): void
    {
        $this->productSupplierDeleter->bulkDelete($productSupplierIds);
    }

    /**
     * @param ProductId $productId
     *
     * @return ProductSupplierId[]
     */
    private function getProductSupplierIds(ProductId $productId): array
    {
        $productSupplierIds = [];

        /** @var ProductSupplier $productSupplierEntity */
        foreach (ProductSupplier::getSupplierCollection($productId->getValue(), false) as $productSupplierEntity) {
            $productSupplierIds[] = new ProductSupplierId((int) $productSupplierEntity->id);
        }

        return $productSupplierIds;
    }
}
