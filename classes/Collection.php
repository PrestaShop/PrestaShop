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
 * Create a collection of ObjectModel objects
 *
 * @since 1.5.0
 */
class CollectionCore implements Iterator, ArrayAccess, Countable
{
	/**
	 * @var string Object class name
	 */
	protected $classname;

	/**
	 * @var int
	 */
	protected $id_lang;

	/**
	 * @var array Object definition
	 */
	protected $definition = array();

	/**
	 * @var DbQuery
	 */
	protected $query;

	/**
	 * @var array Collection of objects in an array
	 */
	protected $results = array();

	/**
	 * @var bool Is current collection already hydrated
	 */
	protected $is_hydrated = false;

	/**
	 * @var int Collection iterator
	 */
	protected $iterator = 0;

	/**
	 * @var int Total of elements for iteration
	 */
	protected $total;

	protected $fields = array();
	protected $alias = array();
	protected $alias_iterator = 0;

	/**
	 * @param string $classname
	 * @param int $id_lang
	 */
	public function __construct($classname, $id_lang = null)
	{
		$this->classname = $classname;
		$this->id_lang = $id_lang;

		$this->definition = ObjectModel::getDefinition($this->classname);
		if (!isset($this->definition['table']))
			throw new PrestashopException('Miss table in definition for class '.$this->classname);
		else if (!isset($this->definition['primary']))
			throw new PrestashopException('Miss primary in definition for class '.$this->classname);

		$alias = $this->generateAlias();
		$this->query = new DbQuery();
		$this->query->select($alias.'.*');
		$this->query->from($this->definition['table'], $alias);

		// If multilang, create association to lang table
		// @todo create virtual association with lang in ObjectModel::getDefinition()
		if (isset($this->definition['multilang']) && $this->definition['multilang'])
		{
			$lang_alias = $this->generateAlias('@lang');
			$this->query->select($lang_alias.'.*');
			$this->query->leftJoin($this->definition['table'].'_lang', $lang_alias, $alias.'.'.$this->definition['primary'].' = '.$lang_alias.'.'. $this->definition['primary']);
			if ($this->id_lang)
				$this->query->where($lang_alias.'.id_lang = '.(int)$this->id_lang);
		}
	}

	/**
	 * Add WHERE restriction on query
	 *
	 * @param string $field Field name
	 * @param string $operator List of operators : =, !=, <>, <, <=, >, >=, like, notlike, regexp, notregexp
	 * @param mixed $value
	 * @return Collection
	 */
	public function where($field, $operator, $value)
	{
		// Create WHERE clause with an array value (IN, NOT IN)
		if (is_array($value))
		{
			switch (strtolower($operator))
			{
				case '=' :
				case 'in' :
					$this->query->where($this->parseField($field).' IN('.implode(', ', $this->formatValue($value, $field)).')');
				break;

				case '!=' :
				case '<>' :
				case 'notin' :
					$this->query->where($this->parseField($field).' NOT IN('.implode(', ', $this->formatValue($value, $field)).')');
				break;

				default :
					throw new PrestashopException('Operator not supported for array value');
			}
		}
		// Create WHERE clause
		else
		{
			switch (strtolower($operator))
			{
				case '=' :
				case '!=' :
				case '<>' :
				case '>' :
				case '>=' :
				case '<' :
				case '<=' :
				case 'like' :
				case 'regexp' :
					$this->query->where($this->parseField($field).' '.$operator.' '.$this->formatValue($value, $field));
				break;

				case 'notlike' :
					$this->query->where($this->parseField($field).' NOT LIKE '.$this->formatValue($value, $field));
				break;

				case 'notregexp' :
					$this->query->where($this->parseField($field).' NOT REGEXP '.$this->formatValue($value, $field));
				break;

				default :
					throw new PrestashopException('Operator not supported');
			}
		}

		return $this;
	}

	/**
	 * Add WHERE restriction on query using real SQL syntax
	 *
	 * @param string $sql
	 */
	public function sqlWhere($sql)
	{
		$this->query->where($this->parseFields($sql));
		return $this;
	}

	/**
	 * Add ORDER BY restriction on query
	 *
	 * @param string $field Field name
	 * @param string $order asc|desc
	 * @return Collection
	 */
	public function orderBy($field, $order = 'asc')
	{
		$order = strtolower($order);
		if ($order != 'asc' && $order != 'desc')
			throw new PrestashopException('Order must be asc or desc');
		$this->query->orderBy($this->parseField($field).' '.$order);
		return $this;
	}

	/**
	 * Add GROUP BY restriction on query
	 *
	 * @param string $field Field name
	 * @return Collection
	 */
	public function groupBy($field)
	{
		$this->query->groupBy($this->parseField($field));
		return $this;
	}

	/**
	 * Launch sql query to create collection of objects
	 *
	 * @param bool $display_query If true, query will be displayed (for debug purpose)
	 * @return Collection
	 */
	public function getAll($display_query = false)
	{
		if ($this->is_hydrated)
			return $this;
		$this->is_hydrated = true;

		if ($display_query)
			echo $this->query.'<br />';

		$this->results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query);
		$this->results = ObjectModel::hydrateCollection($this->classname, $this->results, $this->id_lang);

