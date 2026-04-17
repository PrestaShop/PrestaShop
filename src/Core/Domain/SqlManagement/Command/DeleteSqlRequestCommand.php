<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

/**
 * Class DeleteSqlRequestCommand command delete SqlRequest by given id.
 */
class DeleteSqlRequestCommand
{
    /**
     * @var SqlRequestId
     */
    private $sqlRequestId;

    /**
     * @param SqlRequestId $sqlRequestId
     */
    public function __construct(SqlRequestId $sqlRequestId)
    {
        $this->setSqlRequestId($sqlRequestId);
    }

    /**
     * @return SqlRequestId
     */
    public function getSqlRequestId()
    {
        return $this->sqlRequestId;
    }

    /**
     * @param SqlRequestId $sqlRequestId
     *
     * @return self
     */
    private function setSqlRequestId($sqlRequestId)
    {
        $this->sqlRequestId = $sqlRequestId;

        return $this;
    }
}
