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

use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Adds new product supplier
 */
class AddProductSupplierCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var SupplierId
     */
    private $supplierId;

    /**
     * @var CurrencyId
     */
    private $currencyId;

    /**
     * @var string|null
     */
    private $reference;

    /**
     * @var Number|null
     */
    private $priceTaxExcluded;

    /**
     * @var CombinationId|null
     */
    private $combinationId;

    /**
     * @param int $productId
     * @param int $supplierId
     * @param int $currencyId
     * @param string|null $reference
     * @param string|null $priceTaxExcluded
     * @param int|null $combinationId
     *
     * @throws CurrencyException
     * @throws CombinationConstraintException
     * @throws SupplierException
     */
    public function __construct(
        int $productId,
        int $supplierId,
        int $currencyId,
        ?string $reference = null,
        ?string $priceTaxExcluded = null,
        ?int $combinationId = null
    ) {
        $this->productId = new ProductId($productId);
        $this->supplierId = new SupplierId($supplierId);
        $this->currencyId = new CurrencyId($currencyId);
        $this->reference = $reference;
        $this->priceTaxExcluded = $priceTaxExcluded ? new Number($priceTaxExcluded) : null;
        $this->combinationId = $combinationId ? new CombinationId($combinationId) : null;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
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
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @return Number|null
     */
    public function getPriceTaxExcluded(): ?Number
    {
        return $this->priceTaxExcluded;
    }

    /**
     * @return CombinationId|null
     */
    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }
}
