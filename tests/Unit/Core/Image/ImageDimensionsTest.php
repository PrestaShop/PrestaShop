<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Image;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Image\ImageDimensions;

/**
 * getimagesize() only reads raster headers and returns false for an SVG, which the shop logo is allowed
 * to be. Every caller that destructured it got two nulls - and a "Cannot use bool as array" warning on
 * PHP 8.5 - so the dimensions are read here instead, and the answer is always a pair of integers.
 */
class ImageDimensionsTest extends TestCase
{
    /**
     * @var string[]
     */
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            @unlink($file);
        }
        $this->files = [];

        parent::tearDown();
    }

    /**
     * The raster path is what every caller had before and must keep: a real image still answers with
     * the size getimagesize() reads.
     */
    public function testARasterImageIsStillReadByGetimagesize(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'logo') . '.png';
        $image = imagecreatetruecolor(120, 45);
        imagepng($image, $path);
        imagedestroy($image);
        $this->files[] = $path;

        $this->assertSame([120, 45], ImageDimensions::of($path));
    }

    public function testAbsoluteWidthAndHeightAreRead(): void
    {
        $path = $this->svg('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="80"></svg>');

        $this->assertSame([200, 80], ImageDimensions::of($path));
    }

    public function testPixelUnitsAreAccepted(): void
    {
        $path = $this->svg('<svg xmlns="http://www.w3.org/2000/svg" width="200px" height="80px"></svg>');

        $this->assertSame([200, 80], ImageDimensions::of($path));
    }

    /**
     * A relative size carries no intrinsic dimension, so the viewBox is what describes the image.
     */
    public function testARelativeSizeFallsBackToTheViewBox(): void
    {
        $path = $this->svg('<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 300 120"></svg>');

        $this->assertSame([300, 120], ImageDimensions::of($path));
    }

    public function testNoSizeAtAllFallsBackToTheViewBox(): void
    {
        $path = $this->svg('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480"></svg>');

        $this->assertSame([640, 480], ImageDimensions::of($path));
    }

    public function testAnSvgCarryingNeitherYieldsZero(): void
    {
        $path = $this->svg('<svg xmlns="http://www.w3.org/2000/svg"></svg>');

        $this->assertSame([0, 0], ImageDimensions::of($path));
    }

    public function testAFileThatIsNotXmlYieldsZero(): void
    {
        $path = $this->svg('not xml at all');

        $this->assertSame([0, 0], ImageDimensions::of($path));
    }

    public function testAMissingFileYieldsZero(): void
    {
        $this->assertSame([0, 0], ImageDimensions::of('/does/not/exist.svg'));
    }

    /**
     * The point of the whole thing: a caller may destructure the result without checking it first,
     * which is what "Cannot use bool as array" on PHP 8.5 was about.
     */
    public function testTheResultIsAlwaysAPairOfIntegers(): void
    {
        foreach ([$this->svg('<svg xmlns="http://www.w3.org/2000/svg"></svg>'), '/does/not/exist.svg'] as $path) {
            [$width, $height] = ImageDimensions::of($path);

            $this->assertIsInt($width);
            $this->assertIsInt($height);
        }
    }

    private function svg(string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), 'logo') . '.svg';
        file_put_contents($path, $contents);
        $this->files[] = $path;

        return $path;
    }
}
