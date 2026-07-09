<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\BusinessEntity\CommandHandler;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler\EditBusinessEntityHandler;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\EditBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Psr\Log\LoggerInterface;

class EditBusinessEntityHandlerTest extends TestCase
{
    public function testItUpdatesAllFieldsAndFlushes(): void
    {
        $businessEntity = $this->buildBusinessEntity();

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('flush');

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('getBusinessEntityById')->with(7)->willReturn($businessEntity);

        $handler = new EditBusinessEntityHandler($em, $repository, $this->createMock(LoggerInterface::class));
        $handler->handle(new EditBusinessEntityCommand(7, 'New name', 'New legal', 'NEW-REF', true, BusinessEntityStatus::ACTIVE, 9));

        $this->assertSame('New name', $businessEntity->getName());
        $this->assertSame('New legal', $businessEntity->getLegalName());
        $this->assertSame('NEW-REF', $businessEntity->getExternalRef());
        $this->assertTrue($businessEntity->isDeliveryAuthorized());
        $this->assertSame(BusinessEntityStatus::ACTIVE, $businessEntity->getStatus());
        $this->assertSame(9, $businessEntity->getIdCustomerGroup());
    }

    public function testItLogsOnlyModifiedFieldsAsJson(): void
    {
        $businessEntity = $this->buildBusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('getBusinessEntityById')->willReturn($businessEntity);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with(
                $this->callback(static function (string $message): bool {
                    return str_starts_with($message, 'Business entity updated successfully {')
                        && str_contains($message, '"name"')
                        && str_contains($message, '"status"')
                        && !str_contains($message, '"legal_name"')
                        && !str_contains($message, '"customer_group_id"');
                }),
                ['object_type' => 'BusinessEntity', 'object_id' => 7],
            );

        $handler = new EditBusinessEntityHandler($this->createMock(EntityManagerInterface::class), $repository, $logger);
        $handler->handle(new EditBusinessEntityCommand(7, 'New name', 'Old legal', 'OLD-REF', false, BusinessEntityStatus::ACTIVE, 3));
    }

    public function testItLogsBaseMessageWhenNothingChanged(): void
    {
        $businessEntity = $this->buildBusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('getBusinessEntityById')->willReturn($businessEntity);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with(
                'Business entity updated successfully',
                ['object_type' => 'BusinessEntity', 'object_id' => 7],
            );

        $handler = new EditBusinessEntityHandler($this->createMock(EntityManagerInterface::class), $repository, $logger);
        $handler->handle(new EditBusinessEntityCommand(7, 'Old name', 'Old legal', 'OLD-REF', false, BusinessEntityStatus::PENDING, 3));
    }

    public function testItThrowsWhenBusinessEntityNotFound(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('flush');

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('getBusinessEntityById')->willReturn(null);

        $handler = new EditBusinessEntityHandler($em, $repository, $this->createMock(LoggerInterface::class));

        $this->expectException(BusinessEntityNotFoundException::class);

        $handler->handle(new EditBusinessEntityCommand(7, 'Name', 'Legal', null, false, BusinessEntityStatus::PENDING, 3));
    }

    private function buildBusinessEntity(): BusinessEntity
    {
        $businessEntity = new BusinessEntity();
        $businessEntity->setName('Old name');
        $businessEntity->setLegalName('Old legal');
        $businessEntity->setExternalRef('OLD-REF');
        $businessEntity->setDeliveryAuthorized(false);
        $businessEntity->setStatus(BusinessEntityStatus::PENDING);
        $businessEntity->setIdCustomerGroup(3);

        return $businessEntity;
    }
}
