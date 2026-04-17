<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Tax;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Tax\Ecotax\ProductEcotaxResetterInterface;
use PrestaShop\PrestaShop\Core\Tax\TaxOptionsConfiguration;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class TaxOptionsConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;
    private const TAX_ADDRESS_TYPE = 'id_address_invoice';
    private const ECOTAX_TAX_RULES_GROUP_ID = 5;

    /**
     * @var ProductEcotaxResetterInterface
     */
    protected $mockProductEcotaxResetter;

    protected function setUp(): void
    {
        $this->mockConfiguration = $this->createConfigurationMock();
        $this->mockShopConfiguration = $this->createShopContextMock();
        $this->mockMultistoreFeature = $this->createMultistoreFeatureMock();
        $this->mockProductEcotaxResetter = $this->createProductEcotaxResetterMock();
    }

    /**
     * @return ProductEcotaxResetterInterface
     */
    protected function createProductEcotaxResetterMock(): ProductEcotaxResetterInterface
    {
        return $this->getMockBuilder(ProductEcotaxResetterInterface::class)
            ->onlyMethods(['reset'])
            ->getMock();
    }

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $TaxOptionsConfiguration = new TaxOptionsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature, $this->mockProductEcotaxResetter);

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_TAX', false, $shopConstraint, true],
                    ['PS_TAX_DISPLAY', false, $shopConstraint, true],
                    ['PS_TAX_ADDRESS_TYPE', null, $shopConstraint, self::TAX_ADDRESS_TYPE],
                    ['PS_USE_ECOTAX', false, $shopConstraint, true],
                    ['PS_ECOTAX_TAX_RULES_GROUP_ID', 0, $shopConstraint, self::ECOTAX_TAX_RULES_GROUP_ID],
                ]
            );

        $result = $TaxOptionsConfiguration->getConfiguration();
        $this->assertSame(
            [
                'enable_tax' => true,
                'display_tax_in_cart' => true,
                'tax_address_type' => self::TAX_ADDRESS_TYPE,
                'use_eco_tax' => true,
                'eco_tax_rule_group' => self::ECOTAX_TAX_RULES_GROUP_ID,
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
        $TaxOptionsConfiguration = new TaxOptionsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature, $this->mockProductEcotaxResetter);

        $this->expectException($exception);

        $TaxOptionsConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, [
                'enable_tax' => 'wrong_type',
                'display_tax_in_cart' => true,
                'use_eco_tax' => true,
                'tax_address_type' => self::TAX_ADDRESS_TYPE,
                'eco_tax_rule_group' => self::ECOTAX_TAX_RULES_GROUP_ID,
            ]],
            [InvalidOptionsException::class, [
                'enable_tax' => true,
                'display_tax_in_cart' => 'wrong_type',
                'use_eco_tax' => true,
                'tax_address_type' => self::TAX_ADDRESS_TYPE,
                'eco_tax_rule_group' => self::ECOTAX_TAX_RULES_GROUP_ID,
            ]],
            [InvalidOptionsException::class, [
                'enable_tax' => true,
                'display_tax_in_cart' => true,
                'use_eco_tax' => 'wrong_type',
                'tax_address_type' => self::TAX_ADDRESS_TYPE,
                'eco_tax_rule_group' => 3,
            ]],
            [InvalidOptionsException::class, [
                'enable_tax' => true,
                'display_tax_in_cart' => true,
                'use_eco_tax' => true,
                'tax_address_type' => true,
                'eco_tax_rule_group' => self::ECOTAX_TAX_RULES_GROUP_ID,
            ]],
            [InvalidOptionsException::class, [
                'enable_tax' => true,
                'display_tax_in_cart' => true,
                'use_eco_tax' => true,
                'tax_address_type' => self::TAX_ADDRESS_TYPE,
                'eco_tax_rule_group' => 'wrong_type',
            ]],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $TaxOptionsConfiguration = new TaxOptionsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature, $this->mockProductEcotaxResetter);

        $res = $TaxOptionsConfiguration->updateConfiguration([
            'enable_tax' => true,
            'display_tax_in_cart' => true,
            'tax_address_type' => self::TAX_ADDRESS_TYPE,
            'use_eco_tax' => true,
            'eco_tax_rule_group' => self::ECOTAX_TAX_RULES_GROUP_ID,
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
