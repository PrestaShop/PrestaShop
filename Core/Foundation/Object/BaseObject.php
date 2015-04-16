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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

 abstract class BaseObject
{
	 /**
	  * Objects magic getter
	  * @param $name
	  * @param null $force_type
	  * @return null|mixed
	  */
	 protected function __get($name, $force_type = null)
	{
		if (property_exists(get_called_class(), $name))
		{
			if (!is_null($force_type))
				settype($this->{$name}, (string)$force_type);
			return $this->{$name};
		}
		return null;
	}

	 /**
	  * Objects magic setter
	  * @param $name
	  * @param $value
	  * @param null $force_type
	  * @return bool
	  */
	 protected function __set($name, $value, $force_type = null)
	{
		if (property_exists(get_called_class(), $name))
		{
			$name_given_type = gettype($name);

			if (!is_null($force_type))
				settype($this->{$name}, (string)$force_type);
			else
				settype($this->{$name}, (string)$name_given_type);

			return true;
		}
		return false;
	}
}