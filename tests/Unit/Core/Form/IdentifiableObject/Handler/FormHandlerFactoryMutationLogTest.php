<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\Handler;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertiesFormDataPersister;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerFactory;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Mutation\BackOfficeMutationLogger;
use PrestaShop\PrestaShop\Core\Mutation\MutationTracker;
use PrestaShopBundle\Entity\MutationAction;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormHandlerFactoryMutationLogTest extends TestCase
{
    public function testFactoryForwardsMutationTableToCreatedHandler(): void
    {
        $hookDispatcher = $this->createMock(HookDispatcherInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $persister = $this->createMock(ExtraPropertiesFormDataPersister::class);
        $tracker = $this->createMock(MutationTracker::class);
        $legacyLogger = $this->createMock(LoggerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('isOpen')->willReturn(true);
        $requestStack = new RequestStack();
        $request = new Request();
        $request->attributes->set(BackOfficeMutationLogger::REQUEST_ATTRIBUTE, true);
        $requestStack->push($request);
        $translator->method('trans')->willReturnArgument(0);
        $technicalLogger = $this->createMock(LoggerInterface::class);
        $technicalLogger->expects($this->never())->method('error');
        $mutationLogger = new BackOfficeMutationLogger(
            $tracker,
            new EmployeeContext($this->createMock(Employee::class), []),
            $requestStack,
            $translator,
            $legacyLogger,
            $technicalLogger,
            $entityManager,
        );
        $dataHandler = $this->createMock(FormDataHandlerInterface::class);

        $dataHandler
            ->expects($this->once())
            ->method('create')
            ->with(['name' => 'Test'])
            ->willReturn(42);

        $legacyLogger->expects($this->once())->method('info');
        $tracker->expects($this->once())->method('addMutationForEmployee')
            ->with('product', 42, MutationAction::CREATE, '');

        $factory = new FormHandlerFactory(
            $hookDispatcher,
            $translator,
            false,
            $persister,
            $mutationLogger
        );

        $handler = $factory->create($dataHandler, 'product');
        $handler->handle($this->createValidForm());
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
