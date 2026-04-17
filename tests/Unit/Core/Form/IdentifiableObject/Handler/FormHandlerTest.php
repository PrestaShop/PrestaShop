<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\Handler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormHandler;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

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

        $formMock = $this->createMock(\Symfony\Component\Form\FormInterface::class);
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
}
