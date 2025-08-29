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

namespace PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;

class ShopCollection extends ShopConstraint
{
    /**
     * @var ShopId[]|null
     */
    protected ?array $shopIds = null;

    /**
     * Constraint to target a list of shops.
     *
     * @param int[] $shopIds
     *
     * @return static
     *
     * @throws ShopException
     */
    public static function shops(array $shopIds): self
    {
        return new static(null, null, false, $shopIds);
    }

    /**
     * @param int|null $shopId
     * @param int|null $shopGroupId
     * @param bool $strict
     *
     * @throws ShopException
     */
    protected function __construct(?int $shopId, ?int $shopGroupId, bool $strict = false, ?array $shopIds = null)
    {
        parent::__construct($shopId, $shopGroupId, $strict);
        $this->shopIds = null !== $shopIds ? array_map(fn (int $shopId) => new ShopId($shopId), $shopIds) : null;
    }

    /**
     * @return ShopId[]|null
     */
    public function getShopIds(): ?array
    {
        return $this->shopIds;
    }

    public function hasShopIds(): bool
    {
        return null !== $this->shopIds;
    }

    /**
     * @return bool
     */
    public function forAllShops(): bool
    {
        return null === $this->shopId && null === $this->shopGroupId && null === $this->shopIds;
    }

    /**
     * Clone the constraint, you can specify a force $strict value, but it will always remain false.
     *
     * @param bool|null $strict
     *
     * @return static
     *
     * @throws ShopException
     */
    public function clone(?bool $strict = null): self
    {
        return new static($this->shopId?->getValue(), $this->shopGroupId?->getValue(), false, $this->shopIds);
    }

    public function isEqual(ShopConstraint $constraint): bool
    {
        if (parent::isEqual($constraint) || !($constraint instanceof ShopCollection)) {
            return true;
        }

        if ($this->getShopIds() !== null && $constraint->getShopIds() !== null && count($this->getShopIds()) === count($constraint->getShopIds())) {
            return empty(array_diff(
                array_map(fn (ShopId $shopId) => $shopId->getValue(), $this->getShopIds()),
                array_map(fn (ShopId $shopId) => $shopId->getValue(), $constraint->getShopIds()),
            ));
        }

        return false;
    }
}
