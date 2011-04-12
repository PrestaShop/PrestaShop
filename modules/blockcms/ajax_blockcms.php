<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('blockcms.php');

$blockcms = new BlockCms();
if (!Tools::isSubmit('secure_key') OR Tools::getValue('secure_key') != $blockcms->secure_key OR !Tools::isSubmit('action'))
	die(1);

if (Tools::getValue('action') == 'getCms')
{
	if (!Validate::isInt(Tools::getValue('id_cms_category')) OR !Tools::getValue('id_cms_category'))
		die(1);
	
	$cms_categories = Db::getInstance()->ExecuteS('
	SELECT * FROM `'._DB_PREFIX_.'cms_category` c
	JOIN `'._DB_PREFIX_.'cms_category_lang` cl ON (c.`id_cms_category` = cl.`id_cms_category`)
	WHERE c.`id_parent` = '.(int)(Tools::getValue('id_cms_category')).'
	AND cl.`id_lang` = '.(int)($cookie->id_lang).'
	ORDER BY c.`id_cms_category`');
	$cms_pages = Db::getInstance()->ExecuteS('
	SELECT cl.`meta_title`, c.`id_cms` FROM `'._DB_PREFIX_.'cms` c
	JOIN `'._DB_PREFIX_.'cms_lang` cl ON (c.`id_cms` = cl.`id_cms`)
	WHERE c.`id_cms_category` = '.(int)(Tools::getValue('id_cms_category')).'
	AND c.`active` = 1 
	AND cl.`id_lang` = '.(int)($cookie->id_lang).'
	ORDER BY c.`id_cms`');
	if (Tools::getValue('id_cms_block'))
		$cms_selected = Db::getInstance()->ExecuteS('
		SELECT `is_category`, `id_cms` FROM `'._DB_PREFIX_.'cms_block_page`
		WHERE `id_cms_block` = '.(int)(Tools::getValue('id_cms_block')));
	$html = '';

	if (sizeof($cms_categories) OR sizeof($cms_pages))
	{
		$html .= '<table width="100%" class="table" cellspacing="0" cellpadding="0">
				<thead>
				<tr>
					<th width="5%" class="center"><input type="checkbox" class="noborder" name="checkme" onclick="checkallCMSBoxes($(this).attr(\'checked\'))"></th>
					<th width="10%"><b>'.$blockcms->getL('ID').'</b></th>
					<th width="85%"><b>'.$blockcms->getL('Name').'</b></th>
				</tr>
				</thead>
				<tbody>
				';
		foreach ($cms_categories as $cms_category)
		{
			$html .= '
					<tr>
						<td width="5%" class="center"><input type="checkbox" class="noborder cmsBox" name="cmsBox[]" value="1_'.$cms_category['id_cms_category'].'" '.(in_array(array('id_cms' => $cms_category['id_cms_category'], 'is_category' => 1), $cms_selected) ? 'checked="checked"' : '').'></td>
						<td width="10%"><b>'.$cms_category['id_cms_category'].'</b></td>
						<td width="85%"><b>'.$cms_category['name'].'</b></td>
					</tr>';
		}
		foreach ($cms_pages as $cms_page)
		{
			$html .= '
					<tr>
						<td width="5%" class="center"><input type="checkbox" class="noborder cmsBox" name="cmsBox[]" value="0_'.$cms_page['id_cms'].'" '.(in_array(array('id_cms' => $cms_page['id_cms'], 'is_category' => 0), $cms_selected) ? 'checked="checked"' : '').'></td>
						<td width="10%">'.$cms_page['id_cms'].'</td>
						<td width="85%">'.$cms_page['meta_title'].'</td>
					</tr>';
		}
	}
	else
	{
		$html .= $blockcms->getL('There is nothing to display in this CMS category');
	}

	echo $html;
}
elseif (Tools::getValue('action') == 'dnd')
{
	if ($table = Tools::getValue('table_right') OR $table = Tools::getValue('table_left'));
	{
		$pos = 0;
		foreach ($table as $key =>$row)
		{
			$ids = explode('_', $row);
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'cms_block` SET `position` = '.(int)($pos).' WHERE `id_cms_block` = '.(int)($ids[2]));
			$pos++;
		}
	}
}


