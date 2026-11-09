<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Debug;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer\ClassIndexCacheClearer;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Debug\DebugMode;
use PrestaShop\PrestaShop\Adapter\Debug\DebugModeConfiguration;
use PrestaShop\PrestaShop\Adapter\Debug\DebugProfiling;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;

class DebugModeConfigurationTest extends TestCase
{
    /**
     * @var DebugMode|MockObject
     */
    private $debugMode;

    /**
     * @var Configuration|MockObject
     */
    private $configuration;

    /**
     * @var DebugModeConfiguration
     */
    private $debugModeConfiguration;

    protected function setUp(): void
    {
        $this->debugMode = $this->getMockBuilder(DebugMode::class)
            ->onlyMethods(['changePsModeDevValue'])
            ->getMock();
        $this->configuration = $this->getMockBuilder(Configuration::class)
            ->onlyMethods(['set'])
            ->disableOriginalConstructor()
            ->getMock();
        $debugProfiling = $this->getMockBuilder(DebugProfiling::class)
            ->onlyMethods(['isProfilingEnabled'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->debugModeConfiguration = new DebugModeConfiguration(
            $this->debugMode,
            $this->configuration,
            '/config/defines.inc.php',
            new ClassIndexCacheClearer(),
            $debugProfiling,
            $this->createMock(CacheClearerInterface::class)
        );
    }

    /**
     * @dataProvider provideInvalidCookies
     */
    public function testUpdateConfigurationRejectsUnexpectedCharacters(string $name, string $value, string $field): void
    {
        $this->configuration->expects($this->never())->method('set');
        $this->debugMode->expects($this->never())->method('changePsModeDevValue');

        $configuration = $this->buildConfiguration($name, $value);

        $this->assertFalse($this->debugModeConfiguration->validateConfiguration($configuration));
        $this->assertSame(
            [
                [
                    'key' => 'The %s field is invalid.',
                    'domain' => 'Admin.Notifications.Error',
                    'parameters' => [$field],
                ],
            ],
            $this->debugModeConfiguration->updateConfiguration($configuration)
        );
    }

    public static function provideInvalidCookies(): iterable
    {
        yield 'quote in cookie name' => ["de'bug", '', 'Debug cookie name'];
        yield 'quote in cookie value' => ['debug', "A1'B2", 'Debug cookie value'];
    }

    private function buildConfiguration(string $name, string $value): array
    {
        return [
            'disable_overrides' => false,
            'debug_mode' => true,
            'debug_cookie_name' => $name,
            'debug_cookie_value' => $value,
            'debug_profiling' => false,
        ];
    }
}
