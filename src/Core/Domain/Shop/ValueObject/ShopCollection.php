<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
