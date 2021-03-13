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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;

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
                'label' => $this->trans('Display available quantities on the product page', 'Admin.Shopparameters.Feature'),
            ])
            ->add('display_last_quantities', IntegerType::class, [
                'label' => $this->trans('Display remaining quantities when the quantity is lower than', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Set to "0" to disable this feature.', 'Admin.Shopparameters.Help'),
                'constraints' => [
                    new Type(
                        [
                            'value' => 'numeric',
                            'message' => $this->trans('The field is invalid. Please enter a positive integer number.', 'Admin.Notifications.Error'),
                        ]
                    ),
                    new GreaterThanOrEqual(
                        [
                            'value' => 0,
                            'message' => $this->trans('The field is invalid. Please enter a positive integer number.', 'Admin.Notifications.Error'),
                        ]
                    ),
                ],
            ])
            ->add('display_unavailable_attributes', SwitchType::class, [
                'label' => $this->trans('Display unavailable attributes on the product page', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('If an attribute is not available in every product combination, it will not be displayed.', 'Admin.Shopparameters.Help'),
            ])
            ->add('allow_add_variant_to_cart_from_listing', SwitchType::class, [
                'label' => $this->trans('Display the "%add_to_cart_label%" button when a product has attributes', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Display or hide the "%add_to_cart_label%" button on category pages for products that have attributes forcing customers to see product details.',
                    'Admin.Shopparameters.Help',
                    [
                        '%add_to_cart_label%' => $this->trans('Add to cart', 'Shop.Theme.Actions'),
                    ]
                ),
            ])
            ->add('attribute_anchor_separator', ChoiceType::class, [
                'choices' => [
                    '-' => '-',
                    ',' => ',',
                ],
                'required' => true,
                'choice_translation_domain' => 'Admin.Global',
                'label' => $this->trans('Separator of attribute anchor on the product links', 'Admin.Shopparameters.Feature'),
            ])
            ->add('display_discount_price', SwitchType::class, [
                'label' => $this->trans('Display discounted price', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('In the volume discounts board, display the new price with the applied discount instead of showing the discount (ie. "-5%").', 'Admin.Shopparameters.Help'),
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
