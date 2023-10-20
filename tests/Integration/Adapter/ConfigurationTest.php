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

namespace Tests\Integration\Adapter;

use Configuration as LegacyConfiguration;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
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
        $shop->setThemeName('classic');
        $shop->setDeleted(false);

        $entityManager->persist($shop);
        $entityManager->flush();

        LegacyShop::resetStaticCache();
    }
}
