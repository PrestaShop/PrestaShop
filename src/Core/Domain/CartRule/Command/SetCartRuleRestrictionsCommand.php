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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\Restriction\RestrictionRuleGroup;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

/**
 * Sets cart rule restrictions.
 * Leaving property as NULL will cause no changes, however having an empty array will clear existing restrictions.
 */
class SetCartRuleRestrictionsCommand
{
    private readonly CartRuleId $cartRuleId;

    /**
     * @var CartRuleId[]|null
     */
    private ?array $restrictedCartRuleIds = null;

    /**
     * @var RestrictionRuleGroup[]|null
     */
    private ?array $productRestrictionRuleGroups = null;

    /**
     * @var CarrierId[]|null
     */
    private ?array $restrictedCarrierIds = null;

    /**
     * @var CountryId[]|null
     */
    private ?array $restrictedCountryIds = null;

    /**
     * @var GroupId[]|null
     */
    private ?array $restrictedGroupIds = null;

    /**
     * @param int $cartRuleId
     */
    public function __construct(
        int $cartRuleId,
    ) {
        $this->cartRuleId = new CartRuleId($cartRuleId);
    }

    public function setRestrictedCarrierIds(array $restrictedCarrierIds): self
    {
        $this->restrictedCarrierIds = array_map(static function ($carrierId): CarrierId {
            return new CarrierId($carrierId);
        }, $restrictedCarrierIds);

        return $this;
    }

    public function setRestrictedCountryIds(array $restrictedCountryIds): self
    {
        $this->restrictedCountryIds = array_map(static function ($countryId): CountryId {
            return new CountryId($countryId);
        }, $restrictedCountryIds);

        return $this;
    }

    public function setRestrictedGroupIds(array $restrictedGroupIds): self
    {
        $this->restrictedGroupIds = array_map(static function ($groupId): GroupId {
            return new GroupId($groupId);
        }, $restrictedGroupIds);

        return $this;
    }

    /**
     * @return CarrierId[]|null
     */
    public function getRestrictedCarrierIds(): ?array
    {
        return $this->restrictedCarrierIds;
    }

    /**
     * @return CountryId[]|null
     */
    public function getRestrictedCountryIds(): ?array
    {
        return $this->restrictedCountryIds;
    }

    /**
     * @return GroupId[]|null
     */
    public function getRestrictedGroupIds(): ?array
    {
        return $this->restrictedGroupIds;
    }

    /**
     * @return CartRuleId
     */
    public function getCartRuleId(): CartRuleId
    {
        return $this->cartRuleId;
    }

    /**
     * @param int[] $restrictedCartRuleIds
     *
     * @return self
     *
     * @throws CartRuleConstraintException
     */
    public function setRestrictedCartRuleIds(array $restrictedCartRuleIds): self
    {
        $this->restrictedCartRuleIds = [];
        foreach ($restrictedCartRuleIds as $restrictedCartRuleId) {
            if ($restrictedCartRuleId === $this->getCartRuleId()->getValue()) {
                throw new CartRuleConstraintException(
                    'Restricted CartRule ids cannot contain id of current cart rule',
                    CartRuleConstraintException::INVALID_CART_RULE_RESTRICTION
                );
            }
            $this->restrictedCartRuleIds[] = new CartRuleId($restrictedCartRuleId);
        }

        return $this;
    }

    /**
     * @return CartRuleId[]|null
     */
    public function getRestrictedCartRuleIds(): ?array
    {
        return $this->restrictedCartRuleIds;
    }

    /**
     * @param RestrictionRuleGroup[] $productRestrictionRuleGroups
     *
     * @return self
     */
    public function setProductRestrictionRuleGroups(array $productRestrictionRuleGroups): self
    {
        $this->productRestrictionRuleGroups = $productRestrictionRuleGroups;

        return $this;
    }

    /**
     * @return RestrictionRuleGroup[]|null
     */
    public function getProductRestrictionRuleGroups(): ?array
    {
        return $this->productRestrictionRuleGroups;
    }
}
