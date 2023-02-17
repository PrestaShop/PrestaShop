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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShopBundle\Form\Admin\Type\AccordionType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * For combination update in bulk action
 */
class BulkCombinationType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stock', BulkCombinationStockType::class)
            ->add('price', BulkCombinationPriceType::class, [
                'product_id' => $options['product_id'],
                'country_id' => $options['country_id'],
                'shop_id' => $options['shop_id'],
            ])
            ->add('references', BulkCombinationReferencesType::class)
            ->add('images', BulkCombinationImagesType::class, [
                'label' => $this->trans('Images', 'Admin.Global'),
                'product_id' => $options['product_id'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => false,
                'label_subtitle' => $this->trans('You can bulk edit the selected combinations by enabling and filling each field that needs to be updated.', 'Admin.Catalog.Feature'),
                'expand_first' => false,
                'display_one' => false,
                'required' => false,
                'attr' => [
                    'class' => 'bulk-combination-form',
                ],
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/combination.html.twig',
            ])
            ->setRequired([
                'product_id',
                'country_id',
                'shop_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setAllowedTypes('country_id', 'int')
            ->setAllowedTypes('shop_id', 'int')
        ;
    }

    public function getParent()
    {
        return AccordionType::class;
    }
}
