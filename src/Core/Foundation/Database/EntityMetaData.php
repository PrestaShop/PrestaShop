<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Foundation\Database;

class EntityMetaData
{
    /**
     * @var string|null
     */
    public $entityClassName;
    /**
     * @var array|null
     */
    private $primaryKeyFieldnames;
    /**
     * @var string|null
     */
    public $tableName;

    public function setTableName($name)
    {
        $this->tableName = $name;

        return $this;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param array $primaryKeyFieldnames
     *
     * @return self
     */
    public function setPrimaryKeyFieldNames(array $primaryKeyFieldnames)
    {
        $this->primaryKeyFieldnames = $primaryKeyFieldnames;

        return $this;
    }

    public function getPrimaryKeyFieldnames()
    {
        return $this->primaryKeyFieldnames;
    }

    public function setEntityClassName($entityClassName)
    {
        $this->entityClassName = $entityClassName;

        return $this;
    }

    public function getEntityClassName()
    {
        return $this->entityClassName;
    }
}
