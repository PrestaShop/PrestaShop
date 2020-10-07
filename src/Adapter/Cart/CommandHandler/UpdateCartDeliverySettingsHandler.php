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

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Cart;
use CartRule;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartDeliverySettingsCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartDeliverySettingsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\InvalidGiftMessageException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CannotDeleteCartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleException;
use PrestaShopException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @internal
 */
final class UpdateCartDeliverySettingsHandler extends AbstractCartHandler implements UpdateCartDeliverySettingsHandlerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $configuration
     */
    public function __construct(TranslatorInterface $translator, ConfigurationInterface $configuration)
    {
        $this->translator = $translator;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCartDeliverySettingsCommand $command): void
    {
        $cart = $this->getCart($command->getCartId());

        if (!\Validate::isMessage($command->getGiftMessage())) {
            throw new InvalidGiftMessageException();
        }

        $this->handleFreeShippingOption($cart, $command);
        $this->handleGiftOption($cart, $command);
        $this->handleRecycledWrappingOption($cart, $command);
        $this->handleGiftMessageOption($cart, $command);
    }

    /**
     * @param string $code
     *
     * @return CartRule|null
     *
     * @throws PrestaShopException
     */
    private function getCartRuleForBackOfficeFreeShipping($code): ?CartRule
    {
        $cartRuleId = CartRule::getIdByCode($code);

        if (!$cartRuleId) {
            return null;
        }

        return new CartRule((int) $cartRuleId);
    }

    private function createCartRule(Cart $cart, string $backOfficeOrderCode): CartRule
    {
        $freeShippingCartRule = new CartRule();
        $freeShippingCartRule->code = $backOfficeOrderCode;
        $freeShippingCartRule->name = [
            $this->configuration->get('PS_LANG_DEFAULT') => $this->translator->trans(
                'Free Shipping',
                [],
                'Admin.Orderscustomers.Feature'
            ),
        ];
        $freeShippingCartRule->id_customer = (int) $cart->id_customer;
        $freeShippingCartRule->free_shipping = true;
        $freeShippingCartRule->quantity = 1;
        $freeShippingCartRule->quantity_per_user = 1;
        $freeShippingCartRule->minimum_amount_currency = (int) $cart->id_currency;
        $freeShippingCartRule->reduction_currency = (int) $cart->id_currency;
        $freeShippingCartRule->date_from = date('Y-m-d H:i:s');
        $freeShippingCartRule->date_to = date('Y-m-d H:i:s', time() + 24 * 36000);
        $freeShippingCartRule->active = 1;
        $freeShippingCartRule->add();

        return $freeShippingCartRule;
    }

    /**
     * @param Cart $cart
     * @param UpdateCartDeliverySettingsCommand $command
     *
     * @throws CannotDeleteCartRuleException
     * @throws CartRuleException
     * @throws PrestaShopException
     */
    protected function handleFreeShippingOption(Cart $cart, UpdateCartDeliverySettingsCommand $command): void
    {
        $backOfficeOrderCode = sprintf('%s%s', CartRule::BO_ORDER_CODE_PREFIX, $cart->id);

        $freeShippingCartRule = $this->getCartRuleForBackOfficeFreeShipping($backOfficeOrderCode);

        if ($command->allowFreeShipping()) {
            if (null === $freeShippingCartRule) {
                $freeShippingCartRule = $this->createCartRule($cart, $backOfficeOrderCode);
            }
            $cart->addCartRule((int) $freeShippingCartRule->id);

            return;
        }

        if (null === $freeShippingCartRule) {
            return;
        }

        $cart->removeCartRule((int) $freeShippingCartRule->id);

        try {
            if (false === $freeShippingCartRule->delete()) {
                throw new CannotDeleteCartRuleException(sprintf('Failed deleting cart rule #%d', $freeShippingCartRule->id));
            }
        } catch (PrestaShopException $e) {
            throw new CartRuleException(sprintf('An error occurred when trying to delete cart rule #%d', $freeShippingCartRule->id));
        }
    }

    /**
     * @param Cart $cart
     * @param UpdateCartDeliverySettingsCommand $command
     *
     * @throws CartException
     * @throws PrestaShopException
     */
    private function handleGiftOption(Cart $cart, UpdateCartDeliverySettingsCommand $command): void
    {
        if ($command->isAGift() === null) {
            return;
        }

        $cart->gift = $command->isAGift();

        try {
            if (false === $cart->update()) {
                throw new CartException('Failed to update cart gift option');
            }
        } catch (PrestaShopException $e) {
            throw new CartException(sprintf('An error occurred while trying to update gift option for cart with id "%d"', $cart->id));
        }
    }

    /**
     * @param Cart $cart
     * @param UpdateCartDeliverySettingsCommand $command
     *
     * @throws CartException
     * @throws PrestaShopException
     */
    private function handleRecycledWrappingOption(Cart $cart, UpdateCartDeliverySettingsCommand $command): void
    {
        if ($command->useRecycledPackaging() === null) {
            return;
        }
        $cart->recyclable = $command->useRecycledPackaging();

        try {
            if (false === $cart->update()) {
                throw new CartException('Failed to update cart recycle wrapping option');
            }
        } catch (PrestaShopException $e) {
            throw new CartException(sprintf('An error occurred while trying to update recycle wrapping option for cart with id "%d"', $cart->id));
        }
    }

    /**
     * @param Cart $cart
     * @param UpdateCartDeliverySettingsCommand $command
     *
     * @throws CartException
     */
    private function handleGiftMessageOption(Cart $cart, UpdateCartDeliverySettingsCommand $command): void
    {
        if ($command->getGiftMessage() === null) {
            return;
        }
        $cart->gift_message = $command->getGiftMessage();
        $cart->save();

        try {
            if (false === $cart->update()) {
                throw new CartException('Failed to update cart gift message');
            }
        } catch (PrestaShopException $e) {
            throw new CartException(sprintf('An error occurred while trying to update gift message for cart with id "%d"', $cart->id));
        }
    }
}
