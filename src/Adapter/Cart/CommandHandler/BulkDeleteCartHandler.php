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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\BulkDeleteCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\BulkDeleteCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\BulkDeleteCartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShopException;

/**
 * Deletes cart in bulk action using legacy object model
 */
final class BulkDeleteCartHandler extends AbstractCartHandler implements BulkDeleteCartHandlerInterface
{
    /**
     * @inerhitDoc
     *
     * @throws BulkDeleteCartException
     */
    public function handle(BulkDeleteCartCommand $command): void
    {
        $errors = [];
        foreach ($command->getCartIds() as $cartId) {
            try {
                $cart = $this->getCart($cartId);

                if ($cart->orderExists() || !$cart->delete()) {
                    $errors[] = $cartId->getValue();
                }
            } catch (CartException|PrestaShopException $e) {
                $errors[] = $cartId->getValue();
            }
        }

        if (!empty($errors)) {
            throw new BulkDeleteCartException($errors, 'Failed to delete all of selected cart');
        }
    }
}
