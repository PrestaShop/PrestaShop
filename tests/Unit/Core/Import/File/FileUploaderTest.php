<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Import\File;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Import\File\FileUploader;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShopBundle\Exception\FileUploadException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;

class FileUploaderTest extends TestCase
{
    /**
     * @var string
     */
    private $adminDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminDir = sys_get_temp_dir() . '/ps-import-' . uniqid();
        (new Filesystem())->mkdir($this->adminDir . '/import');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->adminDir);
        parent::tearDown();
    }

    public function testCsvFileIsUploaded(): void
    {
        $file = $this->createUploader()->upload($this->createUploadedFile('products.csv', "id;name\n1;Mug\n"));

        self::assertFileExists($file->getPathname());
        self::assertSame(realpath($this->adminDir . '/import'), realpath($file->getPath()));
    }

    public function testArchiveWithACsvNameIsRejected(): void
    {
        try {
            $this->createUploader()->upload($this->createUploadedFile('products.csv', "<?php __HALT_COMPILER(); ?>\r\n"));
            self::fail('The upload should have been rejected');
        } catch (FileUploadException $e) {
            self::assertSame('The uploaded file is not a valid import file.', $e->getMessage());
        }

        self::assertSame([], array_values(array_diff(scandir($this->adminDir . '/import'), ['.', '..'])));
    }

    private function createUploader(): FileUploader
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturn($this->adminDir);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return new FileUploader($translator, new ImportDirectory($configuration));
    }

    private function createUploadedFile(string $name, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'ps-upload-');
        file_put_contents($path, $content);

        return new UploadedFile($path, $name, null, null, true);
    }
}
