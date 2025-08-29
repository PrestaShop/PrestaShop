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

namespace PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;

class ShopConstraint
{
    /**
     * These are the legacy values used to define the shop context, kept here for backward compatibility
     */
    public const SHOP = 1;
    public const SHOP_GROUP = 2;
    public const ALL_SHOPS = 4;

    protected ?ShopId $shopId = null;

    protected ?ShopGroupId $shopGroupId = null;

    /**
     * Indicate if the value returned matches the constraints strictly, else it fallbacks to Shop > Group > Global value
     *
     * @var bool
     */
    protected $strict;

    /**
     * Constraint to target a specific shop
     *
     * @param int $shopId
     * @param bool $isStrict
     *
     * @return static
     *
     * @throws ShopException
     */
    public static function shop(int $shopId, bool $isStrict = false): self
    {
        return new static($shopId, null, $isStrict);
    }

    /**
     * Constraint to target a specific shop group
     *
     * @param int $shopGroupId
     * @param bool $isStrict
     *
     * @return static
     *
     * @throws ShopException
     */
    public static function shopGroup(int $shopGroupId, bool $isStrict = false): self
    {
        return new static(null, $shopGroupId, $isStrict);
    }

    /**
     * Constraint to target all shops
     *
     * @param bool $isStrict
     *
     * @return static
     */
    public static function allShops(bool $isStrict = false): self
    {
        return new static(null, null, $isStrict);
    }

    /**
     * @param int|null $shopId
     * @param int|null $shopGroupId
     * @param bool $strict
     *
     * @throws ShopException
     */
    protected function __construct(?int $shopId, ?int $shopGroupId, bool $strict = false)
    {
        $this->shopId = null !== $shopId ? new ShopId($shopId) : null;
        $this->shopGroupId = null !== $shopGroupId ? new ShopGroupId($shopGroupId) : null;
        $this->strict = $strict;
    }

    /**
     * Clone the constraint, you can specify a force $strict value, if not set the same value is kept.
     *
     * @param bool|null $strict
     *
     * @return static
     *
     * @throws ShopException
     */
    public function clone(?bool $strict = null): self
    {
        return new static($this->shopId?->getValue(), $this->shopGroupId?->getValue(), $strict !== null ? $strict : $this->strict);
    }

    /**
     * @return ShopId|null
     */
    public function getShopId(): ?ShopId
    {
        return $this->shopId;
    }

    /**
     * @return ShopGroupId|null
     */
    public function getShopGroupId(): ?ShopGroupId
    {
        return $this->shopGroupId;
    }

    /**
     * @return bool
     */
    public function forAllShops(): bool
    {
        return null === $this->shopId && null === $this->shopGroupId;
    }

    /**
     * @return bool
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function isEqual(self $constraint): bool
    {
        if ($this->isStrict() !== $constraint->isStrict()) {
            return false;
        }

        if ($this->getShopId() !== null && $constraint->getShopId() !== null && $this->getShopId()->getValue() === $constraint->getShopId()->getValue()) {
            return true;
        }

        if ($this->getShopGroupId() !== null && $constraint->getShopGroupId() !== null && $this->getShopGroupId()->getValue() === $constraint->getShopGroupId()->getValue()) {
            return true;
        }

        if ($this->forAllShops() && $constraint->forAllShops()) {
            return true;
        }

        return false;
    }

    public function isSingleShopContext(): bool
    {
        return null !== $this->shopId;
    }

    public function isShopGroupContext(): bool
    {
        return null !== $this->shopGroupId;
    }

    public function isAllShopContext(): bool
    {
        return $this->forAllShops();
    }
}
