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

namespace Tests\Unit\Adapter\Product\Image;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;

class ProductImagePathFactoryTest extends TestCase
{
    /**
     * @dataProvider getArgumentsForSmokeTest
     *
     * @param string $pathToBaseDir
     * @param string $temporaryImgDir
     * @param string $contextLangIsoCode
     */
    public function testConstructImagePathFactory(
        string $pathToBaseDir,
        string $temporaryImgDir,
        string $contextLangIsoCode
    ): void {
        $imagePathFactory = new ProductImagePathFactory(
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
     * @param ImageId $imageId
     * @param string $expected
     */
    public function testGetPath(string $pathToBaseDir, ImageId $imageId, string $expected): void
    {
        $imagePathFactory = $this->buildImagePathFactory($pathToBaseDir);

        Assert::assertEquals($expected, $imagePathFactory->getPath($imageId));
    }

    /**
     * @dataProvider getDataForPathByType
     *
     * @param string $pathToBaseDir
     * @param ImageId $imageId
     * @param string $type
     * @param string $expected
     */
    public function testGetPathByType(string $pathToBaseDir, ImageId $imageId, string $type, string $expected): void
    {
        $imagePathFactory = $this->buildImagePathFactory($pathToBaseDir);

        Assert::assertEquals($expected, $imagePathFactory->getPathByType($imageId, $type));
    }

    /**
     * @dataProvider getDataForImageFolder
     *
     * @param string $pathToBaseDir
     * @param ImageId $imageId
     * @param string $expected
     */
    public function testGetImageFolder(string $pathToBaseDir, ImageId $imageId, string $expected): void
    {
        $imagePathFactory = $this->buildImagePathFactory($pathToBaseDir);

        Assert::assertEquals($expected, $imagePathFactory->getImageFolder($imageId));
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
        yield ['/img/p/', 'img/tmp', 'en'];
        yield ['/img', 'img/tmp', 'en'];
        yield ['/img', 'img/tmp', 'lt'];
    }

    /**
     * @return Generator
     */
    public function getDataForBaseImagePathBuilding(): Generator
    {
        yield ['/img/p', new ImageId(10), '/img/p/1/0/10.jpg'];
        yield ['whatever/img/p', new ImageId(11), 'whatever/img/p/1/1/11.jpg'];
        yield ['img/p/', new ImageId(2504), 'img/p/2/5/0/4/2504.jpg'];
    }

    /**
     * @return Generator
     */
    public function getDataForPathByType(): Generator
    {
        yield ['/img/p/', new ImageId(10), ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT, '/img/p/1/0/10-small_default.jpg'];
        yield ['/img/p', new ImageId(11), ProductImagePathFactory::IMAGE_TYPE_MEDIUM_DEFAULT, '/img/p/1/1/11-medium_default.jpg'];
        yield ['img/p/', new ImageId(2504), ProductImagePathFactory::IMAGE_TYPE_LARGE_DEFAULT, 'img/p/2/5/0/4/2504-large_default.jpg'];
        yield ['/img/p/', new ImageId(2504), ProductImagePathFactory::IMAGE_TYPE_CART_DEFAULT, '/img/p/2/5/0/4/2504-cart_default.jpg'];
        yield ['/img/p/', new ImageId(2504), ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT, '/img/p/2/5/0/4/2504-home_default.jpg'];
    }

    public function getDataForImageFolder(): Generator
    {
        yield ['/img/p/', new ImageId(10), '/img/p/1/0'];
        yield ['/img/p', new ImageId(11), '/img/p/1/1'];
        yield ['img/p/', new ImageId(2504), 'img/p/2/5/0/4'];
        yield ['/img/p/', new ImageId(2504), '/img/p/2/5/0/4'];
    }

    public function getDataForNoImagePath(): Generator
    {
        yield ['/img/p', 'small_default', null, '/img/p/en-default-small_default.jpg'];
        yield ['/img/p', 'medium_default', 'lt', '/img/p/lt-default-medium_default.jpg'];
        yield ['/img/p', 'large_default', 'fr', '/img/p/fr-default-large_default.jpg'];
    }

    /**
     * @param string $pathToBaseDir
     *
     * @return ProductImagePathFactory
     */
    private function buildImagePathFactory(string $pathToBaseDir): ProductImagePathFactory
    {
        return new ProductImagePathFactory(
            $pathToBaseDir,
            '/img/tmp/',
            'en'
        );
    }
}
