<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use ImageManager;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageFitment;
use Symfony\Component\Filesystem\Filesystem;

class ImageManagerTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/ps_image_manager_' . uniqid() . '/';
        mkdir($this->dir);
        ImageManager::clearDecodedSource();
    }

    protected function tearDown(): void
    {
        ImageManager::clearDecodedSource();
        (new Filesystem())->remove($this->dir);
    }

    /**
     * @dataProvider fitmentProvider
     */
    public function testThumbnailsOfEveryFormatHaveTheFitmentDimensions(string $fitment): void
    {
        $source = $this->createSource(400, 300);
        [$expectedWidth, $expectedHeight] = ImageManager::computeThumbnailDimensions(400, 300, 100, 100, $fitment);

        foreach (['jpg', 'webp', 'png'] as $format) {
            $thumbnail = $this->dir . 'thumbnail.' . $format;
            $error = 0;
            $this->assertTrue(ImageManager::resize($source, $thumbnail, 100, 100, $format, true, $error, $tw, $th, 5, $sw, $sh, $fitment));
            $this->assertSame([$expectedWidth, $expectedHeight], array_slice(getimagesize($thumbnail), 0, 2), $format);
        }
    }

    public static function fitmentProvider(): iterable
    {
        yield 'fit' => [ImageFitment::FIT];
        yield 'crop' => [ImageFitment::CROP];
        yield 'bound' => [ImageFitment::BOUND];
    }

    public function testUndecodableSourceIsNotKept(): void
    {
        $corrupted = $this->createSource(400, 300, 'png');
        $content = file_get_contents($corrupted);
        // Keep the PNG header readable but break the compressed pixel data
        file_put_contents($corrupted, substr($content, 0, 45) . str_repeat("\xFF", 64) . substr($content, 109));

        $this->assertFalse(@ImageManager::resize($corrupted, $this->dir . 'corrupted.jpg', 100, 100));
        $this->assertTrue(ImageManager::resize($this->createSource(400, 300), $this->dir . 'valid.jpg', 100, 100));
    }

    /**
     * @dataProvider sourceFormatProvider
     */
    public function testFormatsSharingAResampleMatchIndependentResizes(string $sourceFormat): void
    {
        $source = $sourceFormat === 'png' ? $this->createTransparentSource() : $this->createSource(800, 600);
        $formats = ['jpg', 'png', 'webp', 'jpg'];

        foreach ($formats as $index => $format) {
            $this->assertTrue(ImageManager::resize($source, $this->dir . 'shared' . $index . '.' . $format, 150, 150, $format, true));
        }
        foreach ($formats as $index => $format) {
            ImageManager::clearDecodedSource();
            $this->assertTrue(ImageManager::resize($source, $this->dir . 'alone' . $index . '.' . $format, 150, 150, $format, true));
        }

        foreach ($formats as $index => $format) {
            $this->assertFileEquals($this->dir . 'alone' . $index . '.' . $format, $this->dir . 'shared' . $index . '.' . $format, $format);
        }
    }

    public static function sourceFormatProvider(): iterable
    {
        yield 'opaque jpg' => ['jpg'];
        yield 'transparent png' => ['png'];
    }

    public function testTransparencyIsKeptAcrossFormatsOfTheSameSize(): void
    {
        $source = $this->createTransparentSource();

        foreach (['jpg', 'png', 'webp'] as $format) {
            $this->assertTrue(ImageManager::resize($source, $this->dir . 'thumbnail.' . $format, 100, 100, $format, true, $error, $tw, $th, 5, $sw, $sh, ImageFitment::BOUND));
        }

        $jpgCorner = imagecolorsforindex($jpg = imagecreatefromjpeg($this->dir . 'thumbnail.jpg'), imagecolorat($jpg, 0, 0));
        $this->assertGreaterThan(245, min($jpgCorner['red'], $jpgCorner['green'], $jpgCorner['blue']), 'jpg background is white');
        foreach (['png' => imagecreatefrompng($this->dir . 'thumbnail.png'), 'webp' => imagecreatefromwebp($this->dir . 'thumbnail.webp')] as $format => $image) {
            $this->assertSame(127, imagecolorsforindex($image, imagecolorat($image, 0, 0))['alpha'], $format . ' corner is transparent');
            $this->assertSame(0, imagecolorsforindex($image, imagecolorat($image, 31, 21))['alpha'], $format . ' opaque area stays opaque');
        }
    }

    public function testExifOrientationIsAppliedToEveryResizeOfTheSource(): void
    {
        // 400x200 pixels displayed as 200x400 once rotated
        $source = $this->createSource(400, 200);
        $content = file_get_contents($source);
        $tiff = 'II*' . "\x00" . pack('V', 8) . pack('v', 1) . pack('vvVvv', 0x0112, 3, 1, 6, 0) . pack('V', 0);
        $exif = 'Exif' . "\x00\x00" . $tiff;
        file_put_contents($source, substr($content, 0, 2) . "\xFF\xE1" . pack('n', strlen($exif) + 2) . $exif . substr($content, 2));

        foreach ([[100, 'jpg', [50, 100]], [100, 'webp', [50, 100]], [60, 'jpg', [30, 60]]] as [$size, $format, $expectedSize]) {
            $thumbnail = $this->dir . 'rotated' . $size . '.' . $format;
            $this->assertTrue(ImageManager::resize($source, $thumbnail, $size, $size, $format, true, $error, $tw, $th, 5, $sw, $sh, ImageFitment::BOUND));
            $this->assertSame($expectedSize, array_slice(getimagesize($thumbnail), 0, 2), $thumbnail);
        }
    }

    public function testSourceRewrittenAtTheSamePathIsDecodedAgain(): void
    {
        $source = $this->dir . 'source.jpg';
        $this->createSolidSource($source, 255, 0, 0);
        $this->assertTrue(ImageManager::resize($source, $this->dir . 'red.jpg', 100, 100));

        $this->createSolidSource($source, 0, 0, 255);
        $this->assertTrue(ImageManager::resize($source, $this->dir . 'blue.jpg', 100, 100));

        $this->assertGreaterThan(200, $this->centerColor($this->dir . 'blue.jpg')['blue']);
    }

    public function testFullSizeCopyChangedAfterItsWriteIsDecodedAgain(): void
    {
        $upload = $this->dir . 'upload.jpg';
        $original = $this->dir . 'original.jpg';
        $this->createSolidSource($upload, 255, 0, 0);
        $this->assertTrue(ImageManager::resize($upload, $original));

        // What a module listening to actionOnImageResizeAfter may do to the original
        $this->createSolidSource($original, 0, 255, 0);
        $this->assertTrue(ImageManager::resize($original, $this->dir . 'thumbnail.jpg', 100, 100));

        $this->assertGreaterThan(200, $this->centerColor($this->dir . 'thumbnail.jpg')['green']);
    }

    private function createSolidSource(string $path, int $red, int $green, int $blue): void
    {
        $image = imagecreatetruecolor(400, 300);
        imagefill($image, 0, 0, imagecolorallocate($image, $red, $green, $blue));
        imagejpeg($image, $path, 90);
    }

    private function centerColor(string $path): array
    {
        $image = imagecreatefromjpeg($path);

        return imagecolorsforindex($image, imagecolorat($image, (int) (imagesx($image) / 2), (int) (imagesy($image) / 2)));
    }

    /**
     * 800x600 transparent PNG with an opaque area in the middle.
     */
    private function createTransparentSource(): string
    {
        $image = imagecreatetruecolor(800, 600);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        imagefilledrectangle($image, 200, 150, 599, 449, imagecolorallocate($image, 200, 50, 50));
        imagefilledellipse($image, 400, 300, 700, 100, imagecolorallocatealpha($image, 50, 50, 200, 60));
        $path = $this->dir . uniqid('transparent_') . '.png';
        imagepng($image, $path);

        return $path;
    }

    private function createSource(int $width, int $height, string $format = 'jpg'): string
    {
        $image = imagecreatetruecolor($width, $height);
        for ($x = 0; $x < $width; $x += 10) {
            imagefilledrectangle($image, $x, 0, $x + 4, $height - 1, imagecolorallocate($image, $x % 256, 128, 255 - $x % 256));
        }
        $path = $this->dir . uniqid('source_') . '.' . $format;
        $format === 'png' ? imagepng($image, $path) : imagejpeg($image, $path, 90);

        return $path;
    }
}
