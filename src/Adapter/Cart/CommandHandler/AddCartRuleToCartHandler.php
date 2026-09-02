<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Cache;
use Cart;
use CartRule;
use Context;
use Currency;
use Customer;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCartRuleToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\AddCartRuleToCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleValidityException;
use Shop;

/**
 * @internal
 */
#[AsCommandHandler]
final class AddCartRuleToCartHandler extends AbstractCartHandler implements AddCartRuleToCartHandlerInterface
{
    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @param ContextStateManager $contextStateManager
     */
    public function __construct(ContextStateManager $contextStateManager)
    {
        $this->contextStateManager = $contextStateManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddCartRuleToCartCommand $command)
    {
        $cart = $this->getCart($command->getCartId());
        $cartRule = new CartRule($command->getCartRuleId()->getValue());

        $this->contextStateManager
            ->setCart($cart)
            ->setCurrency(new Currency($cart->id_currency))
            ->setLanguage($cart->getAssociatedLanguage())
            ->setCustomer(new Customer($cart->id_customer))
            ->setShop(new Shop($cart->id_shop))
        ;

        $errorMessage = $this->validateCartRule($cartRule, $cart);

        if ($errorMessage) {
            $this->contextStateManager->restorePreviousContext();

            throw new CartRuleValidityException($errorMessage);
        }

        $addResult = $cart->addCartRule($cartRule->id);

        // addCartRule() can return: true (success), false (failure), or string (error message)
        if (is_string($addResult)) {
            $this->contextStateManager->restorePreviousContext();

            throw new CartRuleValidityException($addResult);
        }

        if (!$addResult) {
            $this->contextStateManager->restorePreviousContext();

            throw new CartException('Failed to add cart rule to cart.');
        }

        $this->contextStateManager->restorePreviousContext();
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
        $previousCartRules = $cart->getCartRules();
        $isValid = $cartRule->checkValidity(Context::getContext(), false, true);

        foreach ($previousCartRules as $previousCartRule) {
            Cache::clean('getContextualValue_' . $previousCartRule['id_discount'] . '_*');
        }

        // if its valid, don't return any error message
        if (true === $isValid) {
            return null;
        }

        // if its not valid, then this var contains translated error message
        return $isValid;
    }
}
