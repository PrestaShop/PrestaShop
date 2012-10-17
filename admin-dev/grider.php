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

define('_PS_ADMIN_DIR_', getcwd());
include_once(dirname(__FILE__).'/../config/config.inc.php');

$module = Tools::getValue('module');
$render = Tools::getValue('render');
$type = Tools::getValue('type');
$option = Tools::getValue('option');
$width = (int)(Tools::getValue('width', 600));
$height = (int)(Tools::getValue('height', 920));
$start = (int)(Tools::getValue('start', 0));
$limit = (int)(Tools::getValue('limit', 40));
$sort = Tools::getValue('sort', 0); // Should be a String. Default value is an Integer because we don't know what can be the name of the column to sort.
$dir = Tools::getValue('dir', 0); // Should be a String : Either ASC or DESC
$id_employee = (int)(Tools::getValue('id_employee'));
$id_lang = (int)(Tools::getValue('id_lang'));


if (!isset($cookie->id_employee) || !$cookie->id_employee  || $cookie->id_employee != $id_employee)
    die(Tools::displayError());

if (!Validate::isModuleName($module))
	die(Tools::displayError());

if (!Tools::file_exists_cache($module_path = dirname(__FILE__).'/../modules/'.$module.'/'.$module.'.php'))
	die(Tools::displayError());

require_once($module_path);

$grid = new $module();
$grid->setEmployee($id_employee);
$grid->setLang($id_lang);
if ($option)
	$grid->setOption($option);
	
$grid->create($render, $type, $width, $height, $start, $limit, $sort, $dir);
$grid->render();


