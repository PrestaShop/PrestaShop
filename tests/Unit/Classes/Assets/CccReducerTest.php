<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Assets;

use CccReducer;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use ReflectionMethod;
use Symfony\Component\Filesystem\Filesystem;

class CccReducerTest extends TestCase
{
    private string $workDir;

    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->workDir = sys_get_temp_dir() . '/ps_ccc_reducer_test_' . uniqid();
        $this->filesystem->mkdir($this->workDir . '/cache');
        $this->filesystem->dumpFile($this->workDir . '/theme.css', 'body { color: red; }');
        $this->filesystem->dumpFile($this->workDir . '/custom.css', 'a { color: blue; }');
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->workDir);
    }

    public function testIdentifierIsStableWhenFilesDoNotChange(): void
    {
        $files = [$this->workDir . '/theme.css', $this->workDir . '/custom.css'];

        $this->assertSame($this->getIdentifier($files), $this->getIdentifier($files));
    }

    public function testIdentifierChangesWhenAFileIsModified(): void
    {
        $files = [$this->workDir . '/theme.css', $this->workDir . '/custom.css'];
        $before = $this->getIdentifier($files);

        $this->filesystem->appendToFile($this->workDir . '/custom.css', 'p { color: green; }');

        $this->assertNotSame($before, $this->getIdentifier($files));
    }

    public function testIdentifierIsStableWhenAFileIsRewrittenWithTheSameContent(): void
    {
        $files = [$this->workDir . '/theme.css', $this->workDir . '/custom.css'];
        $before = $this->getIdentifier($files);

        $this->filesystem->touch($this->workDir . '/custom.css', time() + 3600);
        clearstatcache();

        $this->assertSame($before, $this->getIdentifier($files));
    }

    public function testIdentifierIsStableForMissingFiles(): void
    {
        $files = [$this->workDir . '/theme.css', $this->workDir . '/missing.css'];

        $this->assertSame($this->getIdentifier($files), $this->getIdentifier($files));
    }

    /**
     * @param string[] $files
     */
    private function getIdentifier(array $files): string
    {
        $reducer = new CccReducer(
            $this->workDir . '/cache/',
            $this->createMock(ConfigurationInterface::class),
            $this->filesystem
        );
        $method = new ReflectionMethod($reducer, 'getFileNameIdentifierFromList');

        return $method->invoke($reducer, $files);
    }
}
