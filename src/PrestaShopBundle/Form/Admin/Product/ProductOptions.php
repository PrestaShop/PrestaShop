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

/**
 * This form class is risponsible to generate the product options form
 */
class ProductOptions extends AbstractType
{
    private $translator;
    private $suppliers;

    /**
     * Constructor
     *
     * @param object $container The SF2 container
     */
    public function __construct($container)
    {
        $this->translator = $container->get('prestashop.adapter.translator');
        $this->suppliers = $this->formatDataChoicesList(
            $container->get('prestashop.adapter.data_provider.supplier')->getSuppliers(),
            'id_supplier'
        );
    }

    /**
     * Format legacy data list to mapping SF2 form filed choice
     *
     * @param array $list
     * @param string $mapping_value
     * @param string $mapping_name
     * @return array
     */
    private function formatDataChoicesList($list, $mapping_value = 'id', $mapping_name = 'name')
    {
        $new_list = array();
        foreach ($list as $item) {
            $new_list[$item[$mapping_value]] = $item[$mapping_name];
        }
        return $new_list;
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //TODO
        //If product is NOT active, add redirections form

        $builder->add('visibility', 'choice', array(
            'choices'  => array(
                'both' => $this->translator->trans('Everywhere', [], 'AdminProducts'),
                'catalog' => $this->translator->trans('Catalog only', [], 'AdminProducts'),
                'search' => $this->translator->trans('Search only', [], 'AdminProducts'),
                'none' => $this->translator->trans('Nowhere', [], 'AdminProducts'),
            ),
            'required' => true,
            'label' => $this->translator->trans('Visibility', [], 'AdminProducts'),
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
        ))
        ->add('suppliers', 'choice', array(
            'choices' =>  $this->suppliers,
            'expanded' =>  true,
            'multiple' =>  true,
            'required' =>  false,
            'label' => $this->translator->trans('Suppliers', [], 'AdminProducts')
        ))
        ->add('default_supplier', 'choice', array(
            'choices' =>  $this->suppliers,
            'required' =>  true,
            'label' => $this->translator->trans('Default suppliers', [], 'AdminProducts')
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'product_options';
    }
}
