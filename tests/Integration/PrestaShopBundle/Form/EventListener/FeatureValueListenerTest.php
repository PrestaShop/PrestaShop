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

namespace Tests\Integration\PrestaShopBundle\Form\EventListener;

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
     * @param array $formData
     * @param array|null $expectedFilters
     * @param array|null $choices
     * @param bool $disabled
     */
    public function testUpdateFeatureValuesOptions(array $formData, ?array $expectedFilters, ?array $choices, bool $disabled): void
    {
        $providerMock = $this->createChoiceProviderMock($choices, $expectedFilters);
        $listener = new FeatureValueListener($providerMock, $this->formCloner);

        $form = $this->createForm(SimpleFeaturesFormTest::class);
        $this->assertFormChoices($form, []);

        $eventMock = $this->createEventMock($formData, $form);
        $listener->updateFeatureValuesOptions($eventMock);

        $this->assertFormChoices($form, $choices);
        // Use cases where no filters present return early so attributes are not changed
        if (null !== $expectedFilters) {
            $this->assertFormAttributes($form, $disabled);
        }
    }

    public function getTestValues(): Generator
    {
        yield [
            // $formData
            [
                'feature_id' => 42,
            ],
            // $expectedFilters
            [
                'feature_id' => 42,
                'custom' => false,
            ],
            // $choices
            [
                'Cotton' => 51,
                'Ceramic' => 69,
            ],
            // $disabled
            false,
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
                'feature_id' => 42,
                'custom' => false,
            ],
            [
                'Cotton' => 51,
                'Ceramic' => 69,
            ],
            false,
        ];

        // When custom value is present field is disabled
        yield [
            [
                'feature_id' => 42,
                'custom_value' => [
                    1 => 'custom',
                    2 => null,
                ],
            ],
            [
                'feature_id' => 42,
                'custom' => true,
            ],
            [
                'Cotton' => 51,
                'Ceramic' => 69,
            ],
            true,
        ];

        // When no choices returned field is disabled
        yield [
            [
                'feature_id' => 42,
            ],
            [
                'feature_id' => 42,
                'custom' => false,
            ],
            [
            ],
            true,
        ];

        // When data input has no feature_id the listener returns early
        yield [
            [],
            null,
            [],
            false,
        ];

        yield [
            [
                'feature_id' => null,
            ],
            null,
            [],
            false,
        ];

        yield [
            [
                'feature_id' => '',
            ],
            null,
            [],
            false,
        ];

        yield [
            [
                'feature_id' => 0,
            ],
            null,
            [],
            false,
        ];

        yield [
            [
                'feature_id' => '0',
            ],
            null,
            [],
            false,
        ];
    }

    /**
     * @param FormInterface $form
     * @param bool $disabled
     */
    private function assertFormAttributes(FormInterface $form, bool $disabled): void
    {
        $expectedAttr = [
            'disabled' => $disabled,
            'data-toggle' => 'select2',
            'class' => 'feature-value-selector',
        ];
        $featureValueField = $form->get('feature_value_id');
        $fieldAttributes = $featureValueField->getConfig()->getOption('attr');
        $this->assertSame($expectedAttr, $fieldAttributes);
    }

    /**
     * @param FormInterface $form
     * @param array $choices
     */
    private function assertFormChoices(FormInterface $form, array $choices): void
    {
        $featureValueField = $form->get('feature_value_id');
        $fieldChoices = $featureValueField->getConfig()->getOption('choices');
        $this->assertSame($choices, $fieldChoices);
    }

    /**
     * @param array $choices
     * @param array|null $expectedFilters
     *
     * @return MockObject|ConfigurableFormChoiceProviderInterface
     */
    private function createChoiceProviderMock(array $choices, ?array $expectedFilters)
    {
        $providerMock = $this->getMockBuilder(ConfigurableFormChoiceProviderInterface::class)
            ->getMock();

        if (null === $expectedFilters) {
            $providerMock->expects($this->never())->method('getChoices');
        } else {
            $providerMock->expects($this->once())->method('getChoices')
                ->with($this->equalTo($expectedFilters))
                ->willReturn($choices);
        }

        return $providerMock;
    }
}

class SimpleFeaturesFormTest extends CommonAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('feature_value_id', ChoiceType::class);
    }
}
