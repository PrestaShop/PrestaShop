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

function reorderpositions()
{
	/* Clean products positions */
	if ($cat = Category::getCategories(1, false, false))
		foreach($cat AS $i => $categ)
			Product::cleanPositions((int)$categ['id_category']);
	
	//clean Category position and delete old position system
	Language::loadLanguages();
	$language = Language::getLanguages();
	$cat_parent = Db::getInstance()->ExecuteS('SELECT DISTINCT c.id_parent FROM `'._DB_PREFIX_.'category` c WHERE id_category != 1');
	foreach($cat_parent AS $parent)
	{
		$result = Db::getInstance()->ExecuteS('
							SELECT DISTINCT c.*, cl.*
							FROM `'._DB_PREFIX_.'category` c 
							LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)(Configuration::get('PS_LANG_DEFAULT')).')
							WHERE c.id_parent = '.(int)($parent['id_parent']).'
							ORDER BY name ASC');
		foreach($result AS $i => $categ)
		{
			$sizeof = sizeof($result);
			for ($i = 0; $i < $sizeof; ++$i)
			{
				Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'category`
				SET `position` = '.(int)($i).'
				WHERE `id_parent` = '.(int)($categ['id_parent']).'
				AND `id_category` = '.(int)($result[$i]['id_category']));
			}
		
			foreach($language AS $lang)
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'category` c 
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`)  
				SET `name` = \''.preg_replace('/^[0-9]+\./', '',$categ['name']).'\' 
				WHERE c.id_category = '.(int)($categ['id_category']).' AND id_lang = \''.(int)($lang['id_lang']).'\'');
		}
	}
	
	/* Clean CMS positions */
	if ($cms_cat = CMSCategory::getCategories(1, false, false))
		foreach($cms_cat AS $i => $categ)
			CMS::cleanPositions((int)($categ['id_cms_category']));
}