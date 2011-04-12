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

define('PS_ADMIN_DIR', getcwd());

include(PS_ADMIN_DIR.'/../config/config.inc.php');

if (!class_exists('Cookie'))
	exit();

$cookie = new Cookie('psAdmin', substr($_SERVER['SCRIPT_NAME'], strlen(__PS_BASE_URI__), -10));
if (!$cookie->isLoggedBack())
	die;

if (isset($_FILES['virtual_product_file']) AND is_uploaded_file($_FILES['virtual_product_file']['tmp_name']) AND 
(isset($_FILES['virtual_product_file']['error']) AND !$_FILES['virtual_product_file']['error'])	OR 
(!empty($_FILES['virtual_product_file']['tmp_name']) AND $_FILES['virtual_product_file']['tmp_name'] != 'none'))
{
	$filename = $_FILES['virtual_product_file']['name'];
	$file = $_FILES['virtual_product_file']['tmp_name'];
	$newfilename = ProductDownload::getNewFilename();

	if (!copy($file, _PS_DOWNLOAD_DIR_.$newfilename))
	{
		header('HTTP/1.1 500 Error');
		echo '<return result="error" msg="No permissions to write in the download folder" filename="'.$filename.'" />';
	}
	@unlink($file);

	header('HTTP/1.1 200 OK');
	echo '<return result="success" msg="'.$newfilename.'" filename="'.$filename.'" />';
}
else
{
	header('HTTP/1.1 500 Error');
	echo '<return result="error" msg="Unknown error" filename="'.ProductDownload::getNewFilename().'" />';
}