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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Price;

/**
 * Holds supplier and product related information.
 */
class ProductSupplier
{
    /**
     * @var SupplierId
     */
    private $supplierId;

    /**
     * @var bool
     */
    private $isDefaultSupplier;

    /**
     * @var Reference
     */
    private $reference;

    /**
     * @var CurrencyId
     */
    private $currencyId;

    /**
     * @var Price
     */
    private $priceTaxExcluded;

    /**
     * @param int $supplierId
     * @param bool $isDefaultSupplier
     * @param string $reference
     * @param float $priceTaxExcluded
     * @param int $currencyId
     *
     * @throws SupplierException
     * @throws ProductConstraintException
     * @throws DomainConstraintException
     * @throws CurrencyException
     */
    public function __construct(
        int $supplierId,
        bool $isDefaultSupplier,
        string $reference,
        float $priceTaxExcluded,
        int $currencyId
    ) {
        $this->supplierId = new SupplierId($supplierId);
        $this->isDefaultSupplier = $isDefaultSupplier;
        $this->reference = new Reference($reference);
        $this->priceTaxExcluded = new Price($priceTaxExcluded);
        $this->currencyId = new CurrencyId($currencyId);
    }

    /**
     * @return SupplierId
     */
    public function getSupplierId(): SupplierId
    {
        return $this->supplierId;
    }

    /**
     * @return bool
     */
    public function isDefaultSupplier(): bool
    {
        return $this->isDefaultSupplier;
    }

    /**
     * @return Reference
     */
    public function getReference(): Reference
    {
        return $this->reference;
    }

    /**
     * @return CurrencyId
     */
    public function getCurrencyId(): CurrencyId
    {
        return $this->currencyId;
    }

    /**
     * @return Price
     */
    public function getPriceTaxExcluded(): Price
    {
        return $this->priceTaxExcluded;
    }
}
