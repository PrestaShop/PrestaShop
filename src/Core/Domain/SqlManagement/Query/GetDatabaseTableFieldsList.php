<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlManagementConstraintException;

/**
 * Class GetAttributesForDatabaseTableQuery gets list of attributes for given database table name.
 */
class GetDatabaseTableFieldsList
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @param string $tableName
     *
     * @throws SqlManagementConstraintException
     */
    public function __construct($tableName)
    {
        $this->setTableName($tableName);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     *
     * @return self
     *
     * @throws SqlManagementConstraintException
     */
    public function setTableName($tableName)
    {
        if (!is_string($tableName) || empty($tableName)) {
            throw new SqlManagementConstraintException(sprintf('Invalid database table name %s supplied', var_export($tableName, true)), SqlManagementConstraintException::INVALID_DATABASE_TABLE_NAME);
        }

        $this->tableName = $tableName;

        return $this;
    }
}
