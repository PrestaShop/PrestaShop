<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Query\GetOrderMessageForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\QueryResult\EditableOrderMessage;

/**
 * Interface for service that handles retrieving order message data
 */
interface GetOrderMessageForEditingHandlerInterface
{
    /**
     * @param GetOrderMessageForEditing $query
     *
     * @return EditableOrderMessage
     */
    public function handle(GetOrderMessageForEditing $query): EditableOrderMessage;
}
