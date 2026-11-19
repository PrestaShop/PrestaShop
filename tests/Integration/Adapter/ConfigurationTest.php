<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter;

use Configuration as LegacyConfiguration;
use InvalidArgumentException;
use Language;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class ConfigurationTest extends KernelTestCase
{
    /**
     * @var Configuration|null
     */
    private $configuration;

    private static int $otherShopId;

    private static int $otherShopGroupId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::initMultistore();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreAllTables();
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
        Language::resetStaticCache();
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $this->configuration = $container->get('prestashop.adapter.legacy.configuration');
    }

    /**
     * @param array $setParams
     * @param array $getParams
     * @param string $expectedResult
     *
     * @dataProvider getProvider
     */
    public function testGet(array $setParams, array $getParams, string $expectedResult): void
    {
        $this->setAndGetValuesForTesting($setParams, $getParams, $expectedResult);
    }

    /**
     * @param array $setParams
     * @param array $getParams
     * @param string|null $expectedResult
     *
     * @dataProvider getWithStrictParameterProvider
     */
    public function testGetWithSrictParameter(array $setParams, array $getParams, ?string $expectedResult): void
    {
        $this->setAndGetValuesForTesting($setParams, $getParams, $expectedResult);
    }

    /**
     * @param array $setParams
     * @param array $getParams
     * @param bool $expectedResult
     *
     * @dataProvider hasProvider
     */
    public function testHas(array $setParams, array $getParams, bool $expectedResult): void
    {
        if (!empty($setParams)) {
            $this->configuration->set($setParams['key'], $setParams['value'], $setParams['shopConstraint']);
        }
        $result = $this->configuration->has($getParams['key'], $getParams['shopConstraint']);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider explicitShopConstraintProvider
     */
    public function testGetRespectsExplicitShopConstraint(
        string $scope,
        bool $strict,
        bool $withGroupOverride,
        bool $withShopOverride,
        bool $localized,
        ?string $expectedValue,
    ): void {
        $key = $localized ? 'localized_scope_isolation_test' : 'scope_isolation_test';
        $context = LegacyShop::getContext();
        $contextId = $context === LegacyShop::CONTEXT_SHOP
            ? LegacyShop::getContextShopID()
            : LegacyShop::getContextShopGroupID();

        try {
            $this->configuration->remove($key);
            $this->setScopeIsolationValues($key, $localized, $withGroupOverride, $withShopOverride);
            LegacyConfiguration::resetStaticCache();
            LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, 1);

            $constraint = $this->getExplicitShopConstraint($scope, $strict);
            $expected = $expectedValue;
            if ($localized) {
                $this->assertNotNull($expectedValue);
                $expected = $this->getLocalizedTestValues($expectedValue);
            }

            $this->assertSame($expected, $this->configuration->get($key, null, $constraint));
        } finally {
            LegacyShop::setContext($context ?? LegacyShop::CONTEXT_ALL, $contextId);
            $this->configuration->remove($key);
        }
    }

    public static function explicitShopConstraintProvider(): iterable
    {
        $cases = [
            'global' => ['global', false, true, true, 'GLOBAL'],
            'global strict' => ['global', true, true, true, 'GLOBAL'],
            'current group' => ['current_group', false, true, true, 'CURRENT_GROUP'],
            'current group strict' => ['current_group', true, true, true, 'CURRENT_GROUP'],
            'other group' => ['other_group', false, true, true, 'OTHER_GROUP'],
            'other group strict' => ['other_group', true, true, true, 'OTHER_GROUP'],
            'other group inherits global' => ['other_group', false, false, false, 'GLOBAL'],
            'current shop' => ['current_shop', false, true, true, 'CURRENT_SHOP'],
            'current shop strict' => ['current_shop', true, true, true, 'CURRENT_SHOP'],
            'other shop override' => ['other_shop', false, true, true, 'OTHER_SHOP'],
            'other shop override strict' => ['other_shop', true, true, true, 'OTHER_SHOP'],
            'other shop inherits own group' => ['other_shop', false, true, false, 'OTHER_GROUP'],
            'other shop inherits global' => ['other_shop', false, false, false, 'GLOBAL'],
        ];

        foreach ($cases as $name => [$scope, $strict, $withGroupOverride, $withShopOverride, $expected]) {
            foreach ([false, true] as $localized) {
                yield ($localized ? 'localized ' : 'scalar ') . $name => [
                    $scope, $strict, $withGroupOverride, $withShopOverride, $localized, $expected,
                ];
            }
        }

        yield 'scalar strict group does not inherit global' => ['other_group', true, false, false, false, null];
        yield 'scalar strict shop does not inherit group' => ['other_shop', true, true, false, false, null];
        yield 'scalar strict shop does not inherit global' => ['other_shop', true, false, false, false, null];
    }

    /**
     * @dataProvider strictLocalizedShopConstraintProvider
     */
    public function testStrictLocalizedReadDoesNotInherit(string $scope, bool $withGroupOverride): void
    {
        $key = 'strict_localized_scope_test';
        $context = LegacyShop::getContext();
        $contextId = $context === LegacyShop::CONTEXT_SHOP
            ? LegacyShop::getContextShopID()
            : LegacyShop::getContextShopGroupID();

        try {
            $this->configuration->remove($key);
            $this->setScopeIsolationValues($key, true, $withGroupOverride, false);
            LegacyConfiguration::resetStaticCache();
            // Use all-shops context to test strict inheritance independently of the current shop.
            LegacyShop::setContext(LegacyShop::CONTEXT_ALL);

            $constraint = $this->getExplicitShopConstraint($scope, false);
            $inheritedValues = $this->getLocalizedTestValues($withGroupOverride ? 'OTHER_GROUP' : 'GLOBAL');
            $this->assertSame($inheritedValues, $this->configuration->get($key, null, $constraint));

            $strictValues = $this->configuration->get($key, null, $constraint->clone(true));
            $this->assertSame(array_fill_keys(array_keys($inheritedValues), false), $strictValues);
        } finally {
            LegacyShop::setContext($context ?? LegacyShop::CONTEXT_ALL, $contextId);
            $this->configuration->remove($key);
        }
    }

    public static function strictLocalizedShopConstraintProvider(): iterable
    {
        yield 'group must not inherit global' => ['other_group', false];
        yield 'shop must not inherit own group' => ['other_shop', true];
        yield 'shop must not inherit global' => ['other_shop', false];
    }

    private function getExplicitShopConstraint(string $scope, bool $strict): ShopConstraint
    {
        return match ($scope) {
            'global' => ShopConstraint::allShops($strict),
            'current_group' => ShopConstraint::shopGroup(1, $strict),
            'other_group' => ShopConstraint::shopGroup(self::$otherShopGroupId, $strict),
            'current_shop' => ShopConstraint::shop(1, $strict),
            'other_shop' => ShopConstraint::shop(self::$otherShopId, $strict),
            default => throw new InvalidArgumentException(sprintf('Unexpected shop constraint scope "%s".', $scope)),
        };
    }

    private function setScopeIsolationValues(string $key, bool $localized, bool $withGroupOverride, bool $withShopOverride): void
    {
        $values = [
            [ShopConstraint::allShops(), 'GLOBAL'],
            [ShopConstraint::shopGroup(1), 'CURRENT_GROUP'],
            [ShopConstraint::shop(1), 'CURRENT_SHOP'],
        ];
        if ($withGroupOverride) {
            $values[] = [ShopConstraint::shopGroup(self::$otherShopGroupId), 'OTHER_GROUP'];
        }
        if ($withShopOverride) {
            $values[] = [ShopConstraint::shop(self::$otherShopId), 'OTHER_SHOP'];
        }

        foreach ($values as [$constraint, $value]) {
            $this->configuration->set($key, $localized ? $this->getLocalizedTestValues($value) : $value, $constraint);
        }
    }

    private function getLocalizedTestValues(string $value): array
    {
        $values = [];
        foreach (Language::getIDs(false) as $langId) {
            $values[$langId] = $value . '_' . $langId;
        }

        $this->assertNotEmpty($values, 'The multistore fixture must contain an installed language.');

        return $values;
    }

    /**
     * @return iterable
     */
    public function hasProvider(): iterable
    {
        // simple test when value doesn't exist and we ask for it in all shop context
        yield [
            [],
            [
                'key' => 'does_not_exist',
                'shopConstraint' => ShopConstraint::allShops(),
            ],
            false,
        ];
        // simple test when value doesn't exist and we ask for it in group context
        yield [
            [],
            [
                'key' => 'does_not_exist',
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            false,
        ];
        // simple test when value doesn't exist and we ask for it in single shop context
        yield [
            [],
            [
                'key' => 'does_not_exist',
                'shopConstraint' => ShopConstraint::shop(2),
            ],
            false,
        ];
        // simple test in all shop context, when value is set for this context
        yield [
            [
                'key' => 'has_key_test_1',
                'value' => 'has_value_test_1',
                'shopConstraint' => ShopConstraint::allShops(),
            ],
            [
                'key' => 'has_key_test_1',
                'shopConstraint' => ShopConstraint::allShops(),
            ],
            true,
        ];
        // simple test in group context, when value is set for this context
        yield [
            [
                'key' => 'has_key_test_2',
                'value' => 'has_value_test_2',
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            [
                'key' => 'has_key_test_2',
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            true,
        ];
        // simple test in single shop context, when value is set for this context
        yield [
            [
                'key' => 'has_key_test_3',
                'value' => 'has_value_test_3',
                'shopConstraint' => ShopConstraint::shop(2),
            ],
            [
                'key' => 'has_key_test_3',
                'shopConstraint' => ShopConstraint::shop(2),
            ],
            true,
        ];
        // test in group context, value is set for all shop context (not strict)
        yield [
            [
                'key' => 'has_key_test_4',
                'value' => 'has_value_test_4',
                'shopConstraint' => ShopConstraint::allShops(),
            ],
            [
                'key' => 'has_key_test_4',
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            true,
        ];
        // test in single shop context, value is set for parent group context (not strict)
        yield [
            [
                'key' => 'has_key_test_5',
                'value' => 'has_value_test_5',
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            [
                'key' => 'has_key_test_5',
                'shopConstraint' => ShopConstraint::shop(2),
            ],
            true,
        ];
        // test in group context with $isStrict = true, when value is set for all shop context
        yield [
            [
                'key' => 'has_key_test_6',
                'value' => 'has_value_test_6',
                'shopConstraint' => ShopConstraint::allShops(),
            ],
            [
                'key' => 'has_key_test_6',
                'shopConstraint' => ShopConstraint::shopGroup(1, true),
            ],
            false,
        ];
        // test in single shop context with $isStrict = true, when value is set for parent group
        yield [
            [
                'key' => 'has_key_test_7',
                'value' => 'has_value_test_7',
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            [
                'key' => 'has_key_test_7',
                'shopConstraint' => ShopConstraint::shop(2, true),
            ],
            false,
        ];
    }

    /**
     * @return iterable
     */
    public function getProvider(): iterable
    {
        // simple case: get an all shop config value
        yield [
            [
                'key' => 'key_test_1',
                'value' => 'value_test_1',
                'shopConstraint' => ShopConstraint::allShops(),
            ],
            [
                'key' => 'key_test_1',
                'default' => false,
                'shopConstraint' => ShopConstraint::allShops(),
            ],
            'value_test_1',
        ];
        // simple case: get a group shop config value
        yield [
            [
                'key' => 'key_test_2',
                'value' => 'value_test_2',
                'shopConstraint' => ShopConstraint::shop(2),
            ],
            [
                'key' => 'key_test_2',
                'default' => false,
                'shopConstraint' => ShopConstraint::shop(2),
            ],
            'value_test_2',
        ];
        // simple case: get a single shop config value
        yield [
            [
                'key' => 'key_test_3',
                'value' => 'value_test_3',
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            [
                'key' => 'key_test_3',
                'default' => false,
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            'value_test_3',
        ];
        // try to get a non existing value for all shop, get default value instead
        yield [
            [],
            [
                'key' => 'does_not_exist',
                'default' => 'default_value_all_shop',
                'shopConstraint' => ShopConstraint::allShops(),
            ],
            'default_value_all_shop',
        ];
        // try to get a non existing value for group shop, get default value instead
        yield [
            [],
            [
                'key' => 'does_not_exist',
                'default' => 'default_value',
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            'default_value',
        ];
        // try to get a non existing value for single shop, get default value instead
        yield [
            [],
            [
                'key' => 'does_not_exist',
                'default' => 'default_value',
                'shopConstraint' => ShopConstraint::shop(2),
            ],
            'default_value',
        ];
        // get value for a group shop, inherited from all shop
        yield [
            [
                'key' => 'all_shop_key_1',
                'value' => 'all_shop_value_1',
                'shopConstraint' => ShopConstraint::allShops(),
            ],
            [
                'key' => 'all_shop_key_1',
                'default' => false,
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            'all_shop_value_1',
        ];
        // get value for shop 2, inherited from parent group shop
        yield [
            [
                'key' => 'parent_group_key_1',
                'value' => 'parent_group_value',
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            [
                'key' => 'parent_group_key_1',
                'default' => false,
                'shopConstraint' => ShopConstraint::shop(2),
            ],
            'parent_group_value',
        ];
        // get value for shop 2, inherited from parent group which inherits from all shop
        yield [
            [
                'key' => 'all_shop_inheritance_key_1',
                'value' => 'all_shop_inheritance_value_1',
                'shopConstraint' => ShopConstraint::allShops(),
            ],
            [
                'key' => 'all_shop_inheritance_key_1',
                'default' => false,
                'shopConstraint' => ShopConstraint::shop(2),
            ],
            'all_shop_inheritance_value_1',
        ];
        // get value for shop 2, inherited from parent group even if another value is set for all shop context
        // (checks the group context priority over all shop context)
        yield [
            [
                [
                    'key' => 'group_shop_priority_key_1',
                    'value' => 'should_not_get_this_value',
                    'shopConstraint' => ShopConstraint::allShops(),
                ],
                [
                    'key' => 'group_shop_priority_key_1',
                    'value' => 'this_is_the_expected_value',
                    'shopConstraint' => ShopConstraint::shopGroup(1),
                ],
            ],
            [
                'key' => 'group_shop_priority_key_1',
                'default' => false,
                'shopConstraint' => ShopConstraint::shop(2),
            ],
            'this_is_the_expected_value',
        ];
    }

    /**
     * @return iterable
     */
    public function getWithStrictParameterProvider(): iterable
    {
        // try getting a non existing value for a aingle shop, with is strict = true => should not inherit from parent group
        yield [
            [
                'key' => 'parent_group_key_2',
                'value' => 'parent_group_value_2',
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            [
                'key' => 'parent_group_key_2',
                'default' => false,
                'shopConstraint' => ShopConstraint::shop(2, true),
            ],
            null,
        ];
        // try getting a non existing value for a group, with is strict = true => should not inherit from all shop
        yield [
            [
                'key' => 'all_shop_key_1',
                'value' => 'all_shop_value_1',
                'shopConstraint' => ShopConstraint::allShops(),
            ],
            [
                'key' => 'all_shop_key_1',
                'default' => false,
                'shopConstraint' => ShopConstraint::shopGroup(1, true),
            ],
            null,
        ];
        // try getting a non existing value for all shop, with is strict = true => should return null
        yield [
            [],
            [
                'key' => 'does_not_exist',
                'default' => false,
                'shopConstraint' => ShopConstraint::allShops(true),
            ],
            null,
        ];
        // try getting an existing value for a group, with is strict = true => should return value for this group
        yield [
            [
                'key' => 'group_key_1',
                'value' => 'group_value_1',
                'shopConstraint' => ShopConstraint::shopGroup(1),
            ],
            [
                'key' => 'group_key_1',
                'default' => false,
                'shopConstraint' => ShopConstraint::shopGroup(1, true),
            ],
            'group_value_1',
        ];
        // try getting an existing value for a single shop, with is strict = true => should return value for this shop
        yield [
            [
                'key' => 'shop_key_1',
                'value' => 'shop_value_1',
                'shopConstraint' => ShopConstraint::shop(2),
            ],
            [
                'key' => 'shop_key_1',
                'default' => false,
                'shopConstraint' => ShopConstraint::shop(2, true),
            ],
            'shop_value_1',
        ];
    }

    /**
     * @param array $setParams
     * @param array $getParams
     * @param string|null $expectedResult
     */
    private function setAndGetValuesForTesting(array $setParams, array $getParams, ?string $expectedResult): void
    {
        if (!empty($setParams) && isset($setParams['key'])) {
            $this->configuration->set($setParams['key'], $setParams['value'], $setParams['shopConstraint']);
        } elseif (!empty($setParams)) {
            foreach ($setParams as $params) {
                $this->configuration->set($params['key'], $params['value'], $params['shopConstraint']);
            }
        }
        LegacyConfiguration::resetStaticCache();
        $result = $this->configuration->get($getParams['key'], $getParams['default'], $getParams['shopConstraint']);
        $this->assertEquals($expectedResult, $result);
    }

    protected static function initMultistore(): void
    {
        DatabaseDump::restoreAllTables();
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $configuration = $container->get('prestashop.adapter.legacy.configuration');
        $entityManager = $container->get('doctrine.orm.entity_manager');

        // activate multistore
        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        // add a shop in existing group
        $shopGroup = $entityManager->find(ShopGroup::class, 1);
        $shop = new Shop();
        $shop->setActive(true);
        $shop->setIdCategory(2);
        $shop->setName('test_shop_2');
        $shop->setShopGroup($shopGroup);
        $shop->setColor('red');
        $shop->setThemeName(Theme::getDefaultTheme());
        $shop->setDeleted(false);

        $entityManager->persist($shop);
        $entityManager->flush();

        $otherGroup = new ShopGroup();
        $otherGroup->setName('configuration_scope_group');
        $otherGroup->setColor('blue');
        $otherGroup->setShareCustomer(false);
        $otherGroup->setShareOrder(false);
        $otherGroup->setShareStock(false);
        $otherGroup->setActive(true);
        $otherGroup->setDeleted(false);
        $entityManager->persist($otherGroup);

        $otherShop = new Shop();
        $otherShop->setActive(true);
        $otherShop->setIdCategory(2);
        $otherShop->setName('configuration_scope_shop');
        $otherShop->setShopGroup($otherGroup);
        $otherShop->setColor('blue');
        $otherShop->setThemeName(Theme::getDefaultTheme());
        $otherShop->setDeleted(false);
        $entityManager->persist($otherShop);

        foreach ($entityManager->getRepository(Lang::class)->findAll() as $language) {
            $language->addShop($otherShop);
        }
        $entityManager->flush();
        self::$otherShopId = $otherShop->getId();
        self::$otherShopGroupId = $otherGroup->getId();

        LegacyShop::resetStaticCache();
        Language::resetStaticCache();
    }
}
