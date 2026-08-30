<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Image;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImageProvider;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * The product cover URL feeds small UI thumbnails (pack tab, product preview, discount form), so it
 * must resolve to the small_default resized image - consistent with the "no image" fallback - rather
 * than the full-size original, which broke those layouts.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/39612
 */
class ProductImageProviderTest extends TestCase
{
    public function testProductCoverUrlUsesSmallDefaultThumbnail(): void
    {
        $imageRepository = $this->createMock(ProductImageRepository::class);
        $imageRepository->method('getDefaultImageId')->willReturn(new ImageId(24));

        $pathFactory = $this->createMock(ProductImagePathFactory::class);
        $pathFactory->expects($this->once())
            ->method('getPathByType')
            ->with($this->isInstanceOf(ImageId::class), ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT)
            ->willReturn('24-small_default.jpg');
        // The full-size getPath() must no longer be used for the cover thumbnail.
        $pathFactory->expects($this->never())->method('getPath');

        $provider = new ProductImageProvider(
            $imageRepository,
            $this->createMock(CombinationRepository::class),
            $pathFactory
        );

        $this->assertSame(
            '24-small_default.jpg',
            $provider->getProductCoverUrl(new ProductId(2), new ShopId(1))
        );
    }
}
