<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;

/**
 * This command creates new SqlRequest entity with provided data.
 */
class AddSqlRequestCommand
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $sql;

    /**
     * @param string $name
     * @param string $sql
     *
     * @throws SqlRequestConstraintException
     */
    public function __construct($name, $sql)
    {
        $this
            ->setName($name)
            ->setSql($sql);
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
     * Set Request SQL name.
     *
     * @param string $name
     *
     * @return self
     *
     * @throws SqlRequestConstraintException
     */
    private function setName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new SqlRequestConstraintException(sprintf('Invalid SqlRequest name %s', var_export($name, true)), SqlRequestConstraintException::INVALID_NAME);
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Set Request SQL query.
     *
     * @param string $sql
     *
     * @return $this
     *
     * @throws SqlRequestConstraintException
     */
    private function setSql($sql)
    {
        if (!is_string($sql) || empty($sql)) {
            throw new SqlRequestConstraintException(sprintf('Invalid SqlRequest SQL query %s', var_export($sql, true)), SqlRequestConstraintException::INVALID_SQL_QUERY);
        }

        $this->sql = $sql;

        return $this;
    }
}
