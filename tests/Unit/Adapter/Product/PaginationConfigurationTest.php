<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Product\PaginationConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class PaginationConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    /**
     * @dataProvider provideShopConstraints
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $paginationConfiguration = new PaginationConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_PRODUCTS_PER_PAGE', 12, $shopConstraint, '24'],
                    ['PS_PRODUCTS_PER_PAGE_MAX', 96, $shopConstraint, '120'],
                    ['PS_PRODUCTS_ORDER_BY', 0, $shopConstraint, '4'],
                    ['PS_PRODUCTS_ORDER_WAY', 0, $shopConstraint, '1'],
                ]
            );

        $this->assertSame(
            [
                'products_per_page' => 24,
                'max_products_per_page' => 120,
                'default_order_by' => 4,
                'default_order_way' => 1,
            ],
            $paginationConfiguration->getConfiguration()
        );
    }

    /**
     * @dataProvider provideInvalidConfiguration
     */
    public function testUpdateConfigurationWithInvalidConfiguration(string $exception, array $values): void
    {
        $paginationConfiguration = new PaginationConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->expectException($exception);
        $paginationConfiguration->updateConfiguration($values);
    }

    public static function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, ['products_per_page' => 12, 'max_products_per_page' => 'wrong_value', 'default_order_by' => 4, 'default_order_way' => 0]],
        ];
    }

    /**
     * @dataProvider provideMaxProductsPerPage
     */
    public function testSuccessfulUpdate(int $maxProductsPerPage): void
    {
        $paginationConfiguration = new PaginationConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->assertSame([], $paginationConfiguration->updateConfiguration([
            'products_per_page' => 12,
            'max_products_per_page' => $maxProductsPerPage,
            'default_order_by' => 4,
            'default_order_way' => 0,
        ]));
    }

    public static function provideMaxProductsPerPage(): iterable
    {
        yield 'limited' => [96];
        yield 'no limit' => [0];
    }

    public static function provideShopConstraints(): array
    {
        return [
            [ShopConstraint::shop(self::SHOP_ID)],
            [ShopConstraint::shopGroup(self::SHOP_ID)],
            [ShopConstraint::allShops()],
        ];
    }
}
