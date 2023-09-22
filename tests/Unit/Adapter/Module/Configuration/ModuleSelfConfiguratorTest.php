<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Module\Configuration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Doctrine\DBAL\Statement;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Module\Configuration\ModuleSelfConfigurator;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use Symfony\Component\Filesystem\Filesystem;

class ModuleSelfConfiguratorTest extends TestCase
{
    /**
     * @var ModuleSelfConfigurator
     */
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
    /**
     * @var string
     */
    public $defaultDir;

    protected function setUp(): void
    {
        $this->configuration = new ConfigurationMock();
        $this->connection = new ConnectionMock([], new Driver());
        $this->mockModuleRepository();

        $this->defaultDir = dirname(__DIR__, 3) . '/Resources/module-self-config-files';

        parent::setUp();
    }

    private function getModuleSelfConfigurator(
        ModuleRepository $moduleRepository = null,
        Configuration $configuration = null,
        Connection $connection = null,
        Filesystem $filesystem = null
    ): ModuleSelfConfigurator {
        return new ModuleSelfConfigurator(
            $moduleRepository ?: $this->moduleRepository,
            $configuration ?: $this->configuration,
            $connection ?: $this->connection,
            $filesystem ?: new Filesystem()
        );
    }

    public function testSuccessfulConfiguration(): void
    {
        $this->assertTrue($this->getModuleSelfConfigurator()->module('bankwire')->configure());
    }

    public function testFileExists(): void
    {
        $moduleSelfConfigurator = $this->getModuleSelfConfigurator();

        $name = 'ganalytics';
        // Default file - Non existing
        $this->assertNotEmpty($moduleSelfConfigurator->module($name)->validate());

        // Specific file - Non existing
        $filepath = '/path/to/the/file.yml';
        $this->assertNotEmpty($moduleSelfConfigurator->module($name)->file($filepath)->validate());
    }

    public function testModuleInstallationRequirementPass(): void
    {
        // Module installed
        $name = 'ganalytics';
        $filepath = $this->defaultDir . '/moduleConfExample.yml';
        $result = $this->getModuleSelfConfigurator()->module($name)->file($filepath)->validate();
        $this->assertEmpty($result, 'Failed to pass the module for the following reasons: ' . var_export($result, true));
    }

    public function testModuleInstallationRequirementFail(): void
    {
        // Module installed
        $name = 'ganalytics';
        $this->assertNotEmpty($this->getModuleSelfConfigurator()->module($name)->validate());
    }

    public function testFileToUse(): void
    {
        $this->assertNull($this->getModuleSelfConfigurator()->getFile());

        $filepath = '/path/to/the/file.yml';
        $this->assertEquals($filepath, $this->getModuleSelfConfigurator()->file($filepath)->getFile());
    }

    public function testAllValid(): void
    {
        $filepath = $this->defaultDir . '/moduleConfExample.yml';
        $name = 'bankwire';
        $this->assertEmpty($this->getModuleSelfConfigurator()->module($name)->file($filepath)->validate());
    }

    public function testConfigurationUpdate(): void
    {
        $filepath = $this->defaultDir . '/moduleConfExampleConfStep.yml';
        $name = 'bankwire';
        // Test before
        $this->assertNull($this->configuration->get('PAYPAL_SANDBOX'));
        $this->assertTrue($this->getModuleSelfConfigurator()->module($name)->file($filepath)->configure());
        $this->assertEquals(1, $this->configuration->get('PAYPAL_SANDBOX'));
    }

    public function testConfigurationDelete(): void
    {
        $filepath = $this->defaultDir . '/moduleConfExampleConfStep.yml';
        $name = 'bankwire';
        // Test before
        $this->configuration->set('PAYPAL_ONBOARDING', 1);
        $this->assertEquals(1, $this->configuration->get('PAYPAL_ONBOARDING'));
        $this->assertTrue($this->getModuleSelfConfigurator()->module($name)->file($filepath)->configure());
        $this->assertNull($this->configuration->get('PAYPAL_ONBOARDING'));
    }

    public function testFilesExceptionMissingSource(): void
    {
        $filepath = $this->defaultDir . '/moduleConfCrashFileSource.yml';
        $name = 'bankwire';

        $this->expectException('Exception');
        $this->expectExceptionMessage('Missing source file');
        $this->getModuleSelfConfigurator()->module($name)->file($filepath)->configure();
    }

    public function testFilesExceptionMissingDestination(): void
    {
        $filepath = $this->defaultDir . '/moduleConfCrashFileDestination.yml';
        $name = 'bankwire';

        $this->expectException('Exception');
        $this->expectExceptionMessage('Missing destination file');
        $this->getModuleSelfConfigurator()->module($name)->file($filepath)->configure();
    }

