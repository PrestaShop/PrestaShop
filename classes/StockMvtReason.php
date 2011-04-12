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

class StockMvtReasonCore extends ObjectModel
{
	public		$id;
	public		$name;
	
	public		$sign;
	
	public		$date_add;
	public		$date_upd;
	
	protected	$table = 'stock_mvt_reason';
	protected 	$identifier = 'id_stock_mvt_reason';
	

 	protected 	$fieldsRequiredLang = array('name');
 	protected 	$fieldsSizeLang = array('name' => 255);
 	protected 	$fieldsValidateLang = array('name' => 'isGenericName');
	
	protected	$webserviceParameters = array(
		'objectNodeNames' => 'stock_movement_reasons',
	);
	
	public function getFields()
	{
		parent::validateFields();
		$fields['sign'] = (int)$this->sign;
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		return $fields;
	}
	
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('name'));
	}
	
	static public function getStockMvtReasons($id_lang)
	{
		return Db::getInstance()->ExecuteS('SELECT smrl.name, smr.id_stock_mvt_reason, smr.sign
														FROM '._DB_PREFIX_.'stock_mvt_reason smr
														LEFT JOIN '._DB_PREFIX_.'stock_mvt_reason_lang smrl ON (smr.id_stock_mvt_reason = smrl.id_stock_mvt_reason AND smrl.id_lang='.(int)$id_lang.')');
	}
}
