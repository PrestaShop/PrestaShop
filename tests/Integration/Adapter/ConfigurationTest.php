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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Adapter;

use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConfigurationTest extends KernelTestCase
{
    /**
     * @var Configuration|null
     */
    private $configuration;

    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->configuration = $container->get('prestashop.adapter.legacy.configuration');

        $this->initMultistore();
    }

    /**
     * @param array $setParams
     * @param array $getParams
     * @param $expectedResult
     * @param bool $isLastTest
     *
     * @dataProvider getProvider
     */
    public function testGet(array $setParams, array $getParams, $expectedResult, bool $isLastTest): void
    {
        foreach ($setParams as $values) {
            $this->configuration->set($values['key'], $values['value'], $values['shopConstraint']);
        }
        $result = $this->configuration->get($getParams['key'], $getParams['default'], $getParams['shopConstraint']);

        $this->assertEquals($expectedResult, $result);

        if ($isLastTest) {
            $this->cleanDb();
        }
    }

    /**
     * @return array[]
     */
    public function getProvider(): array
    {
        return [
            // simple case: get an all shop config value
            [
                [
                    [
                        'key' => 'key_test_1',
                        'value' => 'value_test_1',
                        'shopConstraint' => ShopConstraint::allShops(),
                    ],
                ],
                [
                    'key' => 'key_test_1',
                    'default' => false,
                    'shopConstraint' => ShopConstraint::allShops(),
                ],
                'value_test_1',
                false,
            ],
            // set and get a value for shop 2
            [
                [
                    [
                        'key' => 'key_test_1',
                        'value' => 'value_test_1',
                        'shopConstraint' => ShopConstraint::shop(2),
                    ],
                ],
                [
                    'key' => 'key_test_1',
                    'default' => false,
                    'shopConstraint' => ShopConstraint::shop(2),
                ],
                'value_test_1',
                false,
            ],
            // try to get a non existing value for shop 2, get default value instead
            [
                [],
                [
                    'key' => 'does_not_exist',
                    'default' => 'default_value',
                    'shopConstraint' => ShopConstraint::shop(2),
                ],
                'default_value',
                false,
            ],
            // try to get a non existing value for group 1, get default value instead
            [
                [],
                [
                    'key' => 'does_not_exist',
                    'default' => 'default_value',
                    'shopConstraint' => ShopConstraint::shopGroup(1),
                ],
                'default_value',
                false,
            ],
            // get value for shop 2, inherited from parent group shop
            [
                [
                    [
                        'key' => 'parent_group_key_2',
                        'value' => 'parent_group_value',
                        'shopConstraint' => ShopConstraint::shopGroup(1),
                    ],
                ],
                [
                    'key' => 'parent_group_key_2',
                    'default' => false,
                    'shopConstraint' => ShopConstraint::shop(2),
                ],
                'parent_group_value',
                false,
            ],
            // try getting a non existing value for shop 2, with is strict = true => should not inherit from parent group
            [
                [
                    [
                        'key' => 'parent_group_key_3',
                        'value' => 'parent_group_value',
                        'shopConstraint' => ShopConstraint::shopGroup(1),
                    ],
                ],
                [
                    'key' => 'parent_group_key_3',
                    'default' => false,
                    'shopConstraint' => ShopConstraint::shop(2, true),
                ],
                null,
                false,
            ],
            // try getting a non existing value for all shop, with is strict = true => should return null
            [
                [],
                [
                    'key' => 'does_not_exist',
                    'default' => false,
                    'shopConstraint' => ShopConstraint::allShops(true),
                ],
                null,
                true,
            ],
        ];
    }

    private function initMultistore(): void
    {
        // we want to execute this only once for the whole class
        $flag = $this->configuration->get('CONFIGURATION_INTEGRATION_TEST_FLAG');

        if ($flag === null) {
            // activate multistore
            $this->configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

            // add a shop in existing group
            $shopGroup = $this->entityManager->find(ShopGroup::class, 1);
            $shop = new Shop();
            $shop->setActive(true);
            $shop->setIdCategory(2);
            $shop->setName('test_shop_2');
            $shop->setShopGroup($shopGroup);
            $shop->setColor('red');
            $shop->setThemeName('classic');
            $shop->setDeleted(false);

            $this->entityManager->persist($shop);
            $this->entityManager->flush();

            // activate flag
            $this->configuration->set('CONFIGURATION_INTEGRATION_TEST_FLAG', 1);
        }
    }

    public function cleanDb(): void
    {
        //remove newly created shop
        $shop = $this->entityManager->getRepository(Shop::class)->findOneBy(['name' => 'test_shop_2']);
        $this->entityManager->remove($shop);
        $this->entityManager->flush();
        // remove multistore feature enabling configuration
        $this->configuration->remove('PS_MULTISHOP_FEATURE_ACTIVE');
        // remove flag
        $this->configuration->remove('CONFIGURATION_INTEGRATION_TEST_FLAG');
    }
}
