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

namespace PrestaShop\PrestaShop\Adapter\OrderMessage\QueryHandler;

use PrestaShop\PrestaShop\Adapter\OrderMessage\AbstractOrderMessageHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Query\GetOrderMessageForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\QueryHandler\GetOrderMessageForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\QueryResult\EditableOrderMessage;

/**
 * Get order message for editing using object model
 *
 * @internal
 */
#[AsQueryHandler]
final class GetOrderMessageForEditingHandler extends AbstractOrderMessageHandler implements GetOrderMessageForEditingHandlerInterface
{
    /**
     * @param GetOrderMessageForEditing $query
     *
     * @return EditableOrderMessage
     */
    public function handle(GetOrderMessageForEditing $query): EditableOrderMessage
    {
        $orderMessage = $this->getOrderMessage($query->getOrderMessageId());

        return new EditableOrderMessage(
            $query->getOrderMessageId(),
            $orderMessage->name,
            $orderMessage->message
        );
    }
}
