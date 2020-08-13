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

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductSupplierHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\DeleteProductSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\DeleteProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotDeleteProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShopException;
use ProductSupplier;

/**
 * Handles @var DeleteProductSupplierCommand using legacy object model
 */
final class DeleteProductSupplierHandler extends AbstractProductSupplierHandler implements DeleteProductSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteProductSupplierCommand $command): void
    {
        $productSupplier = $this->getProductSupplier($command->getProductSupplierId());

        try {
            if (!$productSupplier->delete()) {
                throw new CannotDeleteProductSupplierException(sprintf(
                    'Failed deleting product supplier #%d',
                    $productSupplier->id
                ));
            }
            $this->refreshProductDefaultSupplier($productSupplier);
        } catch (PrestaShopException $e) {
            throw new ProductSupplierException(
                sprintf('Error occurred when deleting product supplier #%d', $productSupplier->id),
                0,
                $e
            );
        }
    }

    /**
     * @param ProductSupplier $productSupplier
     *
     * @throws CannotUpdateProductException
     * @throws ProductException
     * @throws ProductNotFoundException
     */
    private function refreshProductDefaultSupplier(ProductSupplier $productSupplier): void
    {
        $productId = (int) $productSupplier->id_product;
        $product = $this->getProduct(new ProductId($productId));

        // check if default supplier was deleted with this command
        if ((int) $product->id_supplier !== (int) $productSupplier->id_supplier) {
            return;
        }

        $product->id_supplier = 0;
        $product->supplier_reference = '';
        $this->fieldsToUpdate['id_supplier'] = true;
        $this->fieldsToUpdate['supplier_reference'] = true;

        $this->performUpdate($product, CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER);
    }
}
