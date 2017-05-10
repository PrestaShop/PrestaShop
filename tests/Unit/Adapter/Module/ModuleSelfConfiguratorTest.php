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

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Module\ModuleSelfConfigurator;
use PrestaShop\PrestaShop\tests\TestCase\UnitTestCase;
use Symfony\Component\Filesystem\Filesystem;

class ModuleSelfConfiguratorTest extends UnitTestCase
{
    public $moduleSelfConfigurator;

    private $configuration;
    private $moduleRepository;

    public $defaultDir = __DIR__.'/../../../resources/module-self-config-files';
    
    public function setup()
    {
        $this->configuration = new ConfigurationMock();
        $this->mockModuleRepository();
        $this->moduleSelfConfigurator = new ModuleSelfConfigurator($this->moduleRepository, $this->configuration);
        parent::setup();
    }

    public function testSuccessfulConfiguration()
    {
        $name = 'bankwire';
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
        $filepath = $this->defaultDir.'/moduleConfExample.yml';
        $this->assertEmpty($this->moduleSelfConfigurator->module($name)->file($filepath)->validate());
    }

    public function testModuleInstallationRequirementFail()
    {
        // Module installed
        $name = 'ganalytics';
        $this->assertNotEmpty($this->moduleSelfConfigurator->module($name)->validate());
    }

    public function testFileToUse()
    {
        $this->assertNull($this->moduleSelfConfigurator->getFile());

        $filepath = '/path/to/the/file.yml';
        $this->assertEquals($filepath, $this->moduleSelfConfigurator->file($filepath)->getFile());
    }

    public function testAllValid()
    {
        $filepath = $this->defaultDir.'/moduleConfExample.yml';
        $name = 'bankwire';
        $this->assertEmpty($this->moduleSelfConfigurator->module($name)->file($filepath)->validate());
    }

    public function testConfigurationUpdate()
    {
        $filepath = $this->defaultDir.'/moduleConfExampleConfStep.yml';
        $name = 'bankwire';
        // Test before
        $this->assertNull($this->configuration->get('PAYPAL_SANDBOX'));
        $this->moduleSelfConfigurator->module($name)->file($filepath)->configure();
        $this->assertEquals(1, $this->configuration->get('PAYPAL_SANDBOX'));
    }

    public function testConfigurationDelete()
    {
        $filepath = $this->defaultDir.'/moduleConfExampleConfStep.yml';
        $name = 'bankwire';
        // Test before
        $this->configuration->set('PAYPAL_ONBOARDING', 1);
        $this->assertEquals(1, $this->configuration->get('PAYPAL_ONBOARDING'));
        $this->moduleSelfConfigurator->module($name)->file($filepath)->configure();
        $this->assertNull($this->configuration->get('PAYPAL_ONBOARDING'));
    }

    public function testFilesExceptionMissingSource()
    {
        $filepath = $this->defaultDir.'/moduleConfCrashFileSource.yml';
        $name = 'bankwire';

        $this->setExpectedException('Exception', 'Missing source file');
        $this->moduleSelfConfigurator->module($name)->file($filepath)->configure();
    }

    public function testFilesExceptionMissingDestination()
    {
        $filepath = $this->defaultDir.'/moduleConfCrashFileDestination.yml';
        $name = 'bankwire';

        $this->setExpectedException('Exception', 'Missing destination file');
        $this->moduleSelfConfigurator->module($name)->file($filepath)->configure();
    }

    public function testFilesStep()
    {
        $filepath = $this->defaultDir.'/moduleConfExampleFilesStep.yml';
        $name = 'ganalytics';

        $this->moduleSelfConfigurator->module($name)->file($filepath)->configure();

        // Check files are equals
        $this->assertEquals(
            file_get_contents(__DIR__.'/../../../resources/modules/ganalytics/webpage.html'),
            file_get_contents('http://localhost')
        );
        $this->assertEquals(
            file_get_contents(__DIR__.'/../../../resources/modules/ganalytics/ganalytics.php'),
            file_get_contents(__DIR__.'/../../../resources/modules/ganalytics/ganalytics_copy.php')
        );

        // Then clean
        $filesystem = new Filesystem();
        $filesystem->remove(array(
            __DIR__.'/../../../resources/modules/ganalytics/ganalytics_copy.php',
            __DIR__.'/../../../resources/modules/ganalytics/webpage.html',
        ));
    }

    // MOCK

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

class ConfigurationMock extends Configuration
{
    private $configurationData = array();
    
    public function set($key, $value)
    {
        $this->configurationData[$key] = $value;
        return $this;
    }

    public function get($key)
    {
        return isset($this->configurationData[$key])?$this->configurationData[$key]:null;
    }

    public function delete($key)
    {
        unset($this->configurationData[$key]);
        return $this;
    }
}