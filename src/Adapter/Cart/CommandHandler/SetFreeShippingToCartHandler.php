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
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\SetFreeShippingToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\SetFreeShippingToCartHandlerInterface;
use PrestaShopException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @internal
 */
final class SetFreeShippingToCartHandler extends AbstractCartHandler implements SetFreeShippingToCartHandlerInterface
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
    public function handle(SetFreeShippingToCartCommand $command): void
    {
        $cart = $this->getCart($command->getCartId());

        $backOfficeOrderCode = sprintf('%s%s', CartRule::BO_ORDER_CODE_PREFIX, $cart->id);

        $freeShippingCartRule = $this->getCartRuleForBackOfficeFreeShipping($backOfficeOrderCode);

        if (null === $freeShippingCartRule) {
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
        }

        $cart->removeCartRule((int) $freeShippingCartRule->id);

        if ($command->allowFreeShipping()) {
            $cart->addCartRule((int) $freeShippingCartRule->id);
        }
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
}
