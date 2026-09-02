<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\BusinessEntity\QueryHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\QueryHandler\GetPendingBusinessEntitiesCountHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetPendingBusinessEntitiesCount;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;

class GetPendingBusinessEntitiesCountHandlerTest extends TestCase
{
    public function testItCountsAcrossAllShopsInAllShopContext(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->once())
            ->method('getPendingCount')
            ->with(null)
            ->willReturn(5);

        $handler = new GetPendingBusinessEntitiesCountHandler(
            $repository,
            $this->getMockShopContext(true)
        );

        $this->assertSame(5, $handler->handle(new GetPendingBusinessEntitiesCount()));
    }

    public function testItScopesToAssociatedShopsOutsideAllShopContext(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->once())
            ->method('getPendingCount')
            ->with([1, 2])
            ->willReturn(3);

        $handler = new GetPendingBusinessEntitiesCountHandler(
            $repository,
            $this->getMockShopContext(false, [1, 2])
        );

        $this->assertSame(3, $handler->handle(new GetPendingBusinessEntitiesCount()));
    }

    private function getMockShopContext(bool $isAllShop, array $associatedShopIds = []): ShopContext
    {
        $mock = $this->createMock(ShopContext::class);
        $mock->method('isAllShopContext')->willReturn($isAllShop);
        $mock->method('getAssociatedShopIds')->willReturn($associatedShopIds);

        return $mock;
    }
}
