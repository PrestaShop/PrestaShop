<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Foundation\Database;

use PrestaShop\PrestaShop\Core\Foundation\Database\EntityManager\QueryBuilder;

class EntityRepository
{
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var DatabaseInterface
     */
    protected $db;
    /**
     * @var string
     */
    protected $tablesPrefix;
    /**
     * @var EntityMetaData
     */
    protected $entityMetaData;
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    public function __construct(
        EntityManager $entityManager,
        $tablesPrefix,
        EntityMetaData $entityMetaData
    ) {
        $this->entityManager = $entityManager;
        $this->db = $this->entityManager->getDatabase();
        $this->tablesPrefix = $tablesPrefix;
        $this->entityMetaData = $entityMetaData;
        $this->queryBuilder = new QueryBuilder($this->db);
    }

    public function __call($method, $arguments)
    {
        if (str_starts_with($method, 'findOneBy')) {
            $one = true;
            $by = substr($method, 9);
        } elseif (str_starts_with($method, 'findBy')) {
            $one = false;
            $by = substr($method, 6);
        } else {
            throw new Exception(sprintf('Undefind method %s.', $method));
        }

        if (count($arguments) !== 1) {
            throw new Exception(sprintf('Method %s takes exactly one argument.', $method));
        }

        if (!$by) {
            $where = $arguments[0];
        } else {
            $where = [];
            $by = $this->convertToDbFieldName($by);
            $where[$by] = $arguments[0];
        }

        return $this->doFind($one, $where);
    }

    /**
     * Convert a camelCase field name to a snakeCase one
     * e.g.: findAllByIdCMS => id_cms.
     *
     * @param string $camel_case_field_name
     *
     * @return string
     */
    private function convertToDbFieldName($camel_case_field_name)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $camel_case_field_name));
    }

    /**
     * Return ID field name.
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function getIdFieldName()
    {
        $primary = $this->entityMetaData->getPrimaryKeyFieldnames();

        if (count($primary) === 0) {
            throw new Exception(sprintf('No primary key defined in entity `%s`.', $this->entityMetaData->getEntityClassName()));
        } elseif (count($primary) > 1) {
            throw new Exception(sprintf('Entity `%s` has a composite primary key, which is not supported by entity repositories.', $this->entityMetaData->getEntityClassName()));
        }

        return $primary[0];
    }

    /**
     * Returns escaped+prefixed current table name.
     *
     * @return mixed
     */
    protected function getTableNameWithPrefix()
    {
        return $this->db->escape($this->tablesPrefix . $this->entityMetaData->getTableName());
    }

    /**
     * Returns escaped DB table prefix.
     *
     * @return mixed
     */
    protected function getPrefix()
    {
        return $this->db->escape($this->tablesPrefix);
    }

    /**
     * Return a new empty Entity depending on current Repository selected.
     *
     * @return mixed
     */
    public function getNewEntity()
    {
        $entityClassName = $this->entityMetaData->getEntityClassName();

        return new $entityClassName();
    }

    /**
     * This function takes an array of database rows as input
     * and returns an hydrated entity if there is one row only.
     *
     * Null is returned when there are no rows, and an exception is thrown
     * if there are too many rows.
     *
     * @param array $rows Database rows
     */
    protected function hydrateOne(array $rows)
    {
        if (count($rows) === 0) {
            return null;
        } elseif (count($rows) > 1) {
            throw new Exception('Too many rows returned.');
        } else {
            $data = $rows[0];
            $entity = $this->getNewEntity();
            $entity->hydrate($data);

            return $entity;
        }
    }

    protected function hydrateMany(array $rows)
    {
        $entities = [];
        foreach ($rows as $row) {
            $entity = $this->getNewEntity();
            $entity->hydrate($row);
            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     * Constructs and performs 'SELECT' in DB.
     *
     * @param bool $one
     * @param array $cumulativeConditions
     *
     * @return array|mixed|null
     *
     * @throws Exception
     */
    private function doFind($one, array $cumulativeConditions)
    {
        $whereClause = $this->queryBuilder->buildWhereConditions('AND', $cumulativeConditions);

        $sql = 'SELECT * FROM ' . $this->getTableNameWithPrefix() . ' WHERE ' . $whereClause;

        $rows = $this->db->select($sql);

        if ($one) {
            return $this->hydrateOne($rows);
        } else {
            return $this->hydrateMany($rows);
        }
    }

    /**
     * Find one entity in DB.
     *
     * @param int $id
     *
     * @return array|mixed|null
     *
     * @throws Exception
     */
    public function findOne($id)
    {
        $conditions = [];
        $conditions[$this->getIdFieldName()] = $id;

        return $this->doFind(true, $conditions);
    }

    /**
     * Find all entities in DB.
     *
     * @return array
     */
    public function findAll()
    {
        $sql = 'SELECT * FROM ' . $this->getTableNameWithPrefix();

        return $this->hydrateMany($this->db->select($sql));
    }
}
