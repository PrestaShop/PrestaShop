<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
