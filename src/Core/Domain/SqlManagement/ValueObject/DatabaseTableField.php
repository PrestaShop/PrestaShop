<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlManagementConstraintException;

/**
 * Class DatabaseTableField stores information about single database table field.
 */
class DatabaseTableField
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $name
     * @param string $type
     *
     * @throws SqlManagementConstraintException
     */
    public function __construct($name, $type)
    {
        $this
            ->setName($name)
            ->setType($type);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     *
     * @throws SqlManagementConstraintException
     */
    private function setName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new SqlManagementConstraintException(sprintf('Invalid database table field name %s supplied', var_export($name, true)), SqlManagementConstraintException::INVALID_DATABASE_TABLE_FIELD_NAME);
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     *
     * @throws SqlManagementConstraintException
     */
    private function setType($type)
    {
        if (!is_string($type) || empty($type)) {
            throw new SqlManagementConstraintException(sprintf('Invalid database table field type %s supplied', var_export($type, true)), SqlManagementConstraintException::INVALID_DATABASE_TABLE_FIELD_TYPE);
        }

        $this->type = $type;

        return $this;
    }
}
