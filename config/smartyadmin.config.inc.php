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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
global $smarty;
$smarty->template_dir = _PS_ADMIN_DIR_.'/themes/template';

function smartyTranslate($params, &$smarty)
{
	global $_LANGADM;
	$htmlentities = !isset($params['js']);
	$addslashes = !isset($params['slashes']);

	$string = str_replace('\'', '\\\'', $params['s']);
	$filename = ((!isset($smarty->compiler_object) OR !is_object($smarty->compiler_object->template)) ? $smarty->template_resource : $smarty->compiler_object->template->getTemplateFilepath());
	$class = Tools::substr(basename($filename), 0, -4);//.'_'.md5($string);

	if(in_array($class, array('header','footer','password','login')))
		$class = 'index';

	return AdminController::translate($string, $class, $addslashes, $htmlentities);
}
