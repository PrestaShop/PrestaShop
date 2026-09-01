<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\BusinessEntity\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler\BulkDeleteBusinessEntityHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\BulkDeleteBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BulkDeleteBusinessEntityException;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Psr\Log\LoggerInterface;

class BulkDeleteBusinessEntityHandlerTest extends TestCase
{
    public function testItDeletesEveryBusinessEntity(): void
    {
        $first = new BusinessEntity();
        $second = new BusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturnMap([
            [4, null, $first],
            [8, null, $second],
        ]);

        $deleted = [];
        $repository->expects($this->exactly(2))->method('delete')
            ->willReturnCallback(function (BusinessEntity $be) use (&$deleted): void {
                $deleted[] = $be;
            });

        $handler = new BulkDeleteBusinessEntityHandler($repository, $this->allShopContext(), $this->createMock(LoggerInterface::class));
        $handler->handle(new BulkDeleteBusinessEntityCommand([4, 8]));

        $this->assertSame([$first, $second], $deleted);
    }

    public function testItReportsSkippedWhenOneIsNotFoundButDeletesTheOthers(): void
    {
        $existing = new BusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturnMap([
            [4, null, $existing],
            [999, null, null],
        ]);
        $repository->expects($this->once())->method('delete')->with($existing);

        $handler = new BulkDeleteBusinessEntityHandler(
            $repository,
            $this->allShopContext(),
            $this->createMock(LoggerInterface::class)
        );

        try {
            $handler->handle(new BulkDeleteBusinessEntityCommand([4, 999]));
            $this->fail('A BulkDeleteBusinessEntityException should have been thrown.');
        } catch (BulkDeleteBusinessEntityException $e) {
            $this->assertCount(1, $e->getExceptions());
        }
    }

    public function testItScopesEachLookupToTheCurrentShopContext(): void
    {
        $existing = new BusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->once())->method('findById')->with(4, [2])->willReturn($existing);
        $repository->expects($this->once())->method('delete')->with($existing);

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(false);
        $shopContext->method('getAssociatedShopIds')->willReturn([2]);

        $handler = new BulkDeleteBusinessEntityHandler($repository, $shopContext, $this->createMock(LoggerInterface::class));
        $handler->handle(new BulkDeleteBusinessEntityCommand([4]));
    }

    private function allShopContext(): ShopContext
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(true);

        return $shopContext;
    }
}
