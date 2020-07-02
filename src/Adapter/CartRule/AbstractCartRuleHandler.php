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

namespace PrestaShop\PrestaShop\Adapter\CartRule;

use CartRule;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use PrestaShopException;

/**
 * Provides reusable methods for CartRule handlers
 */
abstract class AbstractCartRuleHandler
{
    /**
     * Gets legacy CartRule
     *
     * @param CartRuleId $cartRuleId
     *
     * @return CartRule
     *
     * @throws CartRuleException
     * @throws CartRuleNotFoundException
     */
    protected function getCartRule(CartRuleId $cartRuleId): CartRule
    {
        try {
            $cartRule = new CartRule($cartRuleId->getValue());
        } catch (PrestaShopException $e) {
            throw new CartRuleException('Failed to create new CartRule object', 0, $e);
        }

        if ($cartRule->id !== $cartRuleId->getValue()) {
            throw new CartRuleNotFoundException(sprintf('CartRule with id "%s" was not found.', $cartRuleId->getValue()));
        }

        return $cartRule;
    }

    /**
     * Deletes legacy CartRule
     *
     * @param CartRule $cartRule
     *
     * @return bool
     *
     * @throws CartRuleException
     */
    protected function deleteCartRule(CartRule $cartRule)
    {
        try {
            return $cartRule->delete();
        } catch (PrestaShopException $e) {
            throw new CartRuleException(sprintf('An error occurred when deleting CartRule object with id "%s".', $cartRule->id));
        }
    }

    /**
     * Toggles legacy cart rule status
     *
     * @param CartRule $cartRule
     * @param bool $newStatus
     *
     * @return bool
     *
     * @throws CartRuleException
     */
    protected function toggleCartRuleStatus(CartRule $cartRule, bool $newStatus): ?bool
    {
        $cartRule->active = $newStatus;

        try {
            return $cartRule->save();
        } catch (PrestaShopException $e) {
            throw new CartRuleException(sprintf('An error occurred when updating cart rule status with id "%s"', $cartRule->id));
        }
    }
}
