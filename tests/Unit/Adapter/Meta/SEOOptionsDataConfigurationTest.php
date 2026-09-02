<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Preferences;

use PrestaShop\PrestaShop\Adapter\Meta\SEOOptionsDataConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class SEOOptionsDataConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $sEOOptionsDataConfiguration = new SEOOptionsDataConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_PRODUCT_ATTRIBUTES_IN_TITLE', false, $shopConstraint, true],
                ]
            );

        $result = $sEOOptionsDataConfiguration->getConfiguration();
        $this->assertSame(
            [
                'product_attributes_in_title' => true,
            ],
            $result
        );
    }

    /**
     * @dataProvider provideInvalidConfiguration
     *
     * @param string $exception
     * @param array $values
     */
    public function testUpdateConfigurationWithInvalidConfiguration(string $exception, array $values): void
    {
        $sEOOptionsDataConfiguration = new SEOOptionsDataConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->expectException($exception);
        $sEOOptionsDataConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, ['product_attributes_in_title' => 'wrong_type']],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $sEOOptionsDataConfiguration = new SEOOptionsDataConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $res = $sEOOptionsDataConfiguration->updateConfiguration([
            'product_attributes_in_title' => true,
        ]);

        $this->assertSame([], $res);
    }

    /**
     * @return array[]
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
