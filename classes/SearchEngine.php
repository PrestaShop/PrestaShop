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

class SearchEngineCore extends ObjectModel
{	
	public $server;
	public $getvar;

	protected	$fieldsRequired = array ('server', 'getvar');	
	protected	$fieldsValidate = array ('server' => 'isUrl', 'getvar' => 'isModuleName');

	protected 	$table = 'search_engine';
	protected 	$identifier = 'id_search_engine';
	
	public function getFields()
	{
		parent::validateFields();
		$fields['server'] = pSQL($this->server);
		$fields['getvar'] = pSQL($this->getvar);
		return $fields;
	}
	
	public static function getKeywords($url)
	{
		$parsedUrl = @parse_url($url);
		if (!isset($parsedUrl['host']) OR !isset($parsedUrl['query']))
			return false;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT `server`, `getvar` FROM `'._DB_PREFIX_.'search_engine`');
		foreach ($result as $index => $row)
		{
			$host =& $row['server'];
			$varname =& $row['getvar'];
			if (strstr($parsedUrl['host'], $host))
			{
				$kArray = array();
				preg_match('/[^a-z]'.$varname.'=.+\&'.'/U', $parsedUrl['query'], $kArray);
				if (empty($kArray[0]))
					preg_match('/[^a-z]'.$varname.'=.+$'.'/', $parsedUrl['query'], $kArray);
				if (empty($kArray[0]))
					return false;
				$kString = urldecode(str_replace('+', ' ', ltrim(substr(rtrim($kArray[0], '&'), strlen($varname) + 1), '=')));
				return $kString;
			}
		}
	}
}


