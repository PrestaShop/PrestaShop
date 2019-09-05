<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\Builder;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;

class FormBuilderTest extends TestCase
{
    public function testCanBeConstructed()
    {
        $builder = new FormBuilder(
            $this->createMock(FormFactoryInterface::class),
            $this->createMock(HookDispatcherInterface::class),
            $this->createMock(FormDataProviderInterface::class),
            'a'
        );

        $this->assertInstanceOf(FormBuilderInterface::class, $builder);
    }

    public function testGetFormToCreateModel()
    {
        // constructor mocks
        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $hookDispatcherMock = $this->createMock(HookDispatcherInterface::class);
        $dataProviderMock = $this->createMock(FormDataProviderInterface::class);

        // mocks behavior configuration
        $dataProviderMock
            ->method('getDefaultData')
            ->will($this->returnValue([]));

        $formBuilderMock = $this->createMock(\Symfony\Component\Form\FormBuilderInterface::class);
        $formFactoryMock
            ->method('createBuilder')
            ->will($this->returnValue($formBuilderMock));

        $formMock = $this->createMock(\Symfony\Component\Form\FormInterface::class);
        $formBuilderMock
            ->method('getForm')
            ->will($this->returnValue($formMock));
        $formBuilderMock
            ->method('getName')
            ->will($this->returnValue('Bbcd'));

        $hookDispatcherMock->expects($this->once())
            ->method('dispatchWithParameters');

        $hookDispatcherMock->expects($this->once())
            ->method('dispatchWithParameters')
            ->with(
                $this->equalTo('actionBbcdFormBuilderModifier'),
                $this->equalTo([
                    'form_builder' => $formBuilderMock,
                    'data' => [],
                    'id' => null,
                ])
            );

        $builder = new FormBuilder(
            $formFactoryMock,
            $hookDispatcherMock,
            $dataProviderMock,
            'a'
        );

        $form = $builder->getForm([], []);

        $this->assertEquals($formMock, $form);
    }

    public function testGetFormToEditModel()
    {
        // constructor mocks
        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $hookDispatcherMock = $this->createMock(HookDispatcherInterface::class);
        $dataProviderMock = $this->createMock(FormDataProviderInterface::class);

        // mocks behavior configuration
        $dataProviderMock
            ->method('getData')
            ->will($this->returnValue([]));

        $formBuilderMock = $this->createMock(\Symfony\Component\Form\FormBuilderInterface::class);
        $formFactoryMock
            ->method('createBuilder')
            ->will($this->returnValue($formBuilderMock));

        $formMock = $this->createMock(\Symfony\Component\Form\FormInterface::class);
        $formBuilderMock
            ->method('getForm')
            ->will($this->returnValue($formMock));
        $formBuilderMock
            ->method('getName')
            ->will($this->returnValue('Abcd'));

        $hookDispatcherMock->expects($this->once())
            ->method('dispatchWithParameters')
            ->with(
                $this->equalTo('actionAbcdFormBuilderModifier'),
                $this->equalTo([
                    'form_builder' => $formBuilderMock,
                    'data' => [],
                    'id' => 1,
                ])
            );

        $builder = new FormBuilder(
            $formFactoryMock,
            $hookDispatcherMock,
            $dataProviderMock,
            'a'
        );

        $form = $builder->getFormFor(1, [], []);

        $this->assertEquals($formMock, $form);
    }
}
