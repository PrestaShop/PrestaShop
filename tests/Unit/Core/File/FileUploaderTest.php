<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\File;

function is_uploaded_file($tmpName)
{
    return $tmpName !== 'wrong-upload';
}

function move_uploaded_file($tmpName, $directory)
{
    return $tmpName !== 'wrong-move';
}

namespace Tests\Unit\Core\File;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\File\Exception;
use PrestaShop\PrestaShop\Core\File\FileUploader;
use Symfony\Component\Filesystem\Filesystem;

class FileUploaderTest extends TestCase
{
    /**
     * @var string
     */
    protected $downloadDirectory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var FileUploader
     */
    protected $object;

    protected function setUp(): void
    {
        $this->downloadDirectory = sys_get_temp_dir() . '/' . uniqid();
        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->downloadDirectory);
        $this->object = new FileUploader(
            $this->downloadDirectory,
            5
        );
    }

    protected function tearDown(): void
    {
        if ($this->filesystem->exists($this->downloadDirectory)) {
            $this->filesystem->remove($this->downloadDirectory);
        }
    }

    public function testUploadWithInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->object->upload(10);
    }

    public function testUploadWithInvalidFileSizeAndBinaryContext()
    {
        $this->expectException(Exception\MaximumSizeExceededException::class);

        $this->object->upload('123456');
    }

    public function testUploadWithInvalidFileSizeAndHttpContext()
    {
        $this->expectException(Exception\InvalidFileException::class);

        $this->object->upload([]);
    }

    public function testUploadWithInvalidFilePutContents()
    {
        $object = new FileUploader(
            '/path/to/unknow/directory',
            5
        );
        $this->expectException(Exception\FileUploadException::class);

        $object->upload('test');
    }

    public function testUploadInBinaryContext()
    {
        $result = $this->object->upload('file');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('file_name', $result);
        $this->assertArrayHasKey('mime_type', $result);
        $this->assertEquals('text/plain', $result['mime_type']);
    }

    public function testUploadInHttpWithInvalidTmpName()
    {
        $this->expectException(Exception\InvalidFileException::class);

        $result = $this->object->upload(
            [
                'size' => 3,
            ]
        );
    }

    public function testUploadInHttpWithInvalidUploadedFile()
    {
        $this->expectException(Exception\FileUploadException::class);

        $result = $this->object->upload(
            [
                'tmp_name' => 'wrong-upload',
                'size' => 3,
                'name' => 'good',
                'type' => 'text/plain',
            ]
        );
    }

    public function testUploadInHttpWithInvalidMoveUploadedFile()
    {
        $this->expectException(Exception\FileUploadException::class);

        $result = $this->object->upload(
            [
                'tmp_name' => 'wrong-move',
                'size' => 3,
                'name' => 'good',
                'type' => 'text/plain',
            ]
        );
    }

    public function testUploadInHttpWithInvalidType()
    {
        $this->expectException(Exception\InvalidFileException::class);

        $result = $this->object->upload(
            [
                'tmp_name' => 'wrong-move',
                'size' => 3,
            ]
        );
    }

    public function testUploadInHttpWithInvalidName()
    {
        $this->expectException(Exception\InvalidFileException::class);

        $result = $this->object->upload(
            [
                'tmp_name' => 'wrong-move',
                'size' => 3,
                'type' => 'text/plain',
            ]
        );
    }

    public function testUploadInHttpContext()
    {
        $result = $this->object->upload(
            [
                'tmp_name' => 'vegeta',
                'name' => 'vegeta',
                'size' => 4,
                'type' => 'text/plain',
            ]
        );
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('file_name', $result);
        $this->assertEquals('vegeta', $result['file_name']);
        $this->assertArrayHasKey('mime_type', $result);
        $this->assertEquals('text/plain', $result['mime_type']);
    }
}
