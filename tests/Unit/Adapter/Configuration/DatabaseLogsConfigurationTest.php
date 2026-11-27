<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Configuration;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration\DatabaseLogsConfiguration;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopLogger;

class DatabaseLogsConfigurationTest extends TestCase
{
    public function testDuplicateCheckLimitFallsBackToDefaultWhenNotConfigured(): void
    {
        $configuration = $this->buildConfiguration(['PS_MIN_LOGGER_LEVEL_IN_DB' => '2']);

        $this->assertSame(
            [
                'database_min_logger_level' => 2,
                'database_duplicate_check_limit' => PrestaShopLogger::DEFAULT_DUPLICATE_CHECK_LIMIT,
            ],
            (new DatabaseLogsConfiguration($configuration))->getConfiguration()
        );
    }

    public function testConfiguredDuplicateCheckLimitIsReturnedEvenWhenZero(): void
    {
        $configuration = $this->buildConfiguration([
            'PS_MIN_LOGGER_LEVEL_IN_DB' => '1',
            PrestaShopLogger::DUPLICATE_CHECK_LIMIT_CONFIGURATION => '0',
        ]);

        $this->assertSame(
            0,
            (new DatabaseLogsConfiguration($configuration))->getConfiguration()['database_duplicate_check_limit']
        );
    }

    public function testUpdateStoresBothSettings(): void
    {
        $stored = [];
        $errors = (new DatabaseLogsConfiguration($this->buildConfiguration([], $stored)))->updateConfiguration([
            'database_min_logger_level' => '3',
            'database_duplicate_check_limit' => '500',
        ]);

        $this->assertSame([], $errors);
        $this->assertSame(
            ['PS_MIN_LOGGER_LEVEL_IN_DB' => 3, PrestaShopLogger::DUPLICATE_CHECK_LIMIT_CONFIGURATION => 500],
            $stored
        );
    }

    public function testDuplicateCheckLimitIsLeftUntouchedWhenNotSubmitted(): void
    {
        $stored = [];
        (new DatabaseLogsConfiguration($this->buildConfiguration([], $stored)))->updateConfiguration([
            'database_min_logger_level' => 3,
        ]);

        $this->assertSame(['PS_MIN_LOGGER_LEVEL_IN_DB' => 3], $stored);
    }

    private function buildConfiguration(array $values, array &$stored = []): ConfigurationInterface
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(fn ($key) => $values[$key] ?? null);
        $configuration->method('set')->willReturnCallback(function ($key, $value) use (&$stored): void {
            $stored[$key] = $value;
        });

        return $configuration;
    }
}
