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

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form;

use PrestaShopBundle\Form\Partial\PartialForm;
use PrestaShopBundle\Form\Partial\PartialFormBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Tests\AbstractFormTest;
use Symfony\Component\Form\Tests\Fixtures\FixedDataTransformer;
use Symfony\Component\Form\Tests\Fixtures\FixedFilterListener;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * This test class was mostly copied from https://raw.githubusercontent.com/symfony/form/3.4/Tests/SimpleFormTest.php
 * Since our PartialForm class wraps the default Form class we want to ensure that it behaves as expected
 */
class PartialFormTest extends AbstractFormTest
{
    /**
     * @dataProvider provideFormNames
     */
    public function testGetPropertyPath($name, $propertyPath)
    {
        $config = new FormConfigBuilder($name, null, $this->dispatcher);
        $form = new Form($config);
        $partialForm = new PartialForm($form, $this->dispatcher);

        $this->assertEquals($propertyPath, $form->getPropertyPath());
        $this->assertEquals($propertyPath, $partialForm->getPropertyPath());
    }

    public function provideFormNames()
    {
        yield [null, null];
        yield ['', null];
        yield ['0', new PropertyPath('0')];
        yield [0, new PropertyPath('0')];
        yield ['name', new PropertyPath('name')];
    }

    public function testDataIsInitializedToConfiguredValue()
    {
        $model = new FixedDataTransformer([
            'default' => 'foo',
        ]);
        $view = new FixedDataTransformer([
            'foo' => 'bar',
        ]);

        $config = new PartialFormBuilder('name', null, $this->dispatcher, $this->factory);
        $config->addViewTransformer($view);
        $config->addModelTransformer($model);
        $config->setData('default');
        $form = new Form($config);
        $partialForm = new PartialForm($form, $this->dispatcher);

        $this->assertSame('default', $form->getData());
        $this->assertSame('foo', $form->getNormData());
        $this->assertSame('bar', $form->getViewData());

        $this->assertSame('default', $partialForm->getData());
        $this->assertSame('foo', $partialForm->getNormData());
        $this->assertSame('bar', $partialForm->getViewData());
    }

    public function testDataTransformationFailure()
    {
        $this->expectException('Symfony\Component\Form\Exception\TransformationFailedException');
        $this->expectExceptionMessage('Unable to transform data for property path "name": No mapping for value "arg"');
        $model = new FixedDataTransformer([
            'default' => 'foo',
        ]);
        $view = new FixedDataTransformer([
            'foo' => 'bar',
        ]);

        $config = new PartialFormBuilder('name', null, $this->dispatcher, $this->factory);
        $config->addViewTransformer($view);
        $config->addModelTransformer($model);
        $config->setData('arg');
        $form = new Form($config);
        $partialForm = new PartialForm($form, $this->dispatcher);

        $partialForm->getData();
    }

    // https://github.com/symfony/symfony/commit/d4f4038f6daf7cf88ca7c7ab089473cce5ebf7d8#commitcomment-1632879
    public function testDataIsInitializedFromSubmit()
    {
        $preSetData = false;
        $preSubmit = false;

        $mock = $this->getMockBuilder('\stdClass')
            ->setMethods(['preSetData', 'preSubmit'])
            ->getMock();
        $mock->expects($this->once())
            ->method('preSetData')
            ->with($this->callback(function () use (&$preSetData, $preSubmit) {
                $preSetData = true;

                return false === $preSubmit;
            }));
        $mock->expects($this->once())
            ->method('preSubmit')
            ->with($this->callback(function () use ($preSetData, &$preSubmit) {
                $preSubmit = true;

                return false === $preSetData;
            }));

        $config = new PartialFormBuilder('name', null, $this->dispatcher, $this->factory);
        $config->addEventListener(FormEvents::PRE_SET_DATA, [$mock, 'preSetData']);
        $config->addEventListener(FormEvents::PRE_SUBMIT, [$mock, 'preSubmit']);
        $form = new Form($config);
        $partialForm = new PartialForm($form, $this->dispatcher);

        // no call to setData() or similar where the object would be
        // initialized otherwise

        $partialForm->submit('foobar');
        $this->assertEquals('foobar', $partialForm->getSubmittedData());
    }

