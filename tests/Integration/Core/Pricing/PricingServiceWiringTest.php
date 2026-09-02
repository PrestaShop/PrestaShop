<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Pricing;

use PrestaShop\PrestaShop\Core\Pricing\Cart\Calculator\CartCalculator;
use PrestaShop\PrestaShop\Core\Pricing\Cart\Provider\CartProductProviderInterface;
use PrestaShop\PrestaShop\Core\Pricing\Cart\Provider\DatabaseCartProductProvider;
use PrestaShop\PrestaShop\Core\Pricing\Debug\CartPriceHistoryDisplayer;
use PrestaShop\PrestaShop\Core\Pricing\Debug\PricingRegistry;
use PrestaShop\PrestaShop\Core\Pricing\Debug\ProductPriceHistoryDisplayer;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\ProductCalculator;
use PrestaShop\PrestaShop\Core\Pricing\Product\Provider\CatalogProductProvider;
use PrestaShop\PrestaShop\Core\Pricing\Rounding\RoundingService;
use PrestaShop\PrestaShop\Core\Pricing\Rounding\RoundingServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PricingServiceWiringTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testRoundingServiceIsRegistered(): void
    {
        $service = self::getContainer()->get(RoundingService::class);
        $this->assertInstanceOf(RoundingService::class, $service);
    }

    public function testRoundingServiceInterfaceAlias(): void
    {
        $service = self::getContainer()->get(RoundingServiceInterface::class);
        $this->assertInstanceOf(RoundingService::class, $service);
    }

    public function testCatalogProductProviderIsRegistered(): void
    {
        $service = self::getContainer()->get(CatalogProductProvider::class);
        $this->assertInstanceOf(CatalogProductProvider::class, $service);
    }

    public function testCartOrchestratorIsRegistered(): void
    {
        $calculator = self::getContainer()->get('prestashop.pricing.cart.product_calculator');
        $this->assertInstanceOf(ProductCalculator::class, $calculator);
    }

    public function testOrderOrchestratorIsRegistered(): void
    {
        $calculator = self::getContainer()->get('prestashop.pricing.order.product_calculator');
        $this->assertInstanceOf(ProductCalculator::class, $calculator);
    }

    public function testPricingRegistryIsRegistered(): void
    {
        $registry = self::getContainer()->get(PricingRegistry::class);
        $this->assertInstanceOf(PricingRegistry::class, $registry);
    }

    public function testDatabaseCartProductProviderIsRegistered(): void
    {
        $service = self::getContainer()->get(DatabaseCartProductProvider::class);
        $this->assertInstanceOf(DatabaseCartProductProvider::class, $service);
    }

    public function testCartProductProviderInterfaceAlias(): void
    {
        $service = self::getContainer()->get(CartProductProviderInterface::class);
        $this->assertInstanceOf(DatabaseCartProductProvider::class, $service);
    }

    public function testCartCalculatorIsRegistered(): void
    {
        $calculator = self::getContainer()->get('prestashop.pricing.cart.cart_calculator');
        $this->assertInstanceOf(CartCalculator::class, $calculator);
    }

    public function testProductPriceHistoryDisplayerIsRegistered(): void
    {
        $displayer = self::getContainer()->get(ProductPriceHistoryDisplayer::class);
        $this->assertInstanceOf(ProductPriceHistoryDisplayer::class, $displayer);
    }

    public function testCartPriceHistoryDisplayerIsRegistered(): void
    {
        $displayer = self::getContainer()->get(CartPriceHistoryDisplayer::class);
        $this->assertInstanceOf(CartPriceHistoryDisplayer::class, $displayer);
    }
}
