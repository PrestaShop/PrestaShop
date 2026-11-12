<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Configuration;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration\DatabaseLogsConfiguration;
use PrestaShop\PrestaShop\Adapter\Configuration\LogsConfiguration;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Contracts\Translation\TranslatorInterface;

class LogsConfigurationTest extends TestCase
{
    public function testLogsFormSavesWithAFieldAModuleAdded(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->expects($this->exactly(2))->method('set');
        $validate = $this->createMock(Validate::class);
        $validate->method('isEmail')->willReturn(true);
        $logsConfiguration = new LogsConfiguration($configuration, $this->createMock(TranslatorInterface::class), $validate);

        $errors = $logsConfiguration->updateConfiguration([
            'logs_by_email' => '1',
            'logs_email_receivers' => 'demo@prestashop.com',
            'module_field' => 'value',
        ]);

        $this->assertSame([], $errors);
    }

    public function testLogsFormStillRequiresItsOwnFields(): void
    {
        $logsConfiguration = new LogsConfiguration(
            $this->createMock(ConfigurationInterface::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(Validate::class)
        );

        $this->expectException(MissingOptionsException::class);
        $logsConfiguration->updateConfiguration(['logs_by_email' => '1', 'module_field' => 'value']);
    }

    public function testDatabaseLogsFormSavesWithAFieldAModuleAdded(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->expects($this->once())->method('set')->with('PS_MIN_LOGGER_LEVEL_IN_DB', 2);

        $errors = (new DatabaseLogsConfiguration($configuration))->updateConfiguration([
            'database_min_logger_level' => '2',
            'module_field' => 'value',
        ]);

        $this->assertSame([], $errors);
    }

    public function testDatabaseLogsFormStillRequiresItsOwnField(): void
    {
        $this->expectException(MissingOptionsException::class);
        (new DatabaseLogsConfiguration($this->createMock(ConfigurationInterface::class)))->updateConfiguration(['module_field' => 'value']);
    }
}
