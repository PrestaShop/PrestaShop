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

use PrestaShopBundle\Form\Admin\Sell\Product\Shortcut\PriceShortcutType;
use PrestaShopBundle\Form\Admin\Sell\Product\Shortcut\StockShortcutType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
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
            ->add('stock', StockShortcutType::class, [
                'label' => $this->trans('Quantity', 'Admin.Catalog.Feature'),
                'help' => $this->trans('How many products should be available for sale?', 'Admin.Catalog.Help'),
                'target_tab' => 'stock-tab',
                'target_tab_name' => $this->trans('Quantity', 'Admin.Catalog.Feature'),
            ])
            ->add('price', PriceShortcutType::class, [
                'label' => $this->trans('Retail price', 'Admin.Catalog.Feature'),
                'help' => $this->trans('This is the retail price at which you intend to sell this product to your customers. The tax included price will change according to the tax rule you select.', 'Admin.Catalog.Help'),
                'target_tab' => 'pricing-tab',
                'target_tab_name' => $this->trans('Pricing', 'Admin.Catalog.Feature'),
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        // We must allow extra fields because when we switch product type some former fields may be present in request
        $resolver->setDefaults([
            'allow_extra_fields' => true,
        ]);
    }
}
