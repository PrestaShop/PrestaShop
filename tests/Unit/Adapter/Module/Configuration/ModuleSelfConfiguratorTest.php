<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace Tests\Unit\Adapter\Module\Configuration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Module\Configuration\ModuleSelfConfigurator;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use Tests\TestCase\UnitTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ModuleSelfConfiguratorTest extends UnitTestCase
{
    public $moduleSelfConfigurator;

    /**
     * @var ConfigurationMock
     */
    private $configuration;
    /**
     * @var ConnectionMock
     */
    private $connection;
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    public $defaultDir;

    public function setUp()
    {
        $this->configuration = new ConfigurationMock();
        $this->connection = new ConnectionMock(array(), new Driver);
        $this->mockModuleRepository();

        $this->defaultDir = __DIR__.'/../../../../resources/module-self-config-files';
        parent::setUp();
    }

    private function getModuleSelfConfigurator($moduleRepository = null, $configuration = null, $connection = null, $filesystem = null)
    {
        $moduleSelfConfigurator = new ModuleSelfConfigurator(
            $moduleRepository ?: $this->moduleRepository,
            $configuration ?: $this->configuration,
            $connection ?: $this->connection,
            $filesystem ?: new Filesystem()
        );

        return $moduleSelfConfigurator;
    }

    public function testSuccessfulConfiguration()
    {
        $name = 'bankwire';
        $this->assertTrue($this->getModuleSelfConfigurator()->module($name)->configure());
    }

    public function testFileExists()
    {
        $moduleSelfConfigurator = $this->getModuleSelfConfigurator();

        $name = 'ganalytics';
        // Default file - Non existing
        $this->assertNotEmpty($moduleSelfConfigurator->module($name)->validate());

        // Specific file - Non existing
        $filepath = '/path/to/the/file.yml';
        $this->assertNotEmpty($moduleSelfConfigurator->module($name)->file($filepath)->validate());
    }

    public function testModuleInstallationRequirementPass()
    {
        // Module installed
        $name = 'ganalytics';
        $filepath = $this->defaultDir.'/moduleConfExample.yml';
        $result = $this->getModuleSelfConfigurator()->module($name)->file($filepath)->validate();
        $this->assertEmpty($result, 'Failed to pass the module for the following reasons: '.var_export($result, true));
    }

    public function testModuleInstallationRequirementFail()
    {
        // Module installed
        $name = 'ganalytics';
        $this->assertNotEmpty($this->getModuleSelfConfigurator()->module($name)->validate());
    }

    public function testFileToUse()
    {
        $this->assertNull($this->getModuleSelfConfigurator()->getFile());

        $filepath = '/path/to/the/file.yml';
        $this->assertEquals($filepath, $this->getModuleSelfConfigurator()->file($filepath)->getFile());
    }

    public function testAllValid()
    {
        $filepath = $this->defaultDir.'/moduleConfExample.yml';
        $name = 'bankwire';
        $this->assertEmpty($this->getModuleSelfConfigurator()->module($name)->file($filepath)->validate());
    }

    public function testConfigurationUpdate()
    {
        $filepath = $this->defaultDir.'/moduleConfExampleConfStep.yml';
        $name = 'bankwire';
        // Test before
        $this->assertNull($this->configuration->get('PAYPAL_SANDBOX'));
        $this->assertTrue($this->getModuleSelfConfigurator()->module($name)->file($filepath)->configure());
        $this->assertEquals(1, $this->configuration->get('PAYPAL_SANDBOX'));
    }

    public function testConfigurationDelete()
    {
        $filepath = $this->defaultDir.'/moduleConfExampleConfStep.yml';
        $name = 'bankwire';
        // Test before
        $this->configuration->set('PAYPAL_ONBOARDING', 1);
        $this->assertEquals(1, $this->configuration->get('PAYPAL_ONBOARDING'));
        $this->assertTrue($this->getModuleSelfConfigurator()->module($name)->file($filepath)->configure());
        $this->assertNull($this->configuration->get('PAYPAL_ONBOARDING'));
    }

    public function testFilesExceptionMissingSource()
    {
        $filepath = $this->defaultDir.'/moduleConfCrashFileSource.yml';
        $name = 'bankwire';

        $this->setExpectedException('Exception', 'Missing source file');
        $this->getModuleSelfConfigurator()->module($name)->file($filepath)->configure();
    }

    public function testFilesExceptionMissingDestination()
    {
        $filepath = $this->defaultDir.'/moduleConfCrashFileDestination.yml';
        $name = 'bankwire';

        $this->setExpectedException('Exception', 'Missing destination file');
        $this->getModuleSelfConfigurator()->module($name)->file($filepath)->configure();
    }

    public function testFilesStep()
    {
        $filepath = $this->defaultDir.'/moduleConfExampleFilesStep.yml';
        $name = 'ganalytics';

        $basePath = __DIR__ . '/../../../../resources/module-self-config-files/..';

        $mockFilesystem = $this->getMockBuilder('\Symfony\Component\Filesystem\Filesystem')
            ->getMock();

        $mockFilesystem->expects($this->exactly(2))
            ->method('copy')
            ->withConsecutive(
                [
                    $this->equalTo($basePath.'/modules/ganalytics/ganalytics.php'),
                    $this->equalTo($basePath.'/modules/ganalytics/ganalytics_copy.php'),
                ],
                [
                    $this->equalTo('http://localhost/img/logo.png'),
                    $this->equalTo($basePath.'/modules/ganalytics/another-logo.png'),
                ]
            );

        $moduleSelfConfigurator = $this->getModuleSelfConfigurator(
            null,
            null,
            null,
            $mockFilesystem
        );

        $moduleSelfConfigurator->module($name)->file($filepath)->configure();

        // Then clean
        $filesystem = new Filesystem();
        $filesystem->remove(array(
            __DIR__.'/../../../../resources/modules/ganalytics/ganalytics_copy.php',
            __DIR__.'/../../../../resources/modules/ganalytics/avatar.jpg',
        ));
    }

    public function testSqlStep()
    {
        $filepath = $this->defaultDir.'/moduleConfExampleSqlStep.yml';
        $name = 'ganalytics';

        $this->assertTrue($this->getModuleSelfConfigurator()->module($name)->file($filepath)->configure());
        // Check files are equals
        $this->assertTrue(in_array('TRUNCATE TABLE `ps_doge_army`', $this->connection->executedSql));
        $this->assertTrue(in_array('UPDATE `ps_doge` SET `wow` = 1', $this->connection->executedSql));
        $this->assertFalse(in_array('UPDATE `ps_doge` SET `wow` = 1;', $this->connection->executedSql));
        $this->assertTrue(in_array('TRUNCATE TABLE `ps_lolcat_army`', $this->connection->executedSql));
    }

    public function testSqlExceptionMissingFile()
    {
        $filepath = $this->defaultDir.'/moduleConfCrashSql.yml';
        $name = 'bankwire';

        $this->setExpectedException('Exception', 'Missing file path');
        $this->getModuleSelfConfigurator()->module($name)->file($filepath)->configure();
    }

    public function testPhpStep()
    {
        $filepath = $this->defaultDir.'/moduleConfExamplePhpStep.yml';
        $php_filepath = __DIR__.'/../../../../resources/module-self-config-files/php/MyComplexModuleConfiguration.php';
        $name = 'ganalytics';

        // Test context with mocks
        require_once $php_filepath;
        $mock = $this->getMockBuilder('\MyComplexModuleConfiguration')
                     ->setMethods(array('run'))
                     ->getMock();
        $mock->expects($this->exactly(2))
             ->method('run');

        // Redefine self configuratrion as mock
        $moduleSelfConfigurator = $this
            ->getMockBuilder(
                '\PrestaShop\PrestaShop\Adapter\Module\Configuration\ModuleSelfConfigurator'
            )
            ->setConstructorArgs(array($this->moduleRepository, $this->configuration, $this->connection, new Filesystem()))
            ->setMethods(array('loadPhpFile'))
            ->getMock();

        $moduleSelfConfigurator
            ->expects($this->exactly(2))
            ->method('loadPhpFile')
            ->with($php_filepath)
            ->will($this->returnValue($mock));

        $this->assertTrue($moduleSelfConfigurator->module($name)->file($filepath)->configure());
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
        $moduleS
            ->method('hasValidInstance')
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

    public function set($key, $value, array $options = [])
    {
        $this->configurationData[$key] = $value;
        return $this;
    }

    public function get($key, $default = null)
    {
        return isset($this->configurationData[$key])?$this->configurationData[$key]:$default;
    }

    public function remove($key)
    {
        unset($this->configurationData[$key]);
        return $this;
    }
}

class ConnectionMock extends Connection
{
    public $sql = array();
    public $executedSql = array();

    public function connect()
    {
        return true;
    }

    public function beginTransaction() { }

    public function commit()
    {
        $this->executedSql = array_merge($this->executedSql, $this->sql);
        $this->sql = array();
    }

    public function rollBack()
    {
        $this->sql = array();
    }

    public function prepare($statement)
    {
        $this->sql[] = $statement;
        return new StatementMock($statement, $this);
    }
}

class StatementMock extends Statement
{
    public function __construct($sql, Connection $conn) { }
    public function execute($params = null) { }
}
