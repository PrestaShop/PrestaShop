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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Cart;
use Currency;
use Customer;
use Language;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DuplicateOrderCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\DuplicateOrderCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateOrderCartException;

/**
 * @internal
 */
final class DuplicateOrderCartHandler implements DuplicateOrderCartHandlerInterface
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
    public function handle(DuplicateOrderCartCommand $command)
    {
        // IMPORTANT: context customer must be set in order to correctly fill the address
        $cart = Cart::getCartByOrderId($command->getOrderId()->getValue());
        $this->contextStateManager
            ->setCart($cart)
            ->setCustomer(new Customer($cart->id_customer))
            ->setCurrency(new Currency($cart->id_currency))
            ->setLanguage(new Language($cart->id_lang))
        ;
        $result = $cart->duplicate();

        if (false === $result || !isset($result['cart'])) {
            $this->contextStateManager->restoreContext();
            throw new DuplicateOrderCartException(sprintf('Cannot duplicate cart from order "%s"', $command->getOrderId()->getValue()));
        }

        $this->contextStateManager->restoreContext();

        return new CartId((int) $result['cart']->id);
    }
}
