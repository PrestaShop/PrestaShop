<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem as PsFileSystem;
use PrestaShop\PrestaShop\Core\Security\Exception\ZipExtractionException;
use PrestaShop\PrestaShop\Core\Security\SafeZipExtractor;
use ReflectionProperty;
use Symfony\Component\Filesystem\Exception\IOException;
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
    public function testEntryName(string $entryName, bool $extracted): void
    {
        $archive = $this->createArchive(['my_module/my_module.php' => '<?php', $entryName => 'text']);

        if ($extracted) {
            $this->createExtractor()->extract($archive, $this->workDir . '/destination');
            self::assertFileExists($this->workDir . '/destination/my_module/my_module.php');

            return;
        }

        $exception = $this->extractAndCatch($this->createExtractor(), $archive);
        self::assertSame(ZipExtractionException::UNSAFE_ENTRY, $exception->getCode());
        self::assertSame([], $this->listFiles($this->workDir . '/destination'));
    }

    public static function provideEntryNames(): iterable
    {
        yield 'file' => ['readme.txt', true];
        yield 'nested file' => ['my_module/views/js/front.js', true];
        yield 'folder' => ['my_module/views/', true];
        yield 'dots in a name' => ['my_module/file..name.txt', true];
        yield 'parent segment' => ['../readme.txt', false];
        yield 'parent segment inside the path' => ['my_module/../../readme.txt', false];
        yield 'parent segment with backslashes' => ['my_module\\..\\..\\readme.txt', false];
        yield 'absolute path' => ['/tmp/readme.txt', false];
        yield 'absolute path with a backslash' => ['\\tmp\\readme.txt', false];
        yield 'drive letter' => ['C:/readme.txt', false];
        yield 'empty name' => ['', false];
    }

    public function testArchiveIsExtracted(): void
    {
        $archive = $this->createArchive(['my_module/my_module.php' => '<?php', 'my_module/logo.png' => 'png']);

        $this->createExtractor()->extract($archive, $this->workDir . '/destination');

        self::assertFileExists($this->workDir . '/destination/my_module/my_module.php');
        self::assertFileExists($this->workDir . '/destination/my_module/logo.png');
        self::assertSame([], $this->listFiles($this->workDir . '/sandbox'));
    }

    public function testArchiveWithAnEntryOutsideTheDestinationIsNotExtracted(): void
    {
        $archive = $this->createArchive(['my_module/my_module.php' => '<?php', '../outside.txt' => 'text']);

        $exception = $this->extractAndCatch($this->createExtractor(), $archive);

        self::assertSame(ZipExtractionException::UNSAFE_ENTRY, $exception->getCode());
        self::assertStringContainsString('../outside.txt', $exception->getMessage());
        self::assertTrue($exception->isInvalidArchive());
        self::assertSame([], $this->listFiles($this->workDir . '/destination'));
        self::assertSame([], $this->listFiles($this->workDir . '/sandbox'));
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

        $exception = $this->extractAndCatch($this->createExtractor(), $archive);

        self::assertSame(ZipExtractionException::UNSAFE_ENTRY, $exception->getCode());
        self::assertStringContainsString('my_module/link', $exception->getMessage());
        self::assertSame([], $this->listFiles($this->workDir . '/destination'));
    }

    public function testMissingArchiveIsNotExtracted(): void
    {
        $exception = $this->extractAndCatch($this->createExtractor(), $this->workDir . '/missing.zip');

        self::assertSame(ZipExtractionException::CANNOT_OPEN_ARCHIVE, $exception->getCode());
        self::assertTrue($exception->isInvalidArchive());
        self::assertSame([], $this->listFiles($this->workDir . '/sandbox'));
    }

    public function testOnlyTheGivenFolderIsCopied(): void
    {
        $archive = $this->createArchive([
            'my_module/my_module.php' => '<?php',
            'other_module/other_module.php' => '<?php',
            'readme.txt' => 'text',
        ]);

        $this->createExtractor()->extract($archive, $this->workDir . '/destination', 'my_module');

        self::assertSame(['my_module'], $this->listFiles($this->workDir . '/destination'));
        self::assertFileExists($this->workDir . '/destination/my_module/my_module.php');
        self::assertSame([], $this->listFiles($this->workDir . '/sandbox'));
    }

    public function testFolderMissingFromTheArchiveIsNotCopied(): void
    {
        $archive = $this->createArchive(['other_module/other_module.php' => '<?php']);

        $exception = $this->extractAndCatch($this->createExtractor(), $archive, 'my_module');

        self::assertSame(ZipExtractionException::MISSING_FOLDER, $exception->getCode());
        self::assertTrue($exception->isInvalidArchive());
        self::assertSame([], $this->listFiles($this->workDir . '/destination'));
        self::assertSame([], $this->listFiles($this->workDir . '/sandbox'));
    }

    /**
     * @dataProvider provideUnsafeFolderNames
     */
    public function testUnsafeFolderNameIsRefused(string $folder): void
    {
        $archive = $this->createArchive(['my_module/my_module.php' => '<?php']);

        $exception = $this->extractAndCatch($this->createExtractor(), $archive, $folder, $this->workDir . '/destination/sub');

        self::assertSame(ZipExtractionException::INVALID_FOLDER, $exception->getCode());
        self::assertSame([], $this->listFiles($this->workDir . '/destination'));
    }

    public static function provideUnsafeFolderNames(): iterable
    {
        yield 'empty' => [''];
        yield 'current folder' => ['.'];
        yield 'parent folder' => ['..'];
        yield 'nested folder' => ['my_module/views'];
        yield 'backslash' => ['my_module\\views'];
    }

    /**
     * @dataProvider provideFolders
     */
    public function testExtractedFilesGetTheDefaultModes(?string $folder): void
    {
        // Modes stored in the archive (a private folder and file here) are not applied to the extracted files
        $archive = $this->workDir . '/modes.zip';
        $zip = new ZipArchive();
        $zip->open($archive, ZipArchive::CREATE);
        $zip->addEmptyDir('my_module/views');
        $zip->setExternalAttributesName('my_module/views/', ZipArchive::OPSYS_UNIX, 040700 << 16);
        $zip->addFromString('my_module/my_module.php', '<?php');
        $zip->setExternalAttributesName('my_module/my_module.php', ZipArchive::OPSYS_UNIX, 0100600 << 16);
        $zip->close();

        // The usual umask of a web server
        $umask = umask(022);
        try {
            $this->createExtractor()->extract($archive, $this->workDir . '/destination', $folder);
        } finally {
            umask($umask);
        }

        clearstatcache();
        $module = $this->workDir . '/destination/my_module';
        self::assertSame(PsFileSystem::DEFAULT_MODE_FOLDER, fileperms($module) & 0777);
        self::assertSame(PsFileSystem::DEFAULT_MODE_FOLDER, fileperms($module . '/views') & 0777);
        self::assertSame(PsFileSystem::DEFAULT_MODE_FILE, fileperms($module . '/my_module.php') & 0777);
    }

    public static function provideFolders(): iterable
    {
        yield 'whole archive' => [null];
        yield 'one folder' => ['my_module'];
    }

    public function testFilesAlreadyInTheDestinationAreKept(): void
    {
        file_put_contents($this->workDir . '/destination/kept.txt', 'text');
        $archive = $this->createArchive(['my_module/my_module.php' => '<?php']);

        $this->createExtractor()->extract($archive, $this->workDir . '/destination');

        self::assertSame(['kept.txt', 'my_module'], $this->listFiles($this->workDir . '/destination'));
    }

    public function testNothingIsExtractedWhenTheSandboxCannotBeCreated(): void
    {
        // A file where the sandbox folder should be
        file_put_contents($this->workDir . '/not-a-folder', 'text');
        $archive = $this->createArchive(['my_module/my_module.php' => '<?php']);

        $exception = $this->extractAndCatch(new SafeZipExtractor($this->workDir . '/not-a-folder'), $archive);

        self::assertSame(ZipExtractionException::SANDBOX_UNAVAILABLE, $exception->getCode());
        self::assertFalse($exception->isInvalidArchive());
        // The sandbox path stays in the previous exception, out of the message
        self::assertStringNotContainsString('not-a-folder', $exception->getMessage());
        self::assertInstanceOf(IOException::class, $exception->getPrevious());
        self::assertSame([], $this->listFiles($this->workDir . '/destination'));
    }

    public function testSandboxDefaultsToTheShopCacheSandbox(): void
    {
        $sandboxPath = new ReflectionProperty(SafeZipExtractor::class, 'sandboxPath');
        $sandboxPath->setAccessible(true);

        self::assertSame(_PS_CACHE_DIR_ . 'sandbox' . DIRECTORY_SEPARATOR, $sandboxPath->getValue(new SafeZipExtractor()));
    }

    private function extractAndCatch(SafeZipExtractor $extractor, string $archive, ?string $folder = null, ?string $destination = null): ZipExtractionException
    {
        try {
            $extractor->extract($archive, $destination ?? $this->workDir . '/destination', $folder);
        } catch (ZipExtractionException $exception) {
            return $exception;
        }

        self::fail('The archive should not have been extracted');
    }

    private function createExtractor(): SafeZipExtractor
    {
        return new SafeZipExtractor($this->workDir . '/sandbox');
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
            if ('' !== $name && '/' === substr($name, -1)) {
                $zip->addEmptyDir(rtrim($name, '/'));
            } else {
                $zip->addFromString($name, $content);
            }
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
