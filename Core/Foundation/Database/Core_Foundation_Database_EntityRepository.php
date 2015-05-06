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

class Core_Foundation_Database_EntityRepository
{
	protected $entityManager;
	protected $db;
	protected $tablesPrefix;
	protected $entityMetaData;

	public function __construct(
		Core_Foundation_Database_EntityManager $entityManager,
		$tablesPrefix,
		Core_Foundation_Database_EntityMetaData $entityMetaData
	)
	{
		$this->entityManager = $entityManager;
		$this->db = $this->entityManager->getDatabase();
		$this->tablesPrefix = $tablesPrefix;
		$this->entityMetaData = $entityMetaData;
	}

	protected function getIdFieldName()
	{
		$primary = $this->entityMetaData->getPrimaryKeyFieldnames();

		if (count($primary) === 0) {
			throw new Exception(
				sprintf(
					'No primary key defined in entity `%s`.',
					$this->entityMetaData->getEntityClassName()
				)
			);
		} else if (count($primary) > 1) {
			throw new Exception(
				sprintf(
					'Entity `%s` has a composite primary key, which is not supported by entity repositories.',
					$this->entityMetaData->getEntityClassName()
				)
			);
		}

		return $primary[0];
	}

	protected function getTableNameWithPrefix()
	{
		return $this->tablesPrefix . $this->entityMetaData->getTableName();
	}

	private function getNewEntity()
	{
		$entityClassName = $this->entityMetaData->getEntityClassName();
		return new $entityClassName;
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
		} else if (count($rows) > 1) {
			throw new Exception('Too many rows returned.');
		} else {
			$data = $rows[0];
			$entity = $this->getNewEntity();
			$entity->hydrate($data);
			return $entity;
		}
	}

	public function findOneByName($name)
	{
		$rows = $this->db->select(
			'SELECT * FROM `' . $this->getTableNameWithPrefix() . '` WHERE name = \'' . $this->db->escape($name) . '\''
		);

		return $this->hydrateOne($rows);
	}

	public function find($id)
	{
		$sql = 'SELECT * FROM ' . $this->getTableNameWithPrefix() . ' WHERE ' . $this->getIdFieldName() . ' = ' . (int)$id;
		$rows = $this->db->select($sql);

		return $this->hydrateOne($rows);
	}
}
