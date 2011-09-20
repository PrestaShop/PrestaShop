<?php
/*
* 2007-2011 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * SQL query builder
 *
 * @since 1.5.0
 */
class DbQueryCore
{
	/**
	 * @var array list of data to build the query
	 */
	protected $query = array(
		'select' => array(),
		'from' => 	'',
		'join' => 	array(),
		'where' => 	array(),
		'group' => 	array(),
		'having' => array(),
		'order' => 	array(),
		'limit' => 	array('offset' => 0, 'limit' => 0),
	);

	/**
	 * Add fields in query selection
	 *
	 * @param string $fields List of fields to concat to other fields
	 * @return DbQuery
	 */
	public function select($fields)
	{
		$this->query['select'][] = $fields;
		return $this;
	}

	/**
	 * Set table for FROM clause
	 *
	 * @param string $table Table name
	 * @return DbQuery
	 */
	public function from($table)
	{
		$this->query['from'] = _DB_PREFIX_.$table;
		return $this;
	}

	/**
	 * Add JOIN clause
	 *
	 * @param string $type Join type : left|right|inner|cross|union|natural
	 * @return DbQuery
	 */
	public function join($type, $join)
	{
		$type = strtolower($type);
		$types = array(
			'left' => 'LEFT JOIN',
			'right' => 'RIGHT JOIN',
			'inner' => 'INNER JOIN',
			'cross' => 'CROSS JOIN',
			'union' => 'UNION JOIN',
			'natural' => 'NATURAL JOIN',
		);

		if (!isset($types[$type]))
			die('Bad type in DbQuery->join()');

		$this->query['join'][] = $types[$type].' '._DB_PREFIX_.$join;
		return $this;
	}

	public function leftJoin($join)
	{
		return $this->join('left', $join);
	}

	public function innerJoin($table)
	{
		return $this->join('inner', $join);
	}

	/**
	 * Add a restriction in WHERE clause (each restriction will be separated by AND statement)
	 *
	 * @param string $restriction
	 * @return DbQuery
	 */
	public function where($restriction)
	{
		$this->query['where'][] = $restriction;
		return $this;
	}

	/**
	 * Add a restriction in HAVING clause (each restriction will be separated by AND statement)
	 *
	 * @param string $restriction
	 * @return DbQuery
	 */
	public function having($restriction)
	{
		$this->query['having'][] = $restriction;
		return $this;
	}

	/**
	 * Add an ORDER B restriction
	 *
	 * @param string $fields List of fields to sort. E.g. $this->order('myField, b.mySecondField DESC')
	 * @return DbQuery
	 */
	public function order($fields)
	{
		$this->query['order'][] = $fields;
		return $this;
	}

	/**
	 * Add a GROUP BY restriction
	 *
	 * @param string $fields List of fields to sort. E.g. $this->group('myField, b.mySecondField DESC')
	 * @return DbQuery
	 */
	public function group($fields)
	{
		$this->query['group'][] = $fields;
		return $this;
	}

	/**
	 * Limit results in query
	 *
	 * @param string $fields List of fields to sort. E.g. $this->order('myField, b.mySecondField DESC')
	 * @return DbQuery
	 */
	public function limit($limit, $offset = 0)
	{
		$offset = (int)$offset;
		if ($offset < 0)
			$offset = 0;

		$this->query['limit'] = array(
			'offset' => $offset,
			'limit' =>	(int)$limit,
		);
		return $this;
	}

	/**
	 * Generate and get the query
	 *
	 * @return string
	 */
	public function build()
	{
		$sql = 'SELECT '.((($this->query['select'])) ? implode(",\n", $this->query['select']) : '*')."\n";

		if (!$this->query['from'])
			die('DbQuery->build() missing from clause');
		$sql .= 'FROM '.$this->query['from']."\n";

		if ($this->query['join'])
			$sql .= implode("\n", $this->query['join'])."\n";

		if ($this->query['where'])
			$sql .= 'WHERE '.implode(' AND ', $this->query['where'])."\n";

		if ($this->query['group'])
			$sql .= 'GROUP BY '.implode(', ', $this->query['group'])."\n";

		if ($this->query['having'])
			$sql .= 'HAVING '.implode(' AND ', $this->query['having'])."\n";

		if ($this->query['order'])
			$sql .= 'ORDER BY '.implode(', ', $this->query['order'])."\n";

		if ($this->query['limit']['limit'])
		{
			$limit = $this->query['limit'];
			$sql .= 'LIMIT '.(($limit['offset']) ? $limit['offset'].', '.$limit['limit'] : $limit['limit']);
		}

		return $sql;
	}

	public function __toString()
	{
		return $this->build();
	}
}