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

namespace PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Command;

use DateTime;
use Exception;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

/**
 * Adds specific price
 *
 * @deprecated since 1.7.8.0 Use UpdateProductPriceInCartCommand or AddProductSpecificPriceCommand
 */
class AddSpecificPriceCommand
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
    private $includeTax;

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
    private $shopGroupId;

    /**
     * @var int|null
     */
    private $shopId;

    /**
     * @var int|null
     */
    private $cartId;

    /**
     * @var int|null
     */
    private $productAttributeId;

    /**
     * @var int|null
     */
    private $currencyId;

    /**
     * @var int|null
     */
    private $catalogPriceRuleId;

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
     * @var DateTime|null
     */
    private $dateTimeFrom;

    /**
     * @var DateTime|null
     */
    private $dateTimeTo;

    /**
     * @param int $productId
     * @param string $reductionType
     * @param float $reductionValue
     * @param bool $includeTax
     * @param float $price
     * @param int $fromQuantity
     *
     * @throws DomainConstraintException
     */
    public function __construct(
        int $productId,
        string $reductionType,
        float $reductionValue,
        bool $includeTax,
        float $price,
        int $fromQuantity
    ) {
        $this->productId = new ProductId($productId);
        $this->reduction = new Reduction($reductionType, $reductionValue);
        $this->includeTax = $includeTax;
        $this->price = new DecimalNumber((string) $price);
        $this->fromQuantity = $fromQuantity;
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
    public function isIncludeTax(): bool
    {
        return $this->includeTax;
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
     * @return DateTime|null
     */
    public function getDateTimeFrom(): ?DateTime
    {
        return $this->dateTimeFrom;
    }

    /**
     * @param DateTime|null $dateTimeFrom
     */
    public function setDateTimeFrom(?DateTime $dateTimeFrom): void
    {
        $this->dateTimeFrom = $this->createDateTime($dateTimeFrom);
    }

    /**
     * @return int|null
     */
    public function getShopGroupId(): ?int
    {
        return $this->shopGroupId;
    }

    /**
     * @param int $shopGroupId
     */
    public function setShopGroupId(int $shopGroupId): void
    {
        $this->shopGroupId = $shopGroupId;
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
     */
    public function setShopId(int $shopId): void
    {
        $this->shopId = $shopId;
    }

    /**
     * @return int|null
     */
    public function getCartId(): ?int
    {
        return $this->cartId;
    }

    /**
     * @param int $cartId
     */
    public function setCartId(int $cartId): void
    {
        $this->cartId = $cartId;
    }

    /**
     * @return int|null
     */
    public function getProductAttributeId(): ?int
    {
        return $this->productAttributeId;
    }

    /**
     * @param int $productAttributeId
     */
    public function setProductAttributeId(int $productAttributeId): void
    {
        $this->productAttributeId = $productAttributeId;
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
     */
    public function setCurrencyId(int $currencyId): void
    {
        $this->currencyId = $currencyId;
    }

    /**
     * @return int|null
     *
     * @deprecated use getCatalogPriceRuleId() instead. (wrong naming used in migration process)
     */
    public function getCartRuleId(): ?int
    {
        return $this->catalogPriceRuleId;
    }

    /**
     * @return int|null
     */
    public function getCatalogPriceRuleId(): ?int
    {
        return $this->catalogPriceRuleId;
    }

    /**
     * @param int $cartRuleId
     *
     * @deprecated use setCatalogPriceRuleId() instead. (wrong naming used in migration process)
     */
    public function setCartRuleId(int $cartRuleId): void
    {
        $this->catalogPriceRuleId = $cartRuleId;
    }

    /**
     * @param int $catalogPriceRuleId
     */
    public function setCatalogPriceRuleId(int $catalogPriceRuleId): void
    {
        $this->catalogPriceRuleId = $catalogPriceRuleId;
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
     */
    public function setCountryId(int $countryId): void
    {
        $this->countryId = $countryId;
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
     */
    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
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
     */
    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    /**
     * @return DateTime|null
     */
    public function getDateTimeTo(): ?DateTime
    {
        return $this->dateTimeTo;
    }

    /**
     * @param DateTime|null $dateTimeTo
     */
    public function setDateTimeTo(?DateTime $dateTimeTo): void
    {
        $this->dateTimeTo = $this->createDateTime($dateTimeTo);
    }

    /**
     * @param string $dateTime
     *
     * @return DateTime
     *
     * @throws SpecificPriceConstraintException
     */
    private function createDateTime(string $dateTime): DateTime
    {
        try {
            return new DateTime($dateTime);
        } catch (Exception $e) {
            throw new SpecificPriceConstraintException('An error occurred when creating DateTime object for specific price', SpecificPriceConstraintException::INVALID_DATETIME, $e);
        }
    }
}
