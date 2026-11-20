<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Addon\Theme;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Exception\ThemeUploadException;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeZipUploader;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ThemeZipUploaderTest extends TestCase
{
    private const DUMMY_FILES_DIR = __DIR__ . '/../../../../Resources/dummyFile/';

    /**
     * @dataProvider provideZipArchives
     */
    public function testUploadAcceptsZipArchive(string $fileName): void
    {
        // Not a real HTTP upload, so the move fails: reaching it proves the archive was accepted
        $this->expectException(ThemeUploadException::class);
        $this->expectExceptionCode(ThemeUploadException::FAILED_TO_MOVE_FILE);

        $this->createUploader()->upload($this->createUploadedFile($fileName));
    }

    public static function provideZipArchives(): iterable
    {
        yield 'zip archive' => ['valid_module.zip'];
        // Guessed as application/octet-stream by fileinfo since PHP 8.6
        yield 'zip archive detected as Microsoft OOXML' => ['ooxml_detected_module.zip'];
    }

    /**
     * @dataProvider provideOtherFiles
     */
    public function testUploadRejectsFileThatIsNotZipArchive(string $fileName): void
    {
        $this->expectException(ThemeUploadException::class);
        $this->expectExceptionCode(ThemeUploadException::INVALID_MIME_TYPE);

        $this->createUploader()->upload($this->createUploadedFile($fileName));
    }

    public static function provideOtherFiles(): iterable
    {
        yield 'tar archive' => ['valid_module.tar.gz'];
        yield 'text file' => ['test_text_file.txt'];
    }

    private function createUploader(): ThemeZipUploader
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->with('_PS_ALL_THEMES_DIR_')->willReturn(sys_get_temp_dir() . DIRECTORY_SEPARATOR);

        return new ThemeZipUploader($configuration);
    }

    private function createUploadedFile(string $fileName): UploadedFile
    {
        return new UploadedFile(self::DUMMY_FILES_DIR . $fileName, $fileName, null, UPLOAD_ERR_OK, true);
    }
}
