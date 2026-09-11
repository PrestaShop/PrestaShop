<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Shop;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Shop\LogoUploader;

/**
 * getimagesize() only reads raster headers and returns false for an SVG, which left the stored logo
 * dimensions at zero and the front office img tag without width and height.
 */
class LogoDimensionsTest extends TestCase
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

    public function testAbsoluteWidthAndHeightAreRead(): void
    {
        $path = $this->svg('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="80"></svg>');

        $this->assertSame([200, 80], $this->dimensionsOf($path));
    }

    public function testPixelUnitsAreAccepted(): void
    {
        $path = $this->svg('<svg xmlns="http://www.w3.org/2000/svg" width="200px" height="80px"></svg>');

        $this->assertSame([200, 80], $this->dimensionsOf($path));
    }

    /**
     * A relative size carries no intrinsic dimension, so the viewBox is what describes the image.
     */
    public function testARelativeSizeFallsBackToTheViewBox(): void
    {
        $path = $this->svg('<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 300 120"></svg>');

        $this->assertSame([300, 120], $this->dimensionsOf($path));
    }

    public function testNoSizeAtAllFallsBackToTheViewBox(): void
    {
        $path = $this->svg('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480"></svg>');

        $this->assertSame([640, 480], $this->dimensionsOf($path));
    }

    public function testAnSvgCarryingNeitherYieldsZero(): void
    {
        $path = $this->svg('<svg xmlns="http://www.w3.org/2000/svg"></svg>');

        $this->assertSame([0, 0], $this->dimensionsOf($path));
    }

    public function testAFileThatIsNotXmlYieldsZero(): void
    {
        $path = $this->svg('not xml at all');

        $this->assertSame([0, 0], $this->dimensionsOf($path));
    }

    public function testAMissingFileYieldsZero(): void
    {
        $this->assertSame([0, 0], $this->dimensionsOf('/does/not/exist.svg'));
    }

    private function svg(string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), 'logo') . '.svg';
        file_put_contents($path, $contents);
        $this->files[] = $path;

        return $path;
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function dimensionsOf(string $path): array
    {
        return (new TestableLogoUploader())->dimensionsOf($path);
    }
}

class TestableLogoUploader extends LogoUploader
{
    public function __construct()
    {
        // The real constructor wants a Shop and the image configuration, neither of which the
        // dimension reading touches.
    }

    /**
     * @return array{0: int, 1: int}
     */
    public function dimensionsOf(string $path): array
    {
        return $this->getLogoDimensions($path);
    }
}
