<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Mutation;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\ApiClientContext;
use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Mutation\BackOfficeMutationLogger;
use PrestaShop\PrestaShop\Core\Mutation\MutationTracker;
use PrestaShopBundle\Entity\Mutation;
use PrestaShopBundle\Entity\MutationAction;
use PrestaShopBundle\Entity\MutatorType;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BackOfficeMutationLoggerTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private bool $managerOpen;
    private LoggerInterface&MockObject $legacyLogger;
    private LoggerInterface&MockObject $logger;
    private TranslatorInterface&MockObject $translator;
    private RequestStack $requestStack;
    private EmployeeContext $employeeContext;
    private BackOfficeMutationLogger $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->managerOpen = true;
        $this->entityManager->method('isOpen')->willReturnCallback(fn (): bool => $this->managerOpen);
        $this->legacyLogger = $this->createMock(LoggerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->requestStack = new RequestStack();
        $request = new Request();
        $request->attributes->set(BackOfficeMutationLogger::REQUEST_ATTRIBUTE, true);
        $this->requestStack->push($request);
        $employee = $this->createMock(Employee::class);
        $employee->method('getId')->willReturn(7);
        $this->employeeContext = new EmployeeContext($employee, []);
        $this->service = $this->createService();
    }

    #[DataProvider('operations')]
    public function testRecordsLegacyLogBeforePersistingMutation(
        string $table,
        MutationAction $action,
        array $details,
        string $translationId,
        string $message,
        int $legacyId
    ): void {
        $legacyWritten = false;
        $objectType = 'product' === $table ? 'Product' : 'Category';
        $this->translator->expects(self::once())->method('trans')
            ->with($translationId, [], 'Admin.Advparameters.Feature')->willReturn($translationId);
        $this->legacyLogger->expects(self::once())->method('info')
            ->with($message, ['object_type' => $objectType, 'object_id' => $legacyId, 'allow_duplicate' => true])
            ->willReturnCallback(static function () use (&$legacyWritten): void { $legacyWritten = true; });
        $this->entityManager->expects(self::once())->method('persist')
            ->with(self::callback(static function (Mutation $mutation) use (&$legacyWritten, $table, $action, $details): bool {
                self::assertTrue($legacyWritten);
                self::assertSame($table, $mutation->getMutationTable());
                self::assertSame(42, $mutation->getMutationRowId());
                self::assertSame($action, $mutation->getAction());
                self::assertSame(MutatorType::EMPLOYEE, $mutation->getMutatorType());
                self::assertSame('7', $mutation->getMutatorIdentifier());
                self::assertLessThanOrEqual(255, strlen($mutation->getMutationDetails()));
                self::assertSame($details, '' === $mutation->getMutationDetails() ? [] : json_decode($mutation->getMutationDetails(), true, 512, JSON_THROW_ON_ERROR));

                return true;
            }));
        $this->entityManager->expects(self::once())->method('flush');
        $this->logger->expects(self::never())->method('error');

        $this->service->record($table, 42, $action, $details);
    }

    public static function operations(): iterable
    {
        foreach (['product' => 'Product', 'category' => 'Category'] as $table => $type) {
            yield $table . ' create' => [$table, MutationAction::CREATE, [], '%s addition', $type . ' addition', 42];
            yield $table . ' update' => [$table, MutationAction::UPDATE, [], '%s modification', $type . ' modification', 42];
            yield $table . ' delete' => [$table, MutationAction::DELETE, [], '%s deletion', $type . ' deletion', 42];
        }
        yield 'activate' => ['product', MutationAction::UPDATE, ['active' => true], '%s activated: %d', 'Product activated: 42', 42];
        yield 'deactivate' => ['product', MutationAction::UPDATE, ['active' => false], '%s deactivated: %d', 'Product deactivated: 42', 42];
        yield 'duplicate' => ['product', MutationAction::CREATE, ['source_id' => 12], '%s duplicated: (from %d to %d).', 'Product duplicated: (from 12 to 42).', 0];
        yield 'bulk duplicate' => ['product', MutationAction::CREATE, ['source_id' => 12, 'bulk' => true], '%s duplicated: (from %d to %d).', 'Product duplicated: (from 12 to 42).', 0];
        yield 'bulk status' => ['product', MutationAction::UPDATE, ['active' => false, 'bulk' => true], '%s deactivated: %d', 'Product deactivated: 42', 42];
        yield 'bulk category delete' => ['category', MutationAction::DELETE, ['bulk' => true], '%s deletion', 'Category deletion', 42];
    }

    #[DataProvider('excludedContexts')]
    public function testExcludesUnrelatedContexts(string $context): void
    {
        if ('cli' === $context) {
            $this->requestStack->pop();
        } elseif ('front' === $context) {
            $this->requestStack->getMainRequest()->attributes->remove(BackOfficeMutationLogger::REQUEST_ATTRIBUTE);
        } elseif ('truthy attribute' === $context) {
            $this->requestStack->getMainRequest()->attributes->set(BackOfficeMutationLogger::REQUEST_ATTRIBUTE, 1);
        } elseif ('no employee' === $context) {
            $this->employeeContext = new EmployeeContext(null, []);
        } else {
            $this->requestStack->getMainRequest()->attributes->remove(BackOfficeMutationLogger::REQUEST_ATTRIBUTE);
            $subRequest = new Request();
            $subRequest->attributes->set(BackOfficeMutationLogger::REQUEST_ATTRIBUTE, true);
            $this->requestStack->push($subRequest);
        }
        $this->entityManager->expects(self::never())->method('persist');
        $this->legacyLogger->expects(self::never())->method('info');
        $this->translator->expects(self::never())->method('trans');
        $this->createService()->record('product', 42, MutationAction::CREATE);
    }

    public static function excludedContexts(): iterable
    {
        foreach (['cli', 'front', 'truthy attribute', 'no employee', 'marked subrequest only'] as $context) {
            yield $context => [$context];
        }
    }

    #[DataProvider('invalidOperations')]
    public function testRejectsInvalidMetadataWithoutWriting(int $id, MutationAction $action, array $details): void
    {
        $this->entityManager->expects(self::never())->method('persist');
        $this->legacyLogger->expects(self::never())->method('info');
        $this->logger->expects(self::never())->method('error');
        $this->expectException(InvalidArgumentException::class);
        $this->service->record('product', $id, $action, $details);
    }

    public static function invalidOperations(): iterable
    {
        yield 'invalid ID' => [0, MutationAction::CREATE, []];
        yield 'arbitrary or oversized data' => [42, MutationAction::CREATE, ['payload' => str_repeat('x', 256)]];
        yield 'status on create' => [42, MutationAction::CREATE, ['active' => true]];
        yield 'non boolean status' => [42, MutationAction::UPDATE, ['active' => 1]];
        yield 'null status' => [42, MutationAction::UPDATE, ['active' => null]];
        yield 'duplicate on update' => [42, MutationAction::UPDATE, ['source_id' => 12]];
        yield 'invalid source' => [42, MutationAction::CREATE, ['source_id' => -1]];
        yield 'same source' => [42, MutationAction::CREATE, ['source_id' => 42]];
        yield 'null source' => [42, MutationAction::CREATE, ['source_id' => null]];
        yield 'invalid bulk' => [42, MutationAction::DELETE, ['bulk' => 1]];
    }

    #[DataProvider('unsupportedOperations')]
    public function testUnsupportedOperationsAreProgrammingErrors(string $table, MutationAction $action, array $details): void
    {
        $this->legacyLogger->expects(self::never())->method('info');
        $this->entityManager->expects(self::never())->method('persist');
        $this->logger->expects(self::never())->method('error');
        $this->expectException(InvalidArgumentException::class);
        $this->service->record($table, 42, $action, $details);
    }

    public static function unsupportedOperations(): iterable
    {
        yield 'unsupported table' => ['order', MutationAction::CREATE, []];
        yield 'category duplication' => ['category', MutationAction::CREATE, ['source_id' => 12]];
        yield 'category status' => ['category', MutationAction::UPDATE, ['active' => true]];
    }

    public function testInvalidArgumentsAreVisibleEvenOutsideBackOffice(): void
    {
        $this->requestStack->pop();
        $this->expectException(InvalidArgumentException::class);
        $this->service->record('product', 0, MutationAction::CREATE);
    }

    public function testLegacyFailureDoesNotPreventMutation(): void
    {
        $this->translator->method('trans')->willReturn('%s addition');
        $this->legacyLogger->method('info')->willThrowException(new RuntimeException('Legacy unavailable'));
        $this->logger->expects(self::once())->method('error')->with(self::stringContains('legacy log'), self::arrayHasKey('exception'));
        $this->entityManager->expects(self::once())->method('persist');
        $this->entityManager->expects(self::once())->method('flush');
        $this->service->record('product', 42, MutationAction::CREATE);
    }

    public function testFailedFlushIsDiagnosedAndNotRetriedWhileLegacyLoggingContinues(): void
    {
        $this->translator->method('trans')->willReturn('%s modification');
        $this->legacyLogger->expects(self::exactly(2))->method('info');
        $this->entityManager->expects(self::once())->method('persist');
        $this->entityManager->expects(self::once())->method('flush')->willThrowException(new RuntimeException('Flush failed'));
        $this->logger->expects(self::once())->method('error')->with(self::stringContains('mutation tracker'), self::arrayHasKey('exception'));
        $this->service->record('product', 42, MutationAction::UPDATE);
        $this->service->record('product', 43, MutationAction::UPDATE);
    }

    public function testTrackingResumesOnlyAfterANewRequestAndManagerRecovery(): void
    {
        $this->translator->method('trans')->willReturn('%s modification');
        $this->legacyLogger->expects(self::exactly(5))->method('info');
        $this->entityManager->expects(self::exactly(3))->method('persist');
        $flushCount = 0;
        $this->entityManager->expects(self::exactly(3))->method('flush')
            ->willReturnCallback(function () use (&$flushCount): void {
                if (1 === ++$flushCount) {
                    $this->managerOpen = false;
                    throw new RuntimeException('Flush closed the manager');
                }
            });
        $this->logger->expects(self::exactly(2))->method('error');
        $this->service->record('product', 42, MutationAction::UPDATE);
        $this->service->record('product', 43, MutationAction::UPDATE);

        // A new HTTP request alone must not retry on the still-closed manager.
        $this->startNewAdminRequest();
        $this->service->record('product', 44, MutationAction::UPDATE);

        // Model Doctrine resetting the lazy manager between subsequent requests.
        $this->managerOpen = true;
        $this->startNewAdminRequest();
        $this->service->record('product', 45, MutationAction::UPDATE);
        $this->service->record('product', 46, MutationAction::UPDATE);
    }

    public function testSubrequestsShareTheFailedMainRequestState(): void
    {
        $this->translator->method('trans')->willReturn('%s modification');
        $this->legacyLogger->expects(self::exactly(2))->method('info');
        $this->entityManager->expects(self::once())->method('persist');
        $this->entityManager->expects(self::once())->method('flush')->willThrowException(new RuntimeException('Flush failed'));
        $this->service->record('product', 42, MutationAction::UPDATE);
        $this->requestStack->push(new Request());
        $this->service->record('product', 43, MutationAction::UPDATE);
    }

    public function testClosedManagerDoesNotPreventLegacyLogging(): void
    {
        $this->managerOpen = false;
        $this->translator->method('trans')->willReturn('%s addition');
        $this->legacyLogger->expects(self::once())->method('info');
        $this->logger->expects(self::once())->method('error')->with(self::stringContains('mutation tracker'), self::arrayHasKey('exception'));
        $this->entityManager->expects(self::never())->method('persist');
        $this->entityManager->expects(self::never())->method('flush');
        $this->service->record('product', 42, MutationAction::CREATE);
    }

    private function startNewAdminRequest(): void
    {
        $this->requestStack->pop();
        $request = new Request();
        $request->attributes->set(BackOfficeMutationLogger::REQUEST_ATTRIBUTE, true);
        $this->requestStack->push($request);
    }

    public function testDiagnosticFailureDoesNotBreakCompletedOperation(): void
    {
        $this->translator->method('trans')->willReturn('%s addition');
        $this->legacyLogger->method('info')->willThrowException(new RuntimeException('Legacy unavailable'));
        $this->logger->method('error')->willThrowException(new RuntimeException('Diagnostics unavailable'));
        $this->entityManager->expects(self::once())->method('flush');
        $this->service->record('product', 42, MutationAction::CREATE);
    }

    public function testServiceConfigurationCompilesAndUsesExplicitLegacyLogger(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.project_dir', dirname(__DIR__, 4));
        $services = [
            EntityManagerInterface::class => $this->entityManager,
            ApiClientContext::class => new ApiClientContext(null),
            EmployeeContext::class => $this->employeeContext,
            RequestStack::class => $this->requestStack,
            TranslatorInterface::class => $this->translator,
            'prestashop.adapter.legacy.logger' => $this->legacyLogger,
            'logger' => $this->logger,
        ];
        foreach ($services as $id => $service) {
            $class = match ($id) {
                'prestashop.adapter.legacy.logger', 'logger' => LoggerInterface::class,
                default => $id,
            };
            $container->setDefinition($id, (new Definition($class))->setSynthetic(true)->setPublic(true));
        }
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__, 4)));
        $loader->load('src/PrestaShopBundle/Resources/config/services/core/mutation.yml');
        $container->getDefinition(BackOfficeMutationLogger::class)->setPublic(true);
        $container->compile();
        foreach ($services as $id => $service) {
            $container->set($id, $service);
        }
        $this->translator->method('trans')->willReturn('%s addition');
        $this->legacyLogger->expects(self::once())->method('info');
        $this->logger->expects(self::never())->method('info');
        $this->entityManager->expects(self::once())->method('flush');
        $container->get(BackOfficeMutationLogger::class)->record('product', 42, MutationAction::CREATE);
    }

    public function testUsesTranslatedMessage(): void
    {
        $this->translator->expects(self::once())->method('trans')
            ->with('%s modification', [], 'Admin.Advparameters.Feature')->willReturn('Modification de %s');
        $this->legacyLogger->expects(self::once())->method('info')
            ->with('Modification de Product', self::arrayHasKey('allow_duplicate'));
        $this->service->record('product', 42, MutationAction::UPDATE);
    }

    public function testTranslationFailureStillAllowsMutation(): void
    {
        $this->translator->method('trans')->willThrowException(new RuntimeException('Translation unavailable'));
        $this->legacyLogger->expects(self::never())->method('info');
        $this->logger->expects(self::once())->method('error');
        $this->entityManager->expects(self::once())->method('flush');
        $this->service->record('product', 42, MutationAction::CREATE);
    }

    private function createService(): BackOfficeMutationLogger
    {
        return new BackOfficeMutationLogger(
            new MutationTracker($this->entityManager, new ApiClientContext(null), $this->employeeContext),
            $this->employeeContext,
            $this->requestStack,
            $this->translator,
            $this->legacyLogger,
            $this->logger,
            $this->entityManager,
        );
    }
}
