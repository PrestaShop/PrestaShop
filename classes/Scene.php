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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SceneCore extends ObjectModel
{
 	/** @var string Name */
	public 		$name;

	/** @var boolean Active Scene */
	public 		$active = true;

	/** @var array Zone for image map */
	public		$zones = array();

	/** @var array list of category where this scene is available */
	public		$categories = array();

	/** @var array Products */
	public 		$products;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'scene',
		'primary' => 'id_scene',
		'multilang' => true,
		'fields' => array(
			'active' => 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),

			// Lang fields
			'name' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 100),
		),
	);

 	protected static $feature_active = null;

 	public function __construct($id = NULL, $id_lang = NULL, $liteResult = true, $hideScenePosition = false)
	{
		parent::__construct($id, $id_lang);

		if (!$liteResult)
			$this->products = $this->getProducts(true, (int)($id_lang), false);
		if ($hideScenePosition)
			$this->name = Scene::hideScenePosition($this->name);
		$this->image_dir = _PS_SCENE_IMG_DIR_;
	}

	public function update($nullValues = false)
	{
		if (!$this->updateZoneProducts())
			return false;
		if (!$this->updateCategories())
			return false;

		if (parent::update($nullValues))
		{
			// Refresh cache of feature detachable
			Configuration::updateGlobalValue('PS_SCENE_FEATURE_ACTIVE', self::isCurrentlyUsed($this->def['table'], true));
			return true;
		}
		return false;

	}

	public function add($autodate = true, $nullValues = false)
	{
		if (!empty($this->zones))
			$this->addZoneProducts($this->zones);
		if (!empty($this->categories))
			$this->addCategories($this->categories);

		if (parent::add($autodate, $nullValues))
		{
			// Put cache of feature detachable only if this new scene is active else we keep the old value
			if ($this->active)
				Configuration::updateGlobalValue('PS_SCENE_FEATURE_ACTIVE', '1');
			return true;
		}
		return false;
	}

	public function delete()
	{
		$this->deleteZoneProducts();
		$this->deleteCategories();
		if (parent::delete())
		{
			return $this->deleteImage() &&
				Configuration::updateGlobalValue('PS_SCENE_FEATURE_ACTIVE', self::isCurrentlyUsed($this->def['table'], true));
		}
		return false;
	}

	public function deleteImage()
	{
		if (parent::deleteImage())
		{
			if (file_exists($this->image_dir.'thumbs/'.$this->id.'-thumb_scene.'.$this->image_format)
				&& !unlink($this->image_dir.'thumbs/'.$this->id.'-thumb_scene.'.$this->image_format))
				return false;
		}
		else
			return false;
		return true;
	}

	public function addCategories($categories)
	{
		$result = true;
		foreach ($categories AS $category)
		{
			if (!Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'scene_category` ( `id_scene` , `id_category`) VALUES ('.(int)($this->id).', '.(int)($category).')'))
				$result = false;
		}
		return $result;
	}

	public function deleteCategories()
	{
		return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'scene_category`
		WHERE `id_scene` = '.(int)($this->id));
	}

	public function updateCategories()
	{
		if (!$this->deleteCategories())
			return false;
		if (!empty($this->categories) AND !$this->addCategories($this->categories))
				return false;
		return true;
	}

	public function addZoneProducts($zones)
	{
		$result = true;
		foreach ($zones AS $zone)
		{
			// @todo use multiple insert
			$sql = 'INSERT INTO `'._DB_PREFIX_.'scene_products` ( `id_scene` , `id_product` , `x_axis` , `y_axis` , `zone_width` , `zone_height`) VALUES
				 ('.(int)($this->id).', '.(int)($zone['id_product']).', '.(int)($zone['x1']).', '.(int)($zone['y1']).', '.(int)($zone['width']).', '.(int)($zone['height']).')';
			if (!Db::getInstance()->execute($sql))
				$result = false;
		}
		return $result;
	}

	public function deleteZoneProducts()
	{
		return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'scene_products`
		WHERE `id_scene` = '.(int)($this->id));
	}

	public function updateZoneProducts()
	{
		if (!$this->deleteZoneProducts())
			return false;
		if ($this->zones AND !$this->addZoneProducts($this->zones))
			return false;
		return true;
	}

	/**
	* Get all scenes of a category
	*
	* @return array Products
	*/
	public static function getScenes($id_category, $id_lang = NULL, $onlyActive = true, $liteResult = true, $hideScenePosition = true, Context $context = null)
	{
		if (!self::isFeatureActive())
			return array();

		if (!$context)
			$context = Context::getContext();
		$id_lang = is_null($id_lang) ? $context->language->id : $id_lang;

		$sql = 'SELECT s.*
				FROM `'._DB_PREFIX_.'scene_category` sc
				LEFT JOIN `'._DB_PREFIX_.'scene` s ON (sc.id_scene = s.id_scene)
				'.$context->shop->addSqlAssociation('scene', 's').'
				LEFT JOIN `'._DB_PREFIX_.'scene_lang` sl ON (sl.id_scene = s.id_scene)
				WHERE sc.id_category = '.(int)$id_category.'
					AND sl.id_lang = '.(int)$id_lang
					.($onlyActive ? ' AND s.active = 1' : '').'
				ORDER BY sl.name ASC';
		$scenes = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		if (!$liteResult AND $scenes)
			foreach($scenes AS &$scene)
				$scene = new Scene($scene['id_scene'], $id_lang, false, $hideScenePosition);
		return $scenes;
	}

	/**
	* Get all products of this scene
	*
	* @return array Products
	*/
	public function getProducts($onlyActive = true, $id_lang = NULL, $liteResult = true, Context $context = null)
	{
		if (!self::isFeatureActive())
			return array();

		if (!$context)
			$context = Context::getContext();
		$id_lang = is_null($id_lang) ? $context->language->id : $id_lang;

		$products = Db::getInstance()->executeS('
		SELECT s.*
		FROM `'._DB_PREFIX_.'scene_products` s
		LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = s.id_product)
		WHERE s.id_scene = '.(int)$this->id.($onlyActive ? ' AND p.active = 1' : ''));

		if (!$liteResult AND $products)
			foreach ($products AS &$product)
			{
				$product['details'] = new Product($product['id_product'], !$liteResult, $id_lang);
				$product['link'] = $context->link->getProductLink($product['details']->id, $product['details']->link_rewrite, $product['details']->category, $product['details']->ean13);
				$cover = Product::getCover($product['details']->id);
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
	public static function getIndexedCategories($id_scene)
	{
		return Db::getInstance()->executeS('
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
	public static function hideScenePosition($name)
	{
		return preg_replace('/^[0-9]+\./', '', $name);
	}

	/**
	 * This method is allow to know if a feature is used or active
	 * @since 1.5.0.1
	 * @return bool
	 */
	public static function isFeatureActive()
	{
		return Configuration::get('PS_SCENE_FEATURE_ACTIVE');
	}
}


