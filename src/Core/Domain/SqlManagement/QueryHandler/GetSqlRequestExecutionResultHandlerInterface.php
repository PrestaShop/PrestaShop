<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestExecutionResult;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestExecutionResult;

/**
 * Interface GetSqlRequestResultForViewingHandlerInterface defines contract for getting SqlRequest SQL query result.
 */
interface GetSqlRequestExecutionResultHandlerInterface
{
    /**
     * @param GetSqlRequestExecutionResult $query
     *
     * @return SqlRequestExecutionResult
     */
    public function handle(GetSqlRequestExecutionResult $query);
}
