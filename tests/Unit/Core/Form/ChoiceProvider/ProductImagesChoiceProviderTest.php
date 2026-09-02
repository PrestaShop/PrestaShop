<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\ChoiceProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ProductImagesChoiceProvider;

class ProductImagesChoiceProviderTest extends TestCase
{
    public function testGetEmptyChoices(): void
    {
        $queryBus = $this->getMockBuilder(CommandBusInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $choiceProvider = new ProductImagesChoiceProvider($queryBus, 1, 2);
        $imageChoices = $choiceProvider->getChoices(['plop' => 45]);
        $this->assertEmpty($imageChoices);
    }

    /**
     * @dataProvider getChoicesProvider
     */
    public function testGetChoices(int $productId, int $defaultShopId, ?int $contextShopId, ShopConstraint $expectedShopConstraint): void
    {
        $queryBus = $this->getMockBuilder(CommandBusInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $productImages = $this->getProductImages();
        $queryBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (GetProductImages $query) use ($productId, $expectedShopConstraint) {
                $this->assertEquals($productId, $query->getProductId()->getValue());
                $this->assertEquals($expectedShopConstraint, $query->getShopConstraint());

                return true;
            }))
            ->willReturn($productImages)
        ;

        $choiceProvider = new ProductImagesChoiceProvider($queryBus, $defaultShopId, $contextShopId);
        $imageChoices = $choiceProvider->getChoices(['product_id' => $productId]);
        $expectedChoices = [
            'thumbnail42.jpg' => 42,
            'thumbnail51.jpg' => 51,
        ];
        $this->assertEquals($expectedChoices, $imageChoices);
    }

    public function getChoicesProvider(): array
    {
        return [
            [42, 1, 2, ShopConstraint::shop(2)],
            [42, 1, null, ShopConstraint::shop(1)],
        ];
    }

    /**
     * @return ProductImage[]
     */
    private function getProductImages(): array
    {
        return [
            new ProductImage(
                42,
                true,
                1,
                [],
                'image42.jpg',
                'thumbnail42.jpg',
                []
            ),
            new ProductImage(
                51,
                false,
                2,
                [],
                'image51.jpg',
                'thumbnail51.jpg',
                []
            ),
        ];
    }
}
