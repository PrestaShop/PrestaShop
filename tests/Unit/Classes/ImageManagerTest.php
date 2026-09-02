<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use ImageManager;
use PHPUnit\Framework\TestCase;

class ImageManagerTest extends TestCase
{
    /**
     * @dataProvider dataProviderIsCorrectImageFileExt
     *
     * @param string $filename
     * @param array|null $authorizedExtensions
     * @param bool $isCorrectImageFileExt
     */
    public function testIsCorrectImageFileExt(
        string $filename,
        ?array $authorizedExtensions,
        bool $isCorrectImageFileExt
    ): void {
        self::assertSame(
            $isCorrectImageFileExt,
            ImageManager::isCorrectImageFileExt($filename, $authorizedExtensions)
        );
    }

    public function dataProviderIsCorrectImageFileExt(): array
    {
        return [
            ['name', null, false],
            ['name.gif', null, true],
            ['name.jpg', null, true],
            ['name.jpeg', null, true],
            ['name.jpe', null, true],
            ['name.png', null, true],
            ['name.webp', null, true],
            ['name.name.gif', null, true],
            ['name.GIF', null, true],
            ['name.doc', ['doc'], true],
            ['name.gif', ['doc'], false],
        ];
    }

    /**
     * @dataProvider dataProviderGetMimeTypeByExtension
     *
     * @param string $filename
     * @param string $getMimeTypeByExtension
     */
    public function testGetMimeTypeByExtension(
        string $filename,
        string $getMimeTypeByExtension
    ): void {
        self::assertSame(
            $getMimeTypeByExtension,
            ImageManager::getMimeTypeByExtension($filename)
        );
    }

    public function dataProviderGetMimeTypeByExtension(): array
    {
        return [
            ['file.gif', 'image/gif'],
            ['file.jpg', 'image/jpeg'],
            ['file.jpeg', 'image/jpeg'],
            ['file.png', 'image/png'],
            ['file.webp', 'image/webp'],
            ['file.test', 'image/jpeg'],
            ['file', 'image/jpeg'],
        ];
    }
}
