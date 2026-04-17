<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\State\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\State\Query\GetStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\State\QueryResult\EditableState;

/**
 * Defines contract for GetStateForEditingHandler
 */
interface GetStateForEditingHandlerInterface
{
    /**
     * @param GetStateForEditing $query
     *
     * @return EditableState
     */
    public function handle(GetStateForEditing $query): EditableState;
}
