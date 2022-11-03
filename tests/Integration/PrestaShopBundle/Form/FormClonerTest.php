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

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form;

use Closure;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\ChoiceToValueTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * We use an integration test instead of pure unit because manually creating advanced forms is a complicated and
 * long process, and it would probably end up in mistakes in the test initialization process itself which would
 * reduce the overall quality of the test.
 */
class FormClonerTest extends AbstractFormTester
{
    public function testClone(): void
    {
        $form = $this->createForm(AdvancedFormType::class);
        $this->assertTotalNumberOfListeners($form, 5);
        $formCloner = new FormCloner();
        $clonedForm = $formCloner->cloneForm($form);
        $this->compareForms($form, $clonedForm);
    }

    public function testCloneWithModelTransformer(): void
    {
        $transformer = new TestDataTransformer();
        $form = $this->createForm(AdvancedFormType::class, [
            'add_model_transformer' => $transformer,
        ]);
        $this->assertTotalNumberOfListeners($form, 5);

        $formTransformers = $form->getConfig()->getModelTransformers();
        $this->assertContains($transformer, $formTransformers);

        $targetFormTransformers = $form->get('target')->getConfig()->getModelTransformers();
        $this->assertContains($transformer, $targetFormTransformers);

        $formCloner = new FormCloner();
        $clonedForm = $formCloner->cloneForm($form);
        $this->compareForms($form, $clonedForm);

        $clonedTargetFormTransformers = $clonedForm->get('target')->getConfig()->getModelTransformers();
        $this->assertContains($transformer, $clonedTargetFormTransformers);
    }

    public function testCloneChildWithModelTransformer(): void
    {
        $transformer = new TestDataTransformer();
        $form = $this->createForm(AdvancedFormType::class, [
            'add_model_transformer' => $transformer,
        ]);
        $this->assertTotalNumberOfListeners($form, 5);

        $formTransformers = $form->get('target')->getConfig()->getModelTransformers();
        $this->assertContains($transformer, $formTransformers);

        $targetForm = $form->get('target');
        $formCloner = new FormCloner();
        $clonedForm = $formCloner->cloneForm($targetForm);
        // When children form are cloned auto_initialize must be set to false to be able to add them to another form
        $this->compareForms($targetForm, $clonedForm, ['auto_initialize' => false]);

        $clonedTargetFormTransformers = $clonedForm->getConfig()->getModelTransformers();
        $this->assertContains($transformer, $clonedTargetFormTransformers);
    }

    public function testCloneWithViewTransformer(): void
    {
        $transformer = new TestDataTransformer();
        $form = $this->createForm(AdvancedFormType::class, [
            'add_view_transformer' => $transformer,
        ]);
        $this->assertTotalNumberOfListeners($form, 5);

        $formTransformers = $form->getConfig()->getViewTransformers();
        $this->assertContains($transformer, $formTransformers);

        $targetFormTransformers = $form->get('target')->getConfig()->getViewTransformers();
        $this->assertContains($transformer, $targetFormTransformers);

        $formCloner = new FormCloner();
        $clonedForm = $formCloner->cloneForm($form);
        $this->compareForms($form, $clonedForm);

        $clonedTargetFormTransformers = $clonedForm->get('target')->getConfig()->getViewTransformers();
        $this->assertContains($transformer, $clonedTargetFormTransformers);
    }

    public function testCloneChildWithViewTransformer(): void
    {
        $transformer = new TestDataTransformer();
        $form = $this->createForm(AdvancedFormType::class, [
            'add_view_transformer' => $transformer,
        ]);
        $this->assertTotalNumberOfListeners($form, 5);

        $formTransformers = $form->get('target')->getConfig()->getViewTransformers();
        $this->assertContains($transformer, $formTransformers);

        $targetForm = $form->get('target');
        $formCloner = new FormCloner();
        $clonedForm = $formCloner->cloneForm($targetForm);
        // When children form are cloned auto_initialize must be set to false to be able to add them to another form
        $this->compareForms($targetForm, $clonedForm, ['auto_initialize' => false]);

        $clonedTargetFormTransformers = $clonedForm->getConfig()->getViewTransformers();
        $this->assertContains($transformer, $clonedTargetFormTransformers);
    }

    public function testCloneWithEventSubscriber(): void
    {
        $subscriber = new FormEventCounterSubscriber();
        $form = $this->createForm(AdvancedFormType::class, [
            'add_event_subscriber' => $subscriber,
        ]);
        $this->assertTotalNumberOfListeners($form, 10);

        $formCloner = new FormCloner();
        $clonedForm = $formCloner->cloneForm($form);
        $this->compareForms($form, $clonedForm);
        $this->assertTotalNumberOfListeners($clonedForm, 10);
    }

