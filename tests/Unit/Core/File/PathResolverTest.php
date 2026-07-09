<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\File;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\File\Exception\FileException;
use PrestaShop\PrestaShop\Core\File\PathResolver;

class PathResolverTest extends TestCase
{
    private string $baseDirectory;

    private string $outsideDirectory;

    private PathResolver $resolver;

    protected function setUp(): void
    {
        $rootDirectory = sys_get_temp_dir() . '/ps_path_resolver_' . uniqid('', true);
        $this->baseDirectory = $rootDirectory . '/base';
        $this->outsideDirectory = $rootDirectory . '/outside';
        mkdir($this->baseDirectory, 0777, true);
        mkdir($this->outsideDirectory, 0777, true);

        $this->resolver = new PathResolver();
    }

    protected function tearDown(): void
    {
        $rootDirectory = dirname($this->baseDirectory);
        if (is_dir($rootDirectory)) {
            $this->removeDirectory($rootDirectory);
        }
    }

    public function testItResolvesFileInsideDirectory(): void
    {
        $filePath = $this->baseDirectory . '/import.csv';
        file_put_contents($filePath, 'id,name');

        $this->assertSame(
            realpath($filePath),
            $this->resolver->resolveFileUnderDirectory($this->baseDirectory, 'import.csv')
        );
    }

    public function testItRejectsTraversalOutsideDirectory(): void
    {
        file_put_contents($this->outsideDirectory . '/parameters.php', 'secret');

        $this->expectException(FileException::class);

        $this->resolver->resolveFileUnderDirectory($this->baseDirectory, '../outside/parameters.php');
    }

    public function testItRejectsSymlinkOutsideDirectory(): void
    {
        if (!function_exists('symlink')) {
            $this->markTestSkipped('symlink is not available.');
        }

        file_put_contents($this->outsideDirectory . '/parameters.php', 'secret');
        if (!symlink($this->outsideDirectory . '/parameters.php', $this->baseDirectory . '/parameters-link.php')) {
            $this->markTestSkipped('symlink could not be created.');
        }

        $this->expectException(FileException::class);

        $this->resolver->resolveFileUnderDirectory($this->baseDirectory, 'parameters-link.php');
    }

    public function testItRejectsMissingFile(): void
    {
        $this->expectException(FileException::class);

        $this->resolver->resolveFileUnderDirectory($this->baseDirectory, 'missing.csv');
    }

    private function removeDirectory(string $directory): void
    {
        $items = scandir($directory);
        if (false === $items) {
            return;
        }

        foreach ($items as $item) {
            if ('.' === $item || '..' === $item) {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path) && !is_link($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }
}
