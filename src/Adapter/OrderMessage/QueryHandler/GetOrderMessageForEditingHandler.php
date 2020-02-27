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

namespace PrestaShop\PrestaShop\Adapter\OrderMessage\QueryHandler;

use PrestaShop\PrestaShop\Adapter\OrderMessage\AbstractOrderMessageHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Query\GetOrderMessageForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\QueryHandler\GetOrderMessageForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\QueryResult\EditableOrderMessage;

/**
 * Get order message for editing using object model
 *
 * @internal
 */
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
