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
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\SetCartRuleRestrictionsCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\CommandHandler\SetCartRuleRestrictionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;

class SetCartRuleRestrictionsHandler implements SetCartRuleRestrictionsHandlerInterface
{
    public function __construct(
        protected readonly CartRuleRepository $cartRuleRepository
    ) {
    }

    public function handle(SetCartRuleRestrictionsCommand $command): void
    {
        if ($command->isEmpty()) {
            // no restrictions were modified
            return;
        }

        $cartRule = $this->cartRuleRepository->get($command->cartRuleId);

        $restrictedCartRuleIds = $command->getRestrictedCartRuleIds();
        if (null !== $restrictedCartRuleIds) {
            $this->setCartRuleRestrictions($cartRule, $restrictedCartRuleIds);
            $this->updateRestrictionProperty($cartRule, 'cart_rule_restriction', !empty($restrictedCartRuleIds));
        }
        $productRestrictionGroups = $command->getProductRestrictionRuleGroups();
        if (null !== $productRestrictionGroups) {
            $this->setProductRestrictions($cartRule, $productRestrictionGroups);
            $this->updateRestrictionProperty($cartRule, 'product_restriction', !empty($productRestrictionGroups));
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

        $this->updateRestrictionProperty($cartRule, 'cart_rule_restriction', $hasRestrictions);

        // update cart_rule_restriction property for all the cart rules that have been affected
        foreach ($this->cartRuleRepository->getRestrictedCartRuleIds($cartRuleId) as $restrictedCartRuleId) {
            $affectedCartRule = $this->cartRuleRepository->get(new CartRuleId($restrictedCartRuleId));
            $this->updateRestrictionProperty($affectedCartRule, 'cart_rule_restriction', $hasRestrictions);
        }
    }

    private function updateRestrictionProperty(CartRule $cartRule, string $propertyName, bool $hasRestrictions): void
    {
        $cartRule->cart_rule_restriction = $hasRestrictions;
        $this->cartRuleRepository->partialUpdate(
            $cartRule,
            [$propertyName]
        );
    }

    private function setProductRestrictions(CartRule $cartRule, array $restrictionRuleGroups): void
    {
        $this->cartRuleRepository->setProductRestrictions(new CartRuleId((int) $cartRule->id), $restrictionRuleGroups);

        $cartRule->product_restriction = !empty($restrictionRuleGroups);
        $this->cartRuleRepository->partialUpdate($cartRule, ['product_restriction']);
    }
}
