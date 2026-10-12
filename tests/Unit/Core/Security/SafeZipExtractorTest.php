<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Security\SafeZipExtractor;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

final class SafeZipExtractorTest extends TestCase
{
    /**
     * @var string
     */
    private $workDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workDir = sys_get_temp_dir() . '/ps-safe-zip-' . uniqid();
        (new Filesystem())->mkdir($this->workDir . '/destination');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->workDir);
        parent::tearDown();
    }

    /**
     * @dataProvider provideEntryNames
     */
    public function testIsSafeEntryName(string $entryName, bool $expected): void
    {
        self::assertSame($expected, (new SafeZipExtractor())->isSafeEntryName($entryName));
    }

    public static function provideEntryNames(): iterable
    {
        yield 'file' => ['readme.txt', true];
        yield 'nested file' => ['my_module/views/js/front.js', true];
        yield 'folder' => ['my_module/', true];
        yield 'dots in a name' => ['my_module/file..name.txt', true];
        yield 'parent segment' => ['../readme.txt', false];
        yield 'parent segment inside the path' => ['my_module/../../readme.txt', false];
        yield 'parent segment with backslashes' => ['my_module\\..\\..\\readme.txt', false];
        yield 'absolute path' => ['/tmp/readme.txt', false];
        yield 'absolute path with a backslash' => ['\\tmp\\readme.txt', false];
        yield 'drive letter' => ['C:/readme.txt', false];
        yield 'empty name' => ['', false];
        yield 'null byte' => ["readme.txt\0.png", false];
    }

    public function testArchiveIsExtracted(): void
    {
        $archive = $this->createArchive(['my_module/my_module.php' => '<?php', 'my_module/logo.png' => 'png']);

        self::assertTrue((new SafeZipExtractor())->extract($archive, $this->workDir . '/destination'));
        self::assertFileExists($this->workDir . '/destination/my_module/my_module.php');
        self::assertFileExists($this->workDir . '/destination/my_module/logo.png');
    }

    public function testArchiveWithAnEntryOutsideTheDestinationIsNotExtracted(): void
    {
        $archive = $this->createArchive(['my_module/my_module.php' => '<?php', '../outside.txt' => 'text']);

        self::assertFalse((new SafeZipExtractor())->extract($archive, $this->workDir . '/destination'));
        self::assertSame([], $this->listFiles($this->workDir . '/destination'));
        self::assertFalse(file_exists($this->workDir . '/outside.txt'));
    }

    public function testArchiveWithASymbolicLinkIsNotExtracted(): void
    {
        $archive = $this->workDir . '/link.zip';
        $zip = new ZipArchive();
        $zip->open($archive, ZipArchive::CREATE);
        $zip->addFromString('my_module/my_module.php', '<?php');
        $zip->addFromString('my_module/link', '/etc');
        $zip->setExternalAttributesName('my_module/link', ZipArchive::OPSYS_UNIX, 0120777 << 16);
        $zip->close();

        self::assertFalse((new SafeZipExtractor())->extract($archive, $this->workDir . '/destination'));
        self::assertSame([], $this->listFiles($this->workDir . '/destination'));
    }

    public function testMissingArchiveIsNotExtracted(): void
    {
        self::assertFalse((new SafeZipExtractor())->extract($this->workDir . '/missing.zip', $this->workDir . '/destination'));
    }

    /**
     * @param array<string, string> $entries
     */
    private function createArchive(array $entries): string
    {
        $archive = $this->workDir . '/archive-' . uniqid() . '.zip';
        $zip = new ZipArchive();
        $zip->open($archive, ZipArchive::CREATE);
        foreach ($entries as $name => $content) {
            $zip->addFromString($name, $content);
        }
        $zip->close();

        return $archive;
    }

    /**
     * @return string[]
     */
    private function listFiles(string $directory): array
    {
        return array_values(array_diff(scandir($directory), ['.', '..']));
    }
}
