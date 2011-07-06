<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once(realpath(dirname(__FILE__).'/../mondialrelay.php'));

class MRManagement extends MondialRelay
{
	private $_params = array();
	
	private $_resultList = array(
		'error' => array(),
		'success' => array());
	
	public function __construct($params)
	{
		$this->_params = $params;
		
		parent::__construct();
	}
	
	public function __destruct()
	{
		
	}
	
	static public function replaceAccentedCharacters($string)
	{
		$currentLocale = setlocale(LC_ALL, NULL);
		setlocale(LC_ALL, 'en_US.UTF8');
		$cleanedString = iconv('UTF-8','ASCII//TRANSLIT', $string);
		setLocale(LC_ALL, $currentLocale);
		return $cleanedString;
	}
	
	/*
	** Retro compatibility for 1.3
	** This method fill the database with the selected carrier
	*/
	public function addSelectedCarrierToDB()
	{
		$this->hookProcessCarrier($this->_params, false);
	}

	public function DeleteHistory()
	{
		$success = array();
		$error = array();
		
		if (is_array($this->_params['historyIdList']) && count($this->_params['historyIdList']))
		{
			$query = '
				DELETE FROM `'._DB_PREFIX_.'mr_historique`
				WHERE id IN(';
			foreach($this->_params['historyIdList'] as $id)
				$query .= (int)$id.', ';
			$query = trim($query, ', ').')';
			
			$success['deletedListId'] = $this->_params['historyIdList'];
			$totalDeleted = Db::getInstance()->Execute($query);
			if (count($success['deletedListId']) != $totalDeleted)
			{
				$error[] = $this->l('Some items can\'t be removed, please try to remove it again');
				foreach($success['deletedListId'] as $id)
				{
					$query = '
						SELECT id FROM `'._DB_PREFIX_.'mr_historique`
						WHERE id='.(int)$id;
					if (Db::getInstance()->getRow($query) && 
							($key = array_search($id, $success['deletedListId'])) !== FALSE)
						unset($success['deletedListId'][$key]);
				}
			}
			$this->_resultList['success'] = $success;
			$this->_resultList['other']['error'] = $error; 
		}
		else
			throw new Exception($this->l('Please select at least one history element'));
		return $this->_resultList;
	}
}
?>
