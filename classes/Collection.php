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
class CollectionCore implements Iterator, Countable
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
	 * @var int Current object iteration
	 */
	protected $iterator = 0;

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
			throw new PrestashopException('Miss table in definition');

		$this->query = new DbQuery();
		$this->query->select('a.*');
		$this->query->from($this->definition['table'].' a');

		// If multilang, create association to lang table
		if (isset($this->definition['multilang']) && $this->definition['multilang'])
		{
			$this->query->select('b.*');
			$this->query->leftJoin($this->definition['table'].'_lang b ON a.'.$this->definition['primary'].' = b.'.$this->definition['primary']);
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
	 */
	public function rewind()
	{
		$this->iterator = 0;
		$this->getAll();
	}

	/**
	 * Get current result
	 */
	public function current()
	{
		return $this->results[$this->iterator];
	}

	/**
	 * Check if there is a current result
	 */
	public function valid()
	{
		return isset($this->results[$this->iterator]);
	}

	/**
	 * Get current result index
	 */
	public function key()
	{
		return $this->iterator;
	}

	/**
	 * Go to next result
	 */
	public function next()
	{
		$this->iterator++;
	}

	/**
	 * Get total of results
	 *
	 * @return int
	 */
	public function count()
	{
		$this->getAll();
		return count($this->results);
	}
}