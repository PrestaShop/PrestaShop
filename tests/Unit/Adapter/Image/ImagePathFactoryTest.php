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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Image;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Image\ImagePathFactory;

class ImagePathFactoryTest extends TestCase
{
    /**
     * @dataProvider getArgumentsForSmokeTest
     *
     * @param string $pathToBaseDir
     */
    public function testConstructImagePathFactory(string $pathToBaseDir): void
    {
        $imagePathFactory = new ImagePathFactory($pathToBaseDir);
        $this->assertInstanceOf(ImagePathFactory::class, $imagePathFactory);
    }

    /**
     * @return Generator
     */
    public function getArgumentsForSmokeTest(): Generator
    {
        yield ['/img/p/'];
        yield ['/img/c'];
        yield ['/img'];
    }

    /**
     * @dataProvider getDataForBaseImagePathBuilding
     *
     * @param string $pathToBaseDir
     * @param int $entityId
     * @param string $expected
     */
    public function testGetPath(string $pathToBaseDir, int $entityId, string $expected): void
    {
        $imagePathFactory = new ImagePathFactory($pathToBaseDir);

        $this->assertEquals($expected, $imagePathFactory->getPath($entityId));
    }

    public function getDataForBaseImagePathBuilding(): Generator
    {
        yield ['/img/p', 42, '/img/p/42.jpg'];
        yield ['/img/p', 51, '/img/p/51.jpg'];
        yield ['/img/c', 42, '/img/c/42.jpg'];
        yield ['/img/c', 51, '/img/c/51.jpg'];
        yield ['/img', 42, '/img/42.jpg'];
        yield ['/img', 51, '/img/51.jpg'];

        yield ['/img/p/', 42, '/img/p/42.jpg'];
        yield ['/img/p/', 51, '/img/p/51.jpg'];
        yield ['/img/c/', 42, '/img/c/42.jpg'];
        yield ['/img/c/', 51, '/img/c/51.jpg'];
        yield ['/img/', 42, '/img/42.jpg'];
        yield ['/img/', 51, '/img/51.jpg'];
    }
}
