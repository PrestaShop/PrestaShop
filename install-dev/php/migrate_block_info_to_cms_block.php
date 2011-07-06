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
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function migrate_block_info_to_cms_block()
{	
	//get ids cms of block information
	$id_blockinfos = Db::getInstance()->getValue('SELECT id_module FROM  `'._DB_PREFIX_.'module` WHERE name = \'blockinfos\'');
	//get ids cms of block information
	$ids_cms = Db::getInstance()->ExecuteS('SELECT * FROM  `'._DB_PREFIX_.'block_cms` WHERE `id_block` = '.(int)$id_blockinfos);
	//check if block info is installed and active
	if (sizeof($ids_cms))
	{
		//install module blockcms
		if (Module::getInstanceByName('blockcms')->install())
		{
			//add new block in new cms block
			Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'cms_block` (`id_cms_category`, `name`, `location`, `position`) VALUES( 1, \'\', 0, 0)');
			$id_block = Db::getInstance()->Insert_ID();
			
			$languages = Language::getLanguages(false);
			foreach($languages AS $language)
				Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'cms_block_lang` (`id_cms_block`, `id_lang`, `name`) VALUES ('.(int)$id_block.', '.(int)$language['id_lang'].', \'Information\')');
			
			//save ids cms of block information in new module cms bloc
			foreach($ids_cms AS $id_cms)
				Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'cms_block_page` (`id_cms_block`, `id_cms`, `is_category`) VALUES ('.(int)$id_block.', '.(int)$id_cms['id_cms'].', 0)');
		}
		else
			return true;
	}
	else
		return true;
}