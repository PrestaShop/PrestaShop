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

namespace PrestaShop\PrestaShop\Adapter\OrderMessage\CommandHandler;

use PrestaShop\PrestaShop\Adapter\OrderMessage\AbstractOrderMessageHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\BulkDeleteOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\CommandHandler\BulkDeleteOrderMessageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageException;
use PrestaShopException;

/**
 * Deletes Order messages in bulk action using object model
 *
 * @internal
 */
final class BulkDeleteOrderMessageHandler extends AbstractOrderMessageHandler implements BulkDeleteOrderMessageHandlerInterface
{
    /**
     * @param BulkDeleteOrderMessageCommand $command
     */
    public function handle(BulkDeleteOrderMessageCommand $command): void
    {
        foreach ($command->getOrderMessageIds() as $orderMessageId) {
            $orderMessage = $this->getOrderMessage($orderMessageId);

            try {
                if (false === $orderMessage->delete()) {
                    throw new OrderMessageException(sprintf('Failed to delete Order message with id "%d" during bulk delete', $orderMessage->id), OrderMessageException::FAILED_BULK_DELETE);
                }
            } catch (PrestaShopException $e) {
                throw new OrderMessageException(sprintf('Failed to delete Order message with id "%s"', $orderMessage->id));
            }
        }
    }
}
