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
    public function testItDeletesEveryBusinessEntityInASingleCall(): void
    {
        $first = new BusinessEntity();
        $second = new BusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findByIds')->willReturn([4 => $first, 8 => $second]);
        $repository->expects($this->once())->method('bulkDelete')->with([4 => $first, 8 => $second]);

        $handler = new BulkDeleteBusinessEntityHandler($repository, $this->allShopContext(), $this->createMock(LoggerInterface::class));
        $handler->handle(new BulkDeleteBusinessEntityCommand([4, 8]));
    }

    public function testItReadsTheWholeSelectionInASingleScopedLookup(): void
    {
        $first = new BusinessEntity();
        $second = new BusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->once())
            ->method('findByIds')
            ->with([4, 8], [2])
            ->willReturn([4 => $first, 8 => $second]);
        $repository->expects($this->once())->method('bulkDelete');

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(false);
        $shopContext->method('getAssociatedShopIds')->willReturn([2]);

        $handler = new BulkDeleteBusinessEntityHandler($repository, $shopContext, $this->createMock(LoggerInterface::class));
        $handler->handle(new BulkDeleteBusinessEntityCommand([4, 8]));
    }

    public function testItReadsEveryShopWhenTheContextIsAllShops(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->once())
            ->method('findByIds')
            ->with([4, 8], null)
            ->willReturn([4 => new BusinessEntity(), 8 => new BusinessEntity()]);

        $handler = new BulkDeleteBusinessEntityHandler($repository, $this->allShopContext(), $this->createMock(LoggerInterface::class));
        $handler->handle(new BulkDeleteBusinessEntityCommand([4, 8]));
    }

    public function testItReportsSkippedWhenOneIsNotFoundButDeletesTheOthersAndLogsOnlyThose(): void
    {
        $existing = new BusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findByIds')->willReturn([4 => $existing]);
        $repository->expects($this->once())->method('bulkDelete')->with([4 => $existing]);

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

    public function testAPersistenceFailureAbortsTheWholeSelectionInsteadOfCascading(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findByIds')->willReturn([4 => new BusinessEntity(), 8 => new BusinessEntity()]);
        $repository->expects($this->once())
            ->method('bulkDelete')
            ->willThrowException(new DbalException('Deadlock found'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('info');

        $handler = new BulkDeleteBusinessEntityHandler($repository, $this->allShopContext(), $logger);

        $this->expectException(CannotDeleteBusinessEntityException::class);

        $handler->handle(new BulkDeleteBusinessEntityCommand([4, 8]));
    }

    public function testItNeverWritesWhenNoBusinessEntityWasFound(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findByIds')->willReturn([]);
        $repository->expects($this->never())->method('bulkDelete');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('info');

        $handler = new BulkDeleteBusinessEntityHandler($repository, $this->allShopContext(), $logger);

        $this->expectException(BulkDeleteBusinessEntityException::class);

        $handler->handle(new BulkDeleteBusinessEntityCommand([999, 1000]));
    }

    public function testItLogsEveryDeletionWithItsObjectId(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findByIds')->willReturn([4 => new BusinessEntity(), 8 => new BusinessEntity()]);

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
