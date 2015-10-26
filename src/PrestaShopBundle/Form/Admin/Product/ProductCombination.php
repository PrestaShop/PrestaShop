<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This form class is risponsible to generate the basic product informations form
 */
class ProductCombination extends AbstractType
{
    private $translator;
    private $container;

    /**
     * Constructor
     *
     * @param object $container The SF2 container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->translator = $container->get('prestashop.adapter.translator');
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id_product_attribute', 'hidden', array(
            'required' => false,
        ))
        ->add('attribute_reference', 'text', array(
            'required' => false,
            'label' => $this->translator->trans('Reference code', [], 'AdminProducts')
        ))
        ->add('attribute_ean13', 'text', array(
            'required' => false,
            'error_bubbling' => true,
            'label' => $this->translator->trans('EAN-13 or JAN barcode', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,13}$/"),
            )
        ))
        ->add('attribute_isbn', 'text', array(
            'required' => false,
            'label' => $this->translator->trans('ISBN code', [], 'AdminProducts')
        ))
        ->add('attribute_upc', 'text', array(
            'required' => false,
            'label' => $this->translator->trans('UPC barcode', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,12}$/"),
            )
        ))
        ->add('attribute_wholesale_price', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Pre-tax wholesale price', [], 'AdminProducts')
        ))
        ->add('attribute_price_impact', 'choice', array(
            'choices'  => array(
                '0' => $this->translator->trans('None', [], 'AdminProducts'),
                '1' => $this->translator->trans('Increase', [], 'AdminProducts'),
                '-1' => $this->translator->trans('Decrease', [], 'AdminProducts'),
            ),
            'required' => true,
            'label' => $this->translator->trans('Impact on price', [], 'AdminProducts'),
        ))
        ->add('attribute_price', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('(tax excl.)', [], 'AdminProducts')
        ))
        ->add('attribute_priceTI', 'number', array(
            'required' => false,
            'mapped' => false,
            'label' => $this->translator->trans('(tax incl.)', [], 'AdminProducts')
        ))
        ->add('attribute_weight_impact', 'choice', array(
            'choices'  => array(
                '0' => $this->translator->trans('None', [], 'AdminProducts'),
                '1' => $this->translator->trans('Increase', [], 'AdminProducts'),
                '-1' => $this->translator->trans('Decrease', [], 'AdminProducts'),
            ),
            'required' => true,
            'label' => $this->translator->trans('Impact on weight', [], 'AdminProducts'),
        ))
        ->add('attribute_weight', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Kg', [], 'AdminProducts')
        ))
        ->add('attribute_unit_impact', 'choice', array(
            'choices'  => array(
                '0' => $this->translator->trans('None', [], 'AdminProducts'),
                '1' => $this->translator->trans('Increase', [], 'AdminProducts'),
                '-1' => $this->translator->trans('Decrease', [], 'AdminProducts'),
            ),
            'required' => true,
            'label' => $this->translator->trans('Impact on weight', [], 'AdminProducts'),
        ))
        ->add('attribute_unity', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('€/', [], 'AdminProducts')
        ))
        ->add('attribute_minimal_quantity', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Minimum quantity', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric')),
            )
        ))
        ->add('available_date_attribute', 'text', array(
            'required' => false,
            'label' => $this->translator->trans('Availability date', [], 'AdminProducts'),
            'attr' => ['class' => 'date', 'placeholder' => 'YYY-MM-DD']
        ))
        ->add('attribute_default', 'checkbox', array(
            'label'    => $this->translator->trans('Make this combination the default combination for this product.', [], 'AdminProducts'),
            'required' => false,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'product_combination';
    }
}
