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
 * Transfers product supplier for editing data
 */
class ProductSupplierForEditing
{
    /**
     * @var int
     */
    private $productSupplierId;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $supplierId;

    /**
     * @var string
     */
    private $supplierName;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $priceTaxExcluded;

    /**
     * @var int
     */
    private $currencyId;

    /**
     * @var int|null
     */
    private $combinationId;

    /**
     * @param int $productSupplierId ProductSupplier entity record id
     * @param int $productId the associated product id
     * @param int $supplierId the associated supplier id
     * @param string $reference the reference for this product supplier
     * @param string $priceTaxExcluded
     * @param int $currencyId
     * @param int|null $combinationId
     */
    public function __construct(
        int $productSupplierId,
        int $productId,
        int $supplierId,
        string $supplierName,
        string $reference,
        string $priceTaxExcluded,
        int $currencyId,
        ?int $combinationId = null
    ) {
        $this->productSupplierId = $productSupplierId;
        $this->productId = $productId;
        $this->supplierId = $supplierId;
        $this->supplierName = $supplierName;
        $this->reference = $reference;
        $this->priceTaxExcluded = $priceTaxExcluded;
        $this->currencyId = $currencyId;
        $this->combinationId = $combinationId;
    }

    /**
     * @return int
     */
    public function getProductSupplierId(): int
    {
        return $this->productSupplierId;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getSupplierId(): int
    {
        return $this->supplierId;
    }

    /**
     * @return string
     */
    public function getSupplierName(): string
    {
        return $this->supplierName;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getPriceTaxExcluded(): string
    {
        return $this->priceTaxExcluded;
    }

    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    /**
     * @return int|null
     */
    public function getCombinationId(): ?int
    {
        return $this->combinationId;
    }
}
