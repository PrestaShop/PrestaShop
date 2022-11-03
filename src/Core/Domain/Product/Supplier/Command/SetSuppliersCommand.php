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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * This command is used to set (or assign) suppliers to a product It is used for both product with or without
 * combinations and only defines the association not the content themselves. To update contents you need to use
 * UpdateProductSuppliersCommand or UpdateCombinationSuppliersCommand one you have correctly set the associations
 * with this command.
 *
 * When no default supplier was associated this command will automatically use the first provided one, however
 * to change it to your need you can use SetProductDefaultSupplierCommand.
 */
class SetSuppliersCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var SupplierId[]
     */
    private $supplierIds;

    /**
     * @param int $productId
     * @param array $supplierIds
     */
    public function __construct(int $productId, array $supplierIds)
    {
        $this->productId = new ProductId($productId);
        $this->setSupplierIds($supplierIds);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return SupplierId[]
     */
    public function getSupplierIds(): array
    {
        return $this->supplierIds;
    }

    private function setSupplierIds(array $supplierIds): void
    {
        if (empty($supplierIds)) {
            throw new InvalidArgumentException(sprintf(
                'Empty array of suppliers provided in %s. To remove all product suppliers use %s.',
                self::class,
                RemoveAllAssociatedProductSuppliersCommand::class
            ));
        }

        $this->supplierIds = [];
        foreach ($supplierIds as $supplierId) {
            $this->supplierIds[] = new SupplierId($supplierId);
        }
    }
}
