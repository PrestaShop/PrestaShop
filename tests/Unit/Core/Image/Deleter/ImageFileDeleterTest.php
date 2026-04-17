<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Image\Deleter;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Image\Deleter\ImageFileDeleter;
use Symfony\Component\Filesystem\Filesystem;

class ImageFileDeleterTest extends TestCase
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp(): void
    {
        $this->fs = new Filesystem();
        $this->root = vfsStream::setup();
    }

    public function testItDeletesAllImagesAndDirectories()
    {
        $this->fs->mkdir([
            $this->root->url() . '/p',
            $this->root->url() . '/p/1',
        ]);
        $this->fs->touch([
            $this->root->url() . '/p/1/1.jpg',
            $this->root->url() . '/p/1/1-home_default.jpg',
            $this->root->url() . '/p/1/index.php',
            $this->root->url() . '/p/index.php',
        ]);

        $imageFileDeleter = new ImageFileDeleter();
        $imageFileDeleter->deleteFromPath($this->root->url() . '/p/', true, true);

        $this->assertFalse($this->fs->exists($this->root->url() . '/p/1'));
        $this->assertTrue($this->fs->exists($this->root->url() . '/p/index.php'));
    }

    public function testItDeletesOnlyNumericImageFiles()
    {
        $deleteFiles = [
            $this->root->url() . '/c/1.jpg',
            $this->root->url() . '/c/1-home_default.jpg',
            $this->root->url() . '/c/1-cart_default.jpg',
            $this->root->url() . '/c/2.jpg',
            $this->root->url() . '/c/2-large_default.jpg',
        ];

        $keepFiles = [
            $this->root->url() . '/c/index.php',
            $this->root->url() . '/c/en.jpg',
            $this->root->url() . '/c/lt.jpg',
            $this->root->url() . '/c/fr.jpg',
        ];

        $this->fs->mkdir($this->root->url() . '/c');
        $this->fs->touch($deleteFiles);
        $this->fs->touch($keepFiles);

        $imageFileDeleter = new ImageFileDeleter();
        $imageFileDeleter->deleteFromPath($this->root->url() . '/c/');

        foreach ($deleteFiles as $filePath) {
            $this->assertFalse(
                $this->fs->exists($filePath),
                sprintf('Expected file "%s" to be deleted, but it exists.', $filePath)
            );
        }

        foreach ($keepFiles as $filePath) {
            $this->assertTrue(
                $this->fs->exists($filePath),
                sprintf('Expected file "%s" to exist, but it was deleted.', $filePath)
            );
        }
    }

    public function testItDeletesAllImagesWithAnyName()
    {
        $deleteFiles = [
            $this->root->url() . '/tmp/manufacturer_mini_1_1.jpg',
            $this->root->url() . '/tmp/manufacturer_mini_2_1.jpg',
            $this->root->url() . '/tmp/carrier_mini_1_1.jpg',
            $this->root->url() . '/tmp/carrier_mini_2_1.jpg',
            $this->root->url() . '/tmp/' . str_shuffle(md5((string) time())) . '.jpg',
        ];

        $this->fs->mkdir($this->root->url() . '/tmp');
        $this->fs->touch($deleteFiles);

        $imageFileDeleter = new ImageFileDeleter();
        $imageFileDeleter->deleteAllImages($this->root->url() . '/tmp/');

        foreach ($deleteFiles as $filePath) {
            $this->assertFalse(
                $this->fs->exists($filePath),
                sprintf('Expected file "%s" to be deleted, but it exists.', $filePath)
            );
        }
    }
}