    public function testFilesStep(): void
    {
        $filepath = $this->defaultDir . '/moduleConfExampleFilesStep.yml';
        $name = 'ganalytics';

        $basePath = $this->defaultDir . '/..';

        $mockFilesystem = $this->getMockBuilder('\Symfony\Component\Filesystem\Filesystem')
            ->getMock();

        $mockFilesystem->expects($this->exactly(2))
            ->method('copy')
            ->withConsecutive(
                [
                    $this->equalTo($basePath . '/modules/ganalytics/ganalytics.php'),
                    $this->equalTo($basePath . '/modules/ganalytics/ganalytics_copy.php'),
                ],
                [
                    $this->equalTo('http://localhost/img/logo.png'),
                    $this->equalTo($basePath . '/modules/ganalytics/another-logo.png'),
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
        $filesystem->remove([
            dirname(__DIR__, 3) . '/Resources/modules/ganalytics/ganalytics_copy.php',
            dirname(__DIR__, 3) . '/Resources/modules/ganalytics/avatar.jpg',
        ]);
    }

    public function testSqlStep(): void
    {
        $filepath = $this->defaultDir . '/moduleConfExampleSqlStep.yml';
        $name = 'ganalytics';

        $this->assertTrue($this->getModuleSelfConfigurator()->module($name)->file($filepath)->configure());
        // Check files are equals
        $this->assertTrue(in_array('TRUNCATE TABLE `ps_doge_army`', $this->connection->executedSql));
        $this->assertTrue(in_array('UPDATE `ps_doge` SET `wow` = 1', $this->connection->executedSql));
        $this->assertFalse(in_array('UPDATE `ps_doge` SET `wow` = 1;', $this->connection->executedSql));
        $this->assertTrue(in_array('TRUNCATE TABLE `ps_lolcat_army`', $this->connection->executedSql));
    }

    public function testSqlExceptionMissingFile(): void
    {
        $filepath = $this->defaultDir . '/moduleConfCrashSql.yml';
        $name = 'bankwire';

        $this->expectException('Exception');
        $this->expectExceptionMessage('Missing file path');
        $this->getModuleSelfConfigurator()->module($name)->file($filepath)->configure();
    }

    public function testPhpStep(): void
    {
        $filepath = $this->defaultDir . '/moduleConfExamplePhpStep.yml';
        $php_filepath = $this->defaultDir . '/php/MyComplexModuleConfiguration.php';
        $name = 'ganalytics';

        // Test context with mocks
        require_once $php_filepath;
        $mock = $this->getMockBuilder('\MyComplexModuleConfiguration')
            ->setMethods(['run'])
            ->getMock();
        $mock->expects($this->exactly(2))
            ->method('run');

        // Redefine self configuratrion as mock
        $moduleSelfConfigurator = $this
            ->getMockBuilder(
                '\PrestaShop\PrestaShop\Adapter\Module\Configuration\ModuleSelfConfigurator'
            )
            ->setConstructorArgs([$this->moduleRepository, $this->configuration, $this->connection, new Filesystem()])
            ->setMethods(['loadPhpFile'])
            ->getMock();

        $moduleSelfConfigurator
            ->expects($this->exactly(2))
            ->method('loadPhpFile')
            ->with($php_filepath)
            ->will($this->returnValue($mock));

        $this->assertTrue($moduleSelfConfigurator->module($name)->file($filepath)->configure());
    }

    // MOCK

    private function mockModuleRepository(): void
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
            ->method('hasValidInstance')
            ->willReturn(true);

        $this->moduleRepository = $this->getMockBuilder(ModuleRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->moduleRepository
            ->method('getModule')
            ->willReturn($moduleS);
    }
}

class ConfigurationMock extends Configuration
{
    private $configurationData = [];

    public function set($key, $value, ShopConstraint $shopConstraint = null, array $options = [])
    {
        $this->configurationData[$key] = $value;

        return $this;
    }

    public function get($key, $default = null, ShopConstraint $shopConstraint = null)
    {
        return isset($this->configurationData[$key]) ? $this->configurationData[$key] : $default;
    }

    public function remove($key)
    {
        unset($this->configurationData[$key]);

        return $this;
    }
}

class ConnectionMock extends Connection
{
    public $sql = [];
    public $executedSql = [];

    public function connect()
    {
        return true;
    }

    public function beginTransaction()
    {
        return true;
    }

    public function commit()
    {
        $this->executedSql = array_merge($this->executedSql, $this->sql);
        $this->sql = [];

        return true;
    }

    public function rollBack()
    {
        $this->sql = [];

        return true;
    }

    public function prepare($statement)
    {
        $this->sql[] = $statement;

        return new StatementMock($statement, $this);
    }
}

class StatementMock extends Statement
{
    /** @phpstan-ignore-next-line */
    public function __construct($sql, Connection $conn)
    {
    }

    public function execute($params = null)
    {
        return true;
    }
}
