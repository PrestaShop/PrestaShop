<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
