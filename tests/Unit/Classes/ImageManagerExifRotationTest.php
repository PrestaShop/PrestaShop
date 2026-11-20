<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use ImageManager;
use PHPUnit\Framework\TestCase;

/**
 * The rotation an EXIF orientation asks for, extracted so that the webservice image writer can apply
 * it too. It used to live inline in ImageManager::resize(), which is why the POST upload path rotated
 * an image and the PUT one did not.
 */
class ImageManagerExifRotationTest extends TestCase
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
     * @dataProvider provideOrientations
     */
    public function testTheRotationMatchesTheOrientation(int $orientation, int $expected): void
    {
        $this->assertSame($expected, ImageManager::getRotationForExifOrientation($orientation));
    }

    /**
     * @return iterable<string, array{0: int, 1: int}>
     */
    public static function provideOrientations(): iterable
    {
        yield 'upright needs no rotation' => [1, 0];
        yield 'upside down' => [3, 180];
        yield 'rotated clockwise' => [6, -90];
        yield 'rotated counter clockwise' => [8, 90];
        yield 'a mirrored orientation is not rotated' => [2, 0];
        yield 'another mirrored orientation' => [5, 0];
        yield 'an orientation outside the range' => [42, 0];
    }

    public function testAnImageWithoutExifNeedsNoRotation(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'noexif') . '.png';
        $image = imagecreatetruecolor(4, 2);
        imagepng($image, $path);
        imagedestroy($image);
        $this->files[] = $path;

        $this->assertSame(0, ImageManager::getExifRotationDegrees($path));
    }

    public function testAMissingFileNeedsNoRotation(): void
    {
        $this->assertSame(0, ImageManager::getExifRotationDegrees('/does/not/exist.jpg'));
    }
}
