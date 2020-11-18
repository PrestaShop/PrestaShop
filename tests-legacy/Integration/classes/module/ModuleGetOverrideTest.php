<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Integration;

use LegacyTests\TestCase\IntegrationTestCase;

use LegacyTests\TestCase\Module as HelperModule;
use Module;

class ModulesGetOverrideTest extends IntegrationTestCase
{
    /**
     * @return array a list of modules to control override features.
     */
    public function listModulesOnDisk()
    {
        return [
            ['bankwire'],
            ['cronjobs'],
            ['gamification'],
            ['ganalytics'],
            ['ps_emailsubscription'],
            ['ps_featuredproducts'],
            ['psaddonsconnect'],
            ['pscsx3241'],
        ];
    }

    /**
     * @dataProvider listModulesOnDisk
     * Note: improves module list fixtures in order to cancel any override.
     * @param string $moduleName the module name.
     */
    public function testDummyGetOverride($moduleName)
    {
        $module = Module::getInstanceByName($moduleName);

        if ($module instanceof Module) {
            $this->assertEmpty($module->getOverrides());
        }
    }

    public function testRealOverrideInModuleDir()
    {
        HelperModule::addModule('pscsx3241');
        $module = Module::getInstanceByName('pscsx3241');
        $overrides = $module->getOverrides();

        $this->assertContains('Cart', $overrides);
        $this->assertContains('AdminProductsController', $overrides);
        $this->assertCount(2, $overrides);

        HelperModule::removeModule('pscsx3241');
    }
}
