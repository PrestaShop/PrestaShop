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
