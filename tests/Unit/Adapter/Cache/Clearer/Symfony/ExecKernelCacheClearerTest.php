<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Cache\Clearer\Symfony;

use AppKernel;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer\Symfony\ExecKernelCacheClearer;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use Psr\Log\NullLogger;

class ExecKernelCacheClearerTest extends TestCase
{
    public function testWarmUpRunsSynchronouslyWhenParallelWarmUpIsDisabled(): void
    {
        $clearer = $this->createClearer(false);

        $this->assertTrue($clearer->clearKernelCache($this->createKernel(), 'prod'));
        $this->assertSame(['cache:clear', 'cache:warmup'], $clearer->executedCommands);
    }

    public function testWarmUpIsQueuedWhenParallelWarmUpIsEnabled(): void
    {
        $clearer = $this->createClearer(true);

        $this->assertTrue($clearer->clearKernelCache($this->createKernel(), 'prod'));
        $this->assertSame(['cache:clear'], $clearer->executedCommands);
    }

    public function testNoWarmUpOutsideProd(): void
    {
        $clearer = $this->createClearer(true);

        $this->assertTrue($clearer->clearKernelCache($this->createKernel(), 'dev'));
        $this->assertSame(['cache:clear'], $clearer->executedCommands);
    }

    private function createClearer(bool $parallelWarmUp): RecordingExecKernelCacheClearer
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturn($parallelWarmUp);

        return new RecordingExecKernelCacheClearer(new NullLogger(), $configuration);
    }

    private function createKernel(): AppKernel
    {
        $kernel = $this->createMock(AppKernel::class);
        $kernel->method('getAppId')->willReturn('admin');
        $kernel->method('getProjectDir')->willReturn('/var/www');

        return $kernel;
    }
}

class RecordingExecKernelCacheClearer extends ExecKernelCacheClearer
{
    /** @var string[] */
    public array $executedCommands = [];

    protected function execCommand(AppKernel $kernel, string $command, string $successMessage, $errorMessage): bool
    {
        $this->executedCommands[] = strtok($command, ' ');

        return true;
    }

    protected function isExecDisabled(): bool
    {
        return false;
    }
}
