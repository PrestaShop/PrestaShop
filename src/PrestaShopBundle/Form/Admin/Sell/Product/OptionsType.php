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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;

/**
 * This form class is responsible to generate the product options form.
 */
class OptionsType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $productVisibilityChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $productConditionChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $productVisibilityChoiceProvider
     * @param FormChoiceProviderInterface $productConditionChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $productVisibilityChoiceProvider,
        FormChoiceProviderInterface $productConditionChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->productVisibilityChoiceProvider = $productVisibilityChoiceProvider;
        $this->productConditionChoiceProvider = $productConditionChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Online', 'Admin.Global'),
                'required' => false,
            ])
            ->add('visibility', ChoiceType::class, [
                'choices' => $this->productVisibilityChoiceProvider->getChoices(),
                'attr' => [
                    'class' => 'custom-select',
                ],
                'label' => $this->trans('Visibility', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('available_for_order', SwitchType::class, [
                'label' => $this->trans('Available for order', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('show_price', SwitchType::class, [
                'label' => $this->trans('Show price', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('online_only', SwitchType::class, [
                'label' => $this->trans('Web only (not sold in your retail store)', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('tags', TranslatableType::class, [
                'required' => false,
                'label' => $this->trans('Tags', 'Admin.Catalog.Feature'),
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                    ],
                    'attr' => [
                        'class' => 'js-taggable-field',
                        'placeholder' => $this->trans('Use a comma to create separate tags. E.g.: dress, cotton, party dresses.', 'Admin.Catalog.Help'),
                    ],
                    'required' => false,
                ],
            ])
            ->add('mpn', TextType::class, [
                'required' => false,
                'label' => $this->trans('MPN', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Length(['max' => ProductSettings::MAX_MPN_LENGTH]),
                ],
                'empty_data' => '',
            ])
            ->add('upc', TextType::class, [
                'required' => false,
                'label' => $this->trans('UPC barcode', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_UPC),
                ],
                'empty_data' => '',
            ])
            ->add('ean_13', TextType::class, [
                'required' => false,
                'error_bubbling' => true,
                'label' => $this->trans('EAN-13 or JAN barcode', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_EAN_13),
                ],
                'empty_data' => '',
            ])
            ->add('isbn', TextType::class, [
                'required' => false,
                'label' => $this->trans('ISBN', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_ISBN),
                ],
                'empty_data' => '',
            ])
            ->add('reference', TextType::class, [
                'required' => false,
                'label' => $this->trans('Reference', 'Admin.Global'),
                'empty_data' => '',
            ])
            ->add('show_condition', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Display condition on product page', 'Admin.Catalog.Feature'),
            ])
            ->add('condition', ChoiceType::class, [
                'choices' => $this->productConditionChoiceProvider->getChoices(),
                'attr' => [
                    'class' => 'custom-select',
                ],
                'required' => false,
                'label' => $this->trans('Condition', 'Admin.Catalog.Feature'),
            ])
        ;
    }
}
