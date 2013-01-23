<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

global $smarty;
$smarty->debugging = false;
$smarty->debugging_ctrl = 'NONE';

/* Smarty should be in compile check mode in the BackOffice */
$smarty->force_compile = false;
$smarty->compile_check = true;

function smartyTranslate($params, &$smarty)
{
	$htmlentities = !isset($params['js']);
	$pdf = isset($params['pdf']);
	$addslashes = isset($params['slashes']);
	$sprintf = isset($params['sprintf']) ? $params['sprintf'] : false;

	if ($pdf)
		return Translate::getPdfTranslation($params['s']);

	$filename = ((!isset($smarty->compiler_object) || !is_object($smarty->compiler_object->template)) ? $smarty->template_resource : $smarty->compiler_object->template->getTemplateFilepath());

	// If the template is part of a module
	if (!empty($params['mod']))
		return Translate::getModuleTranslation($params['mod'], $params['s'], basename($filename, '.tpl'), $sprintf);

	// If the tpl is at the root of the template folder
	if (dirname($filename) == '.')
		$class = 'index';
	// If the tpl is used by a Helper
	elseif (strpos($filename, 'helpers') === 0)
		$class = 'Helper';
	// If the tpl is used by a Controller
	else
	{
		// Split by \ and / to get the folder tree for the file
		$folder_tree = preg_split('#[/\\\]#', $filename);
		$key = array_search('controllers', $folder_tree);

		// If there was a match, construct the class name using the child folder name
		// Eg. xxx/controllers/customers/xxx => AdminCustomers
		if ($key !== false)
			$class = 'Admin'.Tools::toCamelCase($folder_tree[$key + 1], true);
		elseif (isset($folder_tree[0]))
			$class = 'Admin'.Tools::toCamelCase($folder_tree[0], true);
		else
			$class = null;
	}

	return Translate::getAdminTranslation($params['s'], $class, $addslashes, $htmlentities, $sprintf);
}

