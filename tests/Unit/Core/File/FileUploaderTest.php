<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
