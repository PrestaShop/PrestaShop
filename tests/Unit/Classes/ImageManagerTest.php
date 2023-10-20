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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
