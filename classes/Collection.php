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

		$this->query = new DbQuery();
		$this->query->select('a.*');
		$this->query->from($this->definition['table'].' a');

		// If multilang, create association to lang table
		if (isset($this->definition['multilang']) && $this->definition['multilang'])
		{
			$this->query->select('b.*');
			$this->query->leftJoin($this->definition['table'].'_lang b ON a.'.$this->definition['primary'].' = b.'.$this->definition['primary']);
			if ($this->id_lang)
				$this->query->where('b.id_lang = '.(int)$this->id_lang);
		}
	}

	/**
	 * Add WHERE restriction on query
	 *
	 * @param string $str
	 * @return Collection
	 */
	public function where($str)
	{
		$this->query->where($str);
		return $this;
	}

	/**
	 * Add ORDER BY restriction on query
	 *
	 * @param string $str
	 * @return Collection
	 */
	public function orderBy($str)
	{
		$this->query->orderBy($str);
		return $this;
	}

	/**
	 * Add GROUP BY restriction on query
	 *
	 * @param string $str
	 * @return Collection
	 */
	public function groupBy($str)
	{
		$this->query->groupBy($str);
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
	 * This method is called when a foreach begin
	 *
	 * @see Iterator::rewind()
	 */
	public function rewind()
	{
		$this->getAll();
		reset($this->results);
	}

	/**
	 * Get current result
	 *
	 * @see Iterator::current()
	 * @return ObjectModel
	 */
	public function current()
	{
		return current($this->results);
	}

	/**
	 * Check if there is a current result
	 *
	 * @see Iterator::valid()
	 * @return bool
	 */
	public function valid()
	{
		return (bool)current($this->results);
	}

	/**
	 * Get current result index
	 *
	 * @see Iterator::key()
	 * @return int
	 */
	public function key()
	{
		return key($this->results);
	}

	/**
	 * Go to next result
	 *
	 * @see Iterator::next()
	 */
	public function next()
	{
		next($this->results);
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
}