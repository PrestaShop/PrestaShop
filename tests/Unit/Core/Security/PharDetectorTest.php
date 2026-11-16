<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Security\PharDetector;

class PharDetectorTest extends TestCase
{
    /**
     * @var string[]
     */
    private $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            @unlink($file);
        }
        parent::tearDown();
    }

    /**
     * @dataProvider provideContents
     */
    public function testIsPhar(string $content, bool $expected): void
    {
        self::assertSame($expected, (new PharDetector())->isPhar($this->createFile($content)));
    }

    public static function provideContents(): iterable
    {
        yield 'csv file' => ["id;name;price\n1;Hummingbird printed t-shirt;23.90\n", false];
        yield 'empty file' => ['', false];
        yield 'binary data' => ["PK\x03\x04" . str_repeat("\x00\x01\x02", 100), false];
        yield 'archive stub' => ["<?php __HALT_COMPILER(); ?>\r\n" . str_repeat('x', 50), true];
        yield 'lowercase token with spaces' => ["<?php __halt_compiler ( ) ; ?>\r\n", true];
        yield 'token after other content' => [str_repeat("a;b;c\n", 1000) . '__HALT_COMPILER();', true];
        yield 'token split over two read chunks' => [str_repeat('a', 1048576 - 6) . '__HALT_COMPILER();', true];
    }

    public function testMissingFileIsNotPhar(): void
    {
        self::assertFalse((new PharDetector())->isPhar(sys_get_temp_dir() . '/ps-phar-detector-missing-' . uniqid()));
    }

    private function createFile(string $content): string
    {
        $file = tempnam(sys_get_temp_dir(), 'ps-phar-detector-');
        file_put_contents($file, $content);
        $this->files[] = $file;

        return $file;
    }
}
