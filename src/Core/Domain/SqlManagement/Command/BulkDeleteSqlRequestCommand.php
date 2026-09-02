<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

/**
 * Class BulkDeleteSqlRequestCommand deletes provided SqlRequests.
 */
class BulkDeleteSqlRequestCommand
{
    /**
     * @var SqlRequestId[]
     */
    private $sqlRequestIds = [];

    /**
     * @param int[] $sqlRequestIds
     *
     * @throws SqlRequestException
     */
    public function __construct(array $sqlRequestIds)
    {
        $this->setSqlRequestIds($sqlRequestIds);
    }

    /**
     * @return SqlRequestId[]
     */
    public function getSqlRequestIds()
    {
        return $this->sqlRequestIds;
    }

    /**
     * @param array $sqlRequestIds
     *
     * @return self
     *
     * @throws SqlRequestException
     */
    private function setSqlRequestIds(array $sqlRequestIds)
    {
        if (empty($sqlRequestIds)) {
            throw new SqlRequestConstraintException('Missing SqlRequest data for bulk deleting', SqlRequestConstraintException::MISSING_BULK_DATA);
        }

        foreach ($sqlRequestIds as $sqlRequestId) {
            $this->sqlRequestIds[] = new SqlRequestId($sqlRequestId);
        }

        return $this;
    }
}
