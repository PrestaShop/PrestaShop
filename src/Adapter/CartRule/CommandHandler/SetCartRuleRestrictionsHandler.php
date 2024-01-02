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

namespace PrestaShop\PrestaShop\Adapter\CartRule\CommandHandler;

use CartRule;
use PrestaShop\PrestaShop\Adapter\CartRule\Repository\CartRuleRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\SetCartRuleRestrictionsCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\CommandHandler\SetCartRuleRestrictionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

#[AsCommandHandler]
class SetCartRuleRestrictionsHandler implements SetCartRuleRestrictionsHandlerInterface
{
    public function __construct(
        protected readonly CartRuleRepository $cartRuleRepository
    ) {
    }

    public function handle(SetCartRuleRestrictionsCommand $command): void
    {
        if (null === $command->getRestrictedCartRuleIds()
            && null === $command->getProductRestrictionRuleGroups()
            && null === $command->getRestrictedCarrierIds()
            && null === $command->getRestrictedCountryIds()
            && null === $command->getRestrictedGroupIds()
        ) {
            // no restrictions were modified
            return;
        }

        $cartRule = $this->cartRuleRepository->get($command->getCartRuleId());

        $restrictedCartRuleIds = $command->getRestrictedCartRuleIds();
        if (null !== $restrictedCartRuleIds) {
            $this->setCartRuleRestrictions($cartRule, $restrictedCartRuleIds);
        }
        $productRestrictionGroups = $command->getProductRestrictionRuleGroups();
        if (null !== $productRestrictionGroups) {
            $this->setProductRestrictions($cartRule, $productRestrictionGroups);
        }
        $restrictedCarrierIds = $command->getRestrictedCarrierIds();
        if (null !== $restrictedCarrierIds) {
            $this->setCarrierRestrictions($cartRule, $restrictedCarrierIds);
        }
        $restrictedCountryIds = $command->getRestrictedCountryIds();
        if (null !== $restrictedCountryIds) {
            $this->setCountryRestrictions($cartRule, $restrictedCountryIds);
        }
        $restrictedGroupIds = $command->getRestrictedGroupIds();
        if (null !== $restrictedGroupIds) {
            $this->setGroupRestrictions($cartRule, $restrictedGroupIds);
        }
        // it would be more performant updating all restriction props at the end with one update call,
        // but that way we might introduce cart rule state failure in case one of steps fails somewhere in the middle
    }

    private function setCartRuleRestrictions(CartRule $cartRule, array $restrictedCartRuleIds): void
    {
        $cartRuleId = new CartRuleId((int) $cartRule->id);
        $this->cartRuleRepository->assertAllCartRulesExists($restrictedCartRuleIds);
        $this->cartRuleRepository->restrictCartRules($cartRuleId, $restrictedCartRuleIds);
        $hasRestrictions = !empty($restrictedCartRuleIds);

        $cartRule->cart_rule_restriction = $hasRestrictions;
        $this->cartRuleRepository->partialUpdate($cartRule, ['cart_rule_restriction']);

        // update cart_rule_restriction property for all the cart rules that have been affected
        foreach ($this->cartRuleRepository->getRestrictedCartRuleIds($cartRuleId) as $restrictedCartRuleId) {
            $affectedCartRule = $this->cartRuleRepository->get(new CartRuleId($restrictedCartRuleId));
            $affectedCartRule->cart_rule_restriction = $hasRestrictions;
            $this->cartRuleRepository->partialUpdate($affectedCartRule, ['cart_rule_restriction']);
        }
    }

    private function setProductRestrictions(CartRule $cartRule, array $restrictionRuleGroups): void
    {
        $this->cartRuleRepository->setProductRestrictions(new CartRuleId((int) $cartRule->id), $restrictionRuleGroups);

        $cartRule->product_restriction = !empty($restrictionRuleGroups);
        $this->cartRuleRepository->partialUpdate($cartRule, ['product_restriction']);
    }

    /**
     * @param CartRule $cartRule
     * @param CarrierId[] $restrictedCarrierIds
     *
     * @return void
     */
    private function setCarrierRestrictions(CartRule $cartRule, array $restrictedCarrierIds): void
    {
        $this->cartRuleRepository->setCarrierRestrictions(new CartRuleId((int) $cartRule->id), $restrictedCarrierIds);

        $cartRule->carrier_restriction = !empty($restrictedCarrierIds);
        $this->cartRuleRepository->partialUpdate($cartRule, ['carrier_restriction']);
    }

    /**
     * @param CartRule $cartRule
     * @param CountryId[] $restrictedCountryIds
     *
     * @return void
     */
    private function setCountryRestrictions(CartRule $cartRule, array $restrictedCountryIds): void
    {
        $this->cartRuleRepository->setCountryRestrictions(new CartRuleId((int) $cartRule->id), $restrictedCountryIds);

        $cartRule->country_restriction = !empty($restrictedCountryIds);
        $this->cartRuleRepository->partialUpdate($cartRule, ['country_restriction']);
    }

    /**
     * @param GroupId[] $restrictedGroupIds
     *
     * @return void
     */
    private function setGroupRestrictions(CartRule $cartRule, array $restrictedGroupIds): void
    {
        $this->cartRuleRepository->setGroupRestrictions(new CartRuleId((int) $cartRule->id), $restrictedGroupIds);

        $cartRule->group_restriction = !empty($restrictedGroupIds);
        $this->cartRuleRepository->partialUpdate($cartRule, ['group_restriction']);
    }
}
