<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use ImageManager;
use PrestaShop\PrestaShop\Adapter\Import\ImageCopier;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * A download that succeeds without yielding a decodable image must be reported as a failure.
 *
 * The import fetches image URLs from a CSV. When the answer is an HTTP error page, a login form or
 * anything else that is not an image, the bytes are written to the temporary file and the download
 * itself is a success, so the only signal left is the resize step. Both copy implementations used to
 * discard it and answer true, which made the caller keep an image row with no file behind it and skip
 * its "Error copying image" warning.
 */
class ImportImageCopyReportsFailureTest extends KernelTestCase
{
    /**
     * High enough not to collide with a real category's image.
     */
    private const CATEGORY_ID = 999123;

    private static string $notAnImage;
    private static string $realImage;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        // What a 404 page, a redirect to a login form or an index.php rewrite leaves in the temp file.
        self::$notAnImage = _PS_TMP_IMG_DIR_ . 'copyimg_not_an_image.jpg';
        file_put_contents(self::$notAnImage, '404: Not Found');

        self::$realImage = _PS_TMP_IMG_DIR_ . 'copyimg_real_image.jpg';
        $image = imagecreatetruecolor(80, 60);
        imagejpeg($image, self::$realImage);
        imagedestroy($image);
    }

    public static function tearDownAfterClass(): void
    {
        @unlink(self::$notAnImage);
        @unlink(self::$realImage);
        parent::tearDownAfterClass();
    }

    protected function tearDown(): void
    {
        foreach (glob(_PS_CAT_IMG_DIR_ . self::CATEGORY_ID . '*.jpg') ?: [] as $generated) {
            @unlink($generated);
        }
        parent::tearDown();
    }

    public function testLegacyCopyImgReportsFailureWhenTheSourceIsNotAnImage(): void
    {
        $copied = ImageManager::copyImg(self::CATEGORY_ID, null, self::$notAnImage, 'categories', false);

        $this->assertFalse($copied, 'copyImg() must not report success when nothing could be written.');
        $this->assertFileDoesNotExist($this->destination());
    }

    public function testLegacyCopyImgStillSucceedsForARealImage(): void
    {
        $copied = ImageManager::copyImg(self::CATEGORY_ID, null, self::$realImage, 'categories', false);

        $this->assertTrue($copied);
        $this->assertFileExists($this->destination());
    }

    public function testImportCopyImgReportsFailureWhenTheSourceIsNotAnImage(): void
    {
        $copied = $this->imageCopier()->copyImg(self::CATEGORY_ID, null, self::$notAnImage, 'categories', false);

        $this->assertFalse($copied, 'ImageCopier::copyImg() must not report success when nothing could be written.');
        $this->assertFileDoesNotExist($this->destination());
    }

    public function testImportCopyImgStillSucceedsForARealImage(): void
    {
        $copied = $this->imageCopier()->copyImg(self::CATEGORY_ID, null, self::$realImage, 'categories', false);

        $this->assertTrue($copied);
        $this->assertFileExists($this->destination());
    }

    /**
     * The back office passes the negation of the "skip thumbnail regeneration" box, so an unchecked box
     * — the common case — reaches copyImg() as true. That is the path where a regression would bite, and
     * none of the cases above cover it.
     */
    public function testImportCopyImgStillRegeneratesThumbnailsForARealImage(): void
    {
        $copied = $this->imageCopier()->copyImg(self::CATEGORY_ID, null, self::$realImage, 'categories', true);

        $this->assertTrue($copied);
        $this->assertFileExists($this->destination());

        $thumbnails = glob(_PS_CAT_IMG_DIR_ . self::CATEGORY_ID . '-*.jpg') ?: [];
        $this->assertNotEmpty($thumbnails, 'Thumbnails must still be generated for a valid image.');
    }

    private function imageCopier(): ImageCopier
    {
        return self::getContainer()->get('prestashop.adapter.import.image_copier');
    }

    private function destination(): string
    {
        return _PS_CAT_IMG_DIR_ . self::CATEGORY_ID . '.jpg';
    }
}
