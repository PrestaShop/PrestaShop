<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use FileLogger;
use PHPUnit\Framework\TestCase;
use PrestaShopException;

class FileLoggerTest extends TestCase
{
    /**
     * @dataProvider providesStreamWrappers
     */
    public function testSetFilenameAcceptsStreamWrappers(string $filename): void
    {
        $logger = new FileLogger();
        $logger->setFilename($filename);

        $this->assertSame($filename, $logger->getFilename());
    }

    public static function providesStreamWrappers(): iterable
    {
        yield 'stderr' => ['php://stderr'];
        yield 'stdout' => ['php://stdout'];
        yield 'uppercase scheme' => ['PHP://stderr'];
    }

    public function testSetFilenameAcceptsFileInWritableDirectory(): void
    {
        $filename = sys_get_temp_dir() . '/ps_file_logger_test.log';

        $logger = new FileLogger();
        $logger->setFilename($filename);

        $this->assertSame($filename, $logger->getFilename());
    }

    /**
     * @dataProvider providesUnwritableFilenames
     */
    public function testSetFilenameThrowsWhenDirectoryIsNotWritable(string $filename): void
    {
        $logger = new FileLogger();

        $this->expectException(PrestaShopException::class);
        $logger->setFilename($filename);
    }

    public static function providesUnwritableFilenames(): iterable
    {
        yield 'missing directory' => ['/ps-file-logger-missing-dir/test.log'];
        yield 'file wrapper on missing directory' => ['file:///ps-file-logger-missing-dir/test.log'];
        yield 'unknown wrapper' => ['unknownwrapper://test.log'];
    }
}
