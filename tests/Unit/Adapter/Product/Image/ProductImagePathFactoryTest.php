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

namespace Tests\Unit\Adapter\Product\Image;

use Generator;
use Image;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;

class ProductImagePathFactoryTest extends TestCase
{
    /**
     * @dataProvider getArgumentsForSmokeTest
     *
     * @param bool $isLegacyImageMode
     * @param string $pathToBaseDir
     * @param string $temporaryImgDir
     * @param string $contextLangIsoCode
     */
    public function testConstructImagePathFactory(
        bool $isLegacyImageMode,
        string $pathToBaseDir,
        string $temporaryImgDir,
        string $contextLangIsoCode
    ): void {
        $imagePathFactory = new ProductImagePathFactory(
            $isLegacyImageMode,
            $pathToBaseDir,
            $temporaryImgDir,
            $contextLangIsoCode
        );
        Assert::assertInstanceOf(ProductImagePathFactory::class, $imagePathFactory);
    }

    /**
     * @dataProvider getDataForBaseImagePathBuilding
     *
     * @param string $pathToBaseDir
     * @param Image $image
     * @param string $expected
     */
    public function testGetBaseImagePath(string $pathToBaseDir, Image $image, string $expected): void
    {
        $imagePathFactory = $this->buildImagePathFactory($pathToBaseDir);

        Assert::assertEquals($expected, $imagePathFactory->getBaseImagePath($image));
    }

    /**
     * @dataProvider getDataForPathByType
     *
     * @param string $pathToBaseDir
     * @param Image $image
     * @param string $type
     * @param string $expected
     */
    public function testGetPathByType(string $pathToBaseDir, Image $image, string $type, string $expected): void
    {
        $imagePathFactory = $this->buildImagePathFactory($pathToBaseDir);

        Assert::assertEquals($expected, $imagePathFactory->getPathByType($image, $type));
    }

    /**
     * @dataProvider getDataForNoImagePath
     *
     * @param string $pathToBaseDir
     * @param string $type
     * @param string|null $langIso
     * @param string $expected
     */
    public function testGetNoImagePath(string $pathToBaseDir, string $type, ?string $langIso, string $expected): void
    {
        $imagePathFactory = $this->buildImagePathFactory($pathToBaseDir);

        Assert::assertEquals($expected, $imagePathFactory->getNoImagePath($type, $langIso));
    }

    /**
     * @return Generator
     */
    public function getArgumentsForSmokeTest(): Generator
    {
        yield [false, '/img/p/', 'img/tmp', 'en'];
        yield [true, '/img', 'img/tmp', 'en'];
        yield [true, '/img', 'img/tmp', 'lt'];
    }

    /**
     * @return Generator
     */
    public function getDataForBaseImagePathBuilding(): Generator
    {
        $img1 = $this->mockImage(10, 'jpg', '/1/0/10');
        $img2 = $this->mockImage(11, 'jpg', '/1/1/11');
        $img3 = $this->mockImage(2504, 'png', '/2/5/0/4/2504');

        yield ['/img/p', $img1, '/img/p/1/0/10.jpg'];
        yield ['whatever/img/p', $img2, 'whatever/img/p/1/1/11.jpg'];
        yield ['img/p/', $img3, 'img/p/2/5/0/4/2504.png'];
        yield ['/', $img3, '/2/5/0/4/2504.png'];
    }

    /**
     * @return Generator
     */
    public function getDataForPathByType(): Generator
    {
        $img1 = $this->mockImage(10, 'jpg', '/1/0/10');
        $img2 = $this->mockImage(11, 'jpg', '/1/1/11');
        $img3 = $this->mockImage(2504, 'png', '/2/5/0/4/2504');

        yield ['/img/p/', $img1, 'small_default', '/img/p/1/0/10-small_default.jpg'];
        yield ['/img/p', $img2, 'medium_default', '/img/p/1/1/11-medium_default.jpg'];
        yield ['img/p/', $img3, 'large_default', 'img/p/2/5/0/4/2504-large_default.png'];
        yield ['/img/p/', $img3, 'cart_default', '/img/p/2/5/0/4/2504-cart_default.png'];
    }

    public function getDataForNoImagePath(): Generator
    {
        yield ['/img/p', 'small_default', null, '/img/p/en-default-small_default.jpg'];
        yield ['/img/p', 'medium_default', 'lt', '/img/p/lt-default-medium_default.jpg'];
        yield ['/img/p', 'large_default', 'fr', '/img/p/fr-default-large_default.jpg'];
    }

    /**
     * @param int $id
     * @param string $format
     * @param string $imgPathWillReturn
     *
     * @return Image
     */
    private function mockImage(int $id, string $format, string $imgPathWillReturn): Image
    {
        $imageMock =
            $this->getMockBuilder(Image::class)
                ->setMethods(['getImgPath'])
                ->getMock();
        $imageMock->method('getImgPath')->willReturn($imgPathWillReturn);
        $imageMock->id = $id;
        $imageMock->image_format = $format;

        return $imageMock;
    }

    /**
     * @param string $pathToBaseDir
     *
     * @return ProductImagePathFactory
     */
    private function buildImagePathFactory(string $pathToBaseDir): ProductImagePathFactory
    {
        return new ProductImagePathFactory(
            false,
            $pathToBaseDir,
            '/img/tmp/',
            'en'
        );
    }
}