		return $this;
	}

	/**
	 * Get results array
	 *
	 * @return array
	 */
	public function getResults()
	{
		$this->getAll();
		return $this->results;
	}

	/**
	 * This method is called when a foreach begin
	 *
	 * @see Iterator::rewind()
	 */
	public function rewind()
	{
		$this->getAll();
		$this->results = array_merge($this->results);
		$this->iterator = 0;
		$this->total = count($this->results);
	}

	/**
	 * Get current result
	 *
	 * @see Iterator::current()
	 * @return ObjectModel
	 */
	public function current()
	{
		return isset($this->results[$this->iterator]) ? $this->results[$this->iterator] : null;
	}

	/**
	 * Check if there is a current result
	 *
	 * @see Iterator::valid()
	 * @return bool
	 */
	public function valid()
	{
		return $this->iterator < $this->total;
	}

	/**
	 * Get current result index
	 *
	 * @see Iterator::key()
	 * @return int
	 */
	public function key()
	{
		return $this->iterator;
	}

	/**
	 * Go to next result
	 *
	 * @see Iterator::next()
	 */
	public function next()
	{
		$this->iterator++;
	}

	/**
	 * Get total of results
	 *
	 * @see Countable::count()
	 * @return int
	 */
	public function count()
	{
		$this->getAll();
		return count($this->results);
	}

	/**
	 * Check if a result exist
	 *
	 * @see ArrayAccess::offsetExists()
	 * @param $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		$this->getAll();
		return isset($this->results[$offset]);
	}

	/**
	 * Get a result by offset
	 *
	 * @see ArrayAccess::offsetGet()
	 * @param $offset
	 * @return ObjectModel
	 */
	public function offsetGet($offset)
	{
		$this->getAll();
		if (!isset($this->results[$offset]))
			throw new PrestashopException('Unknown offset '.$offset.' for collection '.$this->classname);
		return $this->results[$offset];
	}

	/**
	 * Add an element in the collection
	 *
	 * @see ArrayAccess::offsetSet()
	 * @param $offset
	 * @param $value
	 */
	public function offsetSet($offset, $value)
	{
		if (!$value instanceof $this->classname)
			throw new PrestashopException('You cannot add an element which is not an instance of '.$this->classname);

		$this->getAll();
		if (is_null($offset))
			$this->results[] = $value;
		else
			$this->results[$offset] = $value;
	}

	/**
	 * Delete an element from the collection
	 *
	 * @see ArrayAccess::offsetUnset()
	 * @param $offset
	 */
	public function offsetUnset($offset)
	{
		$this->getAll();
		unset($this->results[$offset]);
	}

	/**
	 * Parse all fields with {field} syntax in a string
	 *
	 * @param string $str
	 * @return string
	 */
	protected function parseFields($str)
	{
		preg_match_all('#\{(([a-z0-9_]+\.)*[a-z0-9_]+)\}#i', $str, $m);
		for ($i = 0, $total = count($m[0]); $i < $total; $i++)
			$str = str_replace($m[0][$i], $this->parseField($m[1][$i]), $str);
		return $str;
	}

	/**
	 * Replace a field with its SQL version (E.g. manufacturer.name with a2.name)
	 *
	 * @param string $field Field name
	 * @return string
	 */
	protected function parseField($field)
	{
		$info = $this->getFieldInfo($field);
		return $info['alias'].'.`'.$info['name'].'`';
	}

	/**
	 * Format a value with the type of the given field
	 *
	 * @param mixed $value
	 * @param string $field Field name
	 */
	protected function formatValue($value, $field)
	{
		$info = $this->getFieldInfo($field);
		if (is_array($value))
		{
			$results = array();
			foreach ($value as $item)
				$results[] = ObjectModel::formatValue($item, $info['type'], true);
			return $results;
		}
		return ObjectModel::formatValue($value, $info['type'], true);
	}

	/**
	 * Obtain some information on a field (alias, name, type, etc.)
	 *
	 * @param string $field Field name
	 * @return array
	 */
	protected function getFieldInfo($field)
	{
		if (!isset($this->fields[$field]))
		{
			$split = explode('.', $field);
			$association = '';
			$definition = ObjectModel::getDefinition($this->classname);
			for ($i = 0, $total_association = count($split) - 1; $i < $total_association; $i++)
			{
				// @todo association
			}

			$fieldname = $split[$i];
			if ($fieldname == $definition['primary'])
				$type = ObjectModel::TYPE_INT;
			else
			{
				if (!isset($definition['fields'][$fieldname]))
					throw new PrestashopException('Field '.$fieldname.' not found in class '.$this->classname);

				$type = $definition['fields'][$fieldname]['type'];
			}

			$this->fields[$field] = array(
				'name' => 			$fieldname,
				'association' =>	$association,
				'alias' =>			$this->generateAlias($association),
				'type' =>			$type,
			);
		}
		return $this->fields[$field];
	}

	/**
	 * Generate uniq alias from association name
	 *
	 * @param string $association Use empty association for alias on current table
	 * @return string
	 */
	protected function generateAlias($association = '')
	{
		if (!isset($this->alias[$association]))
			$this->alias[$association] = 'a'.$this->alias_iterator++;
		return $this->alias[$association];
	}
}
