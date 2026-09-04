<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\BusinessEntity\CommandHandler;

use Doctrine\ORM\Exception\ORMException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler\EditBusinessEntityHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\EditBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\CannotUpdateBusinessEntityException;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Psr\Log\LoggerInterface;

class EditBusinessEntityHandlerTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testItUpdatesAllFieldsAndSaves(): void
    {
        $businessEntity = $this->buildBusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->once())->method('findById')->with(7, [self::SHOP_ID])->willReturn($businessEntity);
        $repository->expects($this->once())->method('save')->with($businessEntity);

        $handler = new EditBusinessEntityHandler($repository, $this->getMockShopContext(), $this->createMock(LoggerInterface::class));
        $handler->handle((new EditBusinessEntityCommand(7))
            ->setName('New name')
            ->setLegalName('New legal')
            ->setExternalRef('NEW-REF')
            ->setDeliveryAuthorized(true)
            ->setStatus(BusinessEntityStatus::ACTIVE)
            ->setCustomerGroupId(9));

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
        $repository->method('findById')->willReturn($businessEntity);

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
                ['object_type' => 'BusinessEntity', 'object_id' => 7, 'allow_duplicate' => true],
            );

        $handler = new EditBusinessEntityHandler($repository, $this->getMockShopContext(), $logger);
        $handler->handle((new EditBusinessEntityCommand(7))
            ->setName('New name')
            ->setLegalName('Old legal')
            ->setExternalRef('OLD-REF')
            ->setDeliveryAuthorized(false)
            ->setStatus(BusinessEntityStatus::ACTIVE)
            ->setCustomerGroupId(3));
    }

    public function testItLogsBaseMessageWhenNothingChanged(): void
    {
        $businessEntity = $this->buildBusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($businessEntity);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with(
                'Business entity updated successfully',
                ['object_type' => 'BusinessEntity', 'object_id' => 7, 'allow_duplicate' => true],
            );

        $handler = new EditBusinessEntityHandler($repository, $this->getMockShopContext(), $logger);
        $handler->handle((new EditBusinessEntityCommand(7))
            ->setName('Old name')
            ->setLegalName('Old legal')
            ->setExternalRef('OLD-REF')
            ->setDeliveryAuthorized(false)
            ->setStatus(BusinessEntityStatus::PENDING)
            ->setCustomerGroupId(3));
    }

    public function testItLooksUpWithoutShopScopeInAllShopContext(): void
    {
        $businessEntity = $this->buildBusinessEntity();

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(true);
        $shopContext->expects($this->never())->method('getAssociatedShopIds');

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->once())->method('findById')->with(7, null)->willReturn($businessEntity);

        $handler = new EditBusinessEntityHandler($repository, $shopContext, $this->createMock(LoggerInterface::class));
        $handler->handle((new EditBusinessEntityCommand(7))
            ->setName('New name')
            ->setLegalName('Old legal')
            ->setExternalRef('OLD-REF')
            ->setDeliveryAuthorized(false)
            ->setStatus(BusinessEntityStatus::PENDING)
            ->setCustomerGroupId(3));

        $this->assertSame('New name', $businessEntity->getName());
    }

    public function testItLogsEveryModifiedFieldWithItsOldAndNewValue(): void
    {
        $businessEntity = $this->buildBusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($businessEntity);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with(
                'Business entity updated successfully {'
                . '"name":{"old":"Old name","new":"New name"},'
                . '"legal_name":{"old":"Old legal","new":"New legal"},'
                . '"external_ref":{"old":"OLD-REF","new":"NEW-REF"},'
                . '"delivery_authorized":{"old":false,"new":true},'
                . '"status":{"old":"pending","new":"active"},'
                . '"customer_group_id":{"old":3,"new":9}}',
                ['object_type' => 'BusinessEntity', 'object_id' => 7, 'allow_duplicate' => true],
            );

        $handler = new EditBusinessEntityHandler($repository, $this->getMockShopContext(), $logger);
        $handler->handle((new EditBusinessEntityCommand(7))
            ->setName('New name')
            ->setLegalName('New legal')
            ->setExternalRef('NEW-REF')
            ->setDeliveryAuthorized(true)
            ->setStatus(BusinessEntityStatus::ACTIVE)
            ->setCustomerGroupId(9));
    }

    public function testItThrowsWhenBusinessEntityNotFound(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn(null);
        $repository->expects($this->never())->method('save');

        $handler = new EditBusinessEntityHandler($repository, $this->getMockShopContext(), $this->createMock(LoggerInterface::class));

        $this->expectException(BusinessEntityNotFoundException::class);

        $handler->handle((new EditBusinessEntityCommand(7))
            ->setName('Name')
            ->setLegalName('Legal')
            ->setExternalRef(null)
            ->setDeliveryAuthorized(false)
            ->setStatus(BusinessEntityStatus::PENDING)
            ->setCustomerGroupId(3));
    }

    private function getMockShopContext(): ShopContext
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(false);
        $shopContext->method('getAssociatedShopIds')->willReturn([self::SHOP_ID]);

        return $shopContext;
    }

    public function testItLeavesUnsetFieldsUntouched(): void
    {
        $businessEntity = $this->buildBusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($businessEntity);
        $repository->expects($this->once())->method('save')->with($businessEntity);

        $handler = new EditBusinessEntityHandler($repository, $this->getMockShopContext(), $this->createMock(LoggerInterface::class));
        $handler->handle((new EditBusinessEntityCommand(7))->setStatus(BusinessEntityStatus::ACTIVE));

        $this->assertSame(BusinessEntityStatus::ACTIVE, $businessEntity->getStatus());
        $this->assertSame('Old name', $businessEntity->getName());
        $this->assertSame('Old legal', $businessEntity->getLegalName());
        $this->assertSame('OLD-REF', $businessEntity->getExternalRef());
        $this->assertFalse($businessEntity->isDeliveryAuthorized());
        $this->assertSame(3, $businessEntity->getIdCustomerGroup());
    }

    public function testItLogsOnlyTheFieldsTheCommandActuallyCarries(): void
    {
        $businessEntity = $this->buildBusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($businessEntity);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info')->with(
            $this->callback(function (string $message): bool {
                return str_contains($message, '"status"')
                    && !str_contains($message, '"name"')
                    && !str_contains($message, '"customer_group_id"');
            }),
            $this->anything()
        );

        $handler = new EditBusinessEntityHandler($repository, $this->getMockShopContext(), $logger);
        $handler->handle((new EditBusinessEntityCommand(7))->setStatus(BusinessEntityStatus::ACTIVE));
    }

    public function testItClearsExternalRefWhenExplicitlySetToNull(): void
    {
        $businessEntity = $this->buildBusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($businessEntity);

        $handler = new EditBusinessEntityHandler($repository, $this->getMockShopContext(), $this->createMock(LoggerInterface::class));
        $handler->handle((new EditBusinessEntityCommand(7))->setExternalRef(null));

        $this->assertNull($businessEntity->getExternalRef());
    }

    public function testItKeepsAccentedValuesReadableInTheAuditTrail(): void
    {
        $businessEntity = $this->buildBusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($businessEntity);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info')->with(
            $this->stringContains('Sécurité Générale'),
            $this->anything()
        );

        $handler = new EditBusinessEntityHandler($repository, $this->getMockShopContext(), $logger);
        $handler->handle((new EditBusinessEntityCommand(7))->setName('Sécurité Générale'));
    }

    /**
     * The fixture starts with delivery_authorized = false, so every other test only ever proves
     * the false -> true direction. A guard that ignored `false` would stay green without this.
     */
    public function testItTurnsDeliveryAuthorizedBackOff(): void
    {
        $businessEntity = $this->buildBusinessEntity();
        $businessEntity->setDeliveryAuthorized(true);

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($businessEntity);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info')->with(
            $this->stringContains('"delivery_authorized"'),
            $this->anything()
        );

        $handler = new EditBusinessEntityHandler($repository, $this->getMockShopContext(), $logger);
        $handler->handle((new EditBusinessEntityCommand(7))->setDeliveryAuthorized(false));

        $this->assertFalse($businessEntity->isDeliveryAuthorized());
    }

    /**
     * The sibling AddBusinessEntityHandler translates a persistence failure into a domain
     * exception, and BusinessEntitiesController::getErrorMessages() only maps domain exceptions:
     * a raw Doctrine exception would fall through to the generic "unexpected error" message.
     */
    public function testItTranslatesAPersistenceFailureIntoADomainException(): void
    {
        $businessEntity = $this->buildBusinessEntity();

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($businessEntity);
        $repository->method('save')->willThrowException(new ORMException('Deadlock found'));

        $handler = new EditBusinessEntityHandler($repository, $this->getMockShopContext(), $this->createMock(LoggerInterface::class));

        $this->expectException(CannotUpdateBusinessEntityException::class);

        $handler->handle((new EditBusinessEntityCommand(7))->setName('New name'));
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
