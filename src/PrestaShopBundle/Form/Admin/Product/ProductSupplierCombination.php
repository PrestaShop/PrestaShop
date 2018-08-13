<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class is responsible to generate the basic product suppliers form
 */
class ProductSupplierCombination extends CommonAbstractType
{
    private $translator;
    private $contextLegacy;
    private $currencyAdapter;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $contextLegacy
     * @param object $currencyAdapter
     */
    public function __construct($translator, $contextLegacy, $currencyAdapter)
    {
        $this->translator = $translator;
        $this->contextLegacy = $contextLegacy->getContext();
        $this->currencyAdapter = $currencyAdapter;
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'supplier_reference',
            FormType\TextType::class,
            [
                'required' => false,
                'label' => null,
                'empty_data' => '',
            ]
        )
            ->add(
                'product_price',
                FormType\MoneyType::class,
                [
                    'required' => false,
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Type(['type' => 'float'])
                    ]
                ]
            )
            ->add(
                'product_price_currency',
                FormType\ChoiceType::class,
                [
                    'choices' => $this->formatDataChoicesList($this->currencyAdapter->getCurrencies(), 'id_currency'),
                    'required' => true,
                    'attr' => [
                        'class' => 'custom-select',
                    ],
                ]
            )
            ->add('id_product_attribute', FormType\HiddenType::class)
            ->add('product_id', FormType\HiddenType::class)
            ->add('supplier_id', FormType\HiddenType::class);

        //set default minimal values for collection prototype
        $builder->setData([
            'product_price' => 0,
            'supplier_id' => $options['id_supplier'],
            'product_price_currency' => $this->contextLegacy->currency->id,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'id_supplier' => null,
        ]);
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'product_supplier_combination';
    }
}
