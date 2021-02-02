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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult;

/**
 * Transfers supplier information for product-supplier relation
 */
class ProductSupplierInfo
{
    /**
     * @var string
     */
    private $supplierName;

    /**
     * @var int
     */
    private $supplierId;

    /**
     * @var ProductSupplierForEditing
     */
    private $productSupplierForEditing;

    /**
     * @param string $supplierName
     * @param int $supplierId
     * @param ProductSupplierForEditing $productSupplierForEditing
     */
    public function __construct(
        string $supplierName,
        int $supplierId,
        ProductSupplierForEditing $productSupplierForEditing
    ) {
        $this->supplierName = $supplierName;
        $this->supplierId = $supplierId;
        $this->productSupplierForEditing = $productSupplierForEditing;
    }

    /**
     * @return string
     */
    public function getSupplierName(): string
    {
        return $this->supplierName;
    }

    /**
     * @return int
     */
    public function getSupplierId(): int
    {
        return $this->supplierId;
    }

    /**
     * @return ProductSupplierForEditing
     */
    public function getProductSupplierForEditing(): ProductSupplierForEditing
    {
        return $this->productSupplierForEditing;
    }
}
