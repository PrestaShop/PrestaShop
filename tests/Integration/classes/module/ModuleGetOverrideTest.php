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

namespace PrestaShop\PrestaShop\Tests\Integration;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;

use Module;
use Context;
use Employee;
use PrestaShopAutoload;
use \PrestaShop\PrestaShop\Tests\Helper\Module as HelperModule;

class ModulesGetOverrideTest extends IntegrationTestCase
{
    public function listModulesOnDisk()
    {
        $modules = array();

        foreach (scandir(_PS_MODULE_DIR_) as $entry)
        {
            if ($entry[0] !== '.')
            {
                if (file_exists(_PS_MODULE_DIR_.$entry.DIRECTORY_SEPARATOR.$entry.'.php'))
                {
                    $modules[] = array($entry);
                }
            }
        }

        return $modules;
    }

    /**
     * @dataProvider listModulesOnDisk
     * Note: improves module list fixtures in order to cancel any override.
     * @todo: PHP7 incompatability on module overidding
     */
    public function testDummyGetOverride($moduleName)
    {
        $module = Module::getInstanceByName($moduleName);

        if (version_compare(PHP_VERSION, '7.0.0') < 0) {
            $this->assertEmpty($module->getOverrides());
        }else {
            $this->assertNotEmpty($module->getOverrides());
        }

    }

    public function testRealOverrideInModuleDir()
    {
        HelperModule::addModule('pscsx3241');
        $module = Module::getInstanceByName('pscsx3241');
        $this->assertSame([
            'Cart',
            'AdminProductsController'
        ], $module->getOverrides()
        );
        HelperModule::removeModule('pscsx3241');
    }
}
