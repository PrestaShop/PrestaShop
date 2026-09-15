<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\BusinessEntity\QueryHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\QueryHandler\GetBusinessEntityForEditingHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetBusinessEntityForEditing;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;

class GetBusinessEntityForEditingHandlerTest extends TestCase
{
    public function testItMapsTheEntityToTheEditableDtoScopedToTheCurrentShops(): void
    {
        $entity = $this->createMock(BusinessEntity::class);
        $entity->method('getId')->willReturn(10);
        $entity->method('getName')->willReturn('Tan Emporium');
        $entity->method('getLegalName')->willReturn('Tan Emporium SAS');
        $entity->method('getExternalRef')->willReturn('EXT-1');
        $entity->method('isDeliveryAuthorized')->willReturn(true);
        $entity->method('getStatus')->willReturn(BusinessEntityStatus::ACTIVE);
        $entity->method('getIdCustomerGroup')->willReturn(5);
        $entity->method('getIdShop')->willReturn(2);

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(false);
        $shopContext->method('getAssociatedShopIds')->willReturn([2]);

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with(10, [2])
            ->willReturn($entity);

        $handler = new GetBusinessEntityForEditingHandler($repository, $shopContext);
        $editable = $handler->handle(new GetBusinessEntityForEditing(10));

        $this->assertSame(10, $editable->getBusinessEntityId());
        $this->assertSame('Tan Emporium', $editable->getName());
        $this->assertSame('Tan Emporium SAS', $editable->getLegalName());
        $this->assertSame('EXT-1', $editable->getExternalRef());
        $this->assertTrue($editable->isDeliveryAuthorized());
        $this->assertSame(BusinessEntityStatus::ACTIVE, $editable->getStatus());
        $this->assertSame(5, $editable->getCustomerGroupId());
        $this->assertSame(2, $editable->getShopId());
    }

    public function testItDoesNotScopeByShopInAllShopContext(): void
    {
        $entity = $this->createMock(BusinessEntity::class);
        $entity->method('getStatus')->willReturn(BusinessEntityStatus::PENDING);

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(true);
        $shopContext->expects($this->never())->method('getAssociatedShopIds');

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with(10, null)
            ->willReturn($entity);

        $handler = new GetBusinessEntityForEditingHandler($repository, $shopContext);
        $handler->handle(new GetBusinessEntityForEditing(10));
    }

    public function testItThrowsWhenBusinessEntityNotFound(): void
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(false);
        $shopContext->method('getAssociatedShopIds')->willReturn([1]);

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn(null);

        $handler = new GetBusinessEntityForEditingHandler($repository, $shopContext);

        $this->expectException(BusinessEntityNotFoundException::class);

        $handler->handle(new GetBusinessEntityForEditing(404));
    }
}
