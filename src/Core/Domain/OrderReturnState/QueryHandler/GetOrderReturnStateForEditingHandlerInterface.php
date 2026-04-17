<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Query\GetOrderReturnStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\QueryResult\EditableOrderReturnState;

/**
 * Interface for service that gets order return state data for editing
 */
interface GetOrderReturnStateForEditingHandlerInterface
{
    /**
     * @return EditableOrderReturnState
     */
    public function handle(GetOrderReturnStateForEditing $query);
}
