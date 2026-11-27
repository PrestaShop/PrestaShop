<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Grid;

use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryProvider;
use Tests\TestCase\SymfonyIntegrationTestCase;

class GridFactoryProviderTest extends SymfonyIntegrationTestCase
{
    public function testCoreGridFactoriesAreRegistered(): void
    {
        $provider = $this->getProvider();

        foreach (['order', 'customer', 'language', 'logs', 'cart', 'product'] as $gridId) {
            $this->assertInstanceOf(
                GridFactoryInterface::class,
                $provider->getFactory($gridId),
                sprintf('No grid factory registered for grid "%s"', $gridId)
            );
        }
    }

    public function testUnknownGridHasNoFactory(): void
    {
        $this->assertNull($this->getProvider()->getFactory('unknown_grid'));
    }

    public function testMostCoreGridsAreIndexed(): void
    {
        $gridIds = $this->getProvider()->getGridIds();

        $this->assertGreaterThan(40, count($gridIds));
    }

    private function getProvider(): GridFactoryProvider
    {
        return self::getContainer()->get(GridFactoryProvider::class);
    }
}
