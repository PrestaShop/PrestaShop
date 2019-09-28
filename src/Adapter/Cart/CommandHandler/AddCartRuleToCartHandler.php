<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use CartRule;
use Context;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCartRuleToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\AddCartRuleToCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;

/**
 * @internal
 */
final class AddCartRuleToCartHandler extends AbstractCartHandler implements AddCartRuleToCartHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddCartRuleToCartCommand $command)
    {
        $cartRule = new CartRule($command->getCartRuleId()->getValue());

        if (false === $cartRule->checkValidity(Context::getContext(), false, false)) {
            throw new CartException('Invalid cart rule.');
        }

        $cart = $this->getContextCartObject($command->getCartId());

        if (!$cart->addCartRule($cartRule->id)) {
            throw new CartException('Failed to add cart rule to cart.');
        }
    }
}
