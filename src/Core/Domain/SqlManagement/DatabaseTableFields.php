<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlManagementConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\DatabaseTableField;

/**
 * Class DatabaseTableFields stores fields of single database table.
 */
class DatabaseTableFields
{
    /**
     * @var DatabaseTableField[]
     */
    private $fields;

    /**
     * @param DatabaseTableField[] $fields
     *
     * @throws SqlManagementConstraintException
     */
    public function __construct(array $fields)
    {
        $this->setFields($fields);
    }

    /**
     * @return DatabaseTableField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param DatabaseTableField[] $fields
     *
     * @return self
     *
     * @throws SqlManagementConstraintException
     */
    private function setFields(array $fields)
    {
        foreach ($fields as $field) {
            if (!$field instanceof DatabaseTableField) {
                throw new SqlManagementConstraintException(sprintf('Invalid database field %s supplied. Expected instance of %s', var_export($field, true), DatabaseTableField::class), SqlManagementConstraintException::INVALID_DATABASE_TABLE_FIELD);
            }
        }

        $this->fields = $fields;

        return $this;
    }
}
