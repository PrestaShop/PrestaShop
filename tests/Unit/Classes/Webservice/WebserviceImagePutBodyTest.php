<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Webservice;

use PHPUnit\Framework\TestCase;
use WebserviceSpecificManagementImages;

/**
 * PHP only fills $_FILES for a POST multipart body, so a PUT has to take its image from the raw
 * request body. These cases pin that, pin that a real upload still takes the upload path, and pin
 * that the body is read under the shop's size limit instead of being buffered and measured after.
 */
class WebserviceImagePutBodyTest extends TestCase
{
    private const NO_LIMIT = 1048576;

    /** @var string[] */
    private array $temporaryFilesBefore = [];

    protected function setUp(): void
    {
        $this->temporaryFilesBefore = $this->temporaryFiles();
    }

    protected function tearDown(): void
    {
        unset($_FILES['image']);

        // A failing case throws before its own unlink, and these files land in a tracked directory.
        foreach (array_diff($this->temporaryFiles(), $this->temporaryFilesBefore) as $leftOver) {
            @unlink($leftOver);
        }
    }

    public function testAPutWithoutABodyCarriesNoImage(): void
    {
        $this->assertNull($this->buildManager('')->readPutImageFile(self::NO_LIMIT));
    }

    public function testAPutWithoutABodyLeavesNoTemporaryFileBehind(): void
    {
        $before = $this->countTemporaryFiles();

        $this->buildManager('')->readPutImageFile(self::NO_LIMIT);

        $this->assertSame($before, $this->countTemporaryFiles());
    }

    public function testAPutBodyIsExposedAsAnUploadedFileWouldBe(): void
    {
        $body = 'not really a png, but the shape is what matters';
        $file = $this->buildManager($body)->readPutImageFile(self::NO_LIMIT);

        $this->assertIsArray($file);
        $this->assertSame(strlen($body), $file['size']);
        $this->assertSame(UPLOAD_ERR_OK, $file['error']);
        $this->assertFalse($file['is_upload']);
        $this->assertFileExists($file['tmp_name']);
        $this->assertSame($body, file_get_contents($file['tmp_name']));

        @unlink($file['tmp_name']);
    }

    public function testAnOversizedBodyStopsBeingReadOnceItIsKnownToBeOversized(): void
    {
        $limit = 10;
        $file = $this->buildManager(str_repeat('x', 5000))->readPutImageFile($limit);

        // One byte past the limit: enough for the caller's "> limit" check to fire, and the 4989
        // bytes after it are never read, let alone held in memory.
        $this->assertSame($limit + 1, $file['size']);
        $this->assertSame($limit + 1, filesize($file['tmp_name']));
        $this->assertGreaterThan($limit, $file['size']);

        @unlink($file['tmp_name']);
    }

    public function testABodyExactlyOnTheLimitIsReadWhole(): void
    {
        $limit = 10;
        $file = $this->buildManager(str_repeat('x', $limit))->readPutImageFile($limit);

        $this->assertSame($limit, $file['size']);
        $this->assertLessThanOrEqual($limit, $file['size']);

        @unlink($file['tmp_name']);
    }

    public function testAMultipartUploadIsPreferredWhenTheServerProvidedOne(): void
    {
        $_FILES['image'] = [
            'name' => 'photo.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/php-upload-fixture',
            'error' => UPLOAD_ERR_OK,
            'size' => 1234,
        ];

        $file = $this->buildManager('a raw body that must be ignored')->readPutImageFile(self::NO_LIMIT);

        $this->assertSame('/tmp/php-upload-fixture', $file['tmp_name']);
        $this->assertTrue($file['is_upload']);
    }

    public function testARawBodyIsMovedWithoutGoingThroughMoveUploadedFile(): void
    {
        $manager = $this->buildManager('body');
        $file = $manager->readPutImageFile(self::NO_LIMIT);
        $destination = tempnam(sys_get_temp_dir(), 'ps-test-');

        // move_uploaded_file() refuses a file PHP did not receive as an upload, so a raw body has
        // to be renamed; if this ever goes back through move_uploaded_file() it returns false.
        $this->assertTrue($manager->moveReceived($file, $destination));
        $this->assertSame('body', file_get_contents($destination));

        @unlink($destination);
    }

    private function countTemporaryFiles(): int
    {
        return count($this->temporaryFiles());
    }

    /**
     * @return string[]
     */
    private function temporaryFiles(): array
    {
        return (array) glob(_PS_TMP_IMG_DIR_ . 'PS*');
    }

    /**
     * Only the stream behind php://input is replaced, so the copy under test is the real one.
     */
    private function buildManager(string $body): object
    {
        return new class($body) extends WebserviceSpecificManagementImages {
            private string $body;

            public function __construct(string $body)
            {
                $this->body = $body;
            }

            public function readPutImageFile(int $maxBytes)
            {
                return $this->getPutImageFile($maxBytes);
            }

            public function moveReceived(array $file, string $destination): bool
            {
                return $this->moveReceivedFile($file, $destination);
            }

            protected function openRequestBody()
            {
                $stream = fopen('php://memory', 'r+b');
                fwrite($stream, $this->body);
                rewind($stream);

                return $stream;
            }
        };
    }
}
