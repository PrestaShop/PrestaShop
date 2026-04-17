<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderState\Query\GetOrderStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderState\QueryResult\EditableOrderState;

/**
 * Interface for service that gets order state data for editing
 */
interface GetOrderStateForEditingHandlerInterface
{
    /**
     * @return EditableOrderState
     */
    public function handle(GetOrderStateForEditing $query);
}
