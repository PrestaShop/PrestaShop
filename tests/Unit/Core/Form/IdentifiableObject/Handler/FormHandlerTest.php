<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\Handler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\CreatedApiClient;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertiesFormDataPersister;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormHandler;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandler as IdentifiableObjectFormHandler;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormHandlerTest extends TestCase
{
    /**
     * @var FormHandler
     */
    private $handler;

    private $formBuilderMock;
    private $hookDispatcherMock;
    private $dataProviderMock;

    public function setUp(): void
    {
        // mocks creation
        $formBuilderMock = $this->createMock(FormBuilderInterface::class);
        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $namedFormBuilderMock = $this->createMock(FormBuilderInterface::class);
        $hookDispatcherMock = $this->createMock(HookDispatcherInterface::class);
        $dataProviderMock = $this->createMock(FormDataProviderInterface::class);

        // mocks behavior configuration
        $formBuilderMock
            ->method('getFormFactory')
            ->will($this->returnValue($formFactoryMock));
        $formFactoryMock
            ->method('createNamedBuilder')
            ->with($this->equalTo('formA'))
            ->will($this->returnValue($namedFormBuilderMock));

        $this->handler = new FormHandler(
            $formBuilderMock,
            $hookDispatcherMock,
            $dataProviderMock,
            ['a' => 'a', 'b' => 'b', 'c' => 'c'],
            'AHook',
            'formA'
        );

        $this->formBuilderMock = $namedFormBuilderMock;
        $this->hookDispatcherMock = $hookDispatcherMock;
        $this->dataProviderMock = $dataProviderMock;
    }

    public function testCanBeConstructed()
    {
        $this->assertInstanceOf(FormHandlerInterface::class, $this->handler);
    }

    public function testGetForm()
    {
        $invokedCount = $this->exactly(3);
        $this->formBuilderMock->expects($invokedCount)
            ->method('add')
            ->willReturnCallback(function (string|self $child, ?string $type = null) use ($invokedCount) {
                if ($invokedCount->numberOfInvocations() === 1) {
                    $this->assertEquals('a', $child);
                    $this->assertEquals('a', $type);
                }
                if ($invokedCount->numberOfInvocations() === 2) {
                    $this->assertEquals('b', $child);
                    $this->assertEquals('b', $type);
                }
                if ($invokedCount->numberOfInvocations() === 3) {
                    $this->assertEquals('c', $child);
                    $this->assertEquals('c', $type);
                }

                return $this->formBuilderMock;
            })
        ;

        $this->dataProviderMock
            ->method('getData')
            ->will($this->returnValue(['d' => 'd']));

        $this->formBuilderMock->expects($this->once())
            ->method('setData')
            ->with(
                $this->equalTo(['d' => 'd'])
            );

        $this->hookDispatcherMock->expects($this->once())
            ->method('dispatchWithParameters')
            ->with(
                $this->equalTo('actionAHookForm'),
                $this->equalTo(['form_builder' => $this->formBuilderMock])
            );

        $formMock = $this->createMock(FormInterface::class);
        $this->formBuilderMock
            ->method('getForm')
            ->will($this->returnValue($formMock));

        $form = $this->handler->getForm();

        $this->assertEquals($formMock, $form);
    }

    public function testSaveData()
    {
        $this->dataProviderMock
            ->method('setData')
            ->with($this->equalTo(['x' => 'y']))
            ->will($this->returnValue(['err' => 'or']));

        $this->hookDispatcherMock->expects($this->once())
            ->method('dispatchWithParameters')
            ->with(
                $this->equalTo('actionAHookSave'),
                $this->equalTo([
                    'errors' => ['err' => 'or'],
                    'form_data' => ['x' => 'y'],
                ])
            );

        $this->handler->save(['x' => 'y']);
    }

    /**
     * The following tests cover the identifiable-object FormHandler (a different class,
     * aliased as IdentifiableObjectFormHandler) and specifically the int-id resolution that
     * guards the ExtraPropertiesFormDataPersister::persist() call.
     *
     * @see IdentifiableObjectFormHandler::resolveExtraPropertyEntityId()
     */
    public function testCreatePersistsExtraPropertiesWhenHandlerReturnsInt(): void
    {
        $persister = $this->createMock(ExtraPropertiesFormDataPersister::class);
        $persister
            ->expects($this->once())
            ->method('persist')
            ->with($this->anything(), 'test_entity', 42);

        $handler = $this->createIdentifiableObjectHandler($this->createDataHandler(42), $persister);

        $result = $handler->handle($this->createValidForm());

        $this->assertSame(42, $result->getIdentifiableObjectId());
    }

    public function testCreatePersistsExtraPropertiesWhenHandlerReturnsValueObjectWithGetValue(): void
    {
        $valueObject = new class(42) {
            public function __construct(private int $value)
            {
            }

            public function getValue(): int
            {
                return $this->value;
            }
        };

        $persister = $this->createMock(ExtraPropertiesFormDataPersister::class);
        $persister
            ->expects($this->once())
            ->method('persist')
            ->with($this->anything(), 'test_entity', 42);

        $handler = $this->createIdentifiableObjectHandler($this->createDataHandler($valueObject), $persister);

        $result = $handler->handle($this->createValidForm());

        // The raw value object is preserved for the controller (e.g. CreatedApiClient secret).
        $this->assertSame($valueObject, $result->getIdentifiableObjectId());
    }

    public function testCreatePersistsExtraPropertiesForCreatedApiClient(): void
    {
        // CreatedApiClient has no top-level getValue() (it carries id + secret),
        // but is handled as an explicit exception so its id can still be persisted.
        $createdApiClient = new CreatedApiClient(42, 'a-secret');

        $persister = $this->createMock(ExtraPropertiesFormDataPersister::class);
        $persister
            ->expects($this->once())
            ->method('persist')
            ->with($this->anything(), 'test_entity', 42);

        $handler = $this->createIdentifiableObjectHandler($this->createDataHandler($createdApiClient), $persister);

        $result = $handler->handle($this->createValidForm());

        // The raw value object is preserved for the controller (secret + redirect).
        $this->assertSame($createdApiClient, $result->getIdentifiableObjectId());
    }

    public function testCreateSkipsExtraPropertiesPersistenceWhenIdCannotBeResolved(): void
    {
        // Unresolvable result (no int, not a CreatedApiClient, no getValue()).
        $createdApiClientLike = new class() {
            public function getSecret(): string
            {
                return 'secret';
            }
        };

        $persister = $this->createMock(ExtraPropertiesFormDataPersister::class);
        $persister
            ->expects($this->never())
            ->method('persist');

        $handler = $this->createIdentifiableObjectHandler($this->createDataHandler($createdApiClientLike), $persister);

        $result = $handler->handle($this->createValidForm());

        // No crash, and the original object still flows through to the controller.
        $this->assertSame($createdApiClientLike, $result->getIdentifiableObjectId());
    }

    private function createIdentifiableObjectHandler(
        FormDataHandlerInterface $dataHandler,
        ExtraPropertiesFormDataPersister $persister
    ): IdentifiableObjectFormHandler {
        return new IdentifiableObjectFormHandler(
            $dataHandler,
            $this->createMock(HookDispatcherInterface::class),
            $this->createMock(TranslatorInterface::class),
            false,
            $persister
        );
    }

    /**
     * @param mixed $createReturn
     */
    private function createDataHandler($createReturn): FormDataHandlerInterface
    {
        $dataHandler = $this->createMock(FormDataHandlerInterface::class);
        $dataHandler
            ->method('create')
            ->willReturn($createReturn);

        return $dataHandler;
    }

    private function createValidForm(): FormInterface
    {
        $resolvedType = $this->createMock(ResolvedFormTypeInterface::class);
        $resolvedType->method('getBlockPrefix')->willReturn('test_entity');

        $config = $this->createMock(FormConfigInterface::class);
        $config->method('getType')->willReturn($resolvedType);

        $form = $this->createMock(FormInterface::class);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);
        $form->method('getName')->willReturn('testEntity');
        $form->method('getData')->willReturn([]);
        $form->method('getConfig')->willReturn($config);

        return $form;
    }
}
