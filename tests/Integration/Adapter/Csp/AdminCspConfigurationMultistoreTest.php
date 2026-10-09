<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Csp;

use Configuration as LegacyConfiguration;
use PrestaShop\PrestaShop\Adapter\Csp\AdminCspConfiguration;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use Shop;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\AbstractConfigurationTestCase;

/**
 * Proves the back-office CSP settings are global, not per shop. The back office is a single surface, and
 * the header reads it at the all-shops scope; so even with multistore on and one shop selected in the back
 * office, a save must land on the global (all-shops) value, never a per-shop row, or the header would
 * never see what the form saved. Exercises the real AdminCspConfiguration against a live Configuration
 * store with a shop selected in the context (the regression would write to that shop).
 */
class AdminCspConfigurationMultistoreTest extends AbstractConfigurationTestCase
{
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

    public function testAnAdminSaveWritesTheGlobalValueNotTheSelectedShop(): void
    {
        $selectedShopId = 1;

        // Multistore on, a single shop selected in the back office: the regression resolved this context
        // and wrote a per-shop row the global-reading header never saw.
        Shop::setContext(Shop::CONTEXT_SHOP, $selectedShopId);
        LegacyConfiguration::resetStaticCache();

        $this->adminCspConfiguration()->updateConfiguration([
            'enabled' => true,
            'report_only' => true,
            'retention_days' => 30,
        ]);
        LegacyConfiguration::resetStaticCache();

        // The value is readable at the all-shops scope (where the header and feature checker read it).
        $this->assertSame(30, $this->adminCspConfiguration()->getConfiguration()['retention_days']);
        $this->assertTrue(
            LegacyConfiguration::hasKey('PS_CSP_ADMIN_RETENTION_DAYS', null, null, null),
            'A global configuration row must be written for the back-office setting'
        );

        // ...and it is NOT written as a per-shop override for the selected shop.
        $this->assertFalse(
            LegacyConfiguration::hasKey('PS_CSP_ADMIN_RETENTION_DAYS', null, null, $selectedShopId),
            'Back-office CSP settings must never be written to the selected shop; the back office is global'
        );
        $this->assertFalse(
            LegacyConfiguration::hasKey('PS_CSP_ADMIN_ENABLED', null, null, $selectedShopId),
            'The enabled flag must be global, not a per-shop row'
        );
    }

    private function adminCspConfiguration(): AdminCspConfiguration
    {
        return new AdminCspConfiguration(
            $this->legacyConfigurationAdapter,
            self::$kernel->getContainer()->get('PrestaShopBundle\Entity\Repository\CspRuleRepository'),
            self::$kernel->getContainer()->get('translator'),
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

        Shop::resetContext();
    }
}
