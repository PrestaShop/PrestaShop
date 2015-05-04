<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class EntityRepository
{
	protected $db;
	protected $tablesPrefix;
	protected $entityClass = null;

	public function __construct($db, $tablesPrefix)
	{
		$this->db = $db;
		$this->tablesPrefix = $tablesPrefix;
	}


	public function save($entity)
	{
		$persister = new Adapter_EntityPersister;
		$persister->save($entity);

		return $this;
	}

	public function findOneByName($name)
	{
		$entityClass = $this->entityClass;
		$tableName = $this->tablesPrefix . $entityClass::$definition['table'];

		$rows = $this->db->executeS(
			'SELECT * FROM `' . $tableName . '` WHERE name = \'' . $this->db->escape($name) . '\''
		);

		if (count($rows) === 0) {
			return null;
		} else if (count($rows) > 1) {
			throw new Exception('Too many rows returned.');
		} else {
			$data = $rows[0];
			$entity = new $entityClass;
			$entity->hydrate($data);
			return $entity;
		}
	}
}
