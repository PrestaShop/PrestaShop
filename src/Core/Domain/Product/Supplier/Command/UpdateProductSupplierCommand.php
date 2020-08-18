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
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;

/**
 * Updates product supplier
 */
class UpdateProductSupplierCommand
{
    /**
     * @var ProductSupplierId
     */
    private $productSupplierId;

    /**
     * @var CurrencyId|null
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
     * @param int $productSupplierId
     */
    public function __construct(int $productSupplierId)
    {
        $this->productSupplierId = new ProductSupplierId($productSupplierId);
    }

    /**
     * @return ProductSupplierId
     */
    public function getProductSupplierId(): ProductSupplierId
    {
        return $this->productSupplierId;
    }

    /**
     * @return CurrencyId|null
     */
    public function getCurrencyId(): ?CurrencyId
    {
        return $this->currencyId;
    }

    /**
     * @param int $currencyId
     *
     * @return UpdateProductSupplierCommand
     */
    public function setCurrencyId(int $currencyId): UpdateProductSupplierCommand
    {
        $this->currencyId = new CurrencyId($currencyId);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return UpdateProductSupplierCommand
     */
    public function setReference(string $reference): UpdateProductSupplierCommand
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return Number|null
     */
    public function getPriceTaxExcluded(): ?Number
    {
        return $this->priceTaxExcluded;
    }

    /**
     * @param string $priceTaxExcluded
     *
     * @return UpdateProductSupplierCommand
     */
    public function setPriceTaxExcluded(string $priceTaxExcluded): UpdateProductSupplierCommand
    {
        $this->priceTaxExcluded = new Number($priceTaxExcluded);

        return $this;
    }

    /**
     * @return CombinationId|null
     */
    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @param int $combinationId
     *
     * @return UpdateProductSupplierCommand
     */
    public function setCombinationId(int $combinationId): UpdateProductSupplierCommand
    {
        $this->combinationId = new CombinationId($combinationId);

        return $this;
    }
}
