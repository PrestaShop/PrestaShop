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

use PrestaShopBundle\Form\Admin\Type\CommonModelAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This form class is responsible to generate the product price form
 */
class ProductPrice extends CommonModelAbstractType
{
    private $translator;
    private $tax_rules;

    /**
     * Constructor
     *
     * @param object $container The SF2 container
     */
    public function __construct($container)
    {
        $this->translator = $container->get('prestashop.adapter.translator');
        $this->tax_rules = $this->formatDataChoicesList(
            $container->get('prestashop.adapter.data_provider.tax')->getTaxRulesGroups(true),
            'id_tax_rules_group'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('price', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Pre-tax retail price', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('id_tax_rules_group', 'choice', array(
            'choices' =>  $this->tax_rules,
            'required' => true,
            'label' => $this->translator->trans('Tax rule:', [], 'AdminProducts'),
        ))
        ->add('price_ttc', 'number', array(
            'required' => false,
            'mapped' => false,
            'label' => $this->translator->trans('Retail price with tax', [], 'AdminProducts'),
        ))
        ->add('on_sale', 'checkbox', array(
            'required' => false,
            'label' => $this->translator->trans('On sale', [], 'AdminProducts'),
        ))
        ->add('wholesale_price', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Pre-tax wholesale price', [], 'AdminProducts')
        ))
        ->add('unit_price', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Unit price (tax excl.)', [], 'AdminProducts')
        ))
        ->add('unity', 'text', array(
            'required' => false,
            'label' => $this->translator->trans('per', [], 'AdminProducts')
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'product_price';
    }
}
