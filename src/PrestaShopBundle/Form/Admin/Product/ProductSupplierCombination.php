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
        $builder->add('supplier_reference', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'label' => null
        ))
        ->add('product_price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('product_price_currency', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices'  => $this->formatDataChoicesList($this->currencyAdapter->getCurrencies(), 'id_currency'),
            'choices_as_values' => true,
            'required' => true,
            'attr' => array(
                'class' => 'custom-select',
            ),
        ))
        ->add('id_product_attribute', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
        ->add('product_id', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
        ->add('supplier_id', 'Symfony\Component\Form\Extension\Core\Type\HiddenType');

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
        $resolver->setDefaults(array(
            'id_supplier' => null,
        ));
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
