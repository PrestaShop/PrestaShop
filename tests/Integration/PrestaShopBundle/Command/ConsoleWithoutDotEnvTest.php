<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Every variable in .env has a default, so a shop without the file (removed, or not brought over by an update)
 * has to start. The console reads .env next to its own directory, so it runs from a copy of the project root
 * where everything is a link to the real one except bin/ and .env: the real .env is never touched.
 */
class ConsoleWithoutDotEnvTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $project = dirname(__DIR__, 4);
        $this->root = sys_get_temp_dir() . '/ps-console-without-dotenv-' . uniqid();
        mkdir($this->root . '/bin', 0777, true);
        foreach (scandir($project) as $entry) {
            if (!in_array($entry, ['.', '..', '.env', 'bin'], true)) {
                symlink($project . '/' . $entry, $this->root . '/' . $entry);
            }
        }
        copy($project . '/bin/console', $this->root . '/bin/console');
    }

    protected function tearDown(): void
    {
        // Removes the links, not what they point at
        (new Filesystem())->remove($this->root);
    }

    public function testTheConsoleStartsWithoutADotEnvFile(): void
    {
        $this->assertFileDoesNotExist($this->root . '/.env');

        $process = new Process([PHP_BINARY, '-d', 'memory_limit=-1', $this->root . '/bin/console', 'list']);
        $process->run();

        $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput() . $process->getOutput());
        $this->assertStringContainsString('prestashop:', $process->getOutput());
    }
}
