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

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command;

use DateTime;
use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;

/**
 * Add specific price to a Product
 */
class AddProductSpecificPriceCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var Reduction
     */
    private $reduction;

    /**
     * @var bool
     */
    private $includesTax;

    /**
     * @var DecimalNumber
     */
    private $price;

    /**
     * @var int
     */
    private $fromQuantity;

    /**
     * @var int|null
     */
    private $shopId;

    /**
     * @var CombinationId|null
     */
    private $combinationId;

    /**
     * @var int|null
     */
    private $currencyId;

    /**
     * @var int|null
     */
    private $countryId;

    /**
     * @var int|null
     */
    private $groupId;

    /**
     * @var int|null
     */
    private $customerId;

    /**
     * @var DateTimeInterface
     *
     * @see DateTime
     * @see NullDateTime
     */
    private $dateTimeFrom;

    /**
     * @var DateTimeInterface
     *
     * @see DateTime
     * @see NullDateTime
     */
    private $dateTimeTo;

    /**
     * @param int $productId
     * @param string $reductionType
     * @param string $reductionValue
     * @param bool $includeTax
     * @param string $price
     * @param int $fromQuantity
     *
     * @throws DomainConstraintException
     * @throws ProductConstraintException
     */
    public function __construct(
        int $productId,
        string $reductionType,
        string $reductionValue,
        bool $includeTax,
        string $price,
        int $fromQuantity
    ) {
        $this->productId = new ProductId($productId);
        $this->reduction = new Reduction($reductionType, $reductionValue);
        $this->includesTax = $includeTax;
        $this->price = new DecimalNumber($price);
        $this->fromQuantity = $fromQuantity;
        $this->dateTimeFrom = new NullDateTime();
        $this->dateTimeTo = new NullDateTime();
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return Reduction
     */
    public function getReduction(): Reduction
    {
        return $this->reduction;
    }

    /**
     * @return bool
     */
    public function includesTax(): bool
    {
        return $this->includesTax;
    }

    /**
     * @return DecimalNumber
     */
    public function getPrice(): DecimalNumber
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getFromQuantity(): int
    {
        return $this->fromQuantity;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateTimeFrom(): DateTimeInterface
    {
        return $this->dateTimeFrom;
    }

    /**
     * @param DateTimeInterface $dateTimeFrom
     *
     * @see DateTime
     * @see NullDateTime
     *
     * @return AddProductSpecificPriceCommand
     */
    public function setDateTimeFrom(DateTimeInterface $dateTimeFrom): self
    {
        $this->dateTimeFrom = $dateTimeFrom;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateTimeTo(): ?DateTimeInterface
    {
        return $this->dateTimeTo;
    }

    /**
     * @param DateTimeInterface $dateTimeTo
     *
     * @see DateTime
     * @see NullDateTime
     *
     * @return AddProductSpecificPriceCommand
     */
    public function setDateTimeTo(DateTimeInterface $dateTimeTo): self
    {
        $this->dateTimeTo = $dateTimeTo;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    /**
     * @param int $shopId
     *
     * @return $this
     */
    public function setShopId(int $shopId): self
    {
        $this->shopId = $shopId;

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
     * @return $this
     */
    public function setCombinationId(int $combinationId): self
    {
        $this->combinationId = new CombinationId($combinationId);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCurrencyId(): ?int
    {
        return $this->currencyId;
    }

    /**
     * @param int $currencyId
     *
     * @return $this
     */
    public function setCurrencyId(int $currencyId): self
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     *
     * @return $this
     */
    public function setCountryId(int $countryId): self
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function setGroupId(int $groupId): self
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }
}
