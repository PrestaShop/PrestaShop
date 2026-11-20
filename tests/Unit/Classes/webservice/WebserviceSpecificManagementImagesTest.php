<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\webservice;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Filesystem\Filesystem;
use WebserviceException;
use WebserviceOutputBuilder;
use WebserviceRequest;
use WebserviceSpecificManagementImages;

class WebserviceSpecificManagementImagesTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/ws-no-picture-' . uniqid() . '/';
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
    }

    public function testDeletingNoPictureImageRemovesItsAlternativeThumbnails(): void
    {
        $this->createFiles(['en.jpg', 'en-default-home_default.jpg', 'en-default-home_default.webp', 'en-default-home_default.avif', 'fr-default-home_default.webp']);

        $this->assertTrue($this->deleteNoPictureImage('en'));

        $this->assertFileDoesNotExist($this->directory . 'en-default-home_default.webp');
        $this->assertFileDoesNotExist($this->directory . 'en-default-home_default.avif');
        $this->assertFileExists($this->directory . 'fr-default-home_default.webp');
    }

    public function testFailedDeletionKeepsAlternativeThumbnails(): void
    {
        $this->createFiles(['en-default-home_default.webp']);

        $this->assertFalse($this->deleteNoPictureImage('en'));

        $this->assertFileExists($this->directory . 'en-default-home_default.webp');
    }

    public function testThumbnailRemovalFailureIsReported(): void
    {
        $this->createFiles(['en.jpg', 'en-default-home_default.jpg']);
        // A directory at the thumbnail path cannot be unlinked, on Windows or Unix.
        mkdir($this->directory . 'en-default-home_default.webp');

        $this->expectException(WebserviceException::class);
        $this->expectExceptionMessage('Cannot remove "No Picture" thumbnail');

        $this->deleteNoPictureImage('en');
    }

    private function createFiles(array $filenames): void
    {
        foreach ($filenames as $filename) {
            touch($this->directory . $filename);
        }
    }

    private function deleteNoPictureImage(string $isoCode): bool
    {
        $request = $this->createMock(WebserviceRequest::class);
        $request->method = 'DELETE';
        $request->urlSegment = ['images', 'products', 'default', $isoCode, ''];

        $management = new WebserviceSpecificManagementImages();
        $management->setWsObject($request);
        $management->setObjectOutput($this->createMock(WebserviceOutputBuilder::class));
        (new ReflectionProperty($management, 'defaultImage'))->setValue($management, true);

        return (new ReflectionMethod($management, 'deleteImageOnDisk'))->invoke(
            $management,
            $this->directory . $isoCode . '.jpg',
            [['name' => 'home_default']],
            $this->directory
        );
    }
}
