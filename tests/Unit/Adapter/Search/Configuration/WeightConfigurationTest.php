<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Search\Configuration;

use PrestaShop\PrestaShop\Adapter\Search\Configuration\WeightConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class WeightConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    private const VALID_CONFIGURATION = [
        'product_name_weight' => 6,
        'reference_weight' => 10,
        'short_description_weight' => 1,
        'description_weight' => 3,
        'category_weight' => 3,
        'brand_weight' => 3,
        'tags_weight' => 4,
        'attributes_weight' => 2,
        'features_weight' => 2,
    ];

    /**
     * @dataProvider provideShopConstraints
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $configuration = new WeightConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap([
                ['PS_SEARCH_WEIGHT_PNAME', 0, $shopConstraint, 6],
                ['PS_SEARCH_WEIGHT_REF', 0, $shopConstraint, 10],
                ['PS_SEARCH_WEIGHT_SHORTDESC', 0, $shopConstraint, 1],
                ['PS_SEARCH_WEIGHT_DESC', 0, $shopConstraint, 3],
                ['PS_SEARCH_WEIGHT_CNAME', 0, $shopConstraint, 3],
                ['PS_SEARCH_WEIGHT_MNAME', 0, $shopConstraint, 3],
                ['PS_SEARCH_WEIGHT_TAG', 0, $shopConstraint, 4],
                ['PS_SEARCH_WEIGHT_ATTRIBUTE', 0, $shopConstraint, 2],
                ['PS_SEARCH_WEIGHT_FEATURE', 0, $shopConstraint, 2],
            ]);

        $this->assertSame(self::VALID_CONFIGURATION, $configuration->getConfiguration());
    }

    /**
     * @dataProvider provideInvalidConfiguration
     *
     * @param array<string, mixed> $values
     */
    public function testUpdateConfigurationWithInvalidConfiguration(string $exception, array $values): void
    {
        $configuration = new WeightConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $this->expectException($exception);
        $configuration->updateConfiguration($values);
    }

    /**
     * @return array<int, array{0: class-string, 1: array<string, mixed>}>
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['product_name_weight' => 'not_an_int'])],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $configuration = new WeightConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $this->mockConfiguration
            ->expects($this->exactly(9))
            ->method('set');

        $this->assertSame([], $configuration->updateConfiguration(self::VALID_CONFIGURATION));
    }

    /**
     * @return array<int, array{0: ShopConstraint}>
     */
    public function provideShopConstraints(): array
    {
        return [
            [ShopConstraint::shop(self::SHOP_ID)],
            [ShopConstraint::shopGroup(self::SHOP_ID)],
            [ShopConstraint::allShops()],
        ];
    }
}
