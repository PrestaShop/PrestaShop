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
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\AddProductSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\AddProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\UpdateProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\UpdateProductSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplier;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

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

    public function __construct(
        AddProductSupplierHandlerInterface $addProductSupplierHandler,
        UpdateProductSupplierHandlerInterface $updateProductSupplierHandler,
        DeleteProductSupplierHandler $deleteProductSupplierHandler
    ) {
        $this->addProductSupplierHandler = $addProductSupplierHandler;
        $this->updateProductSupplierHandler = $updateProductSupplierHandler;
        $this->deleteProductSupplierHandler = $deleteProductSupplierHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductSuppliersCommand $command): void
    {
        if (null !== $command->getProductSuppliers()) {
            $this->updateProductSuppliers($command->getProductId(), $command->getProductSuppliers());
        }
    }

    /**
     * @param ProductId $productId
     * @param ProductSupplier[] $productSuppliers
     */
    private function updateProductSuppliers(ProductId $productId, array $productSuppliers): void
    {
        foreach ($productSuppliers as $productSupplier) {
            if ($productSupplier->getProductSupplierId()) {
                $this->updateProductSupplier($productId, $productSupplier);
            } else {
                $this->addProductSupplier($productId, $productSupplier);
            }
        }
    }

    private function addProductSupplier(ProductId $productId, ProductSupplier $productSupplier): ProductSupplierId
    {
        $command = new AddProductSupplierCommand(
            $productId,
            $productSupplier->getSupplierId(),
            $productSupplier->getCurrencyId(),
            $productSupplier->getReference()
        );
        $this->addProductSupplierHandler->handle()
    }

    private function updateProductSupplier(ProductId $productId, ProductSupplier $productSupplier): void
    {

    }
}
