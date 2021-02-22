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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier;

use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Transfers data of product supplier
 */
class ProductSupplier
{
    /**
     * @var SupplierId
     */
    private $supplierId;

    /**
     * @var CurrencyId
     */
    private $currencyId;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $priceTaxExcluded;

    /**
     * @var ProductSupplierId|null
     */
    private $productSupplierId;

    /**
     * @param int $supplierId
     * @param int $currencyId
     * @param string $reference
     * @param string $priceTaxExcluded
     * @param int|null $productSupplierId Provide value to update existing resource. Null means this is new resource
     */
    public function __construct(
        int $supplierId,
        int $currencyId,
        string $reference,
        string $priceTaxExcluded,
        ?int $productSupplierId = null
    ) {
        $this->supplierId = new SupplierId($supplierId);
        $this->currencyId = new CurrencyId($currencyId);
        $this->reference = $reference;
        $this->priceTaxExcluded = $priceTaxExcluded;
        $this->productSupplierId = $productSupplierId ? new ProductSupplierId($productSupplierId) : null;
    }

    /**
     * @return SupplierId
     */
    public function getSupplierId(): SupplierId
    {
        return $this->supplierId;
    }

    /**
     * @return CurrencyId
     */
    public function getCurrencyId(): CurrencyId
    {
        return $this->currencyId;
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
     * @return ProductSupplierId|null
     */
    public function getProductSupplierId(): ?ProductSupplierId
    {
        return $this->productSupplierId;
    }
}
