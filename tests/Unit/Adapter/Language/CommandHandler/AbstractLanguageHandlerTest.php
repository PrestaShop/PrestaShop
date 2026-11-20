<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Language\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Language\CommandHandler\AbstractLanguageHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\CopyingNoPictureException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use ReflectionMethod;
use Symfony\Component\Filesystem\Filesystem;

class AbstractLanguageHandlerTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/no-picture-' . uniqid() . '/';
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
    }

    public function testOnlyAlternativeThumbnailsForTheSelectedLanguageAreRemoved(): void
    {
        $obsolete = [
            'en-default-home_default.webp',
            'en-default-home_default.avif',
            'en-default-category_default.webp',
            'en-default-custom_size.avif',
        ];
        $preserved = [
            'en.jpg',
            'en-default-home_default.jpg',
            'en-default-home_default.png',
            'fr-default-home_default.webp',
            'fr-default-home_default.avif',
            'eng-default-home_default.webp',
            '1-home_default.webp',
            'en-other.webp',
        ];
        foreach (array_merge($obsolete, $preserved) as $filename) {
            file_put_contents($this->directory . $filename, $filename);
        }

        // No format configuration is required, including when both formats are disabled.
        $this->removeThumbnails(CopyingNoPictureException::PRODUCT_IMAGE_COPY_ERROR);

        foreach ($obsolete as $filename) {
            $this->assertFileDoesNotExist($this->directory . $filename);
        }
        foreach ($preserved as $filename) {
            $this->assertSame($filename, file_get_contents($this->directory . $filename));
        }

        // Invalidating again also works when no alternative thumbnails exist.
        $this->removeThumbnails(CopyingNoPictureException::PRODUCT_IMAGE_COPY_ERROR);
    }

    /**
     * @dataProvider getCopyErrorCodes
     */
    public function testRemovalFailureIsReported(int $errorCode): void
    {
        // A directory at the thumbnail path cannot be unlinked, on Windows or Unix.
        mkdir($this->directory . 'en-default-home_default.webp');

        $this->expectException(CopyingNoPictureException::class);
        $this->expectExceptionCode($errorCode);
        $this->expectExceptionMessage('Cannot remove "No Picture" thumbnail');

        $this->removeThumbnails($errorCode);
    }

    public static function getCopyErrorCodes(): iterable
    {
        yield [CopyingNoPictureException::PRODUCT_IMAGE_COPY_ERROR];
        yield [CopyingNoPictureException::CATEGORY_IMAGE_COPY_ERROR];
        yield [CopyingNoPictureException::BRAND_IMAGE_COPY_ERROR];
    }

    private function removeThumbnails(int $errorCode): void
    {
        $handler = new class() extends AbstractLanguageHandler {};
        $method = new ReflectionMethod(AbstractLanguageHandler::class, 'removeNoPictureThumbnails');
        $method->invoke($handler, new IsoCode('en'), $this->directory, $errorCode);
    }
}
