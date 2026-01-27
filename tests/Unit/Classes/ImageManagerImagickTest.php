<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;

/**
 * @requires extension imagick
 */
class ImageManagerImagickTest extends TestCase
{
    private string $fixtureFile;
    private string $outputDir;

    protected function setUp(): void
    {
        $this->outputDir = sys_get_temp_dir() . '/ps_imagick_test_' . uniqid();
        mkdir($this->outputDir, 0777, true);

        // Create a small 10x10 PNG fixture using Imagick
        $this->fixtureFile = $this->outputDir . '/fixture.png';
        $img = new \Imagick();
        $img->newImage(10, 10, new \ImagickPixel('red'));
        $img->setImageFormat('png');
        $img->writeImage($this->fixtureFile);
        $img->destroy();

        // Pre-populate Configuration cache to avoid DB access
        if (!defined('_DB_PREFIX_')) {
            define('_DB_PREFIX_', 'ps_');
        }
        $ref = new \ReflectionClass(\Configuration::class);
        $initialized = $ref->getProperty('_initialized');
        $initialized->setAccessible(true);
        $initialized->setValue(null, true);

        $cacheGlobal = $ref->getProperty('_new_cache_global');
        $cacheGlobal->setAccessible(true);
        $cacheGlobal->setValue(null, [
            'PS_IMAGE_GENERATION_METHOD' => [0 => '0'],
            'PS_JPEG_QUALITY' => [0 => '90'],
            'PS_PNG_QUALITY' => [0 => '7'],
            'PS_WEBP_QUALITY' => [0 => '80'],
            'PS_AVIF_QUALITY' => [0 => '80'],
            'PS_IMAGE_QUALITY' => [0 => 'jpg'],
        ]);

        // Ensure shop/group caches are null so Configuration::get() uses idShop=0 / idShopGroup=0
        $cacheShop = $ref->getProperty('_new_cache_shop');
        $cacheShop->setAccessible(true);
        $cacheShop->setValue(null, null);

        $cacheGroup = $ref->getProperty('_new_cache_group');
        $cacheGroup->setAccessible(true);
        $cacheGroup->setValue(null, null);
    }

    protected function tearDown(): void
    {
        $files = glob($this->outputDir . '/*');
        if ($files) {
            array_map('unlink', $files);
        }
        if (is_dir($this->outputDir)) {
            rmdir($this->outputDir);
        }

        // Reset Configuration statics
        $ref = new \ReflectionClass(\Configuration::class);
        $initialized = $ref->getProperty('_initialized');
        $initialized->setAccessible(true);
        $initialized->setValue(null, false);
    }

    public function testIsImagickAvailableReturnsTrue(): void
    {
        self::assertTrue(\ImageManager::isImagickAvailable());
    }

    public function testCalculateResizeDimensions(): void
    {
        $method = new \ReflectionMethod(\ImageManager::class, 'calculateResizeDimensions');
        $method->setAccessible(true);

        $result = $method->invoke(null, 100, 200, 50, 100);

        self::assertSame(50, $result['destinationWidth']);
        self::assertSame(100, $result['destinationHeight']);
        self::assertArrayHasKey('nextWidth', $result);
        self::assertArrayHasKey('nextHeight', $result);
    }

    public function testCalculateResizeDimensionsWithNullDestination(): void
    {
        $method = new \ReflectionMethod(\ImageManager::class, 'calculateResizeDimensions');
        $method->setAccessible(true);

        $result = $method->invoke(null, 100, 200, null, null);

        self::assertSame(100, $result['destinationWidth']);
        self::assertSame(200, $result['destinationHeight']);
    }

    /**
     * @dataProvider outputFormatProvider
     */
    public function testWriteImagickCreatesFile(string $type, string $extension): void
    {
        $output = $this->outputDir . '/output.' . $extension;

        $imagick = new \Imagick();
        $imagick->newImage(5, 5, new \ImagickPixel('green'));
        $imagick->setImageFormat($extension === 'jpg' ? 'jpeg' : $extension);

        $method = new \ReflectionMethod(\ImageManager::class, 'writeImagick');
        $method->setAccessible(true);

        $result = $method->invoke(null, $type, $imagick, $output);

        self::assertTrue($result);
        self::assertFileExists($output);
        self::assertGreaterThan(0, filesize($output));
    }

    public static function outputFormatProvider(): array
    {
        return [
            'jpg' => ['jpg', 'jpg'],
            'png' => ['png', 'png'],
            'gif' => ['gif', 'gif'],
            'webp' => ['webp', 'webp'],
        ];
    }

    public function testWriteImagickAvif(): void
    {
        $imagick = new \Imagick();
        $formats = $imagick->queryFormats('AVIF');
        $imagick->destroy();

        if (empty($formats)) {
            self::markTestSkipped('Imagick does not support AVIF on this system.');
        }

        $output = $this->outputDir . '/output.avif';

        $imagick = new \Imagick();
        $imagick->newImage(5, 5, new \ImagickPixel('green'));
        $imagick->setImageFormat('png');

        $method = new \ReflectionMethod(\ImageManager::class, 'writeImagick');
        $method->setAccessible(true);

        $result = $method->invoke(null, 'avif', $imagick, $output);

        self::assertTrue($result);
        self::assertFileExists($output);
    }

    public function testWriteImagickJpegIsValid(): void
    {
        $output = $this->outputDir . '/output_jpeg.jpg';

        $imagick = new \Imagick();
        $imagick->newImage(10, 10, new \ImagickPixel('blue'));
        $imagick->setImageFormat('jpeg');

        $method = new \ReflectionMethod(\ImageManager::class, 'writeImagick');
        $method->setAccessible(true);
        $result = $method->invoke(null, 'jpg', $imagick, $output);

        self::assertTrue($result);
        self::assertFileExists($output);

        // Verify it's a valid JPEG (starts with SOI marker FF D8)
        $bytes = file_get_contents($output, false, null, 0, 2);
        self::assertSame("\xFF\xD8", $bytes);
    }

    public function testGetImagickSourceFileType(): void
    {
        $method = new \ReflectionMethod(\ImageManager::class, 'getImagickSourceFileType');
        $method->setAccessible(true);

        $img = new \Imagick();
        $img->newImage(1, 1, new \ImagickPixel('white'));

        $img->setImageFormat('png');
        self::assertSame(IMAGETYPE_PNG, $method->invoke(null, $img));

        $img->setImageFormat('gif');
        self::assertSame(IMAGETYPE_GIF, $method->invoke(null, $img));

        $img->setImageFormat('jpeg');
        self::assertSame(IMAGETYPE_JPEG, $method->invoke(null, $img));

        $img->setImageFormat('webp');
        self::assertSame(IMAGETYPE_WEBP, $method->invoke(null, $img));

        $img->destroy();
    }

    public function testAutoOrientResetsOrientation(): void
    {
        // Create a non-square image and set a non-default orientation
        $img = new \Imagick();
        $img->newImage(20, 10, new \ImagickPixel('blue'));
        $img->setImageFormat('jpeg');
        $img->setImageOrientation(\Imagick::ORIENTATION_RIGHTTOP);

        self::assertSame(\Imagick::ORIENTATION_RIGHTTOP, $img->getImageOrientation());

        // autoOrient() should rotate and reset the orientation to TOPLEFT
        $img->autoOrient();
        self::assertSame(\Imagick::ORIENTATION_TOPLEFT, $img->getImageOrientation());
        // After rotating a 20x10 with orientation 6, dimensions should swap
        self::assertSame(10, $img->getImageWidth());
        self::assertSame(20, $img->getImageHeight());

        $img->destroy();
    }
}