    public function testCloneWithEventListener(): void
    {
        $listener = [
            'event' => FormEvents::SUBMIT,
            'callback' => function (FormEvent $formEvent) {
                // Empty listener
            },
        ];
        $form = $this->createForm(AdvancedFormType::class, [
            'add_event_listener' => $listener,
        ]);
        $this->assertTotalNumberOfListeners($form, 6);

        $formCloner = new FormCloner();
        $clonedForm = $formCloner->cloneForm($form);
        $this->compareForms($form, $clonedForm);
        $this->assertTotalNumberOfListeners($clonedForm, 6);
    }

    public function testCloneWithOptions(): void
    {
        $form = $this->createForm(AdvancedFormType::class, [
            'required' => false,
        ]);
        $this->assertTotalNumberOfListeners($form, 5);
        $formCloner = new FormCloner();
        $clonedForm = $formCloner->cloneForm($form, [
            'required' => true,
        ]);
        $this->compareForms($form, $clonedForm, [
            'required' => true,
        ]);
    }

    /**
     * This is based on a use case of duplicating a choice type, which internally builds its own
     * view transformers based on the options. So we need to check that cloning the form didn't duplicate
     * this transformer but instead replaced it with the appropriate options.
     */
    public function testOverrideViewTransformers(): void
    {
        $originalChoices = [
            'English' => 1,
            'French' => 3,
        ];
        $choiceForm = $this->createForm(ChoiceType::class, [
            'choices' => $originalChoices,
        ]);

        $formCloner = new FormCloner();
        $overrideChoices = [
            'Spanish' => 2,
        ];
        $newOptions = [
            'choices' => $overrideChoices,
        ];
        $clonedChoiceForm = $formCloner->cloneForm($choiceForm, $newOptions);
        $this->compareForms($choiceForm, $clonedChoiceForm, $newOptions);

        $viewTransformers = $choiceForm->getConfig()->getViewTransformers();
        $this->assertCount(1, $viewTransformers);
        $viewTransformer = $viewTransformers[0];
        $this->assertInstanceOf(ChoiceToValueTransformer::class, $viewTransformer);
        $choicesList = $this->getTransformerChoiceList($viewTransformer);
        $this->assertEquals($originalChoices, $choicesList->getStructuredValues());

        $clonedViewTransformers = $clonedChoiceForm->getConfig()->getViewTransformers();
        $this->assertCount(1, $clonedViewTransformers);
        $clonedViewTransformer = $clonedViewTransformers[0];
        $this->assertInstanceOf(ChoiceToValueTransformer::class, $clonedViewTransformer);
        $clonedChoicesList = $this->getTransformerChoiceList($clonedViewTransformer);
        $this->assertEquals($overrideChoices, $clonedChoicesList->getStructuredValues());
    }

    /**
     * @param DataTransformerInterface $transformer
     *
     * @return ChoiceListInterface
     */
    private function getTransformerChoiceList(DataTransformerInterface $transformer): ChoiceListInterface
    {
        $reflectionProperty = new \ReflectionProperty(ChoiceToValueTransformer::class, 'choiceList');
        $reflectionProperty->setAccessible(true);

        $choiceList = $reflectionProperty->getValue($transformer);
        $reflectionProperty->setAccessible(false);

        return $choiceList;
    }

    /**
     * @param FormInterface $form
     * @param int $expectedCount
     */
    private function assertTotalNumberOfListeners(FormInterface $form, int $expectedCount): void
    {
        $listenersCount = 0;
        foreach ($form->getConfig()->getEventDispatcher()->getListeners() as $eventName => $listeners) {
            $listenersCount += count($listeners);
        }
        $this->assertSame($expectedCount, $listenersCount);
    }

    /**
     * @param FormInterface $originalForm
     * @param FormInterface $clonedForm
     * @param array $newOptions
     */
    private function compareForms(FormInterface $originalForm, FormInterface $clonedForm, array $newOptions = []): void
    {
        // Note: we use assertSame everywhere instead of assertEquals because of a bug with isolated process in PHPUnit
        // https://github.com/sebastianbergmann/phpunit/issues/1515
        $this->assertSame($originalForm->getData(), $clonedForm->getData());
        $this->compareFormConfigs($originalForm->getConfig(), $clonedForm->getConfig(), $newOptions);

        // Compare children
        $this->assertSame($originalForm->count(), $clonedForm->count());
        foreach ($originalForm->all() as $childName => $originalChild) {
            $this->assertTrue($clonedForm->has($childName));
            $clonedChild = $clonedForm->get($childName);
            $this->compareForms($originalChild, $clonedChild);
        }
    }

