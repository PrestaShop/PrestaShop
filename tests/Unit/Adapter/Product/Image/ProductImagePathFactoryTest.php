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
     * @param string $basePath
     * @param string $relativeImagesPath
     * @param string $temporaryImgDir
     * @param string $contextLangIsoCode
     */
    public function testConstructImagePathFactory(
        bool $isLegacyImageMode,
        string $basePath,
        string $relativeImagesPath,
        string $temporaryImgDir,
        string $contextLangIsoCode
    ): void {
        $imagePathFactory = new ProductImagePathFactory(
            $isLegacyImageMode,
            $basePath,
            $relativeImagesPath,
            $temporaryImgDir,
            $contextLangIsoCode
        );
        Assert::assertInstanceOf(ProductImagePathFactory::class, $imagePathFactory);
    }

    /**
     * @dataProvider getDataForFullPathBuilding
     *
     * @param string $basePath
     * @param string $relativePath
     * @param string $expected
     */
    public function testBuildsFullPath(string $basePath, string $relativePath, string $expected): void
    {
        $imagePathFactory = $this->buildImagePathFactory($basePath);
        $actual = $imagePathFactory->buildFullPath($relativePath);

        Assert::assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getDataForBaseImagePathBuilding
     *
     * @param string $basePath
     * @param Image $image
     * @param string $expected
     */
    public function testGetBaseImagePath(string $basePath, Image $image, string $expected): void
    {
        $imagePathFactory = $this->buildImagePathFactory($basePath);

        Assert::assertEquals($expected, $imagePathFactory->getBaseImagePath($image));
    }

    /**
     * @dataProvider getDataForPathByType
     *
     * @param string $basePath
     * @param Image $image
     * @param string $type
     * @param string $expected
     */
    public function testGetPathByType(string $basePath, Image $image, string $type, string $expected): void
    {
        $imagePathFactory = $this->buildImagePathFactory($basePath);

        Assert::assertEquals($expected, $imagePathFactory->getPathByType($image, $type));
    }

    /**
     * @dataProvider getDataforNoImagePath
     *
     * @param string $basePath
     * @param string $type
     * @param string|null $langIso
     * @param string $expected
     */
    public function testGetNoImagePath(string $basePath, string $type, ?string $langIso, string $expected): void
    {
        $imagePathFactory = $this->buildImagePathFactory($basePath);

        Assert::assertEquals($expected, $imagePathFactory->getNoImagePath($type, $langIso));
    }

    /**
     * @return Generator
     */
    public function getArgumentsForSmokeTest(): Generator
    {
        yield [false, 'localhost/', '/img/p/', 'img/tmp', 'en'];
        yield [true, '', '/img', 'img/tmp', 'en'];
        yield [true, '', '/img', 'img/tmp', 'lt'];
    }

    /**
     * @return Generator
     */
    public function getDataForFullPathBuilding(): Generator
    {
        yield ['localhost', '/img/p/20.jpg', 'localhost/img/p/20.jpg'];
        yield ['localhost/', '/img/p/20.jpg', 'localhost/img/p/20.jpg'];
        yield ['localhost', 'img/p/20.jpg', 'localhost/img/p/20.jpg'];
        yield ['/var/www/html/prestashop/', 'img/p/20.jpg', '/var/www/html/prestashop/img/p/20.jpg'];
    }

    /**
     * @return Generator
     */
    public function getDataForBaseImagePathBuilding(): Generator
    {
        $img1 = $this->mockImage(10, 'jpg', '/1/0/10');
        $img2 = $this->mockImage(11, 'jpg', '/1/1/11');
        $img3 = $this->mockImage(2504, 'png', '/2/5/0/4/2504');

        yield ['localhost', $img1, 'localhost/img/p/1/0/10.jpg'];
        yield ['localhost', $img2, 'localhost/img/p/1/1/11.jpg'];
        yield ['localhost', $img3, 'localhost/img/p/2/5/0/4/2504.png'];
        yield ['/var/www/presta/', $img3, '/var/www/presta/img/p/2/5/0/4/2504.png'];
    }

    /**
     * @return Generator
     */
    public function getDataForPathByType(): Generator
    {
        $img1 = $this->mockImage(10, 'jpg', '/1/0/10');
        $img2 = $this->mockImage(11, 'jpg', '/1/1/11');
        $img3 = $this->mockImage(2504, 'png', '/2/5/0/4/2504');

        yield ['localhost', $img1, 'small_default', 'localhost/img/p/1/0/10-small_default.jpg'];
        yield ['localhost', $img2, 'medium_default', 'localhost/img/p/1/1/11-medium_default.jpg'];
        yield ['localhost', $img3, 'large_default', 'localhost/img/p/2/5/0/4/2504-large_default.png'];
        yield ['/var/www/presta/', $img3, 'cart_default', '/var/www/presta/img/p/2/5/0/4/2504-cart_default.png'];
    }

    public function getDataForNoImagePath(): Generator
    {
        yield ['localhost', 'small_default', null, 'localhost/img/p/en-default-small_default.jpg'];
        yield ['localhost', 'medium_default', 'lt', 'localhost/img/p/lt-default-medium_default.jpg'];
        yield ['/var/www/presta/', 'large_default', 'fr', '/var/www/presta/img/p/fr-default-large_default.jpg'];
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
                ->getMock()
        ;
        $imageMock->method('getImgPath')->willReturn($imgPathWillReturn);
        $imageMock->id = $id;
        $imageMock->image_format = $format;

        return $imageMock;
    }

    /**
     * @param string $basePath
     *
     * @return ProductImagePathFactory
     */
    private function buildImagePathFactory(string $basePath): ProductImagePathFactory
    {
        return new ProductImagePathFactory(
            false,
            $basePath,
            '/img/p',
            '/img/tmp/',
            'en'
        );
    }
}
