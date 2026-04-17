<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

/**
 * This command modifies an existing SqlRequest object, replacing its data by the provided one.
 */
class EditSqlRequestCommand
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
     * @param SqlRequestId $sqlRequestId
     */
    public function __construct(SqlRequestId $sqlRequestId)
    {
        $this->setSqlRequestId($sqlRequestId);
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
    private function setSqlRequestId(SqlRequestId $sqlRequestId)
    {
        $this->sqlRequestId = $sqlRequestId;

        return $this;
    }

    /**
     * Set Request SQL name.
     *
     * @param string $name
     *
     * @return self
     *
     * @throws SqlRequestConstraintException
     */
    public function setName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new SqlRequestConstraintException(sprintf('Invalid SqlRequest name "%s"', var_export($name, true)), SqlRequestConstraintException::INVALID_NAME);
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Set Request SQL query.
     *
     * @param string $sql
     *
     * @return self
     *
     * @throws SqlRequestConstraintException
     */
    public function setSql($sql)
    {
        if (!is_string($sql) || empty($sql)) {
            throw new SqlRequestConstraintException(sprintf('Invalid SqlRequest SQL query "%s"', var_export($sql, true)), SqlRequestConstraintException::INVALID_SQL_QUERY);
        }

        $this->sql = $sql;

        return $this;
    }
}
