<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

/**
 * Class GetSqlRequestExecutionResultQuery returns the result of executing an SqlRequest query.
 */
class GetSqlRequestExecutionResult
{
    /**
     * @var SqlRequestId
     */
    private $requestSqlId;

    /**
     * @param int $requestSqlId
     *
     * @throws SqlRequestException
     */
    public function __construct($requestSqlId)
    {
        $this->requestSqlId = new SqlRequestId($requestSqlId);
    }

    /**
     * @return SqlRequestId
     */
    public function getSqlRequestId()
    {
        return $this->requestSqlId;
    }
}
