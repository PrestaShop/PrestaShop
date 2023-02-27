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
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Product\EventListener\FeatureValueListener;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
     * @param array<string, mixed> $expectedOptions
     */
    public function testUpdateFeatureValuesOptions(
        array $formData,
        ?array $choices,
        array $expectedOptions
    ): void {
        $providerMock = $this->createChoiceProviderMock($formData, $choices);
        $listener = new FeatureValueListener($providerMock, $this->formCloner);

        $form = $this->createForm(SimpleFeaturesFormTest::class);

        $eventMock = $this->createEventMock($formData, $form);
        $listener->updateFeatureValuesOptions($eventMock);

        $this->assertFeatureValueOptions($form, $expectedOptions);
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
            ],
        ];

        // When no choices returned field is disabled
        yield [
            [
                'feature_id' => 42,
                'custom' => false,
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
            ],
        ];

        yield [
            [
                'feature_id' => null,
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
            ],
        ];
    }

    /**
     * @param FormInterface $form
     * @param array<string, mixed> $expectedOptions
     */
    private function assertFeatureValueOptions(FormInterface $form, array $expectedOptions): void
    {
        // we only assert options that we expect to change,
        // it is too complicated to assert whole array with so many default values which are not expected to change
        $featureValueFieldConfig = $form->get('feature_value_id')->getConfig();

        $this->assertSame($expectedOptions['attr'], $featureValueFieldConfig->getOption('attr'));
        $this->assertSame($expectedOptions['disabled'], $featureValueFieldConfig->getOption('disabled'));
        $this->assertSame($expectedOptions['choices'], $featureValueFieldConfig->getOption('choices'));
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
        $builder->add('feature_value_id', ChoiceType::class, [
            // these are the default options which should change after listener is called
            'disabled' => true,
            'attr' => [
                'disabled' => true,
                'data-toggle' => 'select2',
                'class' => 'feature-value-selector',
            ],
        ]);
    }
}
