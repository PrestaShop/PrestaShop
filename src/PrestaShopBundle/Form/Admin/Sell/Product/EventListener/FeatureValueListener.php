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

namespace PrestaShopBundle\Form\Admin\Sell\Product\EventListener;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This listener dynamically updates the choices allowed in the feature value selector,
 * choices are fetched based on the selected feature.
 */
class FeatureValueListener implements EventSubscriberInterface
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $featureValuesChoiceProvider;

    /**
     * @var FormCloner
     */
    private $formCloner;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ConfigurableFormChoiceProviderInterface $featureValuesChoiceProvider
     * @param FormCloner $formCloner
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ConfigurableFormChoiceProviderInterface $featureValuesChoiceProvider,
        FormCloner $formCloner,
        TranslatorInterface $translator
    ) {
        $this->featureValuesChoiceProvider = $featureValuesChoiceProvider;
        $this->formCloner = $formCloner;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'updateFormOptions',
            FormEvents::PRE_SUBMIT => 'updateFormOptions',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function updateFormOptions(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (empty($data['feature_id'])) {
            // make sure feature_value_id form is disabled in case it was rendered after error of not selecting feature_id
            $newFeatureValueForm = $this->formCloner->cloneForm($form->get('feature_value_id'), $this->getOptions(true));
            $form->add($newFeatureValueForm);

            return;
        }

        $hasCustomValue = array_reduce($data['custom_value'] ?? [], function (bool $hasPresentValue, ?string $customValue) {
            return $hasPresentValue || !empty($customValue);
        }, false);

        $featureValues = $this->featureValuesChoiceProvider->getChoices(['feature_id' => (int) $data['feature_id'], 'custom' => $hasCustomValue]);
        $options = $this->getOptions($hasCustomValue || empty($featureValues));
        $options['choices'] = $featureValues;

        $newFeatureValueForm = $this->formCloner->cloneForm($form->get('feature_value_id'), $options);
        $form->add($newFeatureValueForm);

        if (empty($data['feature_value_id']) && $hasCustomValue) {
            // add constraints for custom_value when it is set, but only if feature_value_id is not set
            $form->add($this->formCloner->cloneForm($form->get('custom_value'), [
                'constraints' => [
                    new DefaultLanguage([
                        'message' => $this->translator->trans(
                            'The field %field_name% is required at least in your default language.',
                            [
                                '%field_name%' => sprintf(
                                    '"%s"',
                                    $this->translator->trans('Custom value', [], 'Admin.Catalog.Feature')
                                ),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ]));
        }
    }

    /**
     * @param bool $disabled
     *
     * @return array<string, mixed>
     */
    private function getOptions(bool $disabled): array
    {
        return [
            'disabled' => $disabled,
            'attr' => [
                'disabled' => $disabled,
                'data-toggle' => 'select2',
                'class' => 'feature-value-selector',
            ],
        ];
    }
}
