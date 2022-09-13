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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
