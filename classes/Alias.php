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

class AliasCore extends ObjectModel
{
	public $alias;
	public $search;
	public $active = true;
		
 	protected 	$fieldsRequired = array('alias', 'search');
 	protected 	$fieldsSize = array('alias' => 255, 'search' => 255);
 	protected 	$fieldsValidate = array('search' => 'isValidSearch', 'alias' => 'isValidSearch', 'active' => 'isBool');

	protected 	$table = 'alias';
	protected 	$identifier = 'id_alias';

	function __construct($id = NULL, $alias = NULL, $search = NULL, $id_lang = NULL)
	{
		if ($id)
			parent::__construct($id);
		elseif ($alias AND Validate::isValidSearch($alias))
		{
			$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT a.id_alias, a.search, a.alias
			FROM `'._DB_PREFIX_.'alias` a
			WHERE `alias` LIKE \''.pSQL($alias).'\' AND `active` = 1');

			if ($row)
			{
			 	$this->id = (int)($row['id_alias']);
			 	$this->search = $search ? trim($search) : $row['search'];
				$this->alias = $row['alias'];
			}
			else
			{
				$this->alias = trim($alias);
				$this->search = trim($search);
			}
		}
	}

	static public function deleteAliases($search)
	{
		return Db::getInstance()->Execute('
		DELETE
		FROM `'._DB_PREFIX_.'alias`
		WHERE `search` LIKE \''.pSQL($search).'\'');
	}
	
	public function getAliases()
	{
		$aliases = Db::getInstance()->ExecuteS('
		SELECT a.alias
		FROM `'._DB_PREFIX_.'alias` a
		WHERE `search` = \''.pSQL($this->search).'\'');

		$aliases = array_map('implode', $aliases);
		return implode(', ', $aliases);
	}
	
	public function getFields()
	{
		parent::validateFields();
		
		$fields['alias'] = pSQL($this->alias);
		$fields['search'] = pSQL($this->search);
		$fields['active'] = (int)($this->active);
		return $fields;
	}
}

