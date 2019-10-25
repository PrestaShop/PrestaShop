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

use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\RemoveProductFromCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\RemoveProductFromCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use Product;

/**
 * Handles removing product from context cart.
 *
 * @internal
 */
final class RemoveProductFromCartHandler extends AbstractCartHandler implements RemoveProductFromCartHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(RemoveProductFromCartCommand $command)
    {
        $cart = $this->getCart($command->getCartId());

        $removed = $cart->deleteProduct(
            $command->getProductId()->getValue(),
            $command->getCombinationId() ?: 0,
            $command->getCustomizationId() ?: 0
        );

        if (!$removed) {
            throw new CartException(
                sprintf('Failed to remove product with id "%d" from cart', $command->getProductId()->getValue())
            );
        }
    }
}
