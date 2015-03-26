<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CategoryThumbLink extends ObjectModel
{
	/** @var int id_thumb_link */
	public $id_thumb_link;

	/** @var string name */
	public $name;

	/** @var string link */
	public $link;

	/** @var int id_category */
	public $id_category;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'category_thumb_link',
		'primary' => 'id_thumb_link',
		'fields' => array(
			'name' => 			array('type' => self::TYPE_STRING, 'validate' => 'isFilename', 'required' => true, 'size' => 80),
			'link' => 			array('type' => self::TYPE_STRING, 'validate' => 'isAbsoluteUrl'),
			'id_category' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
		),
	);


	/**
	 * Get Thumbnail link from given name
	 * @param $filename
	 * @return array|bool|null|object
	 */
	public static function getLinkFromFilename($filename)
	{
		$sql = '
			SELECT `link`
			FROM `'._DB_PREFIX_.self::$definition['table'].'`
			WHERE `name` = "'.pSQL($filename).'"';

		return Db::getInstance()->getRow($sql);
	}

	/**
	 * Get all Thumbnails names that have link associations
	 * @return array|false
	 * @throws PrestaShopDatabaseException
	 */
	public static function getAllThumbnailsNames()
	{
		$sql = '
			SELECT `name`
			FROM `'._DB_PREFIX_.self::$definition['table'].'`';

		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Update Thumbnail link from given name
	 * @param $name
	 * @param $link
	 * @return bool
	 */
	public static function setThumbnailLinkFromName($name, $link)
	{
		$sql = '
			UPDATE `'._DB_PREFIX_.self::$definition['table'].'`
			SET `link` = "'.pSQL($link).'"
			WHERE `name` = "'.pSQL($name).'"';

		return (bool)Db::getInstance()->execute($sql);
	}

	/**
	 * Remove Thumbnail's link form given name
	 * @param $name
	 * @return bool
	 */
	public static function removeThumbnailLinkFromName($name)
	{
		$sql = 'UPDATE `'._DB_PREFIX_.self::$definition['table'].'`
				SET `link` = NULL
				WHERE `name` = "'.pSQL($name).'"';

		return (bool)Db::getInstance()->execute($sql);
	}

	/**
	 * Get Thumbnail's ID from given name
	 * @param $name
	 * @return bool
	 */
	public static function getIdFromName($name)
	{
		$sql = '
			SELECT `id_thumb_link`
			FROM `'._DB_PREFIX_.self::$definition['table'].'` WHERE `name` = "'.pSQL($name).'"';

		return Db::getInstance()->getRow($sql);
	}

	/**
	 * Delete all Thumbnails links associated to a given id_category
	 * @param $id_category
	 * @return bool
	 */
	public static function deleteThumbnailsLinksFromIdCategory($id_category)
	{
		$sql = 'DELETE FROM `'._DB_PREFIX_.self::$definition['table'].'` WHERE `id_category` = '.(int)$id_category;

		return (bool)Db::getInstance()->execute($sql);
	}

	public static function deleteThumbnailLinkFromId($id_thumb_link)
	{
		var_dump($id_thumb_link);
		$sql = 'DELETE FROM `'._DB_PREFIX_.self::$definition['table'].'` WHERE `id_thumb_link` = '.(int)$id_thumb_link;

		var_dump($sql);

		return (bool)Db::getInstance()->execute($sql);
	}

	public function add($autodate = true, $nullValues = false)
	{
		return parent::add($autodate, true);
	}

	public function update($nullValues = false)
	{
		return parent::update(true);
	}
}