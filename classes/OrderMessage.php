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

class OrderMessageCore extends ObjectModel
{
	/** @var string name name */
	public 		$name;
	
	/** @var string message content */
	public 		$message;
	
	/** @var string Object creation date */
	public 		$date_add;
	
	protected	$fieldsRequired = array();
	protected	$fieldsValidate = array();
	protected   $fieldsSize = array();

	protected	$fieldsRequiredLang = array('name', 'message');
	protected	$fieldsSizeLang = array('name' => 128, 'message' => 1200);
	protected	$fieldsValidateLang = array('name' => 'isGenericName', 'message' => 'isMessage');
	
	protected 	$table = 'order_message';
	protected 	$identifier = 'id_order_message';
	
	protected	$webserviceParameters = array(
			'fields' => array(
			'id' => array('sqlId' => 'id_discount_type', 'xlink_resource' => 'order_message_lang'),
			'date_add' => array('sqlId' => 'date_add')
		)
	);
	
	
	public function getFields()
	{
		parent::validateFields();
		return array('date_add' => pSQL($this->date_add));
	}

	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('name', 'message'));
	}

	static public function getOrderMessages($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT om.id_order_message, oml.name, oml.message
		FROM '._DB_PREFIX_.'order_message om
		LEFT JOIN '._DB_PREFIX_.'order_message_lang oml ON (oml.id_order_message = om.id_order_message)
		WHERE oml.id_lang = '.(int)($id_lang).'
		ORDER BY name ASC');
	}
}
