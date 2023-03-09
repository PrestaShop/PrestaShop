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

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Sell\Product\EventListener;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Product\EventListener\FeatureValueListener;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;

class FeatureValueListenerTest extends FormListenerTestCase
{
    /**
     * @var FormCloner
     */
    private $formCloner;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->formCloner = new FormCloner();
    }

    public function testSubscribedEvents(): void
    {
        // Only events are relevant, the matching function is up to implementation
        $expectedSubscribedEvents = [
            FormEvents::PRE_SET_DATA,
            FormEvents::PRE_SUBMIT,
        ];
        $subscribedEvents = FeatureValueListener::getSubscribedEvents();
        $this->assertSame($expectedSubscribedEvents, array_keys($subscribedEvents));
    }

    /**
     * @dataProvider getTestValues
     *
     * @param array<string, mixed> $formData
     * @param array<string, int> $choices
     * @param array<string, mixed> $expectedFeatureValueOptions
     * @param array<string, mixed> $expectedCustomValueOptions
     */
    public function testUpdateFormOptions(
        array $formData,
        ?array $choices,
        array $expectedFeatureValueOptions,
        array $expectedCustomValueOptions
    ): void {
        $listener = new FeatureValueListener(
            $this->createChoiceProviderMock($formData, $choices),
            $this->formCloner,
            $this->createMock(TranslatorInterface::class)
        );

        $form = $this->createForm(SimpleFeaturesFormTest::class);

        $eventMock = $this->createEventMock($formData, $form);
        $listener->updateFormOptions($eventMock);

        $this->assertFeatureValueOptions($form, $expectedFeatureValueOptions, $expectedCustomValueOptions);
    }

    public function getTestValues(): Generator
    {
        yield [
            // formData
            [
                'feature_id' => 42,
            ],
            // choices for choice provider mock
            [
                'Cotton' => 51,
                'Ceramic' => 69,
            ],
            // expected options for feature_value form
            [
                'disabled' => false,
                'choices' => [
                    'Cotton' => 51,
                    'Ceramic' => 69,
                ],
                'attr' => [
                    'disabled' => false,
                    'data-toggle' => 'select2',
                    'class' => 'feature-value-selector',
                ],
                'empty_data' => 0,
            ],
            [
                'attr' => ['class' => 'custom-values'],
                'constraints' => [],
            ],
        ];

        yield [
            [
                'feature_id' => 42,
                'custom_value' => [
                    1 => '',
                    2 => null,
                ],
            ],
            [
                'Cotton' => 51,
                'Ceramic' => 69,
            ],
            [
                'disabled' => false,
                'choices' => [
                    'Cotton' => 51,
                    'Ceramic' => 69,
                ],
                'attr' => [
                    'disabled' => false,
                    'data-toggle' => 'select2',
                    'class' => 'feature-value-selector',
                ],
                'empty_data' => 0,
            ],
            [
                'attr' => ['class' => 'custom-values'],
                'constraints' => [],
            ],
        ];

        // When custom value is present field is disabled and choice provider is called
        yield [
            [
                'feature_id' => 42,
                'custom_value' => [
                    1 => 'custom',
                    2 => null,
                ],
            ],
            [
                'Cotton' => 51,
                'Ceramic' => 69,
            ],
            [
                'disabled' => true,
                'choices' => [
                    'Cotton' => 51,
                    'Ceramic' => 69,
                ],
                'attr' => [
                    'disabled' => true,
                    'data-toggle' => 'select2',
                    'class' => 'feature-value-selector',
                ],
                'empty_data' => 0,
            ],
            [
                'attr' => ['class' => 'custom-values'],
                'constraints' => [new DefaultLanguage()],
            ],
        ];

        // When no choices returned field is disabled. Also custom value is provided, so constraint is added
        yield [
            [
                'feature_id' => 42,
                'custom_value' => [
                    1 => 'custom',
                    2 => null,
                ],
            ],
            [],
            [
                'disabled' => true,
                'choices' => [],
                'attr' => [
                    'disabled' => true,
                    'data-toggle' => 'select2',
                    'class' => 'feature-value-selector',
                ],
                'empty_data' => 0,
            ],
            [
                'attr' => ['class' => 'custom-values'],
                'constraints' => [new DefaultLanguage()],
            ],
        ];

        // When data has no feature_id, the listener returns early without calling choice provider, but still disables feature value
        yield [
            [],
            null,
            [
                'disabled' => true,
                'choices' => [],
                'attr' => [
                    'disabled' => true,
                    'data-toggle' => 'select2',
                    'class' => 'feature-value-selector',
                ],
                'empty_data' => 0,
            ],
            [
                'attr' => ['class' => 'custom-values'],
                'constraints' => [],
            ],
        ];

        yield [
            [
                'feature_id' => null,
                'custom_value' => [
                    1 => 'custom',
                    2 => null,
                ],
            ],
            null,
            [
                'disabled' => true,
                'choices' => [],
                'attr' => [
                    'disabled' => true,
                    'data-toggle' => 'select2',
                    'class' => 'feature-value-selector',
                ],
                'empty_data' => 0,
            ],
            [
                'attr' => ['class' => 'custom-values'],
                'constraints' => [],
            ],
        ];

        yield [
            [
                'feature_id' => '',
            ],
            null,
            [
                'disabled' => true,
                'choices' => [],
                'attr' => [
                    'disabled' => true,
                    'data-toggle' => 'select2',
                    'class' => 'feature-value-selector',
                ],
                'empty_data' => 0,
            ],
            [
                'attr' => ['class' => 'custom-values'],
                'constraints' => [],
            ],
        ];

        yield [
            [
                'feature_id' => 0,
            ],
            null,
            [
                'disabled' => true,
                'choices' => [],
                'attr' => [
                    'disabled' => true,
                    'data-toggle' => 'select2',
                    'class' => 'feature-value-selector',
                ],
                'empty_data' => 0,
            ],
            [
                'attr' => ['class' => 'custom-values'],
                'constraints' => [],
            ],
        ];

        yield [
            [
                'feature_id' => '0',
            ],
            null,
            [
                'disabled' => true,
                'choices' => [],
                'attr' => [
                    'disabled' => true,
                    'data-toggle' => 'select2',
                    'class' => 'feature-value-selector',
                ],
                'empty_data' => 0,
            ],
            [
                'attr' => ['class' => 'custom-values'],
                'constraints' => [],
            ],
        ];
    }

    /**
     * @param FormInterface $form
     * @param array<string, mixed> $expectedFeatureValueOptions
     * @param array<string, mixed> $expectedCustomValueOptions
     */
    private function assertFeatureValueOptions(
        FormInterface $form,
        array $expectedFeatureValueOptions,
        array $expectedCustomValueOptions
    ): void {
        // we only assert options that we expect to change,
        // it is too complicated to assert whole array with so many default values which are not expected to change
        $featureValueFieldConfig = $form->get('feature_value_id')->getConfig();

        $this->assertSame($expectedFeatureValueOptions['empty_data'], $featureValueFieldConfig->getOption('empty_data'));
        $this->assertSame($expectedFeatureValueOptions['attr'], $featureValueFieldConfig->getOption('attr'));
        $this->assertSame($expectedFeatureValueOptions['disabled'], $featureValueFieldConfig->getOption('disabled'));
        $this->assertSame($expectedFeatureValueOptions['choices'], $featureValueFieldConfig->getOption('choices'));

        $customValueFieldConfig = $form->get('custom_value')->getConfig();
        $this->assertSame($expectedCustomValueOptions['attr'], $customValueFieldConfig->getOption('attr'));

        $expectedConstraints = $expectedCustomValueOptions['constraints'];
        $actualConstraints = $customValueFieldConfig->getOption('constraints');
        Assert::assertCount(count($expectedConstraints), $actualConstraints, 'Unexpected count of constraints');

        foreach ($expectedConstraints as $i => $expectedConstraint) {
            $actualConstraint = $actualConstraints[$i];
            // It is enough to assert instance to test if constraint is added/removed, the content of constraint doesn't matter much
            Assert::assertInstanceOf(get_class($expectedConstraint), $actualConstraint);
        }
    }

    /**
     * @param array<string, int> $choices
     *
     * @return MockObject|ConfigurableFormChoiceProviderInterface
     */
    private function createChoiceProviderMock(array $formData, ?array $choices)
    {
        $providerMock = $this->getMockBuilder(ConfigurableFormChoiceProviderInterface::class)
            ->getMock();

        if (null === $choices) {
            $providerMock->expects($this->never())->method('getChoices');
        } else {
            $hasCustomValue = array_reduce($formData['custom_value'] ?? [], function (bool $hasPresentValue, ?string $customValue) {
                return $hasPresentValue || !empty($customValue);
            }, false);

            $providerMock->expects($this->once())->method('getChoices')
                ->with($this->equalTo(['feature_id' => (int) $formData['feature_id'], 'custom' => $hasCustomValue]))
                ->willReturn($choices);
        }

        return $providerMock;
    }
}

class SimpleFeaturesFormTest extends CommonAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('feature_value_id', ChoiceType::class, [
                // these are the default options which are important and might be affected by listener
                'disabled' => true,
                'attr' => [
                    'disabled' => true,
                    'data-toggle' => 'select2',
                    'class' => 'feature-value-selector',
                ],
                // empty data = 0 is important. Without it being set to 0, the "invalid option" constraint is not triggered,
                // and the form is not invalidated (which should happen because field is not optional therefore 0 is invalid option)
                'empty_data' => 0,
            ])
            ->add('custom_value', TranslatableType::class, [
                // we don't check all the options, but at least one to make sure that existing options are intact when adding constraints in listener
                'attr' => ['class' => 'custom-values'],
                // by default there should be no constraints
                'constraints' => [],
            ])
        ;
    }
}
