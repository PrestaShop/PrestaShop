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

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FeatureValueType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $featuresChoiceProvider;

    /**
     * @var EventSubscriberInterface
     */
    private $featureValueListener;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $featuresChoiceProvider,
        EventSubscriberInterface $featureValueListener
    ) {
        parent::__construct($translator, $locales);
        $this->featuresChoiceProvider = $featuresChoiceProvider;
        $this->featureValueListener = $featureValueListener;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $features = $this->featuresChoiceProvider->getChoices();

        $builder
            ->add('feature_id', ChoiceType::class, [
                'choices' => $features,
                'required' => false,
                'placeholder' => $this->trans('Choose a value', 'Admin.Catalog.Feature'),
                'label' => $this->trans('Feature', 'Admin.Catalog.Feature'),
                'attr' => [
                    'data-toggle' => 'select2',
                    'class' => 'feature-selector',
                ],
            ])
            ->add('feature_value_id', ChoiceType::class, [
                'required' => false,
                'empty_data' => null,
                'placeholder' => $this->trans('Choose a value', 'Admin.Catalog.Feature'),
                'label' => $this->trans('Pre-defined value', 'Admin.Catalog.Feature'),
                'attr' => [
                    'disabled' => true,
                    'data-toggle' => 'select2',
                    'class' => 'feature-value-selector',
                ],
            ])
            ->add('custom_value', TranslatableType::class, [
                'label' => $this->trans('OR Customized value', 'Admin.Catalog.Feature'),
                'required' => false,
                'type' => TextType::class,
                'attr' => [
                    'class' => 'custom-values',
                ],
            ])
            ->add('custom_value_id', HiddenType::class, [
                'required' => false,
                'empty_data' => null,
                'attr' => [
                    'class' => 'custom-value-id',
                ],
            ])
            ->add('delete', IconButtonType::class, [
                'icon' => 'delete',
                'attr' => [
                    'class' => 'tooltip-link delete-feature-value pl-0 pr-0',
                    'data-modal-title' => $this->trans('Warning', 'Admin.Notifications.Warning'),
                    'data-modal-message' => $this->trans('Are you sure you want to delete this item?', 'Admin.Notifications.Warning'),
                    'data-modal-apply' => $this->trans('Yes', 'Admin.Global'),
                    'data-modal-cancel' => $this->trans('No', 'Admin.Global'),
                ],
            ])
        ;

        // This listeners register to PRE_SET_DATA and PRE_SUBMIT events to dynamically set the proper choices of the
        // feature_value_id field
        $builder->addEventSubscriber($this->featureValueListener);
    }
}