    // https://github.com/symfony/symfony/pull/7789
    public function testFalseIsConvertedToNull()
    {
        $mock = $this->getMockBuilder('\stdClass')
            ->setMethods(['preSubmit'])
            ->getMock();
        $mock->expects($this->once())
            ->method('preSubmit')
            ->with($this->callback(function ($event) {
                return null === $event->getData();
            }));

        $config = new PartialFormBuilder('name', null, $this->dispatcher, $this->factory);
        $config->addEventListener(FormEvents::PRE_SUBMIT, [$mock, 'preSubmit']);
        $form = new Form($config);
        $partialForm = new PartialForm($form, $this->dispatcher);

        $partialForm->submit(false);

        $this->assertTrue($form->isValid());
        $this->assertNull($form->getData());

        $this->assertTrue($partialForm->isValid());
        $this->assertNull($partialForm->getData());
        $this->assertNull($partialForm->getSubmittedData());
    }

    public function testSubmitThrowsExceptionIfAlreadySubmitted()
    {
        $this->expectException('Symfony\Component\Form\Exception\AlreadySubmittedException');
        $this->form->submit([]);
        $this->form->submit([]);
    }

    public function testSubmitIsIgnoredIfDisabled()
    {
        $form = $this->getBuilder()
            ->setDisabled(true)
            ->setData('initial')
            ->getForm();

        $form->submit('new');

        $this->assertEquals('initial', $form->getData());
        $this->assertTrue($form->isSubmitted());

        // getSubmittedData however knows the new data even if form is disabled
        $this->assertEquals('new', $form->getSubmittedData());
    }

    public function testNeverRequiredIfParentNotRequired()
    {
        $parent = $this->getBuilder()->setRequired(false)->getForm();
        $child = $this->getBuilder()->setRequired(true)->getForm();

        $child->setParent($parent);

        $this->assertFalse($child->isRequired());
    }

    public function testRequired()
    {
        $parent = $this->getBuilder()->setRequired(true)->getForm();
        $child = $this->getBuilder()->setRequired(true)->getForm();

        $child->setParent($parent);

        $this->assertTrue($child->isRequired());
    }

    public function testNotRequired()
    {
        $parent = $this->getBuilder()->setRequired(true)->getForm();
        $child = $this->getBuilder()->setRequired(false)->getForm();

        $child->setParent($parent);

        $this->assertFalse($child->isRequired());
    }

    /**
     * @dataProvider getDisabledStates
     */
    public function testAlwaysDisabledIfParentDisabled($parentDisabled, $disabled, $result)
    {
        $parent = $this->getBuilder()->setDisabled($parentDisabled)->getForm();
        $child = $this->getBuilder()->setDisabled($disabled)->getForm();

        $child->setParent($parent);

        $this->assertSame($result, $child->isDisabled());
    }

    public function getDisabledStates()
    {
        return [
            // parent, button, result
            [true, true, true],
            [true, false, true],
            [false, true, true],
            [false, false, false],
        ];
    }

    public function testGetRootReturnsRootOfParent()
    {
        $root = $this->createForm();

        $parent = $this->createForm();
        $parent->setParent($root);

        $this->form->setParent($parent);

        $this->assertSame($root, $this->form->getRoot());
    }

    public function testGetRootReturnsSelfIfNoParent()
    {
        $this->assertSame($this->form, $this->form->getRoot());
    }

    public function testEmptyIfEmptyArray()
    {
        $this->form->setData([]);

        $this->assertTrue($this->form->isEmpty());
    }

    public function testEmptyIfEmptyCountable()
    {
        $this->form = new PartialForm(new Form(new FormConfigBuilder('name', __NAMESPACE__ . '\SimpleFormTest_Countable', $this->dispatcher)));

        $this->form->setData(new SimpleFormTest_Countable(0));

        $this->assertTrue($this->form->isEmpty());
    }

    public function testNotEmptyIfFilledCountable()
    {
        $this->form = new PartialForm(new Form(new FormConfigBuilder('name', __NAMESPACE__ . '\SimpleFormTest_Countable', $this->dispatcher)));

        $this->form->setData(new SimpleFormTest_Countable(1));

        $this->assertFalse($this->form->isEmpty());
    }

    public function testEmptyIfEmptyTraversable()
    {
        $this->form = new PartialForm(new Form(new FormConfigBuilder('name', __NAMESPACE__ . '\SimpleFormTest_Traversable', $this->dispatcher)));

        $this->form->setData(new SimpleFormTest_Traversable(0));

        $this->assertTrue($this->form->isEmpty());
    }

