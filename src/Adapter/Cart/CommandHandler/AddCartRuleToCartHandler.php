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

use Cart;
use CartRule;
use Context;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCartRuleToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\AddCartRuleToCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleValidityException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @internal
 */
final class AddCartRuleToCartHandler extends AbstractCartHandler implements AddCartRuleToCartHandlerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddCartRuleToCartCommand $command)
    {
        $cart = $this->getCart($command->getCartId());
        $cartRule = new CartRule($command->getCartRuleId()->getValue());

        $errorMessage = $this->validateCartRule($cartRule, $cart);

        if ($errorMessage) {
            throw new CartRuleValidityException($errorMessage);
        }

        if (!$cart->addCartRule($cartRule->id)) {
            throw new CartException('Failed to add cart rule to cart.');
        }
    }

    /**
     * Validates if the cart rule is applicable to cart
     *
     * Returns null if cart rule is valid.
     * Returns translated error message if cart rule is not valid.
     *
     * @param CartRule $cartRule
     *
     * @return string|null
     */
    private function validateCartRule(CartRule $cartRule, Cart $cart): ?string
    {
        Context::getContext()->cart = $cart;
        $isValid = $cartRule->checkValidity(Context::getContext(), false, true);

        // if its valid, don't return any error message
        if (true === $isValid) {
            return null;
        }

        // if its not valid, then this var contains translated error message
        return $isValid;
    }
}
