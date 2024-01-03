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
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeaturesType extends TranslatorAwareType
{
    /**
     * @var FeaturesChoiceProvider
     */
    private $featuresChoiceProvider;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FeaturesChoiceProvider $featuresChoiceProvider,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($translator, $locales);
        $this->featuresChoiceProvider = $featuresChoiceProvider;
        $this->urlGenerator = $urlGenerator;
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
                'external_link' => [
                    'text' => $this->trans('[1]Manage features[/1]', 'Admin.Catalog.Feature'),
                    'href' => $this->urlGenerator->generate('admin_features_index'),
                    'position' => 'prepend',
                ],
            ])
            ->add('feature_value_id', ChoiceType::class, [
                'empty_data' => 0,
                'placeholder' => $this->trans('Choose a value or provide a customized one', 'Admin.Catalog.Feature'),
                'label' => false,
                'disabled' => true,
                'attr' => [
                    'data-placeholder-label' => $this->trans('Choose a value or provide a customized one', 'Admin.Catalog.Feature'),
                    'data-custom-value-label' => $this->trans('Add a customized value', 'Admin.Catalog.Feature'),
                    'class' => 'feature-value-selector',
                ],
            ])
            ->add('custom_value', TranslatableType::class, [
                'label' => false,
                'required' => false,
                'type' => TextType::class,
                'attr' => [
                    'class' => 'new-custom-values d-none',
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
            ->add('feature_collection', FeatureCollectionType::class)
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
