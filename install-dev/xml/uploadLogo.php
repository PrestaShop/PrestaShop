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
define('INSTALL_PATH', dirname(__FILE__));

	$error = "";
	$msg = "";
	$fileElementName = 'fileToUpload';
	
	if(!empty($_FILES[$fileElementName]['error']))
	{
		switch($_FILES[$fileElementName]['error'])
		{

			case '1':
				$error = '38';
				break;
			case '2':
				$error = '39';
				break;
			case '3':
				$error = '40';
				break;
			case '4':
				$error = '41';
				break;

			case '6':
				$error = '42';
				break;
			case '7':
				$error = '43';
				break;
			case '8':
				$error = '44';
				break;
			case '999':
			default:
				$error = '999';
		}
	}
	else
	{
		if(empty($_FILES[$fileElementName]['tmp_name']) OR $_FILES[$fileElementName]['tmp_name'] == 'none')
		{
			$error = '41';
		}
		else
		{				
			list($width, $height, $type, $attr) = getimagesize($_FILES[$fileElementName]['tmp_name']);
			
			if($height == 0)
			{
			$error = '16';
			}
			else
			{
				$newheight = $height > 500 ? 500 : $height;
				$percent = $newheight / $height;
				$newwidth = $width * $percent;
				$newheight = $height * $percent;
				$thumb = imagecreatetruecolor($newwidth, $newheight);
				switch ($type) {
					case 1:
						$sourceImage = imagecreatefromgif($_FILES[$fileElementName]['tmp_name']);
						break;
					case 2:
						$sourceImage = imagecreatefromjpeg($_FILES[$fileElementName]['tmp_name']);
						break;
					case 3:
						$sourceImage = imagecreatefrompng($_FILES[$fileElementName]['tmp_name']);
						break;
					default:
						return false;
				}
				
				imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
				
				if(!is_writable(realpath(INSTALL_PATH.'/../../img').'/logo.jpg'))
						$error = '58';
				else
				{
					if(!imagejpeg($thumb, realpath(INSTALL_PATH.'/../../img').'/logo.jpg', 90))
					{
						$error = '7';
					}
				}
			}
		}
	}		
	echo "{";
	echo "	error: '" . $error . "',\n";
	echo "}";
