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

namespace Tests\Integration\Core\Configuration;

use Configuration as LegacyConfiguration;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;
use Shop;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\AbstractConfigurationTestCase;

class AbstractMultistoreConfigurationTest extends AbstractConfigurationTestCase
{
    /**
     * @var Configuration
     */
    protected $legacyConfigurationAdapter;

    /**
     * @var Context
     */
    protected $shopContext;

    /**
     * @var FeatureInterface
     */
    protected $multistoreFeature;

    /**
     * @var Shop
     */
    protected $newShop;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::resetConfiguration();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::resetConfiguration();
    }

    protected static function resetConfiguration(): void
    {
        // Reset created configurations and shops
        DatabaseDump::restoreTables([
            'configuration',
            'configuration_lang',
            'shop',
        ]);

        LegacyConfiguration::resetStaticCache();
        // reset shop context
        Shop::resetContext();
    }

    public function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->legacyConfigurationAdapter = self::$kernel->getContainer()->get('prestashop.adapter.legacy.configuration');
        $this->initMultistore();
        $this->multistoreFeature = self::$kernel->getContainer()->get('prestashop.adapter.multistore_feature');
    }

    /**
     * @dataProvider updateDataProvider
     *
     * @param ShopConstraint $shopConstraint
     * @param array $data
     * @param array $checkList
     */
    public function testUpdate(ShopConstraint $shopConstraint, array $data, array $checkList): void
    {
        $testedObject = $this->getDummyMultistoreConfiguration($shopConstraint);
        $testedObject->updateConfiguration($data);

        LegacyConfiguration::resetStaticCache();

        foreach ($checkList as $expectedValues) {
            $testedObject = $this->getDummyMultistoreConfiguration($expectedValues[0]);
            $testResults = $testedObject->getConfiguration();
            foreach ($expectedValues[1] as $key => $value) {
                $this->assertTrue($value === $testResults[$key]);
            }
        }
    }

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testUndefinedOptionsException(ShopConstraint $shopConstraint): void
    {
        $isAllShopContext = ($shopConstraint->getShopGroupId() === null && $shopConstraint->getShopId() === null);
        $testedObject = $this->getDummyMultistoreConfiguration($shopConstraint);
        $this->expectException(UndefinedOptionsException::class);

        if ($isAllShopContext) {
            // in all shop context, multistore field are not expected
            $testedObject->updateConfiguration(['test_conf_1' => true, MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'test_conf_1' => true]);
        } else {
            // test in other shop contexts with an undefined field
            $testedObject->updateConfiguration(['undefined_element' => true]);
        }
    }

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testInvalidOptionsException(ShopConstraint $shopConstraint): void
    {
        $isAllShopContext = ($shopConstraint->getShopGroupId() === null && $shopConstraint->getShopId() === null);
        $testedObject = $this->getDummyMultistoreConfiguration($shopConstraint);
        $this->expectException(InvalidOptionsException::class);
        $confValues = [
            'test_conf_1' => 'wrong value type',
            'test_conf_2' => true,
        ];

        if (!$isAllShopContext) {
            $confValues[MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'test_conf_1'] = true;
            $confValues[MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'test_conf_2'] = true;
        }
        $testedObject->updateConfiguration($confValues);
    }

    /**
     * @return array
     */
    public function provideShopConstraints(): array
    {
        return [
            [ShopConstraint::allShops()],
            [ShopConstraint::shopGroup(1)],
            [ShopConstraint::shop(1)],
        ];
    }

    /**
     * @return iterable
     */
    public function updateDataProvider(): iterable
    {
        // First test changes the config for all shops which impacts also shop and shopGroup
        yield [
            // Shop constraint used for update
            ShopConstraint::allShops(),
            // Data for update
            ['test_conf_1' => true, 'test_conf_2' => 'all_shop_conf2'],
            // List of checks to do (for different shop constraints, which implies creating a Configuration object for each one, in a loop)
            [
                [
                    ShopConstraint::allShops(),
                    ['test_conf_1' => true, 'test_conf_2' => 'all_shop_conf2'],
                ],
                [
                    ShopConstraint::shopGroup(1),
                    ['test_conf_1' => true, 'test_conf_2' => 'all_shop_conf2'],
                ],
                [
                    ShopConstraint::shop(1),
                    ['test_conf_1' => true, 'test_conf_2' => 'all_shop_conf2'],
                ],
            ],
        ];

        // Second test changes the config for single shop which does not impact all shops and shopGroup, only one field is checked
        yield [
            ShopConstraint::shop(1),
            ['test_conf_1' => false, 'test_conf_2' => 'single_shop_conf2', 'multistore_test_conf_2' => true],
            [
                [
                    ShopConstraint::allShops(),
                    ['test_conf_1' => true, 'test_conf_2' => 'all_shop_conf2'],
                ],
                [
                    ShopConstraint::shopGroup(1),
                    ['test_conf_1' => true, 'test_conf_2' => 'all_shop_conf2'],
                ],
                [
                    ShopConstraint::shop(1),
                    // Only test_conf_2 is modified since it was the only checkbox enabled
                    ['test_conf_1' => true, 'test_conf_2' => 'single_shop_conf2'],
                ],
            ],
        ];
    }

    /**
     * Tests that overriding an all shop config with the same value for a single shop context does work
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException
     */
    public function testCanUpdateWithSameValueAsParent(): void
    {
        // set a value for test_conf_2 in all shop
        $testedObject = $this->getDummyMultistoreConfiguration(ShopConstraint::allShops());
        $testedObject->updateConfiguration(['test_conf_1' => true, 'test_conf_2' => 'all_shop_value']);

        // set the same value for test_conf_2 in single shop
        $shopId = 1;
        $testedObject = $this->getDummyMultistoreConfiguration(ShopConstraint::shop($shopId));
        $testedObject->updateConfiguration(['test_conf_1' => true, 'test_conf_2' => 'all_shop_value', 'multistore_test_conf_2' => true]);

        // the configuration must have a specific entry for TEST_CONF_2 in shop 1 context
        $this->assertTrue(LegacyConfiguration::hasKey('TEST_CONF_2', null, null, $shopId));
    }

    /**
     * @return MockObject|Context
     */
    protected function createShopContextMock()
    {
        return $this->getMockBuilder(Context::class)
            ->setMethods(['getContextShopGroup', 'getContextShopID', 'isAllShopContext', 'getShopConstraint'])
            ->getMock();
    }

    private function initMultistore(): void
    {
        // activate multistore
        $this->legacyConfigurationAdapter->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);
        $newShop = new Shop();
        $newShop->active = true;
        $newShop->id_category = 2;
        $newShop->name = 'test_shop_2';
        $newShop->id_shop_group = 1;
        $newShop->color = 'red';
        $newShop->theme_name = 'classic';
        $newShop->deleted = false;
        $newShop->add();
        $this->newShop = $newShop;
        Shop::resetContext();
    }
}
