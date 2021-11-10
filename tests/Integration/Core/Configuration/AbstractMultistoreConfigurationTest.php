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

declare(strict_types=1);

namespace Tests\Integration\Core\Configuration;

use Configuration as LegacyConfiguration;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\Resources\DummyMultistoreConfiguration;

class AbstractMultistoreConfigurationTest extends KernelTestCase
{
    /**
     * @var Configuration
     */
    protected $legacyConfiguration;

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

    public function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->legacyConfiguration = self::$kernel->getContainer()->get('prestashop.adapter.legacy.configuration');
        $this->initMultistore();
        $this->multistoreFeature = self::$kernel->getContainer()->get('prestashop.adapter.multistore_feature');
    }

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     * @param bool $isAllShopContext
     */
    public function testUpdate(ShopConstraint $shopConstraint, bool $isAllShopContext): void
    {
        $testedObject = $this->getTestableObject($shopConstraint, $isAllShopContext);

        if ($isAllShopContext) {
            // in all shop we test without multistore checkboxes (it would throw an exception, this is tested elsewhere)
            $testedObject->updateConfiguration(['test_conf_1' => true, 'test_conf_2' => 'string_result']);
        // $testedObject->updateConfiguration(['test_conf_2' => 'string_result']);
        } else {
            // Test with multistore checkboxes, data should be saved for current context
            $testedObject->updateConfiguration(
                [
                    'test_conf_1' => true,
                    MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'test_conf_1' => true,
                    'test_conf_2' => 'string_result',
                    MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'test_conf_2' => true,
                ]
            );
            // $testedObject->updateConfiguration(['test_conf_2' => 'string_result', MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'test_conf_2' => true]);
        }

        $res = $testedObject->getConfiguration();
        $this->assertSame(true, (bool) $res['test_conf_1']);
        $this->assertSame('string_result', $res['test_conf_2']);

        if ($isAllShopContext) {
            // further assertions are for group and single shop contexts
            return;
        }

        // test without multistore checkboxes, previously saved data should be removed for current context,
        // we get data previously saved for all shop context
        $testedObject->updateConfiguration(['test_conf_1' => false]);
        $testedObject->updateConfiguration(['test_conf_2' => 'string_result_not_saved']);
        $res = $testedObject->getConfiguration();
        $this->assertSame(true, (bool) $res['test_conf_1']);
        $this->assertSame('string_result', $res['test_conf_2']);
    }

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     * @param bool $isAllShopContext
     */
    public function testUndefinedOptionsException(ShopConstraint $shopConstraint, bool $isAllShopContext): void
    {
        $testedObject = $this->getTestableObject($shopConstraint, $isAllShopContext);
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
     * @param bool $isAllShopContext
     */
    public function testInvalidOptionsException(ShopConstraint $shopConstraint, bool $isAllShopContext): void
    {
        $testedObject = $this->getTestableObject($shopConstraint, $isAllShopContext);
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
     * @param ShopConstraint $shopConstraint
     * @param bool $isAllShopContext
     *
     * @return DummyMultistoreConfiguration
     */
    private function getTestableObject(ShopConstraint $shopConstraint, bool $isAllShopContext): DummyMultistoreConfiguration
    {
        // we mock the shop context so that its `getShopConstraint` method returns the ShopConstraint from our provider
        $this->shopContext = $this->createShopContextMock();
        $this->shopContext
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->shopContext
            ->method('isAllShopContext')
            ->willReturn($isAllShopContext);

        return new DummyMultistoreConfiguration(
            $this->legacyConfiguration,
            $this->shopContext,
            $this->multistoreFeature
        );
    }

    /**
     * @return array
     */
    public function provideShopConstraints(): array
    {
        return [
            [ShopConstraint::allShops(), true],
            [ShopConstraint::shopGroup(1), false],
            [ShopConstraint::shop(1), false],
        ];
    }

    /**
     * @return ShopContext
     */
    protected function createShopContextMock(): Context
    {
        return $this->getMockBuilder(Context::class)
            ->setMethods(['getContextShopGroup', 'getContextShopID', 'isAllShopContext', 'getShopConstraint'])
            ->getMock();
    }

    private function initMultistore(): void
    {
        // activate multistore
        $this->legacyConfiguration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);
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

    public static function tearDownAfterClass(): void
    {
        // remove previously created shop
        $newShopId = Shop::getIdByName('test_shop_2');
        $newShop = new Shop($newShopId);
        $newShop->delete();

        // disable multistore
        LegacyConfiguration::deleteByName('PS_MULTISHOP_FEATURE_ACTIVE');

        // reset shop context
        Shop::resetContext();
    }
}
