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
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class generates "Products stock" form
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class StockType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('allow_ordering_oos', SwitchType::class, [
                'label' => $this->trans('Allow ordering of out-of-stock products', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'By default, the "%add_to_cart_label%" button is hidden when a product is unavailable. You can choose to have it displayed in all cases.',
                    'Admin.Shopparameters.Help',
                    [
                        '%add_to_cart_label%' => $this->trans('Add to cart', 'Shop.Theme.Actions'),
                    ]
                ),
            ])
            ->add('stock_management', SwitchType::class, [
                'label' => $this->trans('Enable stock management', 'Admin.Shopparameters.Feature'),
            ])
            ->add('in_stock_label', TranslatableType::class, [
                'label' => $this->trans('Label of in-stock products', 'Admin.Shopparameters.Feature'),
                'type' => TextType::class,
                'only_enabled_locales' => false,
            ])
            ->add('oos_allowed_backorders', TranslatableType::class, [
                'label' => $this->trans('Label of out-of-stock products with allowed backorders', 'Admin.Shopparameters.Feature'),
                'type' => TextType::class,
                'only_enabled_locales' => false,
            ])
            ->add('oos_denied_backorders', TranslatableType::class, [
                'label' => $this->trans('Label of out-of-stock products with denied backorders', 'Admin.Shopparameters.Feature'),
                'type' => TextType::class,
                'only_enabled_locales' => false,
            ])
            ->add('delivery_time', TranslatableType::class, [
                'label' => $this->trans('Delivery time of in-stock products', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                        'Advised for European merchants to be legally compliant (eg: Delivered within 3-4 days)',
                        'Admin.Shopparameters.Help'
                    ) . '</br>' . $this->trans('Leave empty to disable.', 'Admin.Shopparameters.Feature'),                'type' => TextType::class,
                'only_enabled_locales' => false,
            ])
            ->add('oos_delivery_time', TranslatableType::class, [
                'label' => $this->trans('Delivery time of out-of-stock products with allowed backorders', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Advised for European merchants to be legally compliant (eg: Delivered within 5-7 days)', 'Admin.Shopparameters.Help'),
                'type' => TextType::class,
                'only_enabled_locales' => false,
            ])
            ->add('pack_stock_management', ChoiceType::class, [
                'label' => $this->trans('Default pack stock management', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('When selling packs of products, how do you want your stock to be calculated?', 'Admin.Shopparameters.Help'),
                'choices' => [
                    'Decrement pack only.' => 0,
                    'Decrement products in pack only.' => 1,
                    'Decrement both.' => 2,
                ],
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
        return 'product_preferences_stock_block';
    }
}
