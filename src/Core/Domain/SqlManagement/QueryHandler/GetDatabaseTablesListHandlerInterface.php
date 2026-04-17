<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTablesList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTablesList;

/**
 * Interface GetDatabaseTablesListHandlerInterface.
 */
interface GetDatabaseTablesListHandlerInterface
{
    /**
     * @param GetDatabaseTablesList $query
     *
     * @return DatabaseTablesList
     */
    public function handle(GetDatabaseTablesList $query);
}
