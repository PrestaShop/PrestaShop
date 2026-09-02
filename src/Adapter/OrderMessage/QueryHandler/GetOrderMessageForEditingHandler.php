<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
