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
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\EditOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\CommandHandler\EditOrderMessageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageException;
use PrestaShopException;

/**
 * Handles editing order message using legacy object model
 *
 * @internal
 */
final class EditOrderMessageHandler extends AbstractOrderMessageHandler implements EditOrderMessageHandlerInterface
{
    /**
     * @param EditOrderMessageCommand $command
     */
    public function handle(EditOrderMessageCommand $command): void
    {
        $orderMessage = $this->getOrderMessage($command->getOrderMessageId());

        if (null !== $command->getLocalizedName()) {
            $orderMessage->name = $command->getLocalizedName();
        }

        if (null !== $command->getLocalizedMessage()) {
            $orderMessage->message = $command->getLocalizedMessage();
        }

        try {
            $orderMessage->validateFields();
            $orderMessage->validateFieldsLang();
        } catch (PrestaShopException $e) {
            throw new OrderMessageException('Order message contains invalid fields', 0, $e);
        }

        try {
            if (false === $orderMessage->update()) {
                throw new OrderMessageException(sprintf('Failed to update order message with id "%s"', $command->getOrderMessageId()->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new OrderMessageException(sprintf('Failed to update order message with id "%s"', $command->getOrderMessageId()->getValue()), 0, $e);
        }
    }
}
