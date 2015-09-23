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
use PrestaShop\PrestaShop\Core\Foundation\Form\Type\TranslateType;
use PrestaShop\PrestaShop\Core\Foundation\Form\Type\DropFilesType;
use PrestaShop\PrestaShop\Core\Foundation\Form\Type\ChoiceCategorysTreeType;

/**
 * This form class is risponsible to generate the step1 product form
 */
class ProductStep1 extends AbstractType
{
    private $router;
    private $context;
    private $translator;
    private $tax_rules;
    private $manufacturers;
    private $suppliers;

    /**
     * Constructor
     *
     * @param \Core_Foundation_IoC_Container $container
     */
    public function __construct(\Core_Foundation_IoC_Container $container)
    {
        $this->router = $container->make('Routing');
        $this->context = $container->make('Context');
        $this->translator = $container->make('Translator');
        $this->tax_rules = $this->formatDataChoicesList(\TaxRulesGroup::getTaxRulesGroups(true), 'id_tax_rules_group');
        $this->manufacturers = $this->formatDataChoicesList(\Manufacturer::getManufacturers(false, 0, true, false, false, false, true), 'id_manufacturer');
        $this->suppliers = $this->formatDataChoicesList(\Supplier::getSuppliers(), 'id_supplier');
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
        $builder->add('type_product', 'choice', array(
            'choices'  => array(
                0 => 'Produit standard',
                1 => 'Pack de produits existants',
                2 => 'Produit dématérialisé (services, réservations, produits téléchargeables, etc.)',
            ),
            'required' => true,
            'data' => 'both'
        ))
        ->add('name', new TranslateType(
            'text',
            array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 3))
                )
            )
        ))
        ->add('description', 'textarea', array(
            'attr' => array('class' => 'autoload_rte')
        ))
        ->add('images', new DropFilesType('Images', $this->router->generateUrl('admin_tools_upload'), array(
            'maxFiles' => '10',
            'dictRemoveFile' => 'Supprimer'
        )))
        ->add('upc', 'text')
        ->add('ean13', 'text')
        ->add('reference', 'text')
        ->add('condition', 'choice', array(
            'choices'  => array(
                'new' => 'Nouveau',
                'used' => 'Utilisé',
                'refurbished' => 'Reconditionné',
            ),
            'required' => true,
            'data' => 'new'
        ))
        ->add('wholesale_price', 'number')
        ->add('price', 'number')
        ->add('id_tax_rules_group', 'choice', array(
            'choices' =>  $this->tax_rules,
            'required' => true,
        ))
        ->add('unit_price', 'number')
        ->add('unity', 'text')
        ->add('on_sale', 'checkbox')

        //RIGHT COL
        ->add('active', 'choice', array(
            'choices'  => array( 1 => 'Oui', 0 => 'Non'),
            'expanded' => true,
            'required' => true,
            'multiple' => false,
            'data' => 0
        ))
        ->add('visibility', 'choice', array(
            'choices'  => array(
                'both' => 'Partout',
                'catalog' => 'Catalogue uniquement',
                'search' => 'Recherche uniquement',
                'none' => 'Nulle part',
            ),
            'required' => true,
            'data' => 'both'
        ))
        ->add(
            $builder->create('options', 'form')
                ->add('available_for_order', 'checkbox', array(
                    'label'    => 'Disponible à la vente',
                    'required' => false,
                ))
                ->add('show_price', 'checkbox', array(
                    'label'    => 'Afficher le prix',
                    'required' => false,
                ))
                ->add('online_only', 'checkbox', array(
                    'label'    => 'Exclusivité web (non vendu en magasin)',
                    'required' => false,
                ))
        )
        ->add('categorys', new ChoiceCategorysTreeType('Catégories', \Category::getNestedCategories()))
        ->add('id_manufacturer', 'choice', array(
            'choices' =>  $this->manufacturers
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
        return 'product_step1';
    }
}
