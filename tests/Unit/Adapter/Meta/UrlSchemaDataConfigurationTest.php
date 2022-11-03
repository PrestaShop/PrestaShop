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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Meta;

use PrestaShop\PrestaShop\Adapter\Meta\UrlSchemaDataConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class UrlSchemaDataConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    private const VALID_CONFIGURATION = [
        'category_rule' => '{id}-{rewrite}{id}',
        'supplier_rule' => 'supplier/{id}-{rewrite}{id}',
        'manufacturer_rule' => 'brand/{id}-{rewrite}{id}',
        'cms_rule' => 'content/{id}-{rewrite}{id}',
        'cms_category_rule' => 'content/category/{id}-{rewrite}{id}',
        'module' => 'module/{module}{/:controller}{id}',
        'product_rule' => '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}{id}.html',
    ];

    /**
     * @var array
     */
    private const RULES = [
        'category_rule' => '{id}-{rewrite}',
        'supplier_rule' => 'supplier/{id}-{rewrite}',
        'manufacturer_rule' => 'brand/{id}-{rewrite}',
        'cms_rule' => 'content/{id}-{rewrite}',
        'cms_category_rule' => 'content/category/{id}-{rewrite}',
        'module' => 'module/{module}{/:controller}',
        'product_rule' => '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}.html',
    ];

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $urlSchemaDataConfiguration = new UrlSchemaDataConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            self::RULES
        );

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_ROUTE_product_rule', null, $shopConstraint, '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}{id}.html'],
                    ['PS_ROUTE_category_rule', null, $shopConstraint, '{id}-{rewrite}{id}'],
                    ['PS_ROUTE_supplier_rule', null, $shopConstraint, 'supplier/{id}-{rewrite}{id}'],
                    ['PS_ROUTE_manufacturer_rule', null, $shopConstraint, 'brand/{id}-{rewrite}{id}'],
                    ['PS_ROUTE_cms_rule', null, $shopConstraint, 'content/{id}-{rewrite}{id}'],
                    ['PS_ROUTE_cms_category_rule', null, $shopConstraint, 'content/category/{id}-{rewrite}{id}'],
                    ['PS_ROUTE_module', null, $shopConstraint, 'module/{module}{/:controller}{id}'],
                ]
            );

        $result = $urlSchemaDataConfiguration->getConfiguration();

        $this->assertSame(self::VALID_CONFIGURATION, $result);
    }

    /**
     * @dataProvider provideInvalidConfiguration
     *
     * @param string $exception
     * @param array $values
     */
    public function testUpdateConfigurationWithInvalidConfiguration(string $exception, array $values): void
    {
        $urlSchemaDataConfiguration = new UrlSchemaDataConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            self::RULES
        );

        $this->expectException($exception);
        $urlSchemaDataConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['category_rule' => false])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['supplier_rule' => false])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['manufacturer_rule' => false])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['cms_rule' => false])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['module' => false])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['product_rule' => false])],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $urlSchemaDataConfiguration = new UrlSchemaDataConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            self::RULES
        );

        $res = $urlSchemaDataConfiguration->updateConfiguration(self::VALID_CONFIGURATION);

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
