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

class DependencyInjector
{
	const TYPE_MODULE = 'MODULE';
	private $component_type;

	/**
	 * @param $component_type
	 */
	public function __construct($component_type)
	{
		$this->component_type = $component_type;
	}

	/**
	 * @param $component_name
	 * @return array
	 */
	public function getDependencies($component_name)
	{
		$dependencies_array = array();

		/* Handle module main class DI */
		if ($this->component_type == DependencyInjector::TYPE_MODULE)
		{
			if (!Tools::file_exists_no_cache(_PS_MODULE_DIR_ . $component_name . '/' . $component_name . '.php'))
				return array();

			$r = new ReflectionMethod($component_name, '__construct');
			$params = $r->getParameters();
			foreach ($params as $param)
			{
				if (is_null($param->getClass()))
					return array();
				else
				{
					$dependency = $param->getClass();
					$dependencies_array[] = new $dependency->name;
				}
			}
		}

		return $dependencies_array;
	}

}