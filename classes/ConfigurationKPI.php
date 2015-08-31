<?php
/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class ConfigurationKPICore extends Configuration
{
    public static $definition_backup;

    public static function __callStatic($name, $arguments)
    {
        if (!method_exists(__CLASS__, $name)) {
            throw new Exception("Method $name does not exist.");
        }

        if (!in_array($name, array('unsetKpiDefinition', 'unsetKpiDefinition'))) {
            ConfigurationKPI::setKpiDefinition();
            $result = call_user_func_array(array(__CLASS__, $name), $arguments);
            ConfigurationKPI::unsetKpiDefinition();

            return $result;
        }

        return call_user_func_array(array(__CLASS__, $name), $arguments);
    }

    public static function setKpiDefinition()
    {
        ConfigurationKPI::$definition_backup = Configuration::$definition;
        Configuration::$definition['table'] = 'configuration_kpi';
        Configuration::$definition['primary'] = 'id_configuration_kpi';
    }

    public static function unsetKpiDefinition()
    {
        Configuration::$definition = ConfigurationKPI::$definition_backup;
    }
}
