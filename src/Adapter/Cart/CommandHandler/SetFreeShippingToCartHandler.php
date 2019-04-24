<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\SetFreeShippingToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\SetFreeShippingToCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @internal
 */
final class SetFreeShippingToCartHandler implements SetFreeShippingToCartHandlerInterface
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
    public function handle(SetFreeShippingToCartCommand $command)
    {
        $cart = $this->getCartObject($command->getCartId());

        $backOfficeOrderCode = sprintf('%s%s', CartRule::BO_ORDER_CODE_PREFIX, $cart->id);

        $cartRule = $this->getCartRuleForBackOfficeFreeShipping($backOfficeOrderCode);

        if (false === $cartRule) {
            $cartRule = new CartRule();
            $cartRule->code = $backOfficeOrderCode;
            $cartRule->name = [
                $this->configuration->get('PS_LANG_DEFAULT') => $this->translator->trans(
                    'Free Shipping',
                    [],
                    'Admin.Orderscustomers.Feature'
                ),
            ];
            $cartRule->id_customer = (int) $cart->id_customer;
            $cartRule->free_shipping = true;
            $cartRule->quantity = 1;
            $cartRule->quantity_per_user = 1;
            $cartRule->minimum_amount_currency = (int) $cart->id_currency;
            $cartRule->reduction_currency = (int) $cart->id_currency;
            $cartRule->date_from = date('Y-m-d H:i:s');
            $cartRule->date_to = date('Y-m-d H:i:s', time() + 24 * 36000);
            $cartRule->active = 1;
            $cartRule->add();
        }

        $cart->removeCartRule((int) $cartRule->id);

        if ($command->allowFreeShipping()) {
            $cart->addCartRule((int) $cartRule->id);
        }
    }

    /**
     * @param CartId $cartId
     *
     * @return Cart
     *
     * @throws CurrencyNotFoundException
     */
    private function getCartObject(CartId $cartId)
    {
        $cart = new Cart($cartId->getValue());

        if ($cartId->getValue() !== $cart->id) {
            throw new CurrencyNotFoundException(
                sprintf('Currency with id "%s" was not found', $cartId->getValue())
            );
        }

        return $cart;
    }

    /**
     * @param string $code
     *
     * @return false|CartRule
     */
    private function getCartRuleForBackOfficeFreeShipping($code)
    {
        $cartRuleId = CartRule::getIdByCode($code);

        if (!$cartRuleId) {
            return false;
        }

        return new CartRule((int) $cartRuleId);
    }
}
