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
use PrestaShop\PrestaShop\Adapter\Product\ProductProvider;
use PrestaShop\PrestaShop\Adapter\Product\ProductSupplierProvider;
use PrestaShop\PrestaShop\Adapter\Product\ProductUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\DeleteProductSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\DeleteProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplierDeleterInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles @var DeleteProductSupplierCommand using legacy object model
 */
final class DeleteProductSupplierHandler extends AbstractProductSupplierHandler implements DeleteProductSupplierHandlerInterface
{
    /**
     * @var ProductSupplierDeleterInterface
     */
    private $productSupplierDeleter;

    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @var ProductSupplierProvider
     */
    private $productSupplierProvider;

    /**
     * @var ProductUpdater
     */
    private $productUpdater;

    /**
     * @param ProductSupplierDeleterInterface $productSupplierDeleter
     * @param ProductProvider $productProvider
     * @param ProductSupplierProvider $productSupplierProvider
     * @param ProductUpdater $productUpdater
     */
    public function __construct(
        ProductSupplierDeleterInterface $productSupplierDeleter,
        ProductProvider $productProvider,
        ProductSupplierProvider $productSupplierProvider,
        ProductUpdater $productUpdater
    ) {
        $this->productSupplierDeleter = $productSupplierDeleter;
        $this->productProvider = $productProvider;
        $this->productSupplierProvider = $productSupplierProvider;
        $this->productUpdater = $productUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteProductSupplierCommand $command): void
    {
        $this->productSupplierDeleter->delete($command->getProductSupplierId());
        $productSupplier = $this->productSupplierProvider->get($command->getProductSupplierId());
        $product = $this->productProvider->get(new ProductId((int) $productSupplier->id_product));

        // reset product default supplier if product default supplier was deleted
        if ((int) $product->id_supplier === (int) $productSupplier->id_supplier) {
            $this->productUpdater->resetProductDefaultSupplier($product);
        }
    }
}
