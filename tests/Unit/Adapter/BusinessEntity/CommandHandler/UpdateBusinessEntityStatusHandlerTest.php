<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\BusinessEntity\CommandHandler;

use DateTime;
use Doctrine\DBAL\Exception as DbalException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler\UpdateBusinessEntityStatusHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\UpdateBusinessEntityStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\CannotUpdateBusinessEntityException;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Psr\Log\LoggerInterface;

class UpdateBusinessEntityStatusHandlerTest extends TestCase
{
    public function testItAppliesTheNewStatusAndSavesIt(): void
    {
        $businessEntity = $this->businessEntityWithStatus(BusinessEntityStatus::PENDING);

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->with(7, null)->willReturn($businessEntity);
        $repository->expects($this->once())->method('save')->with($businessEntity);

        $this->handler($repository)->handle(
            new UpdateBusinessEntityStatusCommand(7, BusinessEntityStatus::ACTIVE)
        );

        $this->assertSame(BusinessEntityStatus::ACTIVE, $businessEntity->getStatus());
    }

    public function testItScopesTheLookupToTheCurrentShopContext(): void
    {
        $businessEntity = $this->businessEntityWithStatus(BusinessEntityStatus::PENDING);

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->once())->method('findById')->with(7, [2])->willReturn($businessEntity);
        $repository->expects($this->once())->method('save')->with($businessEntity);

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(false);
        $shopContext->method('getAssociatedShopIds')->willReturn([2]);

        $handler = new UpdateBusinessEntityStatusHandler(
            $repository,
            $shopContext,
            $this->createMock(LoggerInterface::class)
        );

        $handler->handle(new UpdateBusinessEntityStatusCommand(7, BusinessEntityStatus::ACTIVE));
    }

    public function testItThrowsAndDoesNotSaveWhenBusinessEntityNotFound(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn(null);
        $repository->expects($this->never())->method('save');

        $this->expectException(BusinessEntityNotFoundException::class);

        $this->handler($repository)->handle(
            new UpdateBusinessEntityStatusCommand(7, BusinessEntityStatus::ACTIVE)
        );
    }

    public function testItTranslatesAPersistenceFailureIntoADomainException(): void
    {
        $persistenceFailure = new DbalException('Deadlock found');

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($this->businessEntityWithStatus(BusinessEntityStatus::PENDING));
        $repository->method('save')->willThrowException($persistenceFailure);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('info');

        $handler = new UpdateBusinessEntityStatusHandler($repository, $this->allShopContext(), $logger);

        try {
            $handler->handle(new UpdateBusinessEntityStatusCommand(7, BusinessEntityStatus::ACTIVE));
            $this->fail('A persistence failure must surface as a domain exception.');
        } catch (CannotUpdateBusinessEntityException $e) {
            $this->assertSame(
                $persistenceFailure,
                $e->getPrevious(),
                'the original failure must stay reachable, otherwise the cause is lost for the logs'
            );
        }
    }

    public function testItRefreshesUpdatedAtEvenWhenTheStatusDoesNotChange(): void
    {
        $businessEntity = $this->businessEntityWithStatus(BusinessEntityStatus::ACTIVE);
        $businessEntity->setUpdatedAt(new DateTime('2020-01-01 00:00:00'));

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($businessEntity);
        // The timestamp is touched before save(), so asserting it alone would survive a handler
        // that skipped the write when the status is unchanged.
        $repository->expects($this->once())->method('save')->with($businessEntity);

        $this->handler($repository)->handle(
            new UpdateBusinessEntityStatusCommand(7, BusinessEntityStatus::ACTIVE)
        );

        $this->assertGreaterThan(
            new DateTime('2020-01-01 00:00:00'),
            $businessEntity->getUpdatedAt()
        );
    }

    public function testItLogsTheStatusItReplacedAndTheOneItSet(): void
    {
        $message = $this->captureLoggedMessage(
            BusinessEntityStatus::PENDING,
            new UpdateBusinessEntityStatusCommand(7, BusinessEntityStatus::REJECTED)
        );

        $this->assertStringContainsString('"old":"pending"', $message);
        $this->assertStringContainsString('"new":"rejected"', $message);
    }

    public function testItLogsTheCommentWhenOneIsGiven(): void
    {
        $message = $this->captureLoggedMessage(
            BusinessEntityStatus::PENDING,
            new UpdateBusinessEntityStatusCommand(7, BusinessEntityStatus::REJECTED, 'Expired registration document')
        );

        $this->assertStringContainsString('"comment":"Expired registration document"', $message);
    }

    public function testItLeavesTheCommentOutOfTheLogWhenItHoldsOnlyWhitespace(): void
    {
        $message = $this->captureLoggedMessage(
            BusinessEntityStatus::PENDING,
            new UpdateBusinessEntityStatusCommand(7, BusinessEntityStatus::ACTIVE, "  \n\t ")
        );

        $this->assertSame(
            'Business entity status changed successfully {"status":{"old":"pending","new":"active"}}',
            $message
        );
    }

    public function testItLogsEvenWhenTheSubmittedStatusIsTheCurrentOne(): void
    {
        $message = $this->captureLoggedMessage(
            BusinessEntityStatus::REJECTED,
            new UpdateBusinessEntityStatusCommand(7, BusinessEntityStatus::REJECTED, 'Checked again')
        );

        $this->assertStringContainsString('"old":"rejected"', $message);
        $this->assertStringContainsString('"new":"rejected"', $message);
        $this->assertStringContainsString('"comment":"Checked again"', $message);
    }

    private function captureLoggedMessage(
        BusinessEntityStatus $currentStatus,
        UpdateBusinessEntityStatusCommand $command,
    ): string {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($this->businessEntityWithStatus($currentStatus));

        $loggedMessage = null;
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with(
                $this->anything(),
                [
                    'object_type' => 'BusinessEntity',
                    'object_id' => 7,
                    // A status set back to a value it already held produces a message identical to
                    // an earlier one; without this flag the legacy logger drops it and AC6 loses an
                    // entry. Asserted here so removing the flag turns this test red.
                    'allow_duplicate' => true,
                ]
            )
            ->willReturnCallback(function (string $message) use (&$loggedMessage): void {
                $loggedMessage = $message;
            });

        $handler = new UpdateBusinessEntityStatusHandler($repository, $this->allShopContext(), $logger);
        $handler->handle($command);

        // Mock expectations are only verified at teardown, so without this the callers would all
        // fail on an empty string and name the symptom instead of the cause.
        $this->assertNotNull($loggedMessage, 'the handler must log the status change');

        return (string) $loggedMessage;
    }

    private function businessEntityWithStatus(BusinessEntityStatus $status): BusinessEntity
    {
        $businessEntity = new BusinessEntity();
        $businessEntity->setStatus($status);

        return $businessEntity;
    }

    private function handler(BusinessEntityRepository $repository): UpdateBusinessEntityStatusHandler
    {
        return new UpdateBusinessEntityStatusHandler(
            $repository,
            $this->allShopContext(),
            $this->createMock(LoggerInterface::class)
        );
    }

    private function allShopContext(): ShopContext
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('isAllShopContext')->willReturn(true);

        return $shopContext;
    }
}
