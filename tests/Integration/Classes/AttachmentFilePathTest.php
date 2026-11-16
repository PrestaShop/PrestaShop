<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Attachment;
use Db;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Attachment\QueryHandler\GetAttachmentHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachment;

/**
 * The attachment file is always stored in the download directory under a generated name:
 * a stored file name can never point outside of it.
 */
class AttachmentFilePathTest extends TestCase
{
    /**
     * @var string
     */
    private $outsideFile;

    /**
     * @var int
     */
    private $attachmentId = 0;

    protected function setUp(): void
    {
        $this->outsideFile = 'ps_attachment_file_path_test_' . uniqid();
        file_put_contents(_PS_ROOT_DIR_ . '/var/' . $this->outsideFile, 'outside');
    }

    protected function tearDown(): void
    {
        @unlink(_PS_ROOT_DIR_ . '/var/' . $this->outsideFile);
        if ($this->attachmentId) {
            Db::getInstance()->delete('attachment', 'id_attachment = ' . $this->attachmentId);
            Db::getInstance()->delete('attachment_lang', 'id_attachment = ' . $this->attachmentId);
        }
    }

    public function testGetFilePathOfAGeneratedFileName(): void
    {
        $attachment = new Attachment();
        $attachment->file = sha1('attachment');

        $this->assertSame(_PS_DOWNLOAD_DIR_ . sha1('attachment'), $attachment->getFilePath());
    }

    /**
     * @dataProvider getInvalidFileNames
     */
    public function testInvalidFileNameIsRejected(string $fileName): void
    {
        $attachment = new Attachment();
        $attachment->file = $fileName;

        $this->assertNull($attachment->getFilePath());
        $this->assertNotTrue($attachment->validateField('file', $fileName));
    }

    public function getInvalidFileNames(): iterable
    {
        yield 'parent directory' => ['../var/file'];
        yield 'parent directory in the middle' => ['folder/../../var/file'];
        yield 'windows parent directory' => ['..\\var\\file'];
        yield 'absolute path' => ['/etc/hosts'];
        yield 'sub directory' => ['folder/file'];
        yield 'empty' => [''];
    }

    /**
     * Accepted by the validator as plain names, but they are directories, not files
     */
    public function testDotNamesHaveNoFilePath(): void
    {
        foreach (['.', '..'] as $fileName) {
            $attachment = new Attachment();
            $attachment->file = $fileName;
            $this->assertNull($attachment->getFilePath());
        }
    }

    public function testStoredPathOutsideOfTheDownloadDirectoryIsNeverUsed(): void
    {
        Db::getInstance()->insert('attachment', [
            'file' => '../var/' . $this->outsideFile,
            'file_name' => 'file.txt',
            'file_size' => 0,
            'mime' => 'text/plain',
        ]);
        $this->attachmentId = (int) Db::getInstance()->Insert_ID();

        $attachment = new Attachment($this->attachmentId);
        $this->assertNull($attachment->getFilePath());

        $attachment->delete();
        $this->attachmentId = 0;
        $this->assertFileExists(_PS_ROOT_DIR_ . '/var/' . $this->outsideFile);
    }

    public function testStoredPathOutsideOfTheDownloadDirectoryIsNotReturnedForDownload(): void
    {
        // An existing file next to the download directory: only the file name check can refuse it
        $outsideFile = 'ps_test_' . uniqid(); // short enough for the 40 characters column
        $outsidePath = _PS_DOWNLOAD_DIR_ . '../' . $outsideFile;
        file_put_contents($outsidePath, 'outside');
        Db::getInstance()->insert('attachment', [
            'file' => '../' . $outsideFile,
            'file_name' => 'file.txt',
            'file_size' => 0,
            'mime' => 'text/plain',
        ]);
        $this->attachmentId = (int) Db::getInstance()->Insert_ID();

        try {
            $this->expectException(AttachmentNotFoundException::class);
            (new GetAttachmentHandler(_PS_DOWNLOAD_DIR_))->handle(new GetAttachment($this->attachmentId));
        } finally {
            @unlink($outsidePath);
        }
    }
}
