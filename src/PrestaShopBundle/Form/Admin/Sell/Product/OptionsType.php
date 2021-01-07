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

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

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
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $productVisibilityChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $productVisibilityChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->productVisibilityChoiceProvider = $productVisibilityChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visibility', ChoiceType::class, [
                'choices' => $this->productVisibilityChoiceProvider->getChoices(),
                'attr' => [
                    'class' => 'custom-select',
                ],
                'required' => true,
                'label' => $this->trans('Visibility', 'Admin.Catalog.Feature'),
            ])
            ->add('tags', TranslatableType::class, [
                'required' => false,
                'label' => $this->trans('Tags', 'Admin.Catalog.Feature'),
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => TypedRegex::TYPE_GENERIC_NAME,
                        ]),
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
                    //@todo: adjust TypedRegex use UPC VO
                    new Regex('/^[0-9]{0,12}$/'),
                ],
                'empty_data' => '',
            ])
            ->add('ean13', TextType::class, [
                'required' => false,
                'error_bubbling' => true,
                'label' => $this->trans('EAN-13 or JAN barcode', 'Admin.Catalog.Feature'),
                'constraints' => [
                    //@todo: adjust TypedRegex
                    new Regex('/^[0-9]{0,13}$/'),
                ],
                'empty_data' => '',
            ])
            ->add('isbn', TextType::class, [
                'required' => false,
                'label' => $this->trans('ISBN', 'Admin.Catalog.Feature'),
                'constraints' => [
                    //@todo: adjust TypedRegex
                    new Regex('/^[0-9-]{0,32}$/'),
                ],
                'empty_data' => '',
            ])
            ->add('reference', TextType::class, [
                'required' => false,
                'label' => $this->trans('Reference', 'Admin.Global'),
                'empty_data' => '',
            ])
            ->add('show_condition', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Display condition on product page', 'Admin.Catalog.Feature'),
            ])
            ->add('condition', ChoiceType::class, [
                'choices' => [
                    $this->trans('New', 'Shop.Theme.Catalog') => 'new',
                    $this->trans('Used', 'Shop.Theme.Catalog') => 'used',
                    $this->trans('Refurbished', 'Shop.Theme.Catalog') => 'refurbished',
                ],
                'attr' => [
                    'class' => 'custom-select',
                ],
                'required' => true,
                'label' => $this->trans('Condition', 'Admin.Catalog.Feature'),
            ])
//            ->add('suppliers', ChoiceType::class, [
//                'choices' => $this->suppliers,
//                'expanded' => true,
//                'multiple' => true,
//                'required' => false,
//                'attr' => [
//                    'class' => 'custom-select',
//                ],
//                'label' => $this->trans('Suppliers', 'Admin.Global'),
//            ])
//            ->add('default_supplier', ChoiceType::class, [
//                'choices' => $this->suppliers,
//                'expanded' => true,
//                'multiple' => false,
//                'required' => true,
//                'attr' => [
//                    'class' => 'custom-select',
//                ],
//                'label' => $this->trans('Default suppliers', 'Admin.Catalog.Feature'),
//            ])
        ;
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'product_options';
    }
}
