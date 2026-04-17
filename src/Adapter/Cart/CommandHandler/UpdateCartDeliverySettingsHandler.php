<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Cart;
use CartRule;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartDeliverySettingsCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartDeliverySettingsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\InvalidGiftMessageException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CannotDeleteCartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleException;
use PrestaShopException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
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

        if (($command->getGiftMessage() !== null) && (!Validate::isMessage($command->getGiftMessage()))) {
            throw new InvalidGiftMessageException();
        }

        $this->handleFreeShippingOption($cart, $command);
        $shouldSaveCartAfterGiftOption = $this->handleGiftOption($cart, $command);
        $shouldSaveCartAfterWrappingOption = $this->handleRecycledWrappingOption($cart, $command);
        $shouldSaveCartAfterGiftMessageOption = $this->handleGiftMessageOption($cart, $command);

        $shouldSaveCart = ($shouldSaveCartAfterGiftOption
            || $shouldSaveCartAfterWrappingOption
            || $shouldSaveCartAfterGiftMessageOption);

        if ($shouldSaveCart) {
            try {
                if (false === $cart->update()) {
                    throw new CartException('Failed to update cart delivery settings');
                }
            } catch (PrestaShopException) {
                throw new CartException(sprintf('An error occurred while trying to update delivery settings for cart with id "%d"', $cart->id));
            }
        }
    }

    /**
     * Sometimes, the cart rule to enable 'free shipping' exists
     * but is not linked to the cart. We look for this cart rule
     * to avoid creating duplicates.
     *
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

    /**
     * @param Cart $cart
     * @param string $backOfficeOrderCode
     *
     * @return CartRule
     */
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
        $freeShippingCartRule->active = true;
        $freeShippingCartRule->add();

        return $freeShippingCartRule;
    }

    /**
     * This method works as follows:
     * 1. if free shipping should be enabled, enable it
     * 2. if free shipping should not be enabled and cart already does not have free shipping, do nothing
     * 3.if free shipping should not be enabled and cart has free shipping, disable it
     *
     * @param Cart $cart
     * @param UpdateCartDeliverySettingsCommand $command
     *
     * @throws CannotDeleteCartRuleException
     */
    protected function handleFreeShippingOption(Cart $cart, UpdateCartDeliverySettingsCommand $command): void
    {
        $backOfficeOrderCode = sprintf('%s%s', CartRule::BO_ORDER_CODE_PREFIX, $cart->id);

        $freeShippingCartRule = $this->getCartRuleForBackOfficeFreeShipping($backOfficeOrderCode);

        $freeShippingShouldBeEnabled = $command->allowFreeShipping();

        // Step 1
        if ($freeShippingShouldBeEnabled) {
            if (null === $freeShippingCartRule) {
                // there is not yet a 'free shipping' cart rule available in the system so we create it
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
                throw new CannotDeleteCartRuleException(sprintf('Failed deleting cart rule #%s', $freeShippingCartRule->id));
            }
        } catch (PrestaShopException) {
            throw new CartRuleException(sprintf('An error occurred when trying to delete cart rule #%s', $freeShippingCartRule->id));
        }
    }

    /**
     * @param Cart $cart
     * @param UpdateCartDeliverySettingsCommand $command
     *
     * @return bool should save the cart or not
     *
     * @throws CartException
     * @throws PrestaShopException
     */
    private function handleGiftOption(Cart $cart, UpdateCartDeliverySettingsCommand $command): bool
    {
        if ($command->isAGift() === null) {
            return false;
        }

        $cart->gift = $command->isAGift();

        return true;
    }

    /**
     * @param Cart $cart
     * @param UpdateCartDeliverySettingsCommand $command
     *
     * @return bool should save the cart or not
     */
    private function handleRecycledWrappingOption(Cart $cart, UpdateCartDeliverySettingsCommand $command): bool
    {
        if ($command->useRecycledPackaging() === null) {
            return false;
        }

        $cart->recyclable = $command->useRecycledPackaging();

        return true;
    }

    /**
     * @param Cart $cart
     * @param UpdateCartDeliverySettingsCommand $command
     *
     * @return bool should save the cart or not
     */
    private function handleGiftMessageOption(Cart $cart, UpdateCartDeliverySettingsCommand $command): bool
    {
        if ($command->getGiftMessage() === null) {
            return false;
        }

        $cart->gift_message = $command->getGiftMessage();

        return true;
    }
}
