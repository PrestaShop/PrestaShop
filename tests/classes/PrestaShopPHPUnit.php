<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class	PrestaShopPHPUnit extends PHPUnit_Framework_TestCase
{
	protected function invoke($object, $method)
	{
		$params = array_slice(func_get_args(), 2);

		$reflexion = new ReflectionClass($this->getClass());
		$reflexion_method = $reflexion->getMethod($method);
		$reflexion_method->setAccessible(true);

		return $reflexion_method->invokeArgs($object, $params);
	}
	
	protected function getProperty($object, $property)
	{
		$reflexion = new ReflectionClass($this->getClass());
		$reflexion_property = $reflexion->getProperty($property);
		$reflexion_property->setAccessible(true);

		return $reflexion_property->getValue($object);
	}
	
	protected function setProperty($object, $property, $value)
	{
		$reflexion = new ReflectionClass($this->getClass());
		$reflexion_property = $reflexion->getProperty($property);
		$reflexion_property->setAccessible(true);

		$reflexion_property->setValue($object, $value);
	}

	protected function getClass()
	{
		return preg_replace('/(.*)(?:Core)?Test$/', '$1', get_class($this));
	}
}
