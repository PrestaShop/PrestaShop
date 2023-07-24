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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class generates "Product page" form
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class PageType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('display_quantities', SwitchType::class, [
                'label' => $this->trans(
                    'Display available quantities on the product page',
                    'Admin.Shopparameters.Feature'
                ),
                'required' => false,
            ])
            ->add('allow_add_variant_to_cart_from_listing', SwitchType::class, [
                'label' => $this->trans(
                    'Display the "%add_to_cart_label%" button when a product has attributes',
                    'Admin.Shopparameters.Help',
                    [
                        '%add_to_cart_label%' => $this->trans(
                            'Add to cart',
                            'Shop.Theme.Actions'
                        ),
                    ]
                ),
                'help' => $this->trans(
                    'Display or hide the "%add_to_cart_label%" button on category pages for products that have attributes forcing customers to see product details.',
                    'Admin.Shopparameters.Help',
                    [
                        '%add_to_cart_label%' => $this->trans(
                            'Add to cart',
                            'Shop.Theme.Actions'
                        ),
                    ]
                ),
                'required' => false,
            ])
            ->add('use_combination_image_in_listing', SwitchType::class, [
                'label' => $this->trans(
                    'Use combination image in listings',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'This option allows you to choose which image you want to use in listings for products with combinations. By default, it always uses the cover image of the product. If you enable this option and your filtering module is properly passing required information, you can show directly show the image of this combination.',
                    'Admin.Shopparameters.Feature'
                ),
                'required' => false,
            ])
            ->add('attribute_anchor_separator', ChoiceType::class, [
                'label' => $this->trans(
                    'Separator of attribute anchor on the product links',
                    'Admin.Shopparameters.Feature'
                ),
                'choices' => [
                    '-' => '-',
                    ',' => ',',
                ],
                'placeholder' => false,
                'required' => false,
                'choice_translation_domain' => 'Admin.Global',
            ])
            ->add('display_discount_price', SwitchType::class, [
                'label' => $this->trans(
                    'Display the discounted unit price',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'In the volume discount table on the product page, display the discounted unit price instead of the unit discount. E.g. If you sell a product for $10 with a discount of $2 from 3 items purchased, the discounted unit price ($8) will be displayed instead of the unit discount ($2).',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'product_preferences_page_block';
    }
}
