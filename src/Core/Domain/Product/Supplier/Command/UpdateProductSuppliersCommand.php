<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplier;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;

/**
 * Updates supplier data related with specific product.
 */
class UpdateProductSuppliersCommand
{
    /**
     * @var ProductSupplier[]
     */
    private $suppliers;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     *
     * @param int $productId
     * @param array $suppliers
     *
     * @throws CurrencyException
     * @throws DomainConstraintException
     * @throws ProductConstraintException
     * @throws SupplierException
     */
    public function __construct(int $productId, array $suppliers)
    {
        $this->setSuppliers($suppliers);
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductSupplier[]
     */
    public function getSuppliers(): array
    {
        return $this->suppliers;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @param array $suppliers
     *
     * @throws CurrencyException
     * @throws DomainConstraintException
     * @throws ProductConstraintException
     * @throws SupplierException
     */
    private function setSuppliers(array $suppliers): void
    {
        foreach ($suppliers as $supplier) {
            $this->suppliers[] = new ProductSupplier(
                $supplier['id'],
                $supplier['is_default'],
                $supplier['reference'],
                $supplier['price_tax_excluded'],
                $supplier['currency_id']
            );
        }
    }
}
