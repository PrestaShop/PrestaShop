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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * This value object identifies a specific association between a supplier and a product. It is based on three criteria
 * productId, combinationId and supplierId. For product without combinations combinationId is always 0.
 *
 * The productSupplierId is optional, it may be present when the association already exists in DB or absent if we
 * haven't created it yet but we can still use this VO based on the criteria.
 */
class ProductSupplierAssociation implements SupplierAssociationInterface
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var CombinationIdInterface
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
     * @param int $productId
     * @param int $combinationId
     * @param int $supplierId
     * @param int|null $productSupplierId
     */
    public function __construct(int $productId, int $combinationId, int $supplierId, ?int $productSupplierId = null)
    {
        $this->productId = new ProductId($productId);
        $this->combinationId = $combinationId ? new CombinationId($combinationId) : new NoCombinationId();
        $this->supplierId = new SupplierId($supplierId);
        $this->productSupplierId = null !== $productSupplierId ? new ProductSupplierId($productSupplierId) : null;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
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

    public function __toString()
    {
        return sprintf(
            '[productId: %d, combinationId: %d, supplierId: %d]',
            $this->getProductId()->getValue(),
            $this->getCombinationId()->getValue(),
            $this->getSupplierId()->getValue()
        );
    }
}
