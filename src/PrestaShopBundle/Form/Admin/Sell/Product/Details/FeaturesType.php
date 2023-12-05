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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Details;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\FeaturesChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeaturesType extends TranslatorAwareType
{
    /**
     * @var FeaturesChoiceProvider
     */
    private $featuresChoiceProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FeaturesChoiceProvider $featuresChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->featuresChoiceProvider = $featuresChoiceProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $features = array_merge([
            $this->trans('Choose a feature', 'Admin.Catalog.Feature') => 0,
        ], $this->featuresChoiceProvider->getChoices());

        $builder
            ->add('feature_id', ChoiceType::class, [
                'empty_data' => 0,
                'choices' => $features,
                'label' => $this->trans('Feature', 'Admin.Catalog.Feature'),
                'attr' => [
                    'class' => 'feature-selector',
                ],
            ])
            ->add('feature_value_id', ChoiceType::class, [
                'empty_data' => 0,
                'placeholder' => $this->trans('Choose a value', 'Admin.Catalog.Feature'),
                'label' => $this->trans('Pre-defined value', 'Admin.Catalog.Feature'),
                'invalid_message' => $this->trans('Choose a value or provide a customized one', 'Admin.Catalog.Feature'),
                'disabled' => true,
                'attr' => [
                    'data-custom-value-label' => $this->trans('Add a customized value', 'Admin.Catalog.Feature'),
                    'class' => 'feature-value-selector',
                ],
            ])
            ->add('add_feature', IconButtonType::class, [
                'label' => $this->trans('Add a feature', 'Admin.Catalog.Feature'),
                'icon' => 'add_circle',
                'attr' => [
                    'class' => 'btn-outline-primary feature-value-add-button',
                ],
                'disabled' => true,
            ])
            ->add('feature_values', CollectionType::class, [
                'entry_type' => FeatureValueType::class,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => '__FEATURE_VALUE_INDEX__',
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/features.html.twig',
        ]);
    }
}
