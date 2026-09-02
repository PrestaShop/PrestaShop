<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeForEditing;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EditableEmployee;

/**
 * Interface for service that gets employee data for editing
 */
interface GetEmployeeForEditingHandlerInterface
{
    /**
     * @param GetEmployeeForEditing $query
     *
     * @return EditableEmployee
     */
    public function handle(GetEmployeeForEditing $query);
}
