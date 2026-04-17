<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTableFields;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTableFieldsList;

/**
 * Interface GetAttributesForDatabaseTableHandlerInterface.
 */
interface GetDatabaseTableFieldsListHandlerInterface
{
    /**
     * @param GetDatabaseTableFieldsList $query
     *
     * @return DatabaseTableFields
     */
    public function handle(GetDatabaseTableFieldsList $query);
}
