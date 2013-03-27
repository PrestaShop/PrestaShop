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

function update_customer_default_group()
{
	$filename = _PS_ROOT_DIR_.'/config/defines.inc.php';
	$filename_old = str_replace('.inc.', '.old.', $filename);
	copy($filename, $filename_old);
	@chmod($filename_old, 0664);	
	$content = file_get_contents($filename);
	$pattern = "/define\('_PS_DEFAULT_CUSTOMER_GROUP_', (\d)\);/";
	preg_match($pattern, $content, $matches);
	if (!defined('_PS_DEFAULT_CUSTOMER_GROUP_'))
			define('_PS_DEFAULT_CUSTOMER_GROUP_', ((isset($matches[1]) AND is_numeric($matches[1]))? (int)$matches[1] : 3));
	$ps_customer_group = DB::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'configuration` WHERE name LIKE "PS_CUSTOMER_GROUP"', false);			
	$str_old = 'define(\'_PS_DEFAULT_CUSTOMER_GROUP_\', '.(int)_PS_DEFAULT_CUSTOMER_GROUP_.');';
	$str_new = 'define(\'_PS_DEFAULT_CUSTOMER_GROUP_\', '.(int)$ps_customer_group.');';				
	$content = str_replace($str_old, $str_new, $content);
	$result = false;
	if(file_exists($filename) && is_writable($filename))
		$result = (bool)@file_put_contents($filename, $content);
	if($result === true && file_exists($filename) && file_exists($filename_old))
	{
		@unlink($filename_old);
		@chmod($filename, 0664);
		return true;
	}
	return false;
}