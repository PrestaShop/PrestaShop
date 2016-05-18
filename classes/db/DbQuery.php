<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *
 * This code contains large portions of Doctrine's SQL Query Builder which is subject to the following license:
 * The MIT License (MIT)
 * Copyright (c) 2016 Doctrine Project <http://www.doctrine-project.org>
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

use Doctrine\DBAL\Query\Expression\CompositeExpression;

/**
 * SQL query query
 *
 * @since 1.5.0.1
 */
class DbQueryCore
{
    /**
     * Cached SQL string
     *
     * @var string
     */
    protected $sql;

    /*
     * The query types.
     */
    const SELECT = 0;
    const DELETE = 1;
    const UPDATE = 2;
    const INSERT = 3;

    /*
     * The builder states.
     */
    const STATE_DIRTY = 0;
    const STATE_CLEAN = 1;

    /**
     * List of data to build the query
     *
     * @var array
     */
    protected $query = [
        'select'    => [],
        'from'      => '',
        'join'      => [],
        'free_join' => [], // joins without known table
        'set'       => [],
        'where'     => null,
        'groupBy'   => [],
        'having'    => null,
        'orderBy'   => [],
        'values'    => [],
    ];

    /**
     * The query parameters.
     *
     * @var array
     */
    protected $params = [];

    /**
     * The parameter type map of this query.
     *
     * @var array
     */
    protected $param_types = [];

    /**
     * The type of query this is. Can be select, update or delete.
     *
     * @var integer
     */
    protected $type = self::SELECT;

    /**
     * The state of the query object. Can be dirty or clean.
     *
     * @var integer
     */
    protected $state = self::STATE_CLEAN;

    /**
     * The index of the first result to retrieve.
     *
     * @var integer
     */
    protected $first_result = null;

    /**
     * The maximum number of results to retrieve.
     *
     * @var integer
     */
    protected $max_results = null;

    /**
     * The counter of bound parameters used with {@see bindValue).
     *
     * @var integer
     */
    protected $bound_counter = 0;

    /**
     * Sets type of the query
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: N/A
     *
     * @param string $type SELECT|INSERT|UPDATE|DELETE
     *
     * @return DbQuery
     */
    public function type($type)
    {
        switch ($type) {
            case 'SELECT':
                $this->type = self::SELECT;
                break;
            case 'INSERT':
                $this->type = self::INSERT;
                break;
            case 'UPDATE':
                $this->type = self::UPDATE;
                break;
            case 'DELETE':
                $this->type = self::DELETE;
                break;
        }

        return $this;
    }

    /**
     * Gets type of the query
     *
     * @return int Query type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets a query parameter for the query being constructed.
     *
     * <code>
     *     $qb = new DbQuery()
     *     $qb
     *         ->select('id_product')
     *         ->from('product')
     *         ->where('id_product = ?')
     *         ->setParameter(1, Db::TYPE_INT);
     * </code>
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: not compatible
     *
     * As opposed to Doctrine's SQL Query Builder it is not possible to use named parameters or set the key
     * The order has to be just right
     * To use a different order @see setParameters
     *
     * @param mixed          $value The parameter value.
     * @param string|null    $type  One of the PDO::PARAM_* constants.
     *
     * @return DbQuery This instance.
     */
    public function setParameter($value, $type = null)
    {
        $this->bound_counter++;
        if ($type !== null) {
            $this->param_types[$this->bound_counter] = $type;
        }

        $this->params[$this->bound_counter] = $value;

        return $this;
    }

    /**
     * Sets a collection of query parameters for the query being constructed.
     *
     * <code>
     *     $qb = new DbQuery()
     *     $qb
     *         ->select('`id_product`')
     *         ->from('product', 'p')
     *         ->where('p.`id_product` = ? OR p.`id_product` = ?')
     *         ->setParameters(array(1, 2), array(Db::TYPE_INTEGER, Db::TYPE_INTEGER,);
     * </code>
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param array $params The query parameters to set.
     * @param array $types  The query parameters types to set.
     *
     * @return DbQuery This instance.
     */
    public function setParameters(array $params, array $types = [])
    {
        $this->param_types = $types;
        $this->params = $params;

        return $this;
    }

