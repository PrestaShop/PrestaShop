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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Shortcut;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\UnavailableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShortcutsType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', UnavailableType::class, [
                'label' => $this->trans('Reference', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'label_help_box' => $this->trans('Your reference code for this product. Allowed special characters: .-_#.', 'Admin.Catalog.Help'),
            ])
            ->add('stock', StockShortcutType::class, [
                'label' => $this->trans('Quantity', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'label_help_box' => $this->trans('How many products should be available for sale?', 'Admin.Catalog.Help'),
                'required' => false,
                'external_link' => [
                    'text' => $this->trans('Advanced settings in [1]%settings_label%[/1]', 'Admin.Catalog.Feature', ['%settings_label%' => $this->trans('Stock', 'Admin.Catalog.Feature')]),
                    'href' => '#stock-tab',
                    'align' => 'right',
                    'attr' => [
                        'class' => 'tab-link',
                    ],
                ],
            ])
            ->add('retail_price', PriceShortcutType::class, [
                'label' => $this->trans('Retail price', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'label_help_box' => $this->trans('This is the retail price at which you intend to sell this product to your customers. The tax included price will change according to the tax rule you select.', 'Admin.Catalog.Help'),
                'required' => false,
                'external_link' => [
                    'text' => $this->trans('Advanced settings in [1]%settings_label%[/1]', 'Admin.Catalog.Feature', ['%settings_label%' => $this->trans('Pricing', 'Admin.Catalog.Feature')]),
                    'href' => '#pricing-tab',
                    'align' => 'right',
                    'attr' => [
                        'class' => 'tab-link',
                    ],
                ],
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
            // This avoids an empty label column
            'label' => false,
            // Stock can be removed so there might be extra data in the request during type switching
            'allow_extra_fields' => true,
        ]);
    }
}
