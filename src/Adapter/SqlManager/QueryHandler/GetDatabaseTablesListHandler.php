<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\SqlManager\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTablesList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTablesList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler\GetDatabaseTablesListHandlerInterface;
use RequestSql;

/**
 * Class GetDatabaseTablesListHandler.
 *
 * @internal
 */
#[AsQueryHandler]
final class GetDatabaseTablesListHandler implements GetDatabaseTablesListHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetDatabaseTablesList $query)
    {
        $dbTables = (new RequestSql())->getTables();

        return new DatabaseTablesList($dbTables);
    }
}
