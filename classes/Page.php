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
*  @version  Release: $Revision: 6946 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PageCore extends ObjectModel
{
	public $id_page_type;
	public $id_object;

	public $name;

	protected $fieldsRequired = array ('id_page_type');
	protected $fieldsValidate = array ('id_page_type' => 'isUnsignedId', 'id_object' => 'isUnsignedId');

	protected $table = 'page';
	protected $identifier = 'id_page';

	public function getFields()
	{
		$this->validateFields();
		$fields['id_page_type'] = (int)($this->id_page_type);
		$fields['id_object'] = (int)($this->id_object);
		return $fields;
	}

	/**
	 * @return int Current page ID
	 */
	public static function getCurrentId()
	{
		$controller = Dispatcher::getInstance()->getController();
		$pageTypeID = Page::getPageTypeByName($controller);

		// Some pages must be distinguished in order to record exactly what is being seen
		// @todo dispatcher module
		$specialArray = array(
			'product' => 'id_product',
			'category' => 'id_category',
			'order' => 'step',
			'manufacturer' => 'id_manufacturer',
		);

		$where = '';
		$insertData = array(
			'id_page_type' =>	$pageTypeID,
		);

		if (array_key_exists($controller, $specialArray))
		{
			$objectID = Tools::getValue($specialArray[$controller], null);
			$where = ' AND `id_object` = '.(int)$objectID;
			$insertData['id_object'] = $objectID;
		}

		$sql = 'SELECT `id_page`
				FROM `'._DB_PREFIX_.'page`
				WHERE `id_page_type` = '.(int)$pageTypeID.$where;
		$result = Db::getInstance()->getRow($sql);
		if ($result['id_page'])
			return $result['id_page'];

		Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'page', $insertData, 'INSERT');
		return Db::getInstance()->Insert_ID();
	}

	/**
	 * Return page type ID from page name
	 *
	 * @param string $name Page name (E.g. product.php)
	 */
	public static function getPageTypeByName($name)
	{
		$sql = 'SELECT id_page_type
				FROM '._DB_PREFIX_.'page_type
				WHERE name = \''.pSQL($name).'\'';
		if ($value = Db::getInstance()->getValue($sql))
			return $value;

		Db::getInstance()->autoExecute(_DB_PREFIX_.'page_type', array(
			'name' =>	$name,
		), 'INSERT');
		return Db::getInstance()->Insert_ID();
	}

	public static function setPageViewed($id_page)
	{
		$id_date_range = DateRange::getCurrentRange();
		$context = Context::getContext();

		// Try to increment the visits counter
		$sql = 'UPDATE `'._DB_PREFIX_.'page_viewed`
				SET `counter` = `counter` + 1
				WHERE `id_date_range` = '.(int)$id_date_range.'
					AND `id_page` = '.(int)$id_page.'
					AND `id_shop` = '.$context->shop->getID();
		Db::getInstance()->execute($sql);

		// If no one has seen the page in this date range, it is added
		if (Db::getInstance()->Affected_Rows() == 0)
			Db::getInstance()->autoExecute(_DB_PREFIX_.'page_viewed', array(
				'id_date_range' =>	(int)$id_date_range,
				'id_page' =>		(int)$id_page,
				'counter' =>		1,
				'id_shop' =>		$context->shop->getID(),
				'id_group_shop' =>	$context->shop->getGroupID(),
			), 'INSERT');
	}
}