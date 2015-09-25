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

namespace PrestaShop\PrestaShop\Core\Business\Product\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

/**
 * This form class is risponsible to generate the product options form
 */
class ProductOptions extends AbstractType
{
    private $router;
    private $context;
    private $translator;

    /**
     * Constructor
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->router = $container->make('Routing');
        $this->context = $container->make('Context');
        $this->translator = $container->make('Translator');
        $this->suppliers = $this->formatDataChoicesList(
            $container->make('CoreAdapter:Supplier\\SupplierDataProvider')->getSuppliers(),
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
                'both' => 'Partout',
                'catalog' => 'Catalogue uniquement',
                'search' => 'Recherche uniquement',
                'none' => 'Nulle part',
            ),
            'required' => true,
            'data' => 'both'
        ))
        ->add('wholesale_price', 'number', array(
            'required' => false
        ))
        ->add('unit_price', 'number', array(
            'required' => false
        ))
        ->add('unity', 'text', array(
            'required' => false
        ))
        ->add('suppliers', 'choice', array(
            'choices' =>  $this->suppliers,
            'expanded' =>  true,
            'multiple' =>  true,
            'required' =>  false,
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
