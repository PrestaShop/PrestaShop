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

namespace Tests\Unit\Core\Form\ChoiceProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ProductImagesChoiceProvider;

class ProductImagesChoiceProviderTest extends TestCase
{
    public function testGetEmptyChoices(): void
    {
        $queryBus = $this->getMockBuilder(CommandBusInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $choiceProvider = new ProductImagesChoiceProvider($queryBus);
        $imageChoices = $choiceProvider->getChoices(['plop' => 45]);
        $this->assertEmpty($imageChoices);
    }

    public function testGetChoices(): void
    {
        $productId = 42;
        $queryBus = $this->getMockBuilder(CommandBusInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $productImages = $this->getProductImages();
        $queryBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (GetProductImages $query) use ($productId) {
                $this->assertEquals($productId, $query->getProductId()->getValue());

                return true;
            }))
            ->willReturn($productImages)
        ;

        $choiceProvider = new ProductImagesChoiceProvider($queryBus);
        $imageChoices = $choiceProvider->getChoices(['product_id' => $productId]);
        $expectedChoices = [
            'thumbnail42.jpg' => 42,
            'thumbnail51.jpg' => 51,
        ];
        $this->assertEquals($expectedChoices, $imageChoices);
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
                'thumbnail42.jpg'
            ),
            new ProductImage(
                51,
                false,
                2,
                [],
                'image51.jpg',
                'thumbnail51.jpg'
            ),
        ];
    }
}
