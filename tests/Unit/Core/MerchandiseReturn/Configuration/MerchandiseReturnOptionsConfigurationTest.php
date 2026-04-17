<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\MerchandiseReturn\Configuration;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\MerchandiseReturn\Configuration\MerchandiseReturnOptionsConfiguration;
use PrestaShopBundle\Form\Admin\Sell\CustomerService\MerchandiseReturn\MerchandiseReturnOptionsType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class MerchandiseReturnOptionsConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    private const VALID_CONFIGURATION = [
        MerchandiseReturnOptionsType::FIELD_ENABLE_ORDER_RETURN => true,
        MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PERIOD_IN_DAYS => 123,
        MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PREFIX => ['#RE'],
    ];

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $merchandiseReturnOptionsConfiguration = new MerchandiseReturnOptionsConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    [
                        'PS_ORDER_RETURN',
                        null,
                        $shopConstraint,
                        self::VALID_CONFIGURATION[MerchandiseReturnOptionsType::FIELD_ENABLE_ORDER_RETURN],
                    ],
                    [
                        'PS_ORDER_RETURN_NB_DAYS',
                        null,
                        $shopConstraint,
                        self::VALID_CONFIGURATION[MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PERIOD_IN_DAYS],
                    ],
                    [
                        'PS_RETURN_PREFIX',
                        null,
                        $shopConstraint,
                        self::VALID_CONFIGURATION[MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PREFIX],
                    ],
                ]
            );

        $result = $merchandiseReturnOptionsConfiguration->getConfiguration();
        $this->assertSame(
            self::VALID_CONFIGURATION,
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
        $merchandiseReturnOptionsConfiguration = new MerchandiseReturnOptionsConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $this->expectException($exception);
        $merchandiseReturnOptionsConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [
                UndefinedOptionsException::class,
                ['does_not_exist' => 'does_not_exist'],
            ],
            [
                InvalidOptionsException::class,
                array_merge(
                    self::VALID_CONFIGURATION,
                    [MerchandiseReturnOptionsType::FIELD_ENABLE_ORDER_RETURN => 'wrong_type']
                ),
            ],
            [
                InvalidOptionsException::class,
                array_merge(
                    self::VALID_CONFIGURATION,
                    [MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PERIOD_IN_DAYS => 'wrong_type']
                ),
            ],
            [
                InvalidOptionsException::class,
                array_merge(
                    self::VALID_CONFIGURATION,
                    [MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PREFIX => 'wrong_type']
                ),
            ],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $merchandiseReturnOptionsConfiguration = new MerchandiseReturnOptionsConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $res = $merchandiseReturnOptionsConfiguration->updateConfiguration(self::VALID_CONFIGURATION);

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
