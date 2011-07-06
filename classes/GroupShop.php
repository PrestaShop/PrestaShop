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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class GroupShopCore extends ObjectModel
{
	public	$name;
	public	$active;
	public	$share_datas;
	public	$share_stock;
	public	$deleted;
	
	protected	$fieldsSize = array('name' => 64);
 	protected	$fieldsValidate = array(
 					'active' => 'isBool',
 					'share_datas' => 'isBool',
 					'share_stock' => 'isBool',
 					'name' => 'isGenericName',
 				);
	protected	$table = 'group_shop';
	protected	$identifier = 'id_group_shop';

	private	static $assoTables = array(
		'attribute_group' => 		array('type' => 'group_shop'),
		'attribute' => 				array('type' => 'group_shop'),
		'customer_group' => 		array('type' => 'group_shop'),
		'feature' => 				array('type' => 'group_shop'),
		'group' => 					array('type' => 'group_shop'),
		'manufacturer' => 			array('type' => 'group_shop'),
		'supplier' => 				array('type' => 'group_shop'),
		'zone' => 					array('type' => 'group_shop'),
		'tax_rules_group' => 		array('type' => 'group_shop'),
	);

	public function getFields()
	{
		parent::validateFields();

		$fields['name'] = pSQL($this->name);
		$fields['share_datas'] = (int)$this->share_datas;
		$fields['share_stock'] = (int)$this->share_stock;
		$fields['active'] = (int)$this->active;
		$fields['deleted'] = (int)$this->deleted;
		return $fields;
	}
	
	public static function getGroupShops($active = true)
	{
		return Db::getInstance()->ExecuteS('SELECT * 
														FROM '._DB_PREFIX_.'group_shop
														WHERE `deleted`= 0 AND `active`='.(int)$active);
	}
	
	public function delete()
	{
		if (!$res = parent::delete())
			return false;

		foreach (Shop::getAssoTables() AS $table_name => $row)
		{
		
			$id = 'id_'.$row['type'];
			if ($row['type'] == 'fk_shop')
				$id = 'id_shop';
			else
				$table_name .= '_'.$row['type'];
			$res &= Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.$table_name.'` WHERE `'.$id.'`='.(int)$this->id);
		}

		return $res;
	}
	
	public static function getAssoTables()
	{
		return  self::$assoTables;
	}
	
	/**
	 * @return int Total of groupshops
	 */
	public static function getTotalGroupShops($active = true)
	{
		return sizeof(GroupShop::getGroupShops($active));
	}
	
	public function copyGroupShopData($old_id)
	{
	//TODO
	}
	
	public function haveShops()
	{
		return (bool)$this->getNbShops();
	}
	
	public function getNbShops()
	{
		return (int)Db::getInstance()->getValue('SELECT COUNT(*)
																FROM '._DB_PREFIX_.'shop s
																WHERE id_group_shop='.(int)$this->id);
	}
}
