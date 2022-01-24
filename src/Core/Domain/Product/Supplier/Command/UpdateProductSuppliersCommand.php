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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplierUpdate;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Updates product suppliers
 */
class UpdateProductSuppliersCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductSupplierUpdate[]
     */
    private $productSuppliers;

    /**
     * @param int $productId
     * @param array<int, array<string, mixed>> $productSuppliers
     *
     * @see UpdateProductSuppliersCommand::setProductSuppliers() for $productSuppliers structure
     */
    public function __construct(int $productId, array $productSuppliers)
    {
        $this->setProductSuppliers($productSuppliers, $productId);
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ProductSupplierUpdate[]
     */
    public function getProductSuppliers(): array
    {
        return $this->productSuppliers;
    }

    /**
     * @param array<int, array<string, mixed>> $productSuppliers
     * @param int $productId
     */
    private function setProductSuppliers(array $productSuppliers, int $productId): void
    {
        if (empty($productSuppliers)) {
            throw new InvalidArgumentException(sprintf(
                'Empty array of product suppliers provided in %s. To remove all product suppliers use %s.',
                self::class,
                RemoveAllAssociatedProductSuppliersCommand::class
            ));
        }

        foreach ($productSuppliers as $productSupplier) {
            $this->productSuppliers[] = new ProductSupplierUpdate(
                new ProductSupplierAssociation(
                    $productId,
                    NoCombinationId::NO_COMBINATION_ID,
                    $productSupplier['supplier_id'],
                    !empty($productSupplier['product_supplier_id']) ? $productSupplier['product_supplier_id'] : null
                ),
                $productSupplier['currency_id'],
                $productSupplier['reference'],
                $productSupplier['price_tax_excluded']
            );
        }
    }
}
