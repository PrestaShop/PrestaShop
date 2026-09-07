<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use Tools;

/**
 * Clearing a cache is the main caller of these two helpers, and a file another process still holds is an
 * expected outcome there. What must not happen is the caller being told the directory is empty when it is
 * not, or a PHP warning reaching the page.
 */
class ToolsDeleteDirectoryTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/ps_delete_directory_' . uniqid();
        mkdir($this->root . '/sub', 0777, true);
        file_put_contents($this->root . '/sub/kept.txt', 'x');
        file_put_contents($this->root . '/removable.txt', 'x');
    }

    protected function tearDown(): void
    {
        @chmod($this->root . '/sub', 0777);
        @unlink($this->root . '/sub/kept.txt');
        @rmdir($this->root . '/sub');
        @unlink($this->root . '/removable.txt');
        @rmdir($this->root);
    }

    public function testItEmptiesADirectory(): void
    {
        self::assertTrue(Tools::deleteDirectory($this->root, false));
        self::assertFileDoesNotExist($this->root . '/removable.txt');
        self::assertDirectoryDoesNotExist($this->root . '/sub');
        self::assertDirectoryExists($this->root);
    }

    public function testItRemovesTheDirectoryItself(): void
    {
        self::assertTrue(Tools::deleteDirectory($this->root));
        self::assertDirectoryDoesNotExist($this->root);
    }

    public function testItReportsAFileItCouldNotDelete(): void
    {
        // read + execute only: the entry cannot be unlinked from it
        chmod($this->root . '/sub', 0500);
        if (@unlink($this->root . '/sub/kept.txt')) {
            self::markTestSkipped('the filesystem ignores directory permissions, most likely running as root');
        }

        $warnings = [];
        set_error_handler(static function (int $number, string $message) use (&$warnings): bool {
            // WHY: a custom handler is still called for a diagnostic the @ operator suppressed. What
            // marks it as suppressed is error_reporting() being masked down inside the call.
            if (0 === (error_reporting() & $number)) {
                return true;
            }
            $warnings[] = $message;

            return true;
        });

        try {
            $result = Tools::deleteDirectory($this->root, false);
        } finally {
            restore_error_handler();
        }

        self::assertFalse($result, 'a directory that still holds a file must not report success');
        self::assertFileExists($this->root . '/sub/kept.txt');
        self::assertFileDoesNotExist($this->root . '/removable.txt', 'what could be deleted still is');
        self::assertSame([], $warnings, 'the failure is reported through the return value, not a PHP warning');
    }

    public function testDeleteFileReportsAFileItCouldNotDelete(): void
    {
        chmod($this->root . '/sub', 0500);
        if (@unlink($this->root . '/sub/kept.txt')) {
            self::markTestSkipped('the filesystem ignores directory permissions, most likely running as root');
        }

        $warnings = [];
        set_error_handler(static function (int $number, string $message) use (&$warnings): bool {
            // WHY: a custom handler is still called for a diagnostic the @ operator suppressed. What
            // marks it as suppressed is error_reporting() being masked down inside the call.
            if (0 === (error_reporting() & $number)) {
                return true;
            }
            $warnings[] = $message;

            return true;
        });

        try {
            $result = Tools::deleteFile($this->root . '/sub/kept.txt');
        } finally {
            restore_error_handler();
        }

        self::assertFalse($result);
        self::assertSame([], $warnings);
    }

    public function testDeleteFileSkipsAnExcludedName(): void
    {
        self::assertFalse(Tools::deleteFile($this->root . '/removable.txt', ['removable.txt']));
        self::assertFileExists($this->root . '/removable.txt');
    }

    public function testDeleteFileIgnoresADirectory(): void
    {
        self::assertFalse(Tools::deleteFile($this->root . '/sub'));
        self::assertDirectoryExists($this->root . '/sub');
    }
}
