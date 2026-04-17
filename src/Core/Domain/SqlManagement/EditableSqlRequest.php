<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

/**
 * Class EditableSqlRequest stores information about SqlRequest that can be edited.
 */
class EditableSqlRequest
{
    /**
     * @var SqlRequestId
     */
    private $sqlRequestId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $sql;

    /**
     * @param SqlRequestId $requestSqlId
     * @param string $name
     * @param string $sql
     *
     * @throws SqlRequestException
     */
    public function __construct(
        SqlRequestId $requestSqlId,
        $name,
        $sql
    ) {
        $this
            ->setSqlRequestId($requestSqlId)
            ->setName($name)
            ->setSql($sql);
    }

    /**
     * @return SqlRequestId
     */
    public function getSqlRequestId()
    {
        return $this->sqlRequestId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param SqlRequestId $sqlRequestId
     *
     * @return EditableSqlRequest
     */
    private function setSqlRequestId(SqlRequestId $sqlRequestId)
    {
        $this->sqlRequestId = $sqlRequestId;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return EditableSqlRequest
     *
     * @throws SqlRequestException
     */
    private function setName($name)
    {
        if (empty($name)) {
            throw new SqlRequestException('SqlRequest name cannot be empty');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @param string $sql
     *
     * @return EditableSqlRequest
     *
     * @throws SqlRequestException
     */
    private function setSql($sql)
    {
        if (empty($sql)) {
            throw new SqlRequestException('SqlRequest SQL cannot be empty');
        }

        $this->sql = $sql;

        return $this;
    }
}
