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

namespace PrestaShopBundle\Form\Admin\Sell\Product\EventListener;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
     * @param ConfigurableFormChoiceProviderInterface $featureValuesChoiceProvider
     * @param FormCloner $formCloner
     */
    public function __construct(
        ConfigurableFormChoiceProviderInterface $featureValuesChoiceProvider,
        FormCloner $formCloner
    ) {
        $this->featureValuesChoiceProvider = $featureValuesChoiceProvider;
        $this->formCloner = $formCloner;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'updateFeatureValuesOptions',
            FormEvents::PRE_SUBMIT => 'updateFeatureValuesOptions',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function updateFeatureValuesOptions(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (empty($data['feature_id'])) {
            return;
        }

        $hasCustomValue = array_reduce($data['custom_value'] ?? [], function (bool $hasPresentValue, ?string $customValue) {
            return $hasPresentValue || !empty($customValue);
        }, false);

        $featureValues = $this->featureValuesChoiceProvider->getChoices(['feature_id' => (int) $data['feature_id'], 'custom' => $hasCustomValue]);
        $newFeatureValueForm = $this->formCloner->cloneForm($form->get('feature_value_id'), [
            'choices' => $featureValues,
            'attr' => [
                'disabled' => $hasCustomValue || empty($featureValues),
                'data-toggle' => 'select2',
                'class' => 'feature-value-selector',
            ],
        ]);
        $form->add($newFeatureValueForm);
    }
}
