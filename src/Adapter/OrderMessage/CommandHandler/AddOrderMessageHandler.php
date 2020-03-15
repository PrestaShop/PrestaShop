<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\OrderMessage\CommandHandler;

use OrderMessage;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\AddOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\CommandHandler\AddOrderMessageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageException;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;
use PrestaShopException;

/**
 * Handles adding new order message using legacy object model
 *
 * @internal
 */
final class AddOrderMessageHandler implements AddOrderMessageHandlerInterface
{
    /**
     * @param AddOrderMessageCommand $command
     *
     * @return OrderMessageId
     */
    public function handle(AddOrderMessageCommand $command): OrderMessageId
    {
        $orderMessage = new OrderMessage();

        $orderMessage->name = $command->getLocalizedName();
        $orderMessage->message = $command->getLocalizedMessage();

        try {
            $orderMessage->validateFields();
            $orderMessage->validateFieldsLang();
        } catch (PrestaShopException $e) {
            throw new OrderMessageException('Order message contains invalid fields', 0, $e);
        }

        try {
            if (false === $orderMessage->add()) {
                throw new OrderMessageException('Failed to add order message');
            }
        } catch (PrestaShopException $e) {
            throw new OrderMessageException('Failed to add order message', 0, $e);
        }

        return new OrderMessageId((int) $orderMessage->id);
    }
}
