<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Options;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class SuppliersType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $supplierNameByIdChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $supplierNameByIdChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $supplierNameByIdChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->supplierNameByIdChoiceProvider = $supplierNameByIdChoiceProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $suppliers = $this->supplierNameByIdChoiceProvider->getChoices();

        $builder
            ->add('supplier_ids', ChoiceType::class, [
                'choices' => $suppliers,
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                // placeholder false is important to avoid empty option in select input despite required being false
                'placeholder' => false,
                'choice_attr' => function ($choice, $name) {
                    return ['data-label' => $name];
                },
                'label' => $this->trans('Choose the suppliers associated with this product', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h4',
            ])
            ->add('default_supplier_id', ChoiceType::class, [
                'choices' => $suppliers,
                'expanded' => true,
                'required' => false,
                // placeholder false is important to avoid empty option in select input despite required being false
                'placeholder' => false,
                'label' => $this->trans('Default supplier', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h4',
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
            'label' => $this->trans('Suppliers', 'Admin.Global'),
            'label_tag_name' => 'h3',
            'columns_number' => 2,
            'row_attr' => [
                'class' => 'product-suppliers-block',
            ],
            'alert_position' => 'prepend',
        ]);
    }
}
