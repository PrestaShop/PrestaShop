<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryProvider;
use Symfony\Contracts\Service\ServiceProviderInterface;

class GridFactoryProviderTest extends TestCase
{
    public function testItReturnsTheGridFactoryRegisteredUnderTheGivenServiceId(): void
    {
        $gridFactory = $this->createMock(GridFactoryInterface::class);

        $locator = $this->createMock(ServiceProviderInterface::class);
        $locator
            ->method('get')
            ->with('prestashop.core.grid.factory.order')
            ->willReturn($gridFactory);

        $provider = new GridFactoryProvider($locator);

        $this->assertSame($gridFactory, $provider->getFactory('prestashop.core.grid.factory.order'));
    }
}
