<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\BusinessEntity\CommandHandler;

use Doctrine\DBAL\Exception as DbalException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler\BulkDeleteBusinessEntityHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\BulkDeleteBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BulkDeleteBusinessEntityException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\CannotDeleteBusinessEntityException;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Psr\Log\LoggerInterface;

class BulkDeleteBusinessEntityHandlerTest extends TestCase
{
    public function testItDeletesEveryBusinessEntityOfTheSelection(): void
    {
        $first = new BusinessEntity();
        $second = new BusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturnMap([
            [4, null, $first],
            [8, null, $second],
        ]);

        $deleted = [];
        $repository->expects($this->exactly(2))->method('delete')->willReturnCallback(
            function (BusinessEntity $businessEntity) use (&$deleted): void {
                $deleted[] = $businessEntity;
            }
        );

        $handler = new BulkDeleteBusinessEntityHandler($repository, $this->allShopContext(), $this->createMock(LoggerInterface::class));
        $handler->handle(new BulkDeleteBusinessEntityCommand([4, 8]));

        $this->assertSame([$first, $second], $deleted);
    }

    public function testItLooksEachEntityUpWithinTheShopsOfTheContext(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->exactly(2))
            ->method('findById')
            ->willReturnCallback(
                function (int $businessEntityId, ?array $shopIds): BusinessEntity {
                    $this->assertSame([2], $shopIds, 'a lookup without the shop scope would reach another shop entity');

                    return new BusinessEntity();
                }
            );

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(false);
        $shopContext->method('getAssociatedShopIds')->willReturn([2]);

        $handler = new BulkDeleteBusinessEntityHandler($repository, $shopContext, $this->createMock(LoggerInterface::class));
        $handler->handle(new BulkDeleteBusinessEntityCommand([4, 8]));
    }

    public function testItReadsEveryShopWhenTheContextIsAllShops(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->exactly(2))
            ->method('findById')
            ->willReturnCallback(
                function (int $businessEntityId, ?array $shopIds): BusinessEntity {
                    $this->assertNull($shopIds);

                    return new BusinessEntity();
                }
            );

        $handler = new BulkDeleteBusinessEntityHandler($repository, $this->allShopContext(), $this->createMock(LoggerInterface::class));
        $handler->handle(new BulkDeleteBusinessEntityCommand([4, 8]));
    }

    public function testItReportsSkippedWhenOneIsNotFoundButDeletesTheOthersAndLogsOnlyThose(): void
    {
        $existing = new BusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturnMap([
            [4, null, $existing],
            [999, null, null],
        ]);
        $repository->expects($this->once())->method('delete')->with($existing);

        $loggedIds = [];
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('info')->willReturnCallback(
            function (string $message, array $context) use (&$loggedIds): void {
                $loggedIds[] = $context['object_id'];
            }
        );

        $handler = new BulkDeleteBusinessEntityHandler($repository, $this->allShopContext(), $logger);

        try {
            $handler->handle(new BulkDeleteBusinessEntityCommand([4, 999]));
            $this->fail('A BulkDeleteBusinessEntityException should have been thrown.');
        } catch (BulkDeleteBusinessEntityException $e) {
            $this->assertCount(1, $e->getExceptions());
        }

        $this->assertSame([4], $loggedIds, 'an id that was never deleted must not be logged as deleted');
    }

    public function testAPersistenceFailureIsReportedAmongTheSkippedIds(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn(new BusinessEntity());
        $repository->method('delete')->willThrowException(new DbalException('Deadlock found'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('info');

        $handler = new BulkDeleteBusinessEntityHandler($repository, $this->allShopContext(), $logger);

        try {
            $handler->handle(new BulkDeleteBusinessEntityCommand([4, 8]));
            $this->fail('A BulkDeleteBusinessEntityException should have been thrown.');
        } catch (BulkDeleteBusinessEntityException $e) {
            $this->assertCount(2, $e->getExceptions());
            $this->assertContainsOnlyInstancesOf(CannotDeleteBusinessEntityException::class, $e->getExceptions());
        }
    }

    public function testItNeverWritesWhenNoBusinessEntityWasFound(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn(null);
        $repository->expects($this->never())->method('delete');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('info');

        $handler = new BulkDeleteBusinessEntityHandler($repository, $this->allShopContext(), $logger);

        $this->expectException(BulkDeleteBusinessEntityException::class);

        $handler->handle(new BulkDeleteBusinessEntityCommand([999, 1000]));
    }

    public function testItLogsEveryDeletionWithItsObjectId(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn(new BusinessEntity());

        $loggedIds = [];
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(2))->method('info')->willReturnCallback(
            function (string $message, array $context) use (&$loggedIds): void {
                $this->assertSame('Business entity deleted successfully', $message);
                $this->assertSame('BusinessEntity', $context['object_type']);
                $loggedIds[] = $context['object_id'];
            }
        );

        $handler = new BulkDeleteBusinessEntityHandler($repository, $this->allShopContext(), $logger);
        $handler->handle(new BulkDeleteBusinessEntityCommand([4, 8]));

        $this->assertSame([4, 8], $loggedIds, 'the log must carry the raw id, not the value object');
    }

    private function allShopContext(): ShopContext
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(true);

        return $shopContext;
    }
}