    public function testNotEmptyIfFilledTraversable()
    {
        $this->form = new PartialForm(new Form(new FormConfigBuilder('name', __NAMESPACE__ . '\SimpleFormTest_Traversable', $this->dispatcher)));

        $this->form->setData(new SimpleFormTest_Traversable(1));

        $this->assertFalse($this->form->isEmpty());
    }

    public function testEmptyIfNull()
    {
        $this->form->setData(null);

        $this->assertTrue($this->form->isEmpty());
    }

    public function testEmptyIfEmptyString()
    {
        $this->form->setData('');

        $this->assertTrue($this->form->isEmpty());
    }

    public function testNotEmptyIfText()
    {
        $this->form->setData('foobar');

        $this->assertFalse($this->form->isEmpty());
    }

    public function testValidIfSubmitted()
    {
        $form = $this->getBuilder()->getForm();
        $form->submit('foobar');

        $this->assertTrue($form->isValid());
    }

    public function testValidIfSubmittedAndDisabled()
    {
        $form = $this->getBuilder()->setDisabled(true)->getForm();
        $form->submit('foobar');

        $this->assertTrue($form->isValid());
    }

    /**
     * @group legacy
     * @expectedDeprecation Call Form::isValid() with an unsubmitted form %s.
     */
    public function testNotValidIfNotSubmitted()
    {
        $this->assertFalse($this->form->isValid());
    }

    public function testNotValidIfErrors()
    {
        $form = $this->getBuilder()->getForm();
        $form->submit('foobar');
        $form->addError(new FormError('Error!'));

        $this->assertFalse($form->isValid());
    }

    public function testHasErrors()
    {
        $this->form->addError(new FormError('Error!'));

        $this->assertCount(1, $this->form->getErrors());
    }

    public function testHasNoErrors()
    {
        $this->assertCount(0, $this->form->getErrors());
    }

    public function testSetParentThrowsExceptionIfAlreadySubmitted()
    {
        $this->expectException('Symfony\Component\Form\Exception\AlreadySubmittedException');
        $this->form->submit([]);
        $this->form->setParent($this->getBuilder('parent')->getForm());
    }

    public function testSubmitted()
    {
        $form = $this->getBuilder()->getForm();
        $form->submit('foobar');

        $this->assertTrue($form->isSubmitted());
    }

    public function testNotSubmitted()
    {
        $this->assertFalse($this->form->isSubmitted());
    }

    public function testSetDataThrowsExceptionIfAlreadySubmitted()
    {
        $this->expectException('Symfony\Component\Form\Exception\AlreadySubmittedException');
        $this->form->submit([]);
        $this->form->setData(null);
    }

    public function testSetDataClonesObjectIfNotByReference()
    {
        $data = new \stdClass();
        $form = $this->getBuilder('name', null, '\stdClass')->setByReference(false)->getForm();
        $form->setData($data);

        $this->assertNotSame($data, $form->getData());
        $this->assertEquals($data, $form->getData());
    }

    public function testSetDataDoesNotCloneObjectIfByReference()
    {
        $data = new \stdClass();
        $form = $this->getBuilder('name', null, '\stdClass')->setByReference(true)->getForm();
        $form->setData($data);

        $this->assertSame($data, $form->getData());
    }

    public function testSetDataExecutesTransformationChain()
    {
        // use real event dispatcher now
        $form = $this->getBuilder('name', new EventDispatcher())
            ->addEventSubscriber(new FixedFilterListener([
                'preSetData' => [
                    'app' => 'filtered',
                ],
            ]))
            ->addModelTransformer(new FixedDataTransformer([
                '' => '',
                'filtered' => 'norm',
            ]))
            ->addViewTransformer(new FixedDataTransformer([
                '' => '',
                'norm' => 'client',
            ]))
            ->getForm();

        $form->setData('app');

        $this->assertEquals('filtered', $form->getData());
        $this->assertEquals('norm', $form->getNormData());
        $this->assertEquals('client', $form->getViewData());
    }

    public function testSetDataExecutesViewTransformersInOrder()
    {
        $form = $this->getBuilder()
            ->addViewTransformer(new FixedDataTransformer([
                '' => '',
                'first' => 'second',
            ]))
            ->addViewTransformer(new FixedDataTransformer([
                '' => '',
                'second' => 'third',
            ]))
            ->getForm();

        $form->setData('first');

        $this->assertEquals('third', $form->getViewData());
    }

