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
     */
    public function testUpdate(?ShopConstraint $shopConstraint): void
    {
        // we mock the shop context so that its `getShopConstraint` method returns the ShopConstraint from our provider
        $this->shopContext = $this->createShopContextMock();
        $this->shopContext
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $testedObject = new DummyMultistoreConfiguration(
            $this->legacyConfiguration,
            $this->shopContext,
            $this->multistoreFeature
        );

        // test with multistore checkboxes, data should be saved for current context
        $testedObject->updateConfiguration(['test_conf_1' => true, MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'test_conf_1' => true]);
        $testedObject->updateConfiguration(['test_conf_2' => 'string_result', MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'test_conf_2' => true]);
        $res = $testedObject->getConfiguration();
        $this->assertSame(true, $res['test_conf_1']);
        $this->assertSame('string_result', $res['test_conf_2']);

        // test without multistore checkboxes, previously saved data should be removed for current context
        $testedObject->updateConfiguration(['test_conf_1' => true]);
        $testedObject->updateConfiguration(['test_conf_2' => 'string_result']);
        $res = $testedObject->getConfiguration();
        $this->assertSame(null, $res['test_conf_1']);
        $this->assertSame(null, $res['test_conf_2']);

        // test wrong data (should get related exception)
        $this->expectException(UndefinedOptionsException::class);
        $testedObject->updateConfiguration(['undefined_element' => true, MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'test_conf_1' => true]);
        $this->expectException(InvalidOptionsException::class);
        $testedObject->updateConfiguration(['test_conf_1' => 'wrong value type', MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'test_conf_1' => true]);
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
