<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Meta;

use PrestaShop\PrestaShop\Adapter\Meta\UrlSchemaDataConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class UrlSchemaDataConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    private const VALID_CONFIGURATION = [
        'category_rule' => [
            1 => '{id}-{rewrite}{id}',
            2 => '{id}-{rewrite}{id}',
        ],
        'supplier_rule' => [
            1 => 'supplier/{id}-{rewrite}{id}',
            2 => 'supplier/{id}-{rewrite}{id}',
        ],
        'manufacturer_rule' => [
            1 => 'brand/{id}-{rewrite}{id}',
            2 => 'brand/{id}-{rewrite}{id}',
        ],
        'cms_rule' => [
            1 => 'content/{id}-{rewrite}{id}',
            2 => 'content/{id}-{rewrite}{id}',
        ],
        'cms_category_rule' => [
            1 => 'content/category/{id}-{rewrite}{id}',
            2 => 'content/category/{id}-{rewrite}{id}',
        ],
        'module' => [
            1 => 'module/{module}{/:controller}{id}',
            2 => 'module/{module}{/:controller}{id}',
        ],
        'product_rule' => [
            1 => '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}{id}.html',
            2 => '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}{id}.html',
        ],
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
     * The service definition injects the result of the language repository, which is
     * LanguageInterface objects. Feeding plain ['id_lang' => n] arrays here is what let
     * array_column() keep working in the test while returning nothing in production.
     *
     * @return LanguageInterface[]
     */
    private function getLanguages(): array
    {
        $languages = [];
        foreach ([1, 2] as $id) {
            $language = $this->createMock(LanguageInterface::class);
            $language->method('getId')->willReturn($id);
            $languages[] = $language;
        }

        return $languages;
    }

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
            self::RULES,
            $this->getLanguages()
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
     * A value stored before a language existed has no entry for it. Returning it unchanged leaves
     * that language's field empty - the same symptom this fix is about - so the default fills the gap.
     *
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfigurationFillsLanguagesMissingFromAStoredValue(ShopConstraint $shopConstraint): void
    {
        $urlSchemaDataConfiguration = new UrlSchemaDataConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            self::RULES,
            $this->getLanguages()
        );

        $this->mockShopConfiguration->method('getShopConstraint')->willReturn($shopConstraint);
        // Language 2 is absent, and language 9 no longer exists.
        $this->mockConfiguration->method('get')->willReturn([1 => 'stored/{id}', 9 => 'ghost/{id}']);

        $result = $urlSchemaDataConfiguration->getConfiguration();

        self::assertSame(
            [1 => 'stored/{id}', 2 => self::RULES['category_rule']],
            $result['category_rule'],
            'a language missing from the stored value takes the default, and a removed one is dropped'
        );
    }

    /**
     * The case no test covered: nothing stored for the route, so the default has to be
     * offered for every language. It rendered as an empty field instead.
     *
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfigurationFallsBackToTheDefaultRouteForEveryLanguage(ShopConstraint $shopConstraint): void
    {
        $urlSchemaDataConfiguration = new UrlSchemaDataConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            self::RULES,
            $this->getLanguages()
        );

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturn(null);

        $result = $urlSchemaDataConfiguration->getConfiguration();

        foreach (self::RULES as $routeId => $defaultRule) {
            self::assertSame(
                [1 => $defaultRule, 2 => $defaultRule],
                $result[$routeId],
                sprintf('route "%s" should offer its default for every language', $routeId)
            );
        }
    }

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfigurationAsArray(ShopConstraint $shopConstraint): void
    {
        $urlSchemaDataConfiguration = new UrlSchemaDataConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            self::RULES,
            $this->getLanguages()
        );

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_ROUTE_product_rule', null, $shopConstraint, [
                        1 => '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}{id}.html',
                        2 => '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}{id}.html',
                    ]],
                    ['PS_ROUTE_category_rule', null, $shopConstraint, [
                        1 => '{id}-{rewrite}{id}',
                        2 => '{id}-{rewrite}{id}',
                    ]],
                    ['PS_ROUTE_supplier_rule', null, $shopConstraint, [
                        1 => 'supplier/{id}-{rewrite}{id}',
                        2 => 'supplier/{id}-{rewrite}{id}',
                    ]],
                    ['PS_ROUTE_manufacturer_rule', null, $shopConstraint, [
                        1 => 'brand/{id}-{rewrite}{id}',
                        2 => 'brand/{id}-{rewrite}{id}',
                    ]],
                    ['PS_ROUTE_cms_rule', null, $shopConstraint, [
                        1 => 'content/{id}-{rewrite}{id}',
                        2 => 'content/{id}-{rewrite}{id}',
                    ]],
                    ['PS_ROUTE_cms_category_rule', null, $shopConstraint, [
                        1 => 'content/category/{id}-{rewrite}{id}',
                        2 => 'content/category/{id}-{rewrite}{id}',
                    ]],
                    ['PS_ROUTE_module', null, $shopConstraint, [
                        1 => 'module/{module}{/:controller}{id}',
                        2 => 'module/{module}{/:controller}{id}',
                    ]],
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
            self::RULES,
            $this->getLanguages()
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
            // An unknown key is now left to the module that owns it, so what makes this input
            // invalid is that every field the class does own is missing.
            [MissingOptionsException::class, ['does_not_exist' => 'does_not_exist']],
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
            self::RULES,
            $this->getLanguages()
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
