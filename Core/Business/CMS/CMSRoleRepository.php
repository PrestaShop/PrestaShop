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

class CMSRoleRepository extends Core_Foundation_Database_EntityRepository
{
	protected $entityClass = 'CMSRoleEntity';

	/**
	 * Get CMS Role by its given reference name
	 * @param $name
	 * @return array|false
	 * @throws PrestaShopDatabaseException
	 */
	public function getRoleByName($name)
	{
		$sql = '
		SELECT *
		FROM `'._DB_PREFIX_.CMSRoleEntity::$definition['table'].'`
		WHERE `name` = "'.pSQL($name).'"';

		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Get CMS Roles by their Associated id_cms
	 * @param $id_cms
	 * @return array|false
	 * @throws PrestaShopDatabaseException
	 */
	public function getRoleByIdCms($id_cms)
	{
		$sql = '
		SELECT *
		FROM `'._DB_PREFIX_.CMSRoleEntity::$definition['table'].'`
		WHERE `id_cms` ='.(int)$id_cms;

		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Get CMS Roles by their reference names
	 * @param $names_array
	 * @return array|false
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopExceptionCore
	 */
	public function getCMSRolesWhereNamesIn($names_array)
	{
		if (!is_array($names_array))
			throw new PrestaShopExceptionCore('Expected array given '.gettype($names_array));

		$names_exploded = implode('","', $names_array);

		$sql = '
		SELECT *
		FROM `'._DB_PREFIX_.CMSRoleEntity::$definition['table'].'`
		WHERE `name` IN ("'.$names_exploded.'")';

		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Get associated CMS id from CMS Role reference name
	 * @param $reference_name
	 * @return array|false
	 */
	public function getCMSIdAssociatedFromName($reference_name)
	{
		$sql = '
		SELECT `id_cms`
		FROM `'._DB_PREFIX_.CMSRoleEntity::$definition['table'].'`
		WHERE `name` = "'.pSQL($reference_name).'"';

		return Db::getInstance()->getRow($sql);
	}


	/**
	 * Return all CMS roles already associated to a CMS Page
	 * @return array|false
	 * @throws PrestaShopDatabaseException
	 */
	public function getCMSRolesAssociated()
	{
		$sql = '
			SELECT *
			FROM `'._DB_PREFIX_.CMSRoleEntity::$definition['table'].'`
			WHERE `id_cms` != 0
		';

		$result = Db::getInstance()->executeS($sql);

		if ($result === false || $result === null)
			$result = array();

		return $result;
	}

}