    /**
     * Gets all defined query parameters for the query being constructed indexed by parameter index or name.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @return array The currently defined query parameters indexed by parameter index or name.
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * Gets a (previously set) query parameter of the query being constructed.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: not compatible
     *
     * As opposed to Doctrine's SQL Query Builder it is not possible to used named parameters
     *
     * @param mixed $key The key (index or name) of the bound parameter.
     *
     * @return mixed The value of the bound parameter.
     */
    public function getParameter($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    /**
     * Gets all defined query parameter types for the query being constructed indexed by parameter index or name.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @return array The currently defined query parameter types indexed by parameter index or name.
     */
    public function getParameterTypes()
    {
        return $this->param_types;
    }

    /**
     * Gets a (previously set) query parameter type of the query being constructed.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: not compatible
     *
     * As opposed to Doctrine's SQL Query Builder it is not possible to use named parameters
     *
     * @param mixed $key The key (index) of the bound parameter type.
     *
     * @return mixed The value of the bound parameter type.
     */
    public function getParameterType($key)
    {
        return isset($this->param_types[$key]) ? $this->param_types[$key] : null;
    }

    /**
     * Sets the position of the first result to retrieve (the "offset").
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param integer $first_result The first result to return.
     *
     * @return DbQuery This instance.
     */
    public function setFirstResult($first_result)
    {
        $this->state = self::STATE_DIRTY;
        $this->first_result = $first_result;

        return $this;
    }

    /**
     * Gets the position of the first result the query object was set to retrieve (the "offset").
     * Returns NULL if {@link setFirstResult} was not applied to this DbQuery.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @return integer The position of the first result.
     */
    public function getFirstResult()
    {
        return $this->first_result;
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit").
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param integer $max_results The maximum number of results to retrieve.
     *
     * @return DbQuery This DbQuery instance.
     */
    public function setMaxResults($max_results)
    {
        $this->state = self::STATE_DIRTY;
        $this->max_results = $max_results;

        return $this;
    }

    /**
     * Gets the maximum number of results the query object was set to retrieve (the "limit").
     * Returns NULL if {@link setMaxResults} was not applied to this query builder.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @return integer The maximum number of results.
     */
    public function getMaxResults()
    {
        return $this->max_results;
    }

    /**
     * Either appends to or replaces a single, generic query part.
     *
     * The available parts are: 'select', 'from', 'set', 'where',
     * 'groupBy', 'having' and 'orderBy'.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string  $sql_part_name
     * @param string  $sql_part
     * @param boolean $append
     *
     * @return DbQuery This instance.
     */
    public function add($sql_part_name, $sql_part, $append = false)
    {
        $isArray = is_array($sql_part);
        $isMultiple = is_array($this->query[$sql_part_name]);

        if ($isMultiple && !$isArray) {
            $sql_part = array($sql_part);
        }

        $this->state = self::STATE_DIRTY;

        if ($append) {
            if ($sql_part_name == 'orderBy' || $sql_part_name == 'groupBy' || $sql_part_name == 'select' || $sql_part_name == 'set' || $sql_part_name == 'join') {
                foreach ($sql_part as $part) {
                    $this->query[$sql_part_name][] = $part;
                }
            } elseif ($isArray && is_array($sql_part[key($sql_part)])) {
                $key = key($sql_part);
                $this->query[$sql_part_name][$key][] = $sql_part[$key];
            } elseif ($isMultiple) {
                $this->query[$sql_part_name][] = $sql_part;
            } else {
                $this->query[$sql_part_name] = $sql_part;
            }

            return $this;
        }

        $this->query[$sql_part_name] = $sql_part;

        return $this;
    }

    /**
     * Adds fields to SELECT clause
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: not compatible
     *
     * Behaves a bit different from the original Doctrine SQL Query Builder:
     * Instead of replacing the SELECT clause, the clause will be appended instead
     *
     * @param string $select The (partial) select clause.
     * @return DbQuery This instance.
     * @internal param string $fields List of fields to concat to other fields
     *
     */
    public function select($select = null)
    {
        $this->type = self::SELECT;

        if (empty($select)) {
            return $this;
        }

        $selects = is_array($select) ? $select : func_get_args();

        $this->add('select', $selects, true);
        return $this;
    }

    /**
     * Adds an item that is to be returned in the query result.
     *
     * <code>
     *     $qb = new DbQuery()
     *     $qb
     *         ->select('id_product')
     *         ->addSelect('reference')
     *         ->from('product');
     * </code>
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param mixed $select The selection expression.
     *
     * @return DbQuery This instance.
     */
    public function addSelect($select = null)
    {
        $this->type = self::SELECT;

        if (empty($select)) {
            return $this;
        }

        $selects = is_array($select) ? $select : func_get_args();

        return $this->add('select', $selects, true);
    }

    /**
     * Turns the query being built into a bulk delete query that ranges over
     * a certain table.
     *
     * <code>
     *     $qb = new DbQuery()
     *         ->delete('product')
     *         ->where('id_product = ?');
     *         ->setParameter(1, Db::TYPE_INTEGER);
     * </code>
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $delete The table whose rows are subject to the deletion.
     * @param string $alias  The table alias used in the constructed query.
     *
     * @return DbQuery This instance.
     */
    public function delete($delete = null, $alias = null, $add_prefix = true)
    {
        $this->type = self::DELETE;

        if (!$delete) {
            return $this;
        }

        $this->query['from'][] = [
            'table' => '`'.($add_prefix ? _DB_PREFIX_ : '').$delete.'`',
            'alias' => $alias
        ];

        return $this;
    }

    /**
     * Turns the query being built into a bulk update query that ranges over
     * a certain table
     *
     * <code>
     *     $qb = new DbQuery();
     *     $qb
     *         ->update('users', 'u')
     *         ->set('u.password', md5('password'))
     *         ->where('u.id = ?');
     * </code>
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $update The table whose rows are subject to the update.
     * @param string $alias  The table alias used in the constructed query.
     *
     * @return DbQuery This instance.
     */
    public function update($update = null, $alias = null)
    {
        $this->type = self::UPDATE;

        if ( ! $update) {
            return $this;
        }

        return $this->add('from', [
            'table' => $update,
            'alias' => $alias
        ]);
    }

    /**
     * Turns the query being built into an insert query that inserts into
     * a certain table
     *
     * <code>
     *     $qb = new DbQuery();
     *     $qb
     *         ->insert('users')
     *         ->values(
     *             array(
     *                 'name' => '?',
     *                 'password' => '?'
     *             )
     *         );
     * </code>
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $insert The table into which the rows should be inserted.
     *
     * @return DbQuery This instance.
     */
    public function insert($insert = null)
    {
        $this->type = self::INSERT;

        if ( ! $insert) {
            return $this;
        }

        return $this->add('from', [
            'table' => $insert
        ]);
    }

    /**
     * Sets table for FROM clause
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $table Table name
     * @param string|null $alias Table alias
     * @param bool $add_prefix Add PrestaShop's prefix to table name
     *
     * @return DbQuery This instance.
     */
    public function from($table, $alias = null, $add_prefix = true)
    {
        if (!empty($table)) {
            $this->query['from'][] = [
                'table' => '`'.($add_prefix ? _DB_PREFIX_ : '').$table.'`',
                'alias' => $alias
            ];
        }

        return $this;
    }

    /**
     * Adds JOIN clause
     * E.g. $this->join('RIGHT JOIN '._DB_PREFIX_.'product p ON ...');
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: not compatible
     *
     * Does not support Doctrine's join, use one of the other join types instead
     *
     * @param string $join Complete string
     *
     * @return DbQuery This instance.
     */
    public function join($join)
    {
        if (!empty($join)) {
            $this->query['free_join'][] = $join;
        }

        return $this;
    }

    /**
     * Adds a LEFT JOIN clause
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $table Table name (without prefix)
     * @param string|null $alias Table alias
     * @param string|null $on ON clause
     * @param bool $add_prefix Add PrestaShop's prefix to table name
     *
     * @return DbQuery This instance.
     */
    public function leftJoin($table, $alias = null, $on = null, $add_prefix = true)
    {
        return $this->add('join', [
            [
                'joinType'      => 'left',
                'joinTable'     => ($add_prefix ? _DB_PREFIX_ : '').$table,
                'joinAlias'     => $alias,
                'joinCondition' => $on
            ]
        ], true);
    }

    /**
     * Adds a LEFT OUTER JOIN clause
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $table Table name (without prefix)
     * @param string|null $alias Table alias
     * @param string|null $on ON clause
     * @param bool $add_prefix Add prefix to table name
     *
     * @return DbQuery This instance.
     */
    public function leftOuterJoin($table, $alias = null, $on = null, $add_prefix = true)
    {
        return $this->leftJoin($table, $alias, $on, $add_prefix);
    }

    /**
     * Adds an INNER JOIN clause
     * E.g. $this->innerJoin('product p ON ...')
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $table Table name (without prefix)
     * @param string|null $alias Table alias
     * @param string|null $on ON clause
     * @param bool $add_prefix Add prefix to table name
     *
     * @return DbQuery This instance.
     */
    public function innerJoin($table, $alias = null, $on = '', $add_prefix = true)
    {
        return $this->add('join', [
            [
                'joinType'      => 'inner',
                'joinTable'     => ($add_prefix ? _DB_PREFIX_ : '').$table,
                'joinAlias'     => $alias,
                'joinCondition' => $on
            ]
        ], true);
    }

    /**
     * Adds a NATURAL JOIN clause
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $table Table name (without prefix)
     * @param string|null $alias Table alias
     * @param bool $add_prefix Add prefix to table name
     *
     * @return DbQuery This instance.
     */
    public function naturalJoin($table, $alias = null, $add_prefix = true)
    {
        return $this->add('join', [
            [
                'joinType'      => 'natural',
                'joinTable'     => ($add_prefix ? _DB_PREFIX_ : '').$table,
                'joinAlias'     => $alias
            ]
        ], true);
    }

    /**
     * Adds a RIGHT JOIN clause
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $table Table name (without prefix)
     * @param string|null $alias Table alias
     * @param string|null $on ON clause
     * @param bool $add_prefix Add prefix to table name
     *
     * @return DbQuery This instance.
     */
    public function rightJoin($table, $alias = null, $on = null, $add_prefix = true)
    {
        return $this->add('join', [
            [
                'joinType'      => 'right',
                'joinTable'     => ($add_prefix ? _DB_PREFIX_ : '').$table,
                'joinAlias'     => $alias,
                'joinCondition' => $on
            ]
        ], true);
    }

    /**
     * Adds a RIGHT OUTER JOIN clause
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $table Table name (without prefix)
     * @param string|null $alias Table alias
     * @param string|null $on ON clause
     * @param bool $add_prefix Add prefix to table name
     *
     * @return DbQuery This instance.
     */
    public function rightOuterJoin($table, $alias = null, $on = null, $add_prefix = true)
    {
        return $this->rightJoin($table, $alias, $on, $add_prefix);
    }

    /**
     * Adds a restriction in WHERE clause (each restriction will be separated by AND statement)
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: not compatible
     *
     * Behaves different from Doctrine's SQL Query Builder:
     * This defaults to AND for consecutive WHERE clauses
     *
     * @param string $restriction
     *
     * @return DbQuery This instance.
     */
    public function where($restriction)
    {
        if (!empty($restriction)) {
            $this->andWhere($restriction);
        }

        return $this;
    }

    /**
     * Adds one or more restrictions to the query results, forming a logical
     * conjunction with any previously specified restrictions.
     *
     * <code>
     *     $qb = new DbQuery()
     *         ->select('u')
     *         ->from('users', 'u')
     *         ->where('u.username LIKE ?')
     *         ->andWhere('u.is_active = 1');
     * </code>
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param mixed $where The query restrictions.
     *
     * @return DbQuery This instance.
     */
    public function andWhere($where)
    {
        $args = func_get_args();
        $where = $this->getQueryPart('where');

        if ($where instanceof CompositeExpression && $where->getType() === CompositeExpression::TYPE_AND) {
            $where->addMultiple($args);
        } else {
            array_unshift($args, $where);
            $where = new CompositeExpression(CompositeExpression::TYPE_AND, $args);
        }

        return $this->add('where', $where, true);
    }

    /**
     * Adds one or more restrictions to the query results, forming a logical
     * disjunction with any previously specified restrictions.
     *
     * <code>
     *     $qb = new DbQuery()
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->where('u.id = 1')
     *         ->orWhere('u.id = 2');
     * </code>
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param mixed $where The WHERE statement.
     *
     * @return DbQuery This instance.
     */
    public function orWhere($where)
    {
        $args = func_get_args();
        $where = $this->getQueryPart('where');

        if ($where instanceof CompositeExpression && $where->getType() === CompositeExpression::TYPE_OR) {
            $where->addMultiple($args);
        } else {
            array_unshift($args, $where);
            $where = new CompositeExpression(CompositeExpression::TYPE_OR, $args);
        }

        return $this->add('where', $where, true);
    }

    /**
     * Adds a restriction in HAVING clause (each restriction will be separated by AND statement)
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $restriction
     *
     * @return DbQuery
     */
    public function having($restriction)
    {
        if (!empty($restriction)) {
            $this->query['having'][] = $restriction;
        }

        return $this;
    }

    /**
     * Adds an ORDER BY restriction
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $fields List of fields to sort. E.g. $this->order('myField, b.mySecondField DESC')
     *
     * @return DbQuery
     */
    public function orderBy($fields)
    {
        if (!empty($fields)) {
            $this->query['orderBy'][] = $fields;
        }

        return $this;
    }

    /**
     * Adds a GROUP BY restriction
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $fields List of fields to group. E.g. $this->group('myField1, myField2')
     *
     * @return DbQuery
     */
    public function groupBy($fields)
    {
        if (!empty($fields)) {
            $this->query['groupBy'][] = $fields;
        }

        return $this;
    }

    /**
     * Adds a grouping expression to the query.
     *
     * <code>
     *     $qb = new DbQuery()
     *     $qb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->groupBy('u.lastLogin');
     *         ->addGroupBy('u.createdAt')
     * </code>
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param mixed $groupBy The grouping expression.
     *
     * @return DbQuery This instance.
     */
    public function addGroupBy($groupBy)
    {
        if (empty($groupBy)) {
            return $this;
        }

        $groupBy = is_array($groupBy) ? $groupBy : func_get_args();

        return $this->add('groupBy', $groupBy, true);
    }

    /**
     * Sets a value for a column in an insert query.
     *
     * <code>
     *     $qb = new DbQuery()
     *     $qb
     *         ->insert('users')
     *         ->values(
     *             array(
     *                 'name' => '?'
     *             )
     *         )
     *         ->setValue('password', '?');
     * </code>
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $column The column into which the value should be inserted.
     * @param string $value  The value that should be inserted into the column.
     *
     * @return QueryBuilder This QueryBuilder instance.
     */
    public function setValue($column, $value)
    {
        $this->sqlParts['values'][$column] = $value;

        return $this;
    }

    /**
     * Specifies values for an insert query indexed by column names.
     * Replaces any previous values, if any.
     *
     * <code>
     *     $qb = new DbQuery();
     *     $qb
     *         ->insert('users')
     *         ->values(
     *             array(
     *                 'name' => '?',
     *                 'password' => '?'
     *             )
     *         );
     * </code>
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param array $values The values to specify for the insert query indexed by column names.
     *
     * @return QueryBuilder This QueryBuilder instance.
     */
    public function values(array $values)
    {
        return $this->add('values', $values);
    }

    /**
     * Sets query offset and limit
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: N/A
     *
     * @param int $limit
     * @param int $offset
     *
     * @return DbQuery
     */
    public function limit($limit, $offset = 0)
    {
        $offset = (int)$offset;
        if ($offset < 0) {
            $offset = 0;
        }

        $this->max_results = $limit;
        $this->first_result = $offset;

        return $this;
    }

    /**
     * Gets the complete SQL string formed by the current specifications of this DbQuery.
     *
     * <code>
     *     $qb = new DbQuery()
     *         ->select('u')
     *         ->from('User', 'u')
     *     echo $qb->getSQL(); // SELECT u FROM User u
     * </code>
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @return string The SQL query string.
     */
    public function getSQL()
    {
        if ($this->sql !== null && $this->state === self::STATE_CLEAN) {
            return $this->sql;
        }

        switch ($this->type) {
            case self::INSERT:
                $sql = $this->getSQLForInsert();
                break;
            case self::DELETE:
                $sql = $this->getSQLForDelete();
                break;

            case self::UPDATE:
                $sql = $this->getSQLForUpdate();
                break;

            case self::SELECT:
            default:
                $sql = $this->getSQLForSelect();
                break;
        }

        $this->state = self::STATE_CLEAN;
        $this->sql = $sql;

        return $sql;
    }

    /**
     * Gets a query part by its name.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $query_part_name
     *
     * @return mixed
     */
    public function getQueryPart($query_part_name)
    {
        return $this->query[$query_part_name];
    }

    /**
     * Gets all query parts.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @return array
     */
    public function getQueryParts()
    {
        return $this->query;
    }

    /**
     * Resets SQL parts.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param array|null $query_part_names
     *
     * @return DbQuery This instance.
     */
    public function resetQueryParts($query_part_names = null)
    {
        if (is_null($query_part_names)) {
            $query_part_names = array_keys($this->query);
        }

        foreach ($query_part_names as $query_part_name) {
            $this->resetQueryPart($query_part_name);
        }

        return $this;
    }

    /**
     * Resets a single SQL part.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $query_part_name
     *
     * @return DbQuery This instance.
     */
    public function resetQueryPart($query_part_name)
    {
        $this->query[$query_part_name] = is_array($this->query[$query_part_name])
            ? [] : null;

        $this->state = self::STATE_DIRTY;

        return $this;
    }

    /**
     * @return string
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     */
    protected function getSQLForSelect()
    {
        $query = 'SELECT '.((count($this->query['select']) === 0) ? '*' : implode(', ', $this->query['select'])).' FROM ';

        $query .= implode(', ', $this->getFromClauses())
            .' '.implode(" ", $this->query['free_join'])
            .($this->query['where'] !== null ? ' WHERE '.((string)$this->query['where']) : '')
            .($this->query['groupBy'] ? ' GROUP BY '.implode(', ', $this->query['groupBy']) : '')
            .($this->query['having'] !== null ? ' HAVING '.((string)$this->query['having']) : '')
            .($this->query['orderBy'] ? ' ORDER BY '.implode(', ', $this->query['orderBy']) : '');

        if ($this->isLimitQuery()) {
            $query.= ' LIMIT '.(int)$this->max_results;
            $query.= 'OFFSET '.(int)$this->first_result;
        }

        return $query;
    }

    /**
     * Converts this instance into an INSERT string in SQL.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @return string
     */
    protected function getSQLForInsert()
    {
        return 'INSERT INTO '.$this->query['from'][0]['table'].
        ' ('.implode(', ', array_keys($this->query['values'])).')'.
        ' VALUES('.implode(', ', $this->query['values']).')';
    }

    /**
     * Converts this instance into an UPDATE string in SQL.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @return string
     */
    protected function getSQLForUpdate()
    {
        $table = $this->query['from'][0]['table'].(isset($this->query['from']['alias']) && $this->query['from'][0]['alias'] ? ' '.$this->query['from'][0]['alias'] : '');
        $query = 'UPDATE '.$table
            .' SET '.implode(", ", $this->query['set'])
            .($this->query['where'] !== null ? ' WHERE '.((string)$this->query['where']) : '');

        return $query;
    }

    /**
     * Converts this instance into a DELETE string in SQL.
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @return string
     */
    protected function getSQLForDelete()
    {
        $table = $this->query['from'][0]['table'].(isset($this->query['from'][0]['alias']) && $this->query['from'][0]['alias'] ? ' '.$this->query['from'][0]['alias'] : '');
        $query = 'DELETE FROM '.$table.($this->query['where'] !== null ? ' WHERE '.((string)$this->query['where']) : '');

        return $query;
    }

    /**
     * Generates query and return SQL string
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @return string SQL query
     */
    public function build()
    {
        return $this->getSQL();
    }

    /**
     * Get all FROM clauses
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @return string[]
     */
    protected function getFromClauses()
    {
        $from_clauses = [];
        $known_aliases = [];

        // Loop through all FROM clauses
        foreach ($this->query['from'] as $from) {
            if ($from['alias'] === null) {
                $table_sql = $from['table'];
                $table_reference = $from['table'];
            } else {
                $table_sql = $from['table'].' '.$from['alias'];
                $table_reference = $from['alias'];
            }

            $known_aliases[$table_reference] = true;

            $from_clauses[$table_reference] = $table_sql.$this->getSQLForJoins($table_reference, $known_aliases);
        }

        return $from_clauses;
    }

    /**
     * Does this query have a limit?
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @return bool Whether this query is limited
     */
    protected function isLimitQuery()
    {
        return $this->max_results !== null || $this->first_result !== null;
    }

    /**
     * Get JOIN clauses as SQL string
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: N/A
     * Doctrine's SQL Query Builder: compatible
     *
     * @param string $fromAlias
     * @param array  $knownAliases
     *
     * @return string
     */
    protected function getSQLForJoins($fromAlias, array &$knownAliases)
    {
        $sql = '';

        if (isset($this->query['join'])) {
            foreach ($this->query['join'] as $join) {
                $sql .= ' ' . strtoupper($join['joinType'])
                    . ' JOIN ' . $join['joinTable'] . ' ' . $join['joinAlias']
                    . ' ON ' . ((string) $join['joinCondition']);
            }
        }

        return $sql;
    }

    /**
     * Converts object to string
     *
     * Compatibility:
     * PrestaShop's pre-1.7 DbQuery: compatible
     * Doctrine's SQL Query Builder: compatible
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getSQL();
    }
}
