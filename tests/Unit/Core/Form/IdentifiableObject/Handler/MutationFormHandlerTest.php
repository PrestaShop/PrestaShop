<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\Handler;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertiesFormDataPersister;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandler;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Mutation\BackOfficeMutationLogger;
use PrestaShop\PrestaShop\Core\Mutation\MutationTracker;
use PrestaShopBundle\Entity\MutationAction;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class MutationFormHandlerTest extends TestCase
{
    private $dataHandler;
    private $hookDispatcher;
    private $translator;
    private $extraPropertiesFormDataPersister;
    private $mutationLogger;
    private $mutationTracker;
    private $legacyLogger;
    private array $completedStages;

    public function setUp(): void
    {
        $this->completedStages = [];
        $this->dataHandler = $this->createMock(FormDataHandlerInterface::class);
        $this->hookDispatcher = $this->createMock(HookDispatcherInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->extraPropertiesFormDataPersister = $this->createMock(ExtraPropertiesFormDataPersister::class);
        $this->mutationTracker = $this->createMock(MutationTracker::class);
        $this->legacyLogger = $this->createMock(LoggerInterface::class);
        $requestStack = new RequestStack();
        $request = new Request();
        $request->attributes->set(BackOfficeMutationLogger::REQUEST_ATTRIBUTE, true);
        $requestStack->push($request);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('isOpen')->willReturn(true);
        $this->translator->method('trans')->willReturnArgument(0);
        $technicalLogger = $this->createMock(LoggerInterface::class);
        $technicalLogger->expects($this->never())->method('error');
        $this->mutationLogger = new BackOfficeMutationLogger(
            $this->mutationTracker,
            new EmployeeContext($this->createMock(Employee::class), []),
            $requestStack,
            $this->translator,
            $this->legacyLogger,
            $technicalLogger,
            $entityManager,
        );
    }

    public function testCreateLogsMutationWhenTableIsConfigured(): void
    {
        $form = $this->createValidForm();
        $this->extraPropertiesFormDataPersister->method('persist')->willReturnCallback(function (): void {
            $this->completedStages[] = 'extra properties';
        });
        $this->hookDispatcher->method('dispatchWithParameters')->willReturnCallback(function (string $hookName): void {
            if (str_starts_with($hookName, 'actionAfter')) {
                $this->completedStages[] = 'after hook';
            }
        });

        $this->dataHandler
            ->expects($this->once())
            ->method('create')
            ->with(['name' => 'Test'])
            ->willReturnCallback(function (): int {
                $this->completedStages[] = 'data';

                return 42;
            });

        $this->legacyLogger->expects($this->once())->method('info')->willReturnCallback(function (): void {
            $this->assertSame(['data', 'extra properties', 'after hook'], $this->completedStages);
        });
        $this->mutationTracker
            ->expects($this->once())
            ->method('addMutationForEmployee')
            ->with('product', 42, MutationAction::CREATE, '');

        $result = $this->createHandler('product')->handle($form);

        $this->assertSame(42, $result->getIdentifiableObjectId());
    }

    public function testUpdateLogsMutationWithOriginalObjectId(): void
    {
        $form = $this->createValidForm();
        $this->extraPropertiesFormDataPersister->method('persist')->willReturnCallback(function (): void {
            $this->completedStages[] = 'extra properties';
        });
        $this->hookDispatcher->method('dispatchWithParameters')->willReturnCallback(function (string $hookName): void {
            if (str_starts_with($hookName, 'actionAfter')) {
                $this->completedStages[] = 'after hook';
            }
        });

        $this->dataHandler
            ->expects($this->once())
            ->method('update')
            ->with(42, ['name' => 'Test'])
            ->willReturnCallback(function (): int {
                $this->completedStages[] = 'data';

                return 84;
            });

        $this->legacyLogger->expects($this->once())->method('info')->willReturnCallback(function (): void {
            $this->assertSame(['data', 'extra properties', 'after hook'], $this->completedStages);
        });
        $this->mutationTracker
            ->expects($this->once())
            ->method('addMutationForEmployee')
            ->with('product', 42, MutationAction::UPDATE, '');

        $result = $this->createHandler('product')->handleFor(42, $form);

        $this->assertSame(84, $result->getIdentifiableObjectId());
    }

    public function testCreateDoesNotLogWhenTableIsNotConfigured(): void
    {
        $form = $this->createValidForm();

        $this->dataHandler
            ->expects($this->once())
            ->method('create')
            ->willReturn(42);

        $this->mutationTracker->expects($this->never())->method('addMutationForEmployee');
        $this->legacyLogger->expects($this->never())->method('info');

        $this->createHandler()->handle($form);
    }

    public function testUpdateDoesNotLogWhenTableIsNotConfigured(): void
    {
        $form = $this->createValidForm();

        $this->dataHandler
            ->expects($this->once())
            ->method('update')
            ->with(42, ['name' => 'Test'])
            ->willReturn(null);

        $this->mutationTracker->expects($this->never())->method('addMutationForEmployee');
        $this->legacyLogger->expects($this->never())->method('info');

        $this->createHandler()->handleFor(42, $form);
    }

    public function testCreateDoesNotLogWhenFinalHookFails(): void
    {
        $form = $this->createValidForm();

        $this->dataHandler
            ->expects($this->once())
            ->method('create')
            ->willReturn(42);

        $this->hookDispatcher
            ->method('dispatchWithParameters')
            ->willReturnCallback(function (string $hookName): void {
                if ('actionAfterCreateProductFormHandler' === $hookName) {
                    throw new RuntimeException('After create hook failed');
                }
            });

        $this->mutationTracker->expects($this->never())->method('addMutationForEmployee');
        $this->legacyLogger->expects($this->never())->method('info');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('After create hook failed');

        $this->createHandler('product')->handle($form);
    }

    public function testUpdateDoesNotLogWhenFinalHookFails(): void
    {
        $form = $this->createValidForm();

        $this->dataHandler
            ->expects($this->once())
            ->method('update')
            ->with(42, ['name' => 'Test'])
            ->willReturn(null);

        $this->hookDispatcher
            ->method('dispatchWithParameters')
            ->willReturnCallback(function (string $hookName): void {
                if ('actionAfterUpdateProductFormHandler' === $hookName) {
                    throw new RuntimeException('After update hook failed');
                }
            });

        $this->mutationTracker->expects($this->never())->method('addMutationForEmployee');
        $this->legacyLogger->expects($this->never())->method('info');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('After update hook failed');

        $this->createHandler('product')->handleFor(42, $form);
    }

    #[DataProvider('failedStages')]
    public function testDoesNotLogWhenPersistenceFails(bool $update, bool $extraProperties): void
    {
        $method = $update ? 'update' : 'create';
        if ($extraProperties) {
            $this->dataHandler->method($method)->willReturn(42);
            $this->extraPropertiesFormDataPersister->method('persist')->willThrowException(new RuntimeException('Persistence failed'));
        } else {
            $this->dataHandler->method($method)->willThrowException(new RuntimeException('Persistence failed'));
        }
        $this->mutationTracker->expects($this->never())->method('addMutationForEmployee');
        $this->legacyLogger->expects($this->never())->method('info');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Persistence failed');
        $handler = $this->createHandler('product');
        if ($update) {
            $handler->handleFor(42, $this->createValidForm());
        } else {
            $handler->handle($this->createValidForm());
        }
    }

    public static function failedStages(): iterable
    {
        yield 'create data' => [false, false];
        yield 'create extra properties' => [false, true];
        yield 'update data' => [true, false];
        yield 'update extra properties' => [true, true];
    }

    private function createHandler(?string $mutationTable = null): FormHandler
    {
        return new FormHandler(
            $this->dataHandler,
            $this->hookDispatcher,
            $this->translator,
            false,
            $this->extraPropertiesFormDataPersister,
            $this->mutationLogger,
            $mutationTable
        );
    }

    private function createValidForm(): FormInterface
    {
        $resolvedType = $this->createMock(ResolvedFormTypeInterface::class);
        $resolvedType
            ->method('getBlockPrefix')
            ->willReturn('product');

        $config = $this->createMock(FormConfigInterface::class);
        $config
            ->method('getType')
            ->willReturn($resolvedType);

        $form = $this->createMock(FormInterface::class);
        $form
            ->method('isSubmitted')
            ->willReturn(true);
        $form
            ->method('isValid')
            ->willReturn(true);
        $form
            ->method('getName')
            ->willReturn('product');
        $form
            ->method('getData')
            ->willReturn(['name' => 'Test']);
        $form
            ->method('getConfig')
            ->willReturn($config);

        return $form;
    }
}
