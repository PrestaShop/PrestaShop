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
*  @version  Release: $Revision: 13573 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function p15018_change_image_types()
{
	$replace_types = array(
		'small' => 'small_default',
		'medium' => 'medium_default',
		'large' => 'large_default',
		'thickbox' => 'thickbox_default',
		'category' => 'category_default',
		'home' => 'home_default',
		'large_scene' => 'scene_default',
		'thumb_scene' => 'm_scene_default'		
	);
	
	$option = false;
	if (Db::getInstance()->getValue('SELECT id_theme FROM `'._DB_PREFIX_.'theme` WHERE directory != "default"'))
		$option = true;

	// If there is another theme than the default one, duplicate
	if ($option)
		foreach ($replace_types as $old_type => $new_type)
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'image_type` (
				SELECT "'.$new_type.'", width, height, products, categories, manufacturers, suppliers, scenes, stores
				FROM `'._DB_PREFIX_.'image_type` WHERE name = "'.$old_type.'" LIMIT 1');
	// But if there is only the default one, we can update de names
	else
		foreach ($replace_types as $old_type => $new_type)
			Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'image_type` SET name = "'.$new_type.'" WHERE name = "'.$old_type.'"');

	// If there is less than 500 images, copy to the new format (if there is more, the merchant will have to click "regenerate thumbnails")
	$result = Db::getInstance()->executeS('SELECT id_image, id_product FROM `'._DB_PREFIX_.'image`');
	if (Db::getInstance()->numRows() < 500)
	{
		if (!defined('_PS_ROOT_DIR_'))
			define('_PS_ROOT_DIR_', realpath(INSTALL_PATH.'/../'));
		foreach ($result as $row)
		{
			if (file_exists(_PS_ROOT_DIR_.'img/p/'.$row['id_product'].'-'.$row['id_image'].'.jpg'))
				foreach ($replace_types as $old_type => $new_type)
					if ($option)
						@copy(_PS_ROOT_DIR_.'img/p/'.$row['id_product'].'-'.$row['id_image'].'-'.$old_type.'.jpg', _PS_ROOT_DIR_.'img/p/'.$row['id_product'].'-'.$row['id_image'].'-'.$new_type.'.jpg');
					else
						@rename(_PS_ROOT_DIR_.'img/p/'.$row['id_product'].'-'.$row['id_image'].'-'.$old_type.'.jpg', _PS_ROOT_DIR_.'img/p/'.$row['id_product'].'-'.$row['id_image'].'-'.$new_type.'.jpg');
			$folder = Image::getImgFolderStatic($row['id_image']);
			if (file_exists(_PS_ROOT_DIR_.'img/p/'.$folder.$row['id_image'].'.jpg'))
				foreach ($replace_types as $old_type => $new_type)
					if ($option)
						@copy(_PS_ROOT_DIR_.'img/p/'.$folder.$row['id_image'].'-'.$old_type.'.jpg', _PS_ROOT_DIR_.'img/p/'.$folder.$row['id_image'].'-'.$new_type.'.jpg');
					else
						@rename(_PS_ROOT_DIR_.'img/p/'.$folder.$row['id_image'].'-'.$old_type.'.jpg', _PS_ROOT_DIR_.'img/p/'.$folder.$row['id_image'].'-'.$new_type.'.jpg');
		}
	}

	return true;
}