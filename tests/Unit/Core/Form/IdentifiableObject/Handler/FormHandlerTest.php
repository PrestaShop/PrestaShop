<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        $this->formBuilderMock->expects($this->exactly(3))
            ->method('add')
            ->withConsecutive(
                ['a', 'a'],
                ['b', 'b'],
                ['c', 'c']
            );

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
