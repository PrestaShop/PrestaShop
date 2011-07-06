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
class Stock extends ObjectModel
{
	public $id_product;
	public $id_product_attribute;
	public $id_group_shop;
	public $id_shop;
	public $quantity;

	protected $fieldsRequired = array('id_shop', 'id_group_shop', 'id_product', 'id_product_attribute');
	protected $fieldsSize = array();
	protected $fieldsValidate = array();

	protected $table = 'stock';
	protected $identifier = 'id_stock';

	public function getFields()
	{
		parent::validateFields();
		$fields['id_product'] = (int)$this->id_product;
		$fields['id_product_attribute'] = (int)$this->id_product_attribute;
		$fields['id_group_shop'] = (int)$this->id_group_shop;
		$fields['id_shop'] = (int)$this->id_shop;
		$fields['quantity'] = (int)$this->quantity;
		return $fields;
	}

	public static function getStockId($id_product, $id_product_attribute, $shopID = null)
	{
		$sql = 'SELECT id_stock
				FROM '._DB_PREFIX_.'stock
				WHERE id_product = '.(int)$id_product.'
					AND id_product_attribute = '.(int)$id_product_attribute
					.Shop::sqlSharedStock('', $shopID);
		return (int)Db::getInstance()->getValue($sql);
	}
}