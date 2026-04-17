<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Foundation\FileSystem;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem;

class FileSystemTest extends TestCase
{
    /**
     * @var FileSystem
     */
    private $fs;
    /**
     * @var string
     */
    private $fixturesPath;

    protected function setUp(): void
    {
        $this->fs = new FileSystem();
        $this->fixturesPath = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures';
    }

    public function testJoinPathsTwoPaths()
    {
        $this->assertEquals(
            'a' . DIRECTORY_SEPARATOR . 'b',
            $this->fs->joinPaths('a', 'b')
        );
    }

    public function testJoinPathsThreePaths()
    {
        $this->assertEquals(
            'a' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'c',
            $this->fs->joinPaths('a', 'b', 'c')
        );
    }

    public function testJoinPathsOnePathThrows()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\Filesystem\Exception::class);

        $this->fs->joinPaths('a');
    }

    public function testJoinPathsZeroPathThrows()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\Filesystem\Exception::class);

        $this->fs->joinPaths();
    }

    public function testJoinPathsNormalizesDirectorySeparators()
    {
        $this->assertEquals(
            'a' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'c' . DIRECTORY_SEPARATOR . 'd',
            $this->fs->joinPaths('a\\', 'b///', 'c\\', 'd/')
        );
    }

    public function testListEntriesRecursively()
    {
        $expectedPaths = [
            $this->fs->joinPaths($this->fixturesPath, 'a'),
            $this->fs->joinPaths($this->fixturesPath, 'a', 'a.tmp'),
            $this->fs->joinPaths($this->fixturesPath, 'a', 'b'),
            $this->fs->joinPaths($this->fixturesPath, 'a', 'b', 'b.file'),
            $this->fs->joinPaths($this->fixturesPath, 'toplevel.txt'),
        ];

        $this->assertEquals(
            $expectedPaths,
            array_keys($this->fs->listEntriesRecursively($this->fixturesPath))
        );
    }

    public function testListFilesRecursively()
    {
        $expectedPaths = [
            $this->fs->joinPaths($this->fixturesPath, 'a', 'a.tmp'),
            $this->fs->joinPaths($this->fixturesPath, 'a', 'b', 'b.file'),
            $this->fs->joinPaths($this->fixturesPath, 'toplevel.txt'),
        ];

        $this->assertEquals(
            $expectedPaths,
            array_keys($this->fs->listFilesRecursively($this->fixturesPath))
        );
    }

    /**
     * Rationale: ls /some/non/existing/file => ls: cannot access /some/non/existing/file: No such file or directory
     */
    public function testListEntriesRecursivelyThrowsIfPathDoesNotExist()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\Filesystem\Exception::class);

        $this->fs->listEntriesRecursively('/some/w/h/e/r/e/over/the/rainbow');
    }

    public function testListEntriesRecursivelyThrowsWhenPathIsAFile()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\Filesystem\Exception::class);

        $this->fs->listEntriesRecursively(__FILE__);
    }
}