    /**
     * @param FormConfigInterface $originalConfig
     * @param FormConfigInterface $clonedConfig
     * @param array $newOptions
     */
    private function compareFormConfigs(
        FormConfigInterface $originalConfig,
        FormConfigInterface $clonedConfig,
        array $newOptions = []
    ): void {
        $this->assertSame($originalConfig->getName(), $clonedConfig->getName());
        $this->assertSame($originalConfig->getPropertyPath(), $clonedConfig->getPropertyPath());
        $this->assertSame($originalConfig->getMapped(), $clonedConfig->getMapped());
        $this->assertSame($originalConfig->getByReference(), $clonedConfig->getByReference());
        $this->assertSame($originalConfig->getInheritData(), $clonedConfig->getInheritData());
        $this->assertSame($originalConfig->getCompound(), $clonedConfig->getCompound());
        $this->assertSame($originalConfig->getType(), $clonedConfig->getType());

        $this->compareFormOptions($originalConfig->getOptions(), $clonedConfig->getOptions(), $newOptions);
        $this->compareEventDispatchers($originalConfig->getEventDispatcher(), $clonedConfig->getEventDispatcher());
        $this->compareDataTransformers($originalConfig->getModelTransformers(), $clonedConfig->getModelTransformers());
        $this->compareDataTransformers($originalConfig->getViewTransformers(), $clonedConfig->getViewTransformers());
    }

    /**
     * @param array $originalOptions
     * @param array $clonedOptions
     * @param array $newOptions
     */
    private function compareFormOptions(array $originalOptions, array $clonedOptions, array $newOptions = []): void
    {
        $this->assertSame(count($originalOptions), count($clonedOptions));
        foreach ($originalOptions as $optionName => $originalOption) {
            $this->assertArrayHasKey($optionName, $clonedOptions);
            $clonedOption = $clonedOptions[$optionName];
            if ($originalOption instanceof Closure) {
                // Closure cannot be serialized so we just check the type
                $this->assertInstanceOf(Closure::class, $clonedOption);
            } else {
                // This allows us to check if option overriding works correctly
                if (isset($newOptions[$optionName])) {
                    $expectedOption = $newOptions[$optionName];
                } else {
                    $expectedOption = $originalOption;
                }
                $this->assertSame($expectedOption, $clonedOption, sprintf(
                    'Option "%s", expected %s but got %s instead',
                    $optionName,
                    var_export($originalOption, true),
                    var_export($clonedOption, true)
                ));
            }
        }
    }

    /**
     * @param EventDispatcherInterface $originalDispatcher
     * @param EventDispatcherInterface $clonedDispatcher
     */
    private function compareEventDispatchers(EventDispatcherInterface $originalDispatcher, EventDispatcherInterface $clonedDispatcher): void
    {
        foreach ($originalDispatcher->getListeners() as $eventName => $originalListeners) {
            $clonedListeners = $clonedDispatcher->getListeners($eventName);
            $this->assertSame(count($originalListeners), count($clonedListeners));
            foreach ($originalListeners as $index => $originalListener) {
                $this->assertArrayHasKey($index, $clonedListeners);
                $clonedListener = $clonedListeners[$index];
                if ($originalListener instanceof Closure) {
                    // Closure cannot be serialized so we just check the type
                    $this->assertInstanceOf(Closure::class, $clonedListener);
                } elseif (is_array($originalListener) && 2 === count($originalListener) && is_object($originalListener[0])) {
                    // Probably a callback array
                    $this->assertIsArray($clonedListener);
                    $this->assertSame(2, count($clonedListener));
                    $this->assertIsObject($clonedListener[0]);
                    $this->assertInstanceOf(get_class($originalListener[0]), $clonedListener[0]);
                    // This is the event name
                    $this->assertSame($originalListener[1], $clonedListener[1]);
                } else {
                    $this->assertSame($originalListener, $clonedListener);
                }
            }
        }
    }

    /**
     * @param DataTransformerInterface[] $originalTransformers
     * @param DataTransformerInterface[] $clonedTransformers
     */
    private function compareDataTransformers(array $originalTransformers, array $clonedTransformers): void
    {
        $this->assertSame(count($originalTransformers), count($clonedTransformers));
        foreach ($originalTransformers as $index => $originalTransformer) {
            $this->assertArrayHasKey($index, $clonedTransformers);
            $clonedTransformer = $clonedTransformers[$index];
            // We can't check the whole object but at least the type
            $this->assertInstanceOf(get_class($originalTransformer), $clonedTransformer);
        }
    }
}
