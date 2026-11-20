<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\SecurityHeader;

use Configuration as LegacyConfiguration;
use PrestaShop\PrestaShop\Adapter\SecurityHeader\SecurityHeadersConfiguration;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;
use Shop;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\AbstractConfigurationTestCase;

/**
 * Proves the static-header settings are genuinely per-shop end to end: an all-shops save is inherited
 * by every shop, and a single-shop save writes an override for that shop only, leaving the all-shops
 * value and the sibling shop untouched. Exercises the real SecurityHeadersConfiguration against a live
 * Configuration store and a second shop (the AbstractMultistoreConfiguration wiring, not a mock).
 */
class SecurityHeadersConfigurationMultistoreTest extends AbstractConfigurationTestCase
{
    /** All eight fields with their all-shops baseline (X-Frame-Options SAMEORIGIN). */
    private const ALL_SHOPS_DATA = [
        'nosniff' => true,
        'frame_options' => 'SAMEORIGIN',
        'referrer_policy' => 'strict-origin',
        'hsts' => false,
        'hsts_max_age' => 15552000,
        'hsts_subdomains' => false,
        'hsts_preload' => false,
        'permissions_policy' => 'camera=()',
    ];

    /**
     * @var Shop
     */
    private $secondShop;

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
        DatabaseDump::restoreTables([
            'configuration',
            'configuration_lang',
            'shop',
        ]);

        LegacyConfiguration::resetStaticCache();
        Shop::resetContext();
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->legacyConfigurationAdapter = self::$kernel->getContainer()->get('prestashop.adapter.legacy.configuration');
        $this->multistoreFeature = self::$kernel->getContainer()->get('prestashop.adapter.multistore_feature');
        $this->initMultistore();
    }

    public function testASingleShopSaveOverridesOnlyThatShop(): void
    {
        $overriddenShopId = 1;
        $siblingShopId = (int) $this->secondShop->id;

        // 1. Save the baseline for all shops: both shops must inherit it.
        $this->securityHeadersConfiguration(ShopConstraint::allShops())->updateConfiguration(self::ALL_SHOPS_DATA);
        LegacyConfiguration::resetStaticCache();

        $this->assertSame('SAMEORIGIN', $this->readFrameOptions(ShopConstraint::shop($overriddenShopId)));
        $this->assertSame('SAMEORIGIN', $this->readFrameOptions(ShopConstraint::shop($siblingShopId)));

        // 2. Override X-Frame-Options for the first shop only (its multistore checkbox is ticked).
        $this->securityHeadersConfiguration(ShopConstraint::shop($overriddenShopId))->updateConfiguration(
            array_merge(self::ALL_SHOPS_DATA, [
                'frame_options' => 'DENY',
                MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'frame_options' => true,
            ])
        );
        LegacyConfiguration::resetStaticCache();

        // The overridden shop sees DENY; the all-shops value and the sibling shop are untouched.
        $this->assertSame('DENY', $this->readFrameOptions(ShopConstraint::shop($overriddenShopId)), 'The overridden shop must see its own value');
        $this->assertSame('SAMEORIGIN', $this->readFrameOptions(ShopConstraint::allShops()), 'The all-shops value must not change');
        $this->assertSame('SAMEORIGIN', $this->readFrameOptions(ShopConstraint::shop($siblingShopId)), 'The sibling shop must still inherit the all-shops value, not the override');

        // A physical per-shop row exists for the overridden shop only (the sibling inherits).
        $this->assertTrue(
            LegacyConfiguration::hasKey('PS_SEC_FRAME_OPTIONS', null, null, $overriddenShopId),
            'A per-shop configuration row must be written for the overriding shop'
        );
        $this->assertFalse(
            LegacyConfiguration::hasKey('PS_SEC_FRAME_OPTIONS', null, null, $siblingShopId),
            'The sibling shop must have no per-shop row (it inherits the all-shops value)'
        );
    }

    private function readFrameOptions(ShopConstraint $shopConstraint): string
    {
        return $this->securityHeadersConfiguration($shopConstraint)->getConfiguration()['frame_options'];
    }

    private function securityHeadersConfiguration(ShopConstraint $shopConstraint): SecurityHeadersConfiguration
    {
        $shopContext = $this->createShopContextMock();
        $shopContext->method('getShopConstraint')->willReturn($shopConstraint);
        $shopContext->method('isAllShopContext')->willReturn($shopConstraint->forAllShops());

        return new SecurityHeadersConfiguration(
            $this->legacyConfigurationAdapter,
            $shopContext,
            $this->multistoreFeature
        );
    }

    private function initMultistore(): void
    {
        $this->legacyConfigurationAdapter->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        $newShop = new Shop();
        $newShop->active = true;
        $newShop->id_category = 2;
        $newShop->name = 'test_shop_2';
        $newShop->id_shop_group = 1;
        $newShop->color = 'red';
        $newShop->theme_name = Theme::getDefaultTheme();
        $newShop->deleted = false;
        $newShop->add();

        $this->secondShop = $newShop;
        Shop::resetContext();
    }
}
