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

namespace Tests\Unit\Adapter\Product\Update\Filler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\ProductBasicInformationPropertyFiller;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Product;

class ProductBasicInformationPropertyFillerTest extends TestCase
{
    private const DEFAULT_LANG_ID = 1;
    private const DEFAULT_SHOP_ID = 2;
    private const PRODUCT_ID = 3;

    /**
     * @var ProductBasicInformationPropertyFiller
     */
    private $basicInformationFiller;

    public function setUp(): void
    {
        parent::setUp();
        $this->basicInformationFiller = new ProductBasicInformationPropertyFiller(
            self::DEFAULT_LANG_ID,
            $this->mockTools()
        );
    }

    /**
     * @dataProvider getExpectedData
     */
    public function testFillsUpdatableProperties(UpdateProductCommand $command, array $expectedUpdatableProperties): void
    {
        $product = $this->mockProduct();

        $this->assertSame($expectedUpdatableProperties, $this->basicInformationFiller->fillUpdatableProperties($product, $command));
    }

    /**
     * @return iterable
     */
    public function getExpectedData(): iterable
    {
        $command = $this->getEmptyCommand();

        yield [$command, []];

        $command = $this
            ->getEmptyCommand()
            ->setLocalizedNames([
                self::DEFAULT_LANG_ID => 'My name',
                2 => 'Your name',
            ])
        ;

        yield [
            $command,
            ['name' => [self::DEFAULT_LANG_ID, 2]],
        ];

        $command = $this
            ->getEmptyCommand()
            ->setLocalizedNames([
                self::DEFAULT_LANG_ID => 'My name',
            ])
            ->setLocalizedShortDescriptions([
                self::DEFAULT_LANG_ID => 'short desc 1',
                2 => 'short desc 2',
            ])
            ->setLocalizedDescriptions([
                self::DEFAULT_LANG_ID => 'desc 1',
            ])
        ;

        yield [
            $command,
            [
                'name' => [self::DEFAULT_LANG_ID],
                'description' => [self::DEFAULT_LANG_ID],
                'description_short' => [self::DEFAULT_LANG_ID, 2],
            ],
        ];
    }

    /**
     * @return UpdateProductCommand
     */
    private function getEmptyCommand(): UpdateProductCommand
    {
        return new UpdateProductCommand(self::PRODUCT_ID, ShopConstraint::shop(self::DEFAULT_SHOP_ID));
    }

    /**
     * @return Product
     */
    private function mockProduct(): Product
    {
        $product = $this->createMock(Product::class);
        $product->name = [];
        $product->description = [];
        $product->description_short = [];
        $product->link_rewrite = [];

        return $product;
    }

    /**
     * @return Tools
     */
    private function mockTools(): Tools
    {
        $toolsMock = $this->getMockBuilder(Tools::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['linkRewrite'])
            ->getMock()
        ;

        $toolsMock
            ->method('linkRewrite')
            ->willReturnArgument(0)
        ;

        return $toolsMock;
    }
}
