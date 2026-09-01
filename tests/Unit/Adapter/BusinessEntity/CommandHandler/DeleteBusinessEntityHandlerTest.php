<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\BusinessEntity\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler\DeleteBusinessEntityHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\DeleteBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Psr\Log\LoggerInterface;

class DeleteBusinessEntityHandlerTest extends TestCase
{
    public function testItDelegatesTheSoftDeleteToTheRepository(): void
    {
        $businessEntity = new BusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->with(7, null)->willReturn($businessEntity);
        $repository->expects($this->once())->method('delete')->with($businessEntity);

        $handler = new DeleteBusinessEntityHandler($repository, $this->allShopContext(), $this->createMock(LoggerInterface::class));
        $handler->handle(new DeleteBusinessEntityCommand(7));
    }

    public function testItScopesTheLookupToTheCurrentShopContext(): void
    {
        $businessEntity = new BusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->once())->method('findById')->with(7, [2])->willReturn($businessEntity);
        $repository->expects($this->once())->method('delete')->with($businessEntity);

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(false);
        $shopContext->method('getAssociatedShopIds')->willReturn([2]);

        $handler = new DeleteBusinessEntityHandler($repository, $shopContext, $this->createMock(LoggerInterface::class));
        $handler->handle(new DeleteBusinessEntityCommand(7));
    }

    public function testItLogsTheDeletion(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn(new BusinessEntity());

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with(
                'Business entity deleted successfully',
                ['object_type' => 'BusinessEntity', 'object_id' => 7],
            );

        $handler = new DeleteBusinessEntityHandler($repository, $this->allShopContext(), $logger);
        $handler->handle(new DeleteBusinessEntityCommand(7));
    }

    public function testItThrowsAndDoesNotDeleteWhenBusinessEntityNotFound(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn(null);
        $repository->expects($this->never())->method('delete');

        $handler = new DeleteBusinessEntityHandler($repository, $this->allShopContext(), $this->createMock(LoggerInterface::class));

        $this->expectException(BusinessEntityNotFoundException::class);

        $handler->handle(new DeleteBusinessEntityCommand(7));
    }

    private function allShopContext(): ShopContext
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(true);

        return $shopContext;
    }
}
