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

use PrestaShop\PrestaShop\Adapter\CartRule\Repository\CartRuleRepository;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\RestrictCartRulesCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\CommandHandler\RestrictCartRulesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use CartRule;

class RestrictCartRulesHandler implements RestrictCartRulesHandlerInterface
{
    /**
     * @var CartRuleRepository
     */
    private $cartRuleRepository;

    public function __construct(
        CartRuleRepository $cartRuleRepository
    ) {
        $this->cartRuleRepository = $cartRuleRepository;
    }

    public function handle(RestrictCartRulesCommand $command): void
    {
        $cartRuleId = $command->getCartRuleId();
        $cartRule = $this->cartRuleRepository->get($cartRuleId);
        $this->cartRuleRepository->assertAllCartRulesExists($command->getRestrictedCartRuleIds());
        $this->cartRuleRepository->restrictCartRules($cartRuleId, $command->getRestrictedCartRuleIds());
        $hasRestrictions = !empty($command->getRestrictedCartRuleIds());

        $this->updateRestrictionProperty($cartRule, $hasRestrictions);

        // update cart_rule_restriction property for all the cart rules that have been affected
        foreach ($this->cartRuleRepository->getRestrictedCartRuleIds($cartRuleId) as $restrictedCartRuleId) {
            $affectedCartRule = $this->cartRuleRepository->get(new CartRuleId($restrictedCartRuleId));
            $this->updateRestrictionProperty($affectedCartRule, $hasRestrictions);
        }
    }

    private function updateRestrictionProperty(CartRule $cartRule, bool $hasRestrictions): void
    {
        $cartRule->cart_rule_restriction = $hasRestrictions;
        $this->cartRuleRepository->partialUpdate(
            $cartRule,
            ['cart_rule_restriction']
        );
    }
}
