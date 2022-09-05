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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * This value object identifies a specific association between a supplier and a combination. Usually the association is
 * based on three criteria (productId, combinationId, supplierId) @see ProductSupplierAssociation. But in a context of
 * search only the combinationId is mandatory. So this VO can be used to search a supplier association with a combination.
 */
class CombinationSupplierAssociation implements SupplierAssociationInterface
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @var SupplierId
     */
    private $supplierId;

    /**
     * @var ProductSupplierId|null
     */
    private $productSupplierId;

    /**
     * @param int $combinationId
     * @param int $supplierId
     * @param int|null $productSupplierId
     */
    public function __construct(int $combinationId, int $supplierId, ?int $productSupplierId = null)
    {
        $this->combinationId = new CombinationId($combinationId);
        $this->supplierId = new SupplierId($supplierId);
        $this->productSupplierId = null !== $productSupplierId ? new ProductSupplierId($productSupplierId) : null;
    }

    /**
     * @return CombinationIdInterface
     */
    public function getCombinationId(): CombinationIdInterface
    {
        return $this->combinationId;
    }

    /**
     * @return SupplierId
     */
    public function getSupplierId(): SupplierId
    {
        return $this->supplierId;
    }

    /**
     * @return ProductSupplierId|null
     */
    public function getProductSupplierId(): ?ProductSupplierId
    {
        return $this->productSupplierId;
    }

    /**
     * No need for product ID to identify a supplier association for a designated combination, we can match it by
     * combination ID only.
     *
     * @return ProductId|null
     */
    public function getProductId(): ?ProductId
    {
        return null;
    }

    public function __toString()
    {
        return sprintf(
            '[productId: null, combinationId: %d, supplierId: %d]',
            $this->getCombinationId()->getValue(),
            $this->getSupplierId()->getValue()
        );
    }
}
