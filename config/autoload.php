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
*  @version  Release: $Revision: 7040 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function __autoload($className)
{
		if (function_exists('smartyAutoload') AND smartyAutoload($className)) 
			return true;

	$className = str_replace(chr(0), '', $className);
	$classDir = dirname(__FILE__).'/../classes/';
	$overrideDir = dirname(__FILE__).'/../override/classes/';
	$file_in_override = file_exists($overrideDir.$className.'.php');
	$file_in_classes = file_exists($classDir.$className.'.php');
	
	// This is a Core class and its name is the same as its declared name
	if (substr($className, -4) == 'Core')
		require_once($classDir.substr($className, 0, -4).'.php');
	else
		{
		if ($file_in_override && $file_in_classes)
			{
			require_once($classDir.str_replace(chr(0), '', $className).'.php');
			require_once($overrideDir.$className.'.php');
		}
		elseif (!$file_in_override && $file_in_classes)
				{
			require_once($classDir.str_replace(chr(0), '', $className).'.php');
			$classInfos = new ReflectionClass($className.((interface_exists($className, false) or class_exists($className, false)) ? '' : 'Core'));
			if (!$classInfos->isInterface() && substr($classInfos->name, -4) == 'Core')
				eval(($classInfos->isAbstract() ? 'abstract ' : '').'class '.$className.' extends '.$className.'Core {}');
				}
		elseif ($file_in_override && !$file_in_classes)
			require_once($overrideDir.$className.'.php');
			}
		}

