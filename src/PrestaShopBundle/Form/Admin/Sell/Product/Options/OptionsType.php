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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Options;

use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class is responsible to generate the product options form.
 */
class OptionsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_new', DatePickerType::class, [
                'required' => false,
                'label' => $this->trans('Product as New from', 'Admin.Catalog.Feature'),
                'label_subtitle' => $this->trans('The date allows you to specify when the product is considered as new. It is used by modules to showcase new products from your catalog or to arrange products by the most recent ones. By default, the date is set to the product creation date for the new date.', 'Admin.Catalog.Feature'),
                'modify_all_shops' => true,
                'attr' => ['placeholder' => 'YYYY-MM-DD'],
                'label_tag_name' => 'h3',
            ])
            ->add('visibility', VisibilityType::class)
            ->add('suppliers', SuppliersType::class)
            ->add('product_suppliers', ProductSupplierCollectionType::class)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
            'label' => $this->trans('Options', 'Admin.Catalog.Feature'),
            // Suppliers can be removed so there might be extra data in the request during type switching
            'allow_extra_fields' => true,
        ]);
    }
}
