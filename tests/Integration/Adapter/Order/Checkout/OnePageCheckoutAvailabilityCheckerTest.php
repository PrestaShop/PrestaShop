<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Order\Checkout;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\Checkout\OnePageCheckoutAvailabilityCheckerInterface;
use PrestaShop\PrestaShop\Core\Checkout\OnePageCheckoutSettings;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class OnePageCheckoutAvailabilityCheckerTest extends KernelTestCase
{
    private OnePageCheckoutAvailabilityCheckerInterface $onePageCheckoutAvailabilityChecker;
    private FeatureFlagManager $featureFlagManager;
    private Configuration $configuration;
    private ShopContext $shopContext;

    private int $shopId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::resetTables();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::resetTables();
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->onePageCheckoutAvailabilityChecker = $container->get(OnePageCheckoutAvailabilityCheckerInterface::class);
        $this->featureFlagManager = $container->get(FeatureFlagManager::class);
        $this->configuration = $container->get(Configuration::class);
        $this->shopContext = $container->get(ShopContext::class);

        $this->shopId = $this->shopContext->getContextShopID();
    }

    protected static function resetTables(): void
    {
        DatabaseDump::restoreTables(['configuration', 'feature_flag']);
    }

    public function testItReturnsTrueWhenFeatureFlagAndShopConfigurationAreEnabled(): void
    {
        $this->configuration->set(OnePageCheckoutSettings::ONE_PAGE_CHECKOUT_ENABLED, true, ShopConstraint::shop($this->shopId));
        $this->featureFlagManager->enable(FeatureFlagSettings::FEATURE_FLAG_ONE_PAGE_CHECKOUT);

        $this->assertTrue($this->onePageCheckoutAvailabilityChecker->isEnabledForShop($this->shopId));
    }

    public function testItReturnsFalseWhenFeatureFlagIsDisabled(): void
    {
        $this->configuration->set(OnePageCheckoutSettings::ONE_PAGE_CHECKOUT_ENABLED, true, ShopConstraint::shop($this->shopId));
        $this->featureFlagManager->disable(FeatureFlagSettings::FEATURE_FLAG_ONE_PAGE_CHECKOUT);

        $this->assertFalse($this->onePageCheckoutAvailabilityChecker->isEnabledForShop($this->shopId));
    }

    public function testItReturnsFalseWhenConfigurationIsDisabled(): void
    {
        $this->configuration->set(OnePageCheckoutSettings::ONE_PAGE_CHECKOUT_ENABLED, false, ShopConstraint::shop($this->shopId));
        $this->featureFlagManager->enable(FeatureFlagSettings::FEATURE_FLAG_ONE_PAGE_CHECKOUT);

        $this->assertFalse($this->onePageCheckoutAvailabilityChecker->isEnabledForShop($this->shopId));
    }

    public function testItUsesGlobalFallbackWhenShopValueIsNotDefined(): void
    {
        $this->configuration->deleteFromContext(OnePageCheckoutSettings::ONE_PAGE_CHECKOUT_ENABLED, ShopConstraint::shop($this->shopId));
        $this->configuration->set(OnePageCheckoutSettings::ONE_PAGE_CHECKOUT_ENABLED, true, ShopConstraint::allShops());
        $this->featureFlagManager->enable(FeatureFlagSettings::FEATURE_FLAG_ONE_PAGE_CHECKOUT);

        $this->assertTrue($this->onePageCheckoutAvailabilityChecker->isEnabledForShop($this->shopId));
    }
}
