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

class SceneCore extends ObjectModel
{
 	/** @var string Name */
	public 		$name;
	
	/** @var boolean Active Scene */
	public 		$active = true;
	
	/** @var array Products */
	public 		$products;
		
	protected 	$table = 'scene';
	protected 	$identifier = 'id_scene';

 	protected 	$fieldsRequired = array('active');
 	protected 	$fieldsValidate = array('active' => 'isBool');
 	protected 	$fieldsRequiredLang = array('name');
 	protected 	$fieldsSizeLang = array('name' => 100);
 	protected 	$fieldsValidateLang = array('name' => 'isGenericName');
 	
 	public function __construct($id = NULL, $id_lang = NULL, $liteResult = true, $hideScenePosition = false)
	{
		parent::__construct((int)($id), (int)($id_lang));
		
		if (!$liteResult)
			$this->products = $this->getProducts(true, (int)($id_lang), false);		
		if ($hideScenePosition)
			$this->name = Scene::hideScenePosition($this->name);
	}
	
	public function getFields()
	{
		parent::validateFields();
		$fields['active'] = (int)($this->active);
		return $fields;
	}
	
	/**
  * Check then return multilingual fields for database interaction
  *
  * @return array Multilingual fields
  */
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('name'));
	}	
	
	public function update($nullValues = false)
	{
		if (!$this->updateZoneProducts())
			return false;
		if (!$this->updateCategories())
			return false;
		return parent::update($nullValues);
	}
	
	public function add($autodate = true, $nullValues = false)
	{
		$zones = Tools::getValue('zones');
		if ($zones)
			$this->addZoneProducts($zones);
		$categories = Tools::getValue('categoryBox');
		if ($categories)
			$this->addCategories($categories);
		
		return parent::add($autodate, $nullValues);
	}
	
	public function delete()
	{
		$this->deleteZoneProducts();
		$this->deleteCategories();
		return parent::delete();
	}
	
	public function addCategories($categories)
	{
		$result = true;
		foreach ($categories AS $category)
		{
			if (!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'scene_category` ( `id_scene` , `id_category`) VALUES ('.(int)($this->id).', '.(int)($category).')'))
				$result = false;
		}
		return $result;
	}
	
	public function deleteCategories()
	{
		return Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'scene_category` 
		WHERE `id_scene` = '.(int)($this->id));
	}
	
	public function updateCategories()
	{
		
		if (!$this->deleteCategories())
			return false;
		$categories = Tools::getValue('categoryBox');
		if ($categories AND !$this->addCategories($categories))
				return false;
		return true;
	}
	
	public function addZoneProducts($zones)
	{
		$result = true;
		foreach ($zones AS $zone)
		{
			$sql = 'INSERT INTO `'._DB_PREFIX_.'scene_products` ( `id_scene` , `id_product` , `x_axis` , `y_axis` , `zone_width` , `zone_height`) VALUES
				 ('.(int)($this->id).', '.(int)($zone['id_product']).', '.(int)($zone['x1']).', '.(int)($zone['y1']).', '.(int)($zone['width']).', '.(int)($zone['height']).')';
			if (!Db::getInstance()->Execute($sql))
				$result = false;
		}
		return $result;
	}
	
	public function deleteZoneProducts()
	{
		return Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'scene_products`
		WHERE `id_scene` = '.(int)($this->id));
	}
	
	public function updateZoneProducts()
	{
		if (!$this->deleteZoneProducts())
			return false;
		$zones = Tools::getValue('zones');
		if ($zones AND !$this->addZoneProducts($zones))
			return false;
		return true;
	}
	
	/**
	* Get all scenes of a category
	*
	* @return array Products
	*/
	static public function getScenes($id_category, $id_lang = NULL, $onlyActive = true, $liteResult = true, $hideScenePosition = true)
	{
		$id_lang = is_null($id_lang) ? _USER_ID_LANG_ : (int)($id_lang);

		$scenes = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT s.*
		FROM `'._DB_PREFIX_.'scene_category` sc
		LEFT JOIN `'._DB_PREFIX_.'scene` s ON (sc.id_scene = s.id_scene)
		LEFT JOIN `'._DB_PREFIX_.'scene_lang` sl ON (sl.id_scene = s.id_scene)
		WHERE sc.id_category = '.(int)($id_category).'	AND sl.id_lang = '.(int)($id_lang).($onlyActive ? ' AND s.active = 1' : '').'
		ORDER BY sl.name ASC');
		
		if (!$liteResult AND $scenes)
			foreach($scenes AS &$scene)
				$scene = new Scene((int)($scene['id_scene']), (int)($id_lang), false, $hideScenePosition);
		return $scenes;
	}
	
	/**
	* Get all products of this scene
	*
	* @return array Products
	*/
	public function getProducts($onlyActive = true, $id_lang = NULL, $liteResult = true)
	{
		global $link;
		
		$id_lang = is_null($id_lang) ? _USER_ID_LANG_ : (int)($id_lang);
		
		$products = Db::getInstance()->ExecuteS('
		SELECT s.*
		FROM `'._DB_PREFIX_.'scene_products` s
		LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = s.id_product)
		WHERE s.id_scene = '.(int)($this->id).($onlyActive ? ' AND p.active = 1' : ''));
		
		if (!$liteResult AND $products)
			foreach ($products AS &$product)
			{
				$product['details'] = new Product((int)($product['id_product']), !$liteResult, (int)($id_lang));
				$product['link'] = $link->getProductLink((int)($product['details']->id), $product['details']->link_rewrite, $product['details']->category, $product['details']->ean13);
				$cover = Product::getCover((int)($product['details']->id));
				if(is_array($cover))
					$product = array_merge($cover, $product);
			}
		
		return $products;
	}
	
	/**
	* Get categories where scene is indexed
	*
	* @param integer $id_scene Scene id
	* @return array Categories where scene is indexed
	*/
	static public function getIndexedCategories($id_scene)
	{
		return Db::getInstance()->ExecuteS('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'scene_category`
		WHERE `id_scene` = '.(int)($id_scene));
	}
	
	/**
	  * Hide scene prefix used for position
	  *
	  * @param string $name Scene name
	  * @return string Name without position
	  */
	static public function hideScenePosition($name)
	{
		return preg_replace('/^[0-9]+\./', '', $name);
	}
	
}


