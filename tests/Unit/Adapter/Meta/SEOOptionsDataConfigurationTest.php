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
                    ['PS_NOINDEX_URL_PARAMETERS_LIST', '', $shopConstraint, 'back,page'],
                ]
            );

        $result = $sEOOptionsDataConfiguration->getConfiguration();
        $this->assertSame(
            [
                'product_attributes_in_title' => true,
                'noindex_url_parameters_list' => 'back,page',
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
            [InvalidOptionsException::class, ['product_attributes_in_title' => 'wrong_type', 'noindex_url_parameters_list' => 'back']],
            [InvalidOptionsException::class, ['product_attributes_in_title' => true, 'noindex_url_parameters_list' => ['back']]],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $sEOOptionsDataConfiguration = new SEOOptionsDataConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $savedValues = [];
        $this->mockConfiguration
            ->method('set')
            ->willReturnCallback(function (string $key, $value) use (&$savedValues): void {
                $savedValues[$key] = $value;
            });

        $res = $sEOOptionsDataConfiguration->updateConfiguration([
            'product_attributes_in_title' => true,
            'noindex_url_parameters_list' => ' back, page ,,utm_source,back',
        ]);

        $this->assertSame([], $res);
        $this->assertSame(
            [
                'PS_PRODUCT_ATTRIBUTES_IN_TITLE' => true,
                'PS_NOINDEX_URL_PARAMETERS_LIST' => 'back,page,utm_source',
            ],
            $savedValues
        );
    }

    public function testUpdateWithHistoricalFieldOnlyKeepsNoIndexList(): void
    {
        $sEOOptionsDataConfiguration = new SEOOptionsDataConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->mockShopConfiguration
            ->method('isAllShopContext')
            ->willReturn(true);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_PRODUCT_ATTRIBUTES_IN_TITLE', false, null, false],
                    ['PS_NOINDEX_URL_PARAMETERS_LIST', '', null, 'page'],
                ]
            );

        $savedValues = [];
        $this->mockConfiguration
            ->method('set')
            ->willReturnCallback(function (string $key, $value) use (&$savedValues): void {
                $savedValues[$key] = $value;
            });

        $res = $sEOOptionsDataConfiguration->updateConfiguration([
            'product_attributes_in_title' => true,
        ]);

        $this->assertSame([], $res);
        $this->assertSame(
            [
                'PS_PRODUCT_ATTRIBUTES_IN_TITLE' => true,
                'PS_NOINDEX_URL_PARAMETERS_LIST' => 'page',
            ],
            $savedValues
        );
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
