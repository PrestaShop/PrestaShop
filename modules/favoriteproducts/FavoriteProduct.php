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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class FavoriteProduct extends ObjectModel
{
	public		$id;

	public 		$id_product;

	public 		$id_customer;

	public 		$id_shop;

	public 		$date_add;

	public 		$date_upd;

	protected 	$fieldRequired = array(
		'id_product',
		'id_customer',
		'id_shop'
	);

	protected 	$fieldsValidate = array(
		'id_product' => 'isUnsignedInt',
		'id_customer' => 'isUnsignedInt',
		'id_shop' => 'isUnsignedInt'
	);

	protected $table = 'favorite_product';

	protected $identifier = 'id_favorite_product';

	public function getFields()
	{
		$this->validateFields();

		$fields['id_product'] = (int)$this->id_product;
		$fields['id_customer'] = (int)$this->id_customer;
		$fields['id_shop'] = (int)$this->id_shop;

		return $fields;
	}

	public static function getFavoriteProducts($id_customer, $id_lang, Shop $shop = null)
	{
		if (!$shop)
			$shop = Context::getContext()->shop;

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT fp.`id_shop`, p.`id_product`, pl.`description_short`, pl.`link_rewrite`, pl.`name`, i.`id_image`, CONCAT(p.`id_product`, \'-\', i.`id_image`) as image
		FROM `'._DB_PREFIX_.'favorite_product` fp
		LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = fp.`id_product`)
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.$shop->addSqlRestrictionOnLang('pl').')
		LEFT OUTER JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product` AND `default_on` = 1)
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)($id_lang).')
		WHERE p.`active` = 1
			'.$shop->addSqlRestriction(false, 'fp'));
	}

	public static function getFavoriteProduct($id_customer, $id_product, Shop $shop = null)
	{
		if (!$shop)
			$shop = Context::getContext()->shop;

		$id_favorite_product =  Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_favorite_product`
			FROM `'._DB_PREFIX_.'favorite_product`
			WHERE `id_customer` = '.(int)($id_customer).'
			AND `id_product` = '.(int)($id_product).'
			AND `id_shop` = '.(int)($shop->getID()));

		if ($id_favorite_product)
			return new FavoriteProduct($id_favorite_product);
		return null;
	}

	public static function isCustomerFavoriteProduct($id_customer, $id_product, Shop $shop = null)
	{
		if (!$shop)
			$shop = Context::getContext()->shop;

		return (bool)Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'favorite_product`
			WHERE `id_customer` = '.(int)($id_customer).'
			AND `id_product` = '.(int)($id_product).'
			AND `id_shop` = '.(int)($shop->getID()));
	}
}