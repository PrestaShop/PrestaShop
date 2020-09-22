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

use PrestaShop\PrestaShop\Adapter\Product\ProductSupplierPersister;
use PrestaShop\PrestaShop\Adapter\Product\ProductSupplierProvider;
use PrestaShop\PrestaShop\Adapter\Product\ProductSupplierUpdater;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\SetProductSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplier as ProductSupplierDTO;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplierDeleterInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use ProductSupplier;

/**
 * Handles @var SetProductSuppliersCommand using legacy object model
 */
final class SetProductSuppliersHandler implements SetProductSuppliersHandlerInterface
{
    /**
     * @var ProductSupplierDeleterInterface
     */
    private $productSupplierDeleter;

    /**
     * @var ProductSupplierPersister
     */
    private $productSupplierPersister;

    /**
     * @var ProductSupplierProvider
     */
    private $productSupplierProvider;

    /**
     * @var ProductSupplierUpdater
     */
    private $productSupplierUpdater;

    /**
     * @param ProductSupplierDeleterInterface $productSupplierDeleter
     * @param ProductSupplierPersister $productSupplierPersister
     * @param ProductSupplierProvider $productSupplierProvider
     * @param ProductSupplierUpdater $productSupplierUpdater
     */
    public function __construct(
        ProductSupplierDeleterInterface $productSupplierDeleter,
        ProductSupplierPersister $productSupplierPersister,
        ProductSupplierProvider $productSupplierProvider,
        ProductSupplierUpdater $productSupplierUpdater
    ) {
        $this->productSupplierDeleter = $productSupplierDeleter;
        $this->productSupplierPersister = $productSupplierPersister;
        $this->productSupplierProvider = $productSupplierProvider;
        $this->productSupplierUpdater = $productSupplierUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SetProductSuppliersCommand $command): array
    {
        $productId = $command->getProductId();

        $this->setProductSuppliers($productId, $command->getProductSuppliers());
        $this->productSupplierUpdater->updateDefaultSupplier($productId, $command->getDefaultSupplierId());

        return $this->getProductSupplierIds($productId);
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
        $productSupplier->id_supplier = $productSupplierDTO->getSupplierId();
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
        $productSupplier = $this->productSupplierProvider->get(new ProductSupplierId($productSupplierDTO->getProductSupplierId()));
        $this->productSupplierPersister->update($productSupplier, $this->formatUpdatableProperties($productSupplierDTO));
    }

    /**
     * @param ProductSupplierDTO $productSupplierDTO
     *
     * @return array<string, mixed>
     */
    private function formatUpdatableProperties(ProductSupplierDTO $productSupplierDTO): array
    {
        return [
            'id_currency' => $productSupplierDTO->getCurrencyId(),
            'product_supplier_reference' => $productSupplierDTO->getReference(),
            'id_product_attribute' => $productSupplierDTO->getCombinationId(),
            'id_supplier' => $productSupplierDTO->getSupplierId(),
            'product_supplier_price_te' => $productSupplierDTO->getPriceTaxExcluded(),
        ];
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
