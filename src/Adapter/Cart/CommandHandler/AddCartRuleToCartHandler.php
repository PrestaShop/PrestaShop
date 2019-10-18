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
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
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
            $this->throwExceptionByErrorMessage($errorMessage);
        }

        if (!$cart->addCartRule($cartRule->id)) {
            throw new CartException('Failed to add cart rule to cart.');
        }
    }

    private function throwExceptionByErrorMessage(string $errorMessage)
    {
        $exceptionMap = [
            $this->translator->trans('This voucher is disabled', [], 'Shop.Notifications.Error') => CartRuleConstraintException::DISABLED,
            $this->translator->trans('This voucher has already been used', [], 'Shop.Notifications.Error') => CartRuleConstraintException::NO_QUANTITY,
            $this->translator->trans('This voucher is not valid yet', [], 'Shop.Notifications.Error') => CartRuleConstraintException::NOT_VALID_YET,
            $this->translator->trans('This voucher has expired', [], 'Shop.Notifications.Error') => CartRuleConstraintException::EXPIRED,
            $this->translator->trans(
                'You cannot use this voucher anymore (usage limit reached)',
                [],
                'Shop.Notifications.Error'
            ) => CartRuleConstraintException::USAGE_LIMIT_REACHED,
            $this->translator->trans('You cannot use this voucher', array(), 'Shop.Notifications.Error') => CartRuleConstraintException::NOT_ALLOWED,
            $this->translator->trans(
                'You must choose a delivery address before applying this voucher to your order',
                [],
                'Shop.Notifications.Error'
            ) => CartRuleConstraintException::UNAVAILABLE_FOR_DELIVERY_ADDRESS,
            $this->translator->trans(
                'You cannot use this voucher in your country of delivery',
                [],
                'Shop.Notifications.Error'
            ) => CartRuleConstraintException::UNAVAILABLE_FOR_COUNTRY,
            $this->translator->trans(
                'You must choose a carrier before applying this voucher to your order',
                [],
                'Shop.Notifications.Error'
            ) => CartRuleConstraintException::UNAVAILABLE_FOR_CARRIER,
            $this->translator->trans(
                'You cannot use this voucher with this carrier',
                [],
                'Shop.Notifications.Error'
            ) => CartRuleConstraintException::UNAVAILABLE_FOR_CARRIER,
            $this->translator->trans(
                'You cannot use this voucher on products on sale',
                [],
                'Shop.Notifications.Error'
            ) => CartRuleConstraintException::UNAVAILABLE_FOR_SALE_PRODUCTS,
            $this->translator->trans(
                'You have not reached the minimum amount required to use this voucher',
                [],
                'Shop.Notifications.Error'
            ) => CartRuleConstraintException::REQUIRES_AMOUNT,
            $this->translator->trans(
                'This voucher is already in your cart',
                [],
                'Shop.Notifications.Error'
            ) => CartRuleConstraintException::ALREADY_IN_CART,
            //@todo: this wont't work until trans param is known.. Find another solution.
            $this->translator->trans(
                'This voucher is not combinable with an other voucher already in your cart: %s',
                [],
                'Shop.Notifications.Error'
            ) => CartRuleConstraintException::CANNOT_BE_COMBINED,
        ];

        if (isset($exceptionMap[$errorMessage])) {
            throw new CartRuleConstraintException($errorMessage, $exceptionMap[$errorMessage]);
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

        if (true === $isValid) {
            return null;
        }

        // if its not valid, then this var contains translated error message
        return $isValid;
    }
}