    public function testSetDataExecutesModelTransformersInReverseOrder()
    {
        $form = $this->getBuilder()
            ->addModelTransformer(new FixedDataTransformer([
                '' => '',
                'second' => 'third',
            ]))
            ->addModelTransformer(new FixedDataTransformer([
                '' => '',
                'first' => 'second',
            ]))
            ->getForm();

        $form->setData('first');

        $this->assertEquals('third', $form->getNormData());
    }

    /*
     * When there is no data transformer, the data must have the same format
     * in all three representations
     */
    public function testSetDataConvertsScalarToStringIfNoTransformer()
    {
        $form = $this->getBuilder()->getForm();

        $form->setData(1);

        $this->assertSame('1', $form->getData());
        $this->assertSame('1', $form->getNormData());
        $this->assertSame('1', $form->getViewData());
    }

    /*
     * Data in client format should, if possible, always be a string to
     * facilitate differentiation between '0' and ''
     */
    public function testSetDataConvertsScalarToStringIfOnlyModelTransformer()
    {
        $form = $this->getBuilder()
            ->addModelTransformer(new FixedDataTransformer([
                '' => '',
                1 => 23,
            ]))
            ->getForm();

        $form->setData(1);

        $this->assertSame(1, $form->getData());
        $this->assertSame(23, $form->getNormData());
        $this->assertSame('23', $form->getViewData());
    }

    /*
     * NULL remains NULL in app and norm format to remove the need to treat
     * empty values and NULL explicitly in the application
     */
    public function testSetDataConvertsNullToStringIfNoTransformer()
    {
        $form = $this->getBuilder()->getForm();

        $form->setData(null);

        $this->assertNull($form->getData());
        $this->assertNull($form->getNormData());
        $this->assertSame('', $form->getViewData());
    }

    public function testSetDataIsIgnoredIfDataIsLocked()
    {
        $form = $this->getBuilder()
            ->setData('default')
            ->setDataLocked(true)
            ->getForm();

        $form->setData('foobar');

        $this->assertSame('default', $form->getData());
    }

