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

namespace Tests\Integration\PrestaShopBundle\Form;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\Admin\Type\UnavailableType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is not used in the project but in the tests, it allows to build a simple
 * form type for product listener and use it in test.
 *
 * @see ProductTypeListener
 * @see ProductTypeListenerTest
 */
class TestProductFormType extends CommonAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stock', FormType::class)
            ->add('shipping', FormType::class)
            ->add('options', FormType::class)
            ->add('pricing', FormType::class)
            ->add('combinations', FormType::class)
            ->add('extra_modules', FormType::class)
        ;

        $stockForm = $builder->get('stock');
        $stockForm->add('packed_products', FormType::class);
        $stockForm->add('pack_stock_type', ChoiceType::class);
        $stockForm->add('virtual_product_file', FormType::class);
        $stockForm->add('quantities', FormType::class);

        $quantities = $stockForm->get('quantities');
        $quantities->add('stock_movements', FormType::class);

        $optionsForm = $builder->get('options');

        $optionsForm->add('suppliers', FormType::class);
        $suppliersForm = $optionsForm->get('suppliers');
        $suppliersForm->add('supplier_ids', ChoiceType::class, [
            'choices' => $options['suppliers'],
        ]);
        $optionsForm->add('product_suppliers', ChoiceType::class);

        $pricingForm = $builder->get('pricing');
        $pricingForm->add('retail_price', FormType::class);

        $retailPricingForm = $pricingForm->get('retail_price');
        $retailPricingForm->add('ecotax_tax_excluded', UnavailableType::class);
        $retailPricingForm->add('ecotax_tax_included', UnavailableType::class);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'suppliers' => [],
            ])
            ->setAllowedTypes('suppliers', 'array')
        ;
    }
}
