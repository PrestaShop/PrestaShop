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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\Builder;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider\FormOptionsProviderInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormBuilderInterface as SymfonyFormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

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
        $formMock = $this->createMock(FormInterface::class);
        $formBuilderMock = $this->createSymfonyFormBuilderMock($formMock, 'Bbcd');
        $formFactoryMock = $this->createFormFactoryMock($formBuilderMock);

        $builder = new FormBuilder(
            $formFactoryMock,
            $this->createHookDispatcherMock($formBuilderMock, 'Bbcd'),
            $this->createDefaultDataProviderMock(),
            'a'
        );

        $form = $builder->getForm([], []);

        $this->assertEquals($formMock, $form);
    }

    public function testGetFormToEditModel()
    {
        $formMock = $this->createMock(FormInterface::class);
        $formBuilderMock = $this->createSymfonyFormBuilderMock($formMock, 'Abcd');
        $formFactoryMock = $this->createFormFactoryMock($formBuilderMock);

        $builder = new FormBuilder(
            $formFactoryMock,
            $this->createHookDispatcherMock($formBuilderMock, 'Abcd', 1),
            $this->createDataProviderMock(),
            'a'
        );

        $form = $builder->getFormFor(1, [], []);

        $this->assertEquals($formMock, $form);
    }

    public function testGetFormWithOptionsToCreateModel()
    {
        $formMock = $this->createMock(FormInterface::class);
        $formBuilderMock = $this->createSymfonyFormBuilderMock($formMock, 'Bbcd');
        $formFactoryMock = $this->createFormFactoryMock($formBuilderMock);

        $options = ['option' => 'value'];
        $builder = new FormBuilder(
            $formFactoryMock,
            $this->createHookDispatcherMock($formBuilderMock, 'Bbcd', null, $options),
            $this->createDefaultDataProviderMock(),
            'a',
            $this->createDefaultOptionProviderMock($options)
        );

        $form = $builder->getForm([], []);

        $this->assertEquals($formMock, $form);
    }

    public function testGetFormWithOptionsToEditModel()
    {
        $formMock = $this->createMock(FormInterface::class);
        $formBuilderMock = $this->createSymfonyFormBuilderMock($formMock, 'Abcd');
        $formFactoryMock = $this->createFormFactoryMock($formBuilderMock);

        $options = ['option' => 'value'];
        $builder = new FormBuilder(
            $formFactoryMock,
            $this->createHookDispatcherMock($formBuilderMock, 'Abcd', 1, $options),
            $this->createDataProviderMock(),
            'a',
            $this->createOptionProviderMock($options)
        );

        $form = $builder->getFormFor(1, [], []);

        $this->assertEquals($formMock, $form);
    }

    /**
     * @param SymfonyFormBuilderInterface $formBuilder
     *
     * @return FormFactoryInterface
     */
    private function createFormFactoryMock(SymfonyFormBuilderInterface $formBuilder): FormFactoryInterface
    {
        $formFactoryMock = $this->createMock(FormFactoryInterface::class);

        $formFactoryMock
            ->method('createBuilder')
            ->will($this->returnValue($formBuilder));

        return $formFactoryMock;
    }

    /**
     * @param FormInterface $form
     *
     * @return SymfonyFormBuilderInterface
     */
    private function createSymfonyFormBuilderMock(FormInterface $form, string $formName): SymfonyFormBuilderInterface
    {
        $formBuilderMock = $this->createMock(SymfonyFormBuilderInterface::class);
        $formBuilderMock
            ->method('getForm')
            ->will($this->returnValue($form));
        $formBuilderMock
            ->method('getName')
            ->will($this->returnValue($formName));

        return $formBuilderMock;
    }

    /**
     * @param SymfonyFormBuilderInterface $formBuilder
     * @param string $formName
     * @param int|null $expectedId
     * @param array $expectedOptions
     *
     * @return HookDispatcherInterface
     */
    private function createHookDispatcherMock(
        SymfonyFormBuilderInterface $formBuilder,
        string $formName,
        ?int $expectedId = null,
        array $expectedOptions = []
    ): HookDispatcherInterface {
        $hookDispatcherMock = $this->createMock(HookDispatcherInterface::class);
        $hookDispatcherMock->expects($this->once())
            ->method('dispatchWithParameters')
            ->with(
                $this->equalTo('action' . $formName . 'FormBuilderModifier'),
                $this->equalTo([
                    'form_builder' => $formBuilder,
                    'data' => [],
                    'options' => $expectedOptions,
                    'id' => $expectedId,
                ])
            );

        return $hookDispatcherMock;
    }

    /**
     * @return FormDataProviderInterface
     */
    private function createDataProviderMock(): FormDataProviderInterface
    {
        $dataProviderMock = $this->createMock(FormDataProviderInterface::class);
        $dataProviderMock
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue([]));

        $dataProviderMock
            ->expects($this->never())
            ->method('getDefaultData');

        return $dataProviderMock;
    }

    /**
     * @return FormDataProviderInterface
     */
    private function createDefaultDataProviderMock(): FormDataProviderInterface
    {
        $dataProviderMock = $this->createMock(FormDataProviderInterface::class);
        $dataProviderMock
            ->expects($this->once())
            ->method('getDefaultData')
            ->will($this->returnValue([]));

        $dataProviderMock
            ->expects($this->never())
            ->method('getData');

        return $dataProviderMock;
    }

    /**
     * @param array $options
     *
     * @return FormOptionsProviderInterface
     */
    private function createDefaultOptionProviderMock(array $options): FormOptionsProviderInterface
    {
        $optionProviderMock = $this->createMock(FormOptionsProviderInterface::class);
        $optionProviderMock
            ->expects($this->once())
            ->method('getDefaultOptions')
            ->will($this->returnValue($options));

        $optionProviderMock
            ->expects($this->never())
            ->method('getOptions');

        return $optionProviderMock;
    }

    /**
     * @param array $options
     *
     * @return FormOptionsProviderInterface
     */
    private function createOptionProviderMock(array $options): FormOptionsProviderInterface
    {
        $optionProviderMock = $this->createMock(FormOptionsProviderInterface::class);
        $optionProviderMock
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options));

        $optionProviderMock
            ->expects($this->never())
            ->method('getDefaultOptions')
            ;

        return $optionProviderMock;
    }
}
