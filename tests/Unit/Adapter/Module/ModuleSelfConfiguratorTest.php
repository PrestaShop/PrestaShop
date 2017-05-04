<?php
/*
 * 2007-2017 PrestaShop
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
 *  @copyright  2007-2017 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\tests\Unit\Adapter\Module;

use PrestaShop\PrestaShop\Adapter\Module\ModuleSelfConfigurator;
use PrestaShop\PrestaShop\tests\TestCase\UnitTestCase;

class ModuleSelfConfiguratorTest extends UnitTestCase
{
    public $moduleSelfConfigurator;

    private $moduleRepository;
    
    public function setup()
    {
        $this->mockModuleRepository();
        $this->moduleSelfConfigurator = new ModuleSelfConfigurator($this->moduleRepository);
        parent::setup();
    }

    public function testSuccessfulConfiguration()
    {
        $name = 'dummy';
        $this->assertTrue($this->moduleSelfConfigurator->module($name)->configure());
    }

    public function testFileExists()
    {
        $name = 'ganalytics';
        // Default file - Non existing
        $this->assertNotEmpty($this->moduleSelfConfigurator->module($name)->validate());

        // Specific file - Non existing
        $filepath = '/path/to/the/file.yml';
        $this->assertNotEmpty($this->moduleSelfConfigurator->module($name)->file($filepath)->validate());
    }

    public function testModuleInstallationRequirementPass()
    {
        // Module installed
        $name = 'ganalytics';
        $this->assertEmpty($this->moduleSelfConfigurator->module($name)->file(__DIR__.'/moduleConfExample.yml')->validate());
    }

    public function testModuleInstallationRequirementFail()
    {
        // Module installed
        $name = 'bankwire';
        $this->assertNotEmpty($this->moduleSelfConfigurator->module($name)->validate());
    }

    public function testFileToUse()
    {
        $this->assertNull($this->moduleSelfConfigurator->getFile());

        $filepath = '/path/to/the/file.yml';
        $this->assertEquals($filepath, $this->moduleSelfConfigurator->file($filepath)->getFile());
    }


    private function mockModuleRepository()
    {
        $moduleS = $this->getMockBuilder('PrestaShop\PrestaShop\Adapter\Module\Module')
            ->disableOriginalConstructor()
            ->getMock();
        $moduleS
            ->method('onInstall')
            ->willReturn(true);
        $moduleS
            ->method('onUninstall')
            ->willReturn(true);
        $moduleS
            ->method('onDisable')
            ->willReturn(true);
        $moduleS
            ->method('onEnable')
            ->willReturn(true);
        $moduleS
            ->method('onReset')
            ->willReturn(true);
        $moduleS
            ->method('onMobileDisable')
            ->willReturn(true);
        $moduleS
            ->method('onMobileEnable')
            ->willReturn(true);

        $this->moduleRepository = $this->getMockBuilder('PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->moduleRepository
            ->method('getModule')
            ->willReturn($moduleS);
    }
}