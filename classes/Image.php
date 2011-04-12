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

class ImageCore extends ObjectModel
{
	public		$id;

	/** @var integer Image ID */
	public $id_image;
	
	/** @var integer Product ID */
	public		$id_product;
	
	/** @var string HTML title and alt attributes */
	public		$legend;
	
	/** @var integer Position used to order images of the same product */
	public		$position;
	
	/** @var boolean Image is cover */
	public		$cover;

	protected $tables = array ('image', 'image_lang');
	
	protected	$fieldsRequired = array('id_product');
	protected 	$fieldsValidate = array('id_product' => 'isUnsignedId', 'position' => 'isUnsignedInt', 'cover' => 'isBool');
	protected 	$fieldsRequiredLang = array('legend');
	protected 	$fieldsSizeLang = array('legend' => 128);
	protected 	$fieldsValidateLang = array('legend' => 'isGenericName');
	
	protected 	$table = 'image';
	protected 	$identifier = 'id_image';	
	
	protected	static $_cacheGetSize = array();
	
	public function getFields()
	{
		parent::validateFields();
		$fields['id_product'] = (int)($this->id_product);
		$fields['position'] = (int)($this->position);
		$fields['cover'] = (int)($this->cover);
		return $fields;
	}
	
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('legend'));
	}
	
	public function delete()
	{
		parent::delete();
		$result = Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'image`
		WHERE `id_product` = '.(int)($this->id_product).'
		ORDER BY `position`');
		$i = 1;
		
		foreach ($result as $row)
		{
			$row['position'] = $i++;
			Db::getInstance()->AutoExecute(_DB_PREFIX_.$this->table, $row, 'UPDATE', '`id_image` = '.(int)($row['id_image']), 1);
		}
	}
		
	/**
	  * Return available images for a product
	  *
	  * @param integer $id_lang Language ID
	  * @param integer $id_product Product ID
	  * @return array Images
	  */
	static public function getImages($id_lang, $id_product)
	{
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'image` i
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON i.`id_image` = il.`id_image`
		WHERE i.`id_product` = '.(int)($id_product).'
		AND il.`id_lang` = '.(int)($id_lang).'
		ORDER BY `position` ASC');
	}
	
	/**
	  * Return Images
	  *
	  * @return array Images
	  */
	static public function getAllImages()
	{
		return Db::getInstance()->ExecuteS('
		SELECT `id_image`, `id_product`
		FROM `'._DB_PREFIX_.'image`
		ORDER BY `id_image` ASC');
	}
	
	/**
	  * Return number of images for a product
	  *
	  * @param integer $id_product Product ID
	  * @return integer number of images
	  */
	static public function getImagesTotal($id_product)
	{
		$result = Db::getInstance()->getRow('
		SELECT COUNT(`id_image`) AS total
		FROM `'._DB_PREFIX_.'image`
		WHERE `id_product` = '.(int)($id_product));
		return $result['total'];
	}
	
	/**
	  * Return highest position of images for a product
	  *
	  * @param integer $id_product Product ID
	  * @return integer highest position of images
	  */
	static public function getHighestPosition($id_product)
	{
		$result = Db::getInstance()->getRow('
		SELECT MAX(`position`) AS max
		FROM `'._DB_PREFIX_.'image`
		WHERE `id_product` = '.(int)($id_product));
		return $result['max'];
	}
	
	/**
	  * Delete product cover
	  *
	  * @param integer $id_product Product ID
	  * @return boolean result
	  */
	static public function deleteCover($id_product)
	{
	 	if (!Validate::isUnsignedId($id_product))
	 		die(Tools::displayError());
			
		if (file_exists(_PS_TMP_IMG_DIR_.'product_'.$id_product.'.jpg'))
			unlink(_PS_TMP_IMG_DIR_.'product_'.$id_product.'.jpg');
		return Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'image` 
		SET `cover` = 0 
		WHERE `id_product` = '.(int)($id_product));
	}
	
	/**
	  *Get product cover
	  *
	  * @param integer $id_product Product ID
	  * @return boolean result
	  */
	static public function getCover($id_product)
	{
		return Db::getInstance()->getRow('
		SELECT * FROM `'._DB_PREFIX_.'image` 
		WHERE `id_product` = '.(int)($id_product).'
		AND `cover`= 1');
	}
	
	/**
	  * Copy images from a product to another
	  *
	  * @param integer $id_product_old Source product ID
	  * @param boolean $id_product_new Destination product ID
	  */
	static public function duplicateProductImages($id_product_old, $id_product_new, $combinationImages)
	{
		$imagesTypes = ImageType::getImagesTypes('products');
		$result = Db::getInstance()->ExecuteS('
		SELECT `id_image`
		FROM `'._DB_PREFIX_.'image`
		WHERE `id_product` = '.(int)($id_product_old));
		foreach ($result as $row)
		{
			$image = new Image($row['id_image']);
			$saved_id = $image->id_image;
			unset($image->id);
			unset($image->id_image);
			$image->id_product = (int)($id_product_new);
			if ($image->add())
            {
				foreach ($imagesTypes AS $k => $imageType)
					if (file_exists(_PS_PROD_IMG_DIR_.(int)($id_product_old).'-'.(int)($row['id_image']).'-'.$imageType['name'].'.jpg'))
						copy(_PS_PROD_IMG_DIR_.(int)($id_product_old).'-'.(int)($row['id_image']).'-'.$imageType['name'].'.jpg', _PS_PROD_IMG_DIR_.
						(int)($id_product_new).'-'.(int)($image->id).'-'.$imageType['name'].'.jpg');
                if (file_exists(_PS_PROD_IMG_DIR_.(int)($id_product_old).'-'.(int)($row['id_image']).'.jpg'))
                    copy(_PS_PROD_IMG_DIR_.(int)($id_product_old).'-'.(int)($row['id_image']).'.jpg',
                            _PS_PROD_IMG_DIR_.(int)($id_product_new).'-'.(int)($image->id).'.jpg');
				self::replaceAttributeImageAssociationId($combinationImages, (int)($saved_id), (int)($image->id));
            }
			else
				return false;
		}
		return self::duplicateAttributeImageAssociations($combinationImages);
	}

	static protected function replaceAttributeImageAssociationId(&$combinationImages, $saved_id, $id_image)
	{
		if (!isset($combinationImages['new']) OR !is_array($combinationImages['new']))
			return ;
		foreach ($combinationImages['new'] AS $id_product_attribute => $imageIds)
			foreach ($imageIds AS $key => $imageId)
				if ((int)($imageId) == (int)($saved_id))
					$combinationImages['new'][$id_product_attribute][$key] = (int)($id_image);
	}

	/**
	* Duplicate product attribute image associations
	* @param integer $id_product_attribute_old
	* @return boolean
	*/
	static public function duplicateAttributeImageAssociations($combinationImages)
	{
		if (!isset($combinationImages['new']) OR !is_array($combinationImages['new']))
			return true;
		$query = 'INSERT INTO `'._DB_PREFIX_.'product_attribute_image` (`id_product_attribute`, `id_image`) VALUES ';
		foreach ($combinationImages['new'] AS $id_product_attribute => $imageIds)
			foreach ($imageIds AS $imageId)
				$query .= '('.(int)($id_product_attribute).', '.(int)($imageId).'), ';
		$query = rtrim($query, ', ');
		return DB::getInstance()->Execute($query);
	}

	/**
	  * Reposition image
	  *
	  * @param integer $position Position
	  * @param boolean $direction Direction
	  */
	public function	positionImage($position, $direction)
	{
		$position = (int)($position);
		$direction = (int)($direction);
		
		// temporary position
		$high_position = Image::getHighestPosition($this->id_product) + 1;
		
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'image`
		SET `position` = '.(int)($high_position).'
		WHERE `id_product` = '.(int)($this->id_product).'
		AND `position` = '.($direction ? $position - 1 : $position + 1));
		
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'image`
		SET `position` = `position`'.($direction ? '-1' : '+1').'
		WHERE `id_image` = '.(int)($this->id));
		
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'image`
		SET `position` = '.$this->position.'
		WHERE `id_product` = '.(int)($this->id_product).'
		AND `position` = '.(int)($high_position));
	}
	
	static public function getSize($type)
	{
		if (!isset(self::$_cacheGetSize[$type]) OR self::$_cacheGetSize[$type] === NULL)
			self::$_cacheGetSize[$type] = Db::getInstance()->getRow('SELECT `width`, `height` FROM '._DB_PREFIX_.'image_type WHERE `name` = \''.pSQL($type).'\'');
	 	return self::$_cacheGetSize[$type];
	}
	
	/**
	  * Clear all images in tmp dir
	  */
	static public function clearTmpDir()
	{
		foreach (scandir(_PS_TMP_IMG_DIR_) AS $d)
			if (preg_match('/(.*)\.jpg$/', $d))
				unlink(_PS_TMP_IMG_DIR_.$d);
	}
	
}


