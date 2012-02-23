<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ImageTypeCore extends ObjectModel
{
	public $id;
	
	/** @var string Name */
	public $name;

	/** @var integer Width */
	public $width;

	/** @var integer Height */
	public $height;

	/** @var boolean Apply to products */
	public $products;

	/** @var integer Apply to categories */
	public $categories;

	/** @var integer Apply to manufacturers */
	public $manufacturers;

	/** @var integer Apply to suppliers */
	public $suppliers;

	/** @var integer Apply to scenes */
	public $scenes;

	/** @var integer Apply to store */
	public $stores;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'image_type',
		'primary' => 'id_image_type',
		'fields' => array(
			'name' => 			array('type' => self::TYPE_STRING, 'validate' => 'isImageTypeName', 'required' => true, 'size' => 16),
			'width' => 			array('type' => self::TYPE_INT, 'validate' => 'isImageSize', 'required' => true),
			'height' => 		array('type' => self::TYPE_INT, 'validate' => 'isImageSize', 'required' => true),
			'categories' => 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'products' => 		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'manufacturers' => 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'suppliers' => 		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'scenes' => 		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'stores' => 		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
		),
	);

	/**
	 * @var array Image types cache
	 */
	protected static $images_types_cache = array();

	protected $webserviceParameters = array();

	/**
	* Returns image type definitions
	*
	* @param string|null Image type
	* @return array Image type definitions
	*/
	public static function getImagesTypes($type = null, $id_theme = false)
	{
		if (!isset(self::$images_types_cache[$type.($id_theme ? '-'.$id_theme : '')]))
		{
			$where = 'WHERE 1';
			if ($id_theme)
				$where .= ' AND id_theme='.(int)$id_theme;
			if (!empty($type))
				$where .= ' AND '.pSQL($type).' = 1 ';

			$query = 'SELECT * FROM `'._DB_PREFIX_.'image_type`'.$where.' ORDER BY `name` ASC';
			self::$images_types_cache[$type] = Db::getInstance()->executeS($query);
		}

		return self::$images_types_cache[$type];
	}

	/**
	* Check if type already is already registered in database
	*
	* @param string $typeName Name
	* @return integer Number of results found
	*/
	public static function typeAlreadyExists($typeName)
	{
		if (!Validate::isImageTypeName($typeName))
			die(Tools::displayError());

		Db::getInstance()->executeS('
		SELECT `id_image_type`
		FROM `'._DB_PREFIX_.'image_type`
		WHERE `name` = \''.pSQL($typeName).'\'');

		return Db::getInstance()->NumRows();
	}

	/**
	 * Finds image type definition by name and type
	 * @param string $name
	 * @param string $type
	 */
	public static function getByNameNType($name, $type)
	{
		return Db::getInstance()->getRow('SELECT `id_image_type`, `name`, `width`, `height`, `products`, `categories`, `manufacturers`, `suppliers`, `scenes` FROM `'._DB_PREFIX_.'image_type` WHERE `name` = \''.pSQL($name).'\' AND `'.pSQL($type).'` = 1');
	}

}