    public function testPreSetDataChangesDataIfDataIsLocked()
    {
        $config = new FormConfigBuilder('name', null, $this->dispatcher);
        $config
            ->setData('default')
            ->setDataLocked(true)
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $event->setData('foobar');
            });
        $form = new Form($config);
        $partialForm = new PartialForm($form, $this->dispatcher);

        $this->assertSame('foobar', $form->getData());
        $this->assertSame('foobar', $form->getNormData());
        $this->assertSame('foobar', $form->getViewData());

        $this->assertSame('foobar', $partialForm->getData());
        $this->assertSame('foobar', $partialForm->getNormData());
        $this->assertSame('foobar', $partialForm->getViewData());
    }

    public function testSubmitConvertsEmptyToNullIfNoTransformer()
    {
        $form = $this->getBuilder()->getForm();

        $form->submit('');

        $this->assertNull($form->getData());
        $this->assertNull($form->getNormData());
        $this->assertSame('', $form->getViewData());
    }

    public function testSubmitExecutesTransformationChain()
    {
        // use real event dispatcher now
        $form = $this->getBuilder('name', new EventDispatcher())
            ->addEventSubscriber(new FixedFilterListener([
                'preSubmit' => [
                    'client' => 'filteredclient',
                ],
                'onSubmit' => [
                    'norm' => 'filterednorm',
                ],
            ]))
            ->addViewTransformer(new FixedDataTransformer([
                '' => '',
                // direction is reversed!
                'norm' => 'filteredclient',
                'filterednorm' => 'cleanedclient',
            ]))
            ->addModelTransformer(new FixedDataTransformer([
                '' => '',
                // direction is reversed!
                'app' => 'filterednorm',
            ]))
            ->getForm();

        $form->submit('client');

        $this->assertEquals('app', $form->getData());
        $this->assertEquals('filterednorm', $form->getNormData());
        $this->assertEquals('cleanedclient', $form->getViewData());

        // The first FixedFilterListener has a preSubmit mapping which changes submitted data
        $this->assertEquals('filteredclient', $form->getSubmittedData());
    }

    public function testSubmitExecutesViewTransformersInReverseOrder()
    {
        $form = $this->getBuilder()
            ->addViewTransformer(new FixedDataTransformer([
                '' => '',
                'third' => 'second',
            ]))
            ->addViewTransformer(new FixedDataTransformer([
                '' => '',
                'second' => 'first',
            ]))
            ->getForm();

        $form->submit('first');

        $this->assertEquals('third', $form->getNormData());
        $this->assertEquals('first', $form->getSubmittedData());
    }

    public function testSubmitExecutesModelTransformersInOrder()
    {
        $form = $this->getBuilder()
            ->addModelTransformer(new FixedDataTransformer([
                '' => '',
                'second' => 'first',
            ]))
            ->addModelTransformer(new FixedDataTransformer([
                '' => '',
                'third' => 'second',
            ]))
            ->getForm();

        $form->submit('first');

        $this->assertEquals('third', $form->getData());
        $this->assertEquals('first', $form->getSubmittedData());
    }

    public function testSynchronizedByDefault()
    {
        $this->assertTrue($this->form->isSynchronized());
    }

    public function testSynchronizedAfterSubmission()
    {
        $this->form->submit('foobar');

        $this->assertTrue($this->form->isSynchronized());
    }

    public function testNotSynchronizedIfViewReverseTransformationFailed()
    {
        $transformer = $this->getDataTransformer();
        $transformer->expects($this->once())
            ->method('reverseTransform')
            ->willThrowException(new TransformationFailedException());

        $form = $this->getBuilder()
            ->addViewTransformer($transformer)
            ->getForm();

        $form->submit('foobar');

        $this->assertFalse($form->isSynchronized());
        // Submitted data is still updated though
        $this->assertEquals('foobar', $form->getSubmittedData());
    }

    public function testNotSynchronizedIfModelReverseTransformationFailed()
    {
        $transformer = $this->getDataTransformer();
        $transformer->expects($this->once())
            ->method('reverseTransform')
            ->willThrowException(new TransformationFailedException());

        $form = $this->getBuilder()
            ->addModelTransformer($transformer)
            ->getForm();

        $form->submit('foobar');

        $this->assertFalse($form->isSynchronized());
        // Submitted data is still updated though
        $this->assertEquals('foobar', $form->getSubmittedData());
    }

    public function testEmptyDataCreatedBeforeTransforming()
    {
        $form = $this->getBuilder()
            ->setEmptyData('foo')
            ->addViewTransformer(new FixedDataTransformer([
                '' => '',
                // direction is reversed!
                'bar' => 'foo',
            ]))
            ->getForm();

        $form->submit('');

        $this->assertEquals('bar', $form->getData());
        $this->assertEquals('', $form->getSubmittedData());
    }

    public function testEmptyDataFromClosure()
    {
        $form = $this->getBuilder()
            ->setEmptyData(function ($form) {
                // the form instance is passed to the closure to allow use
                // of form data when creating the empty value
                $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $form);

                return 'foo';
            })
            ->addViewTransformer(new FixedDataTransformer([
                '' => '',
                // direction is reversed!
                'bar' => 'foo',
            ]))
            ->getForm();

        $form->submit('');

        $this->assertEquals('bar', $form->getData());
        $this->assertEquals('', $form->getSubmittedData());
    }

    public function testSubmitResetsErrors()
    {
        $this->form->addError(new FormError('Error!'));
        $this->form->submit('foobar');

        $this->assertCount(0, $this->form->getErrors());
    }

    public function testCreateView()
    {
        $type = $this->getMockBuilder('Symfony\Component\Form\ResolvedFormTypeInterface')->getMock();
        $view = $this->getMockBuilder('Symfony\Component\Form\FormView')->getMock();
        $form = $this->getBuilder()->setType($type)->getForm();

        // It's the innerForm that is used to create the view not the PartialForm itself
        $type->expects($this->once())
            ->method('createView')
            ->with($form->getInnerForm())
            ->willReturn($view);

        $this->assertSame($view, $form->createView());
    }

    public function testCreateViewWithParent()
    {
        $type = $this->getMockBuilder('Symfony\Component\Form\ResolvedFormTypeInterface')->getMock();
        $view = $this->getMockBuilder('Symfony\Component\Form\FormView')->getMock();
        $parentType = $this->getMockBuilder('Symfony\Component\Form\ResolvedFormTypeInterface')->getMock();
        $parentForm = $this->getBuilder()->setType($parentType)->getForm();
        $parentView = $this->getMockBuilder('Symfony\Component\Form\FormView')->getMock();
        $form = $this->getBuilder()->setType($type)->getForm();
        $form->setParent($parentForm);

        $parentType->expects($this->once())
            ->method('createView')
            ->willReturn($parentView);

        // It's the innerForm that is used to create the view not the PartialForm itself
        $type->expects($this->once())
            ->method('createView')
            ->with($form->getInnerForm(), $parentView)
            ->willReturn($view);

        $this->assertSame($view, $form->createView());
    }

    public function testCreateViewWithExplicitParent()
    {
        $type = $this->getMockBuilder('Symfony\Component\Form\ResolvedFormTypeInterface')->getMock();
        $view = $this->getMockBuilder('Symfony\Component\Form\FormView')->getMock();
        $parentView = $this->getMockBuilder('Symfony\Component\Form\FormView')->getMock();
        $form = $this->getBuilder()->setType($type)->getForm();

        // It's the innerForm that is used to create the view not the PartialForm itself
        $type->expects($this->once())
            ->method('createView')
            ->with($form->getInnerForm(), $parentView)
            ->willReturn($view);

        $this->assertSame($view, $form->createView($parentView));
    }

    public function testFormCanHaveEmptyName()
    {
        $form = $this->getBuilder('')->getForm();

        $this->assertEquals('', $form->getName());
    }

    public function testSetNullParentWorksWithEmptyName()
    {
        $form = $this->getBuilder('')->getForm();
        $form->setParent(null);

        $this->assertNull($form->getParent());
    }

    public function testFormCannotHaveEmptyNameNotInRootLevel()
    {
        $this->expectException('Symfony\Component\Form\Exception\LogicException');
        $this->expectExceptionMessage('A form with an empty name cannot have a parent form.');
        $this->getBuilder()
            ->setCompound(true)
            ->setDataMapper($this->getDataMapper())
            ->add($this->getBuilder(''))
            ->getForm();
    }

    public function testGetPropertyPathReturnsConfiguredPath()
    {
        $form = $this->getBuilder()->setPropertyPath('address.street')->getForm();

        $this->assertEquals(new PropertyPath('address.street'), $form->getPropertyPath());
    }

    // see https://github.com/symfony/symfony/issues/3903
    public function testGetPropertyPathDefaultsToNameIfParentHasDataClass()
    {
        $parent = $this->getBuilder(null, null, 'stdClass')
            ->setCompound(true)
            ->setDataMapper($this->getDataMapper())
            ->getForm();
        $form = $this->getBuilder('name')->getForm();
        $parent->add($form);

        $this->assertEquals(new PropertyPath('name'), $form->getPropertyPath());
    }

    // see https://github.com/symfony/symfony/issues/3903
    public function testGetPropertyPathDefaultsToIndexedNameIfParentDataClassIsNull()
    {
        $parent = $this->getBuilder()
            ->setCompound(true)
            ->setDataMapper($this->getDataMapper())
            ->getForm();
        $form = $this->getBuilder('name')->getForm();
        $parent->add($form);

        $this->assertEquals(new PropertyPath('[name]'), $form->getPropertyPath());
    }

    public function testGetPropertyPathDefaultsToNameIfFirstParentWithoutInheritDataHasDataClass()
    {
        $grandParent = $this->getBuilder(null, null, 'stdClass')
            ->setCompound(true)
            ->setDataMapper($this->getDataMapper())
            ->getForm();
        $parent = $this->getBuilder()
            ->setCompound(true)
            ->setDataMapper($this->getDataMapper())
            ->setInheritData(true)
            ->getForm();
        $form = $this->getBuilder('name')->getForm();
        $grandParent->add($parent);
        $parent->add($form);

        $this->assertEquals(new PropertyPath('name'), $form->getPropertyPath());
    }

    public function testGetPropertyPathDefaultsToIndexedNameIfDataClassOfFirstParentWithoutInheritDataIsNull()
    {
        $grandParent = $this->getBuilder()
            ->setCompound(true)
            ->setDataMapper($this->getDataMapper())
            ->getForm();
        $parent = $this->getBuilder()
            ->setCompound(true)
            ->setDataMapper($this->getDataMapper())
            ->setInheritData(true)
            ->getForm();
        $form = $this->getBuilder('name')->getForm();
        $grandParent->add($parent);
        $parent->add($form);

        $this->assertEquals(new PropertyPath('[name]'), $form->getPropertyPath());
    }

    public function testViewDataMayBeObjectIfDataClassIsNull()
    {
        $object = new \stdClass();
        $config = new FormConfigBuilder('name', null, $this->dispatcher);
        $config->addViewTransformer(new FixedDataTransformer([
            '' => '',
            'foo' => $object,
        ]));
        $form = new Form($config);
        $partialForm = new PartialForm($form);

        $partialForm->setData('foo');

        $this->assertSame($object, $form->getViewData());
        $this->assertSame($object, $partialForm->getViewData());
    }

    public function testViewDataMayBeArrayAccessIfDataClassIsNull()
    {
        $arrayAccess = $this->getMockBuilder('\ArrayAccess')->getMock();
        $config = new FormConfigBuilder('name', null, $this->dispatcher);
        $config->addViewTransformer(new FixedDataTransformer([
            '' => '',
            'foo' => $arrayAccess,
        ]));
        $form = new Form($config);
        $partialForm = new PartialForm($form);

        $partialForm->setData('foo');

        $this->assertSame($arrayAccess, $form->getViewData());
        $this->assertSame($arrayAccess, $partialForm->getViewData());
    }

    public function testViewDataMustBeObjectIfDataClassIsSet()
    {
        $this->expectException('Symfony\Component\Form\Exception\LogicException');
        $config = new FormConfigBuilder('name', 'stdClass', $this->dispatcher);
        $config->addViewTransformer(new FixedDataTransformer([
            '' => '',
            'foo' => ['bar' => 'baz'],
        ]));
        $form = new Form($config);
        $partialForm = new PartialForm($form);

        $partialForm->setData('foo');
    }

    public function testSetDataCannotInvokeItself()
    {
        $this->expectException('Symfony\Component\Form\Exception\RuntimeException');
        $this->expectExceptionMessage('A cycle was detected. Listeners to the PRE_SET_DATA event must not call setData(). You should call setData() on the FormEvent object instead.');
        // Cycle detection to prevent endless loops
        $config = new FormConfigBuilder('name', 'stdClass', $this->dispatcher);
        $config->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $event->getForm()->setData('bar');
        });
        $form = new Form($config);
        $partialForm = new PartialForm($form);

        $partialForm->setData('foo');
    }

    public function testSubmittingWrongDataIsIgnored()
    {
        $called = 0;

        $child = $this->getBuilder('child', $this->dispatcher);
        $child->addEventListener(FormEvents::PRE_SUBMIT, function () use (&$called) {
            ++$called;
        });

        $parent = $this->getBuilder('parent', new EventDispatcher())
            ->setCompound(true)
            ->setDataMapper($this->getDataMapper())
            ->add($child)
            ->getForm();

        $parent->submit('not-an-array');

        $this->assertSame(0, $called, 'PRE_SUBMIT event listeners are not called for wrong data');
        // Listeners are not called but submit call still stored the initial submitted data
        $this->assertEquals('not-an-array', $parent->getSubmittedData());
    }

    public function testHandleRequestForwardsToRequestHandler()
    {
        $handler = $this->getMockBuilder('Symfony\Component\Form\RequestHandlerInterface')->getMock();

        $form = $this->getBuilder()
            ->setRequestHandler($handler)
            ->getForm();

        $handler->expects($this->once())
            ->method('handleRequest')
            ->with($this->identicalTo($form), 'REQUEST');

        $this->assertSame($form, $form->handleRequest('REQUEST'));
        // Handler is mocked so submit is never called
        $this->assertNull($form->getSubmittedData());
    }

    public function testFormInheritsParentData()
    {
        $child = $this->getBuilder('child')
            ->setInheritData(true);

        $parent = $this->getBuilder('parent')
            ->setCompound(true)
            ->setDataMapper($this->getDataMapper())
            ->setData('foo')
            ->addModelTransformer(new FixedDataTransformer([
                'foo' => 'norm[foo]',
            ]))
            ->addViewTransformer(new FixedDataTransformer([
                'norm[foo]' => 'view[foo]',
            ]))
            ->add($child)
            ->getForm();

        $this->assertSame('foo', $parent->get('child')->getData());
        $this->assertSame('norm[foo]', $parent->get('child')->getNormData());
        $this->assertSame('view[foo]', $parent->get('child')->getViewData());
    }

    public function testInheritDataDisallowsSetData()
    {
        $this->expectException('Symfony\Component\Form\Exception\RuntimeException');
        $form = $this->getBuilder()
            ->setInheritData(true)
            ->getForm();

        $form->setData('foo');
    }

    public function testGetDataRequiresParentToBeSetIfInheritData()
    {
        $this->expectException('Symfony\Component\Form\Exception\RuntimeException');
        $form = $this->getBuilder()
            ->setInheritData(true)
            ->getForm();

        $form->getData();
    }

    public function testGetNormDataRequiresParentToBeSetIfInheritData()
    {
        $this->expectException('Symfony\Component\Form\Exception\RuntimeException');
        $form = $this->getBuilder()
            ->setInheritData(true)
            ->getForm();

        $form->getNormData();
    }

    public function testGetViewDataRequiresParentToBeSetIfInheritData()
    {
        $this->expectException('Symfony\Component\Form\Exception\RuntimeException');
        $form = $this->getBuilder()
            ->setInheritData(true)
            ->getForm();

        $form->getViewData();
    }

    public function testPostSubmitDataIsNullIfInheritData()
    {
        $form = $this->getBuilder()
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $this->assertNull($event->getData());
            })
            ->setInheritData(true)
            ->getForm();

        $form->submit('foo');
    }

    public function testSubmitIsNeverFiredIfInheritData()
    {
        $called = 0;
        $form = $this->getBuilder()
            ->addEventListener(FormEvents::SUBMIT, function () use (&$called) {
                ++$called;
            })
            ->setInheritData(true)
            ->getForm();

        $form->submit('foo');

        $this->assertSame(0, $called, 'The SUBMIT event is not fired when data are inherited from the parent form');
    }

    public function testInitializeSetsDefaultData()
    {
        $config = $this->getBuilder()->setData('DEFAULT')->getFormConfig();
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->setMethods(['setData'])->setConstructorArgs([$config])->getMock();

        $form->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo('DEFAULT'));

        /* @var Form $form */
        $form->initialize();
    }

    public function testInitializeFailsIfParent()
    {
        $this->expectException('Symfony\Component\Form\Exception\RuntimeException');
        $parent = $this->getBuilder()->setRequired(false)->getForm();
        $child = $this->getBuilder()->setRequired(true)->getForm();

        $child->setParent($parent);

        $child->initialize();
    }

    public function testCannotCallGetDataInPreSetDataListenerIfDataHasNotAlreadyBeenSet()
    {
        $this->expectException('Symfony\Component\Form\Exception\RuntimeException');
        $this->expectExceptionMessage('A cycle was detected. Listeners to the PRE_SET_DATA event must not call getData() if the form data has not already been set. You should call getData() on the FormEvent object instead.');
        $config = new FormConfigBuilder('name', 'stdClass', $this->dispatcher);
        $config->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $event->getForm()->getData();
        });
        $form = new Form($config);
        $partialForm = new PartialForm($form);

        $partialForm->setData('foo');
    }

    public function testCannotCallGetNormDataInPreSetDataListener()
    {
        $this->expectException('Symfony\Component\Form\Exception\RuntimeException');
        $this->expectExceptionMessage('A cycle was detected. Listeners to the PRE_SET_DATA event must not call getNormData() if the form data has not already been set.');
        $config = new FormConfigBuilder('name', 'stdClass', $this->dispatcher);
        $config->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $event->getForm()->getNormData();
        });
        $form = new Form($config);
        $partialForm = new PartialForm($form);

        $partialForm->setData('foo');
    }

    public function testCannotCallGetViewDataInPreSetDataListener()
    {
        $this->expectException('Symfony\Component\Form\Exception\RuntimeException');
        $this->expectExceptionMessage('A cycle was detected. Listeners to the PRE_SET_DATA event must not call getViewData() if the form data has not already been set.');
        $config = new FormConfigBuilder('name', 'stdClass', $this->dispatcher);
        $config->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $event->getForm()->getViewData();
        });
        $form = new Form($config);
        $partialForm = new PartialForm($form);

        $partialForm->setData('foo');
    }

    protected function createForm()
    {
        return $this->getBuilder()->getForm();
    }

    /**
     * @param string $name
     * @param EventDispatcherInterface|null $dispatcher
     * @param null $dataClass
     * @param array $options
     *
     * @return PartialFormBuilder
     */
    protected function getBuilder($name = 'name', EventDispatcherInterface $dispatcher = null, $dataClass = null, array $options = [])
    {
        return new PartialFormBuilder($name, $dataClass, $dispatcher ?: $this->dispatcher, $this->factory, $options);
    }
}

class SimpleFormTest_Countable implements \Countable
{
    private $count;

    public function __construct($count)
    {
        $this->count = $count;
    }

    public function count()
    {
        return $this->count;
    }
}

class SimpleFormTest_Traversable implements \IteratorAggregate
{
    private $iterator;

    public function __construct($count)
    {
        $this->iterator = new \ArrayIterator($count > 0 ? array_fill(0, $count, 'Foo') : []);
    }

    public function getIterator()
    {
        return $this->iterator;
    }
}
