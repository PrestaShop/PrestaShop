<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class is responsible to generate the product quantity form.
 */
class ProductQuantity extends CommonAbstractType
{
    private $router;
    private $translator;
    private $configuration;

    /**
     * Constructor.
     *
     * @param object $translator
     * @param object $router
     * @param object $legacyContext
     */
    public function __construct($translator, $router, $legacyContext)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->legacyContext = $legacyContext;
        $this->locales = $this->legacyContext->getLanguages();
        $this->configuration = $this->getConfiguration();
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $is_stock_management = $this->configuration->get('PS_STOCK_MANAGEMENT');
        $builder->add('attributes', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'attr' => array(
                'class' => 'tokenfield',
                'data-minLength' => 1,
                'placeholder' => $this->translator->trans('Combine several attributes, e.g.: "Size: all", "Color: red".', array(), 'Admin.Catalog.Help'),
                'data-prefetch' => $this->router->generate('admin_attribute_get_all'),
                'data-action' => $this->router->generate('admin_attribute_generator'),
            ),
            'label' => $this->translator->trans('Create combinations', array(), 'Admin.Catalog.Feature'),
            ))
            ->add('advanced_stock_management', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => $this->translator->trans('I want to use the advanced stock management system for this product.', array(), 'Admin.Catalog.Feature'),
            ))
            ->add('pack_stock_type', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array('choices_as_values' => true)) //see eventListener for details
            ->add('depends_on_stock', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' => array(
                    $this->translator->trans('The available quantities for the current product and its combinations are based on the stock in your warehouse (using the advanced stock management system). ', array(), 'Admin.Catalog.Feature') => 1,
                    $this->translator->trans('I want to specify available quantities manually.', array(), 'Admin.Catalog.Feature') => 0,
                ),
                'choices_as_values' => true,
                'expanded' => true,
                'required' => true,
                'multiple' => false,
            ));
            if($is_stock_management){
                $builder->add('qty_0', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                    'required' => true,
                    'label' => $this->translator->trans('Quantity', array(), 'Admin.Catalog.Feature'),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Type(array('type' => 'numeric')),
                    ),
                ));
            }
            $builder->add('out_of_stock', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices_as_values' => true,
            ))
            ->add('minimal_quantity', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'required' => true,
                'label' => $this->translator->trans('Minimum quantity for sale', array(), 'Admin.Catalog.Feature'),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Type(array('type' => 'numeric')),
                ),
            ))
            ->add('available_now', 'PrestaShopBundle\Form\Admin\Type\TranslateType', array(
                'type' => 'Symfony\Component\Form\Extension\Core\Type\TextType',
                'options' => array(),
                'locales' => $this->locales,
                'hideTabs' => true,
                'label' => $this->translator->trans('Label when in stock', array(), 'Admin.Catalog.Feature'),
            ))
            ->add('available_later', 'PrestaShopBundle\Form\Admin\Type\TranslateType', array(
                'type' => 'Symfony\Component\Form\Extension\Core\Type\TextType',
                'options' => array(),
                'locales' => $this->locales,
                'hideTabs' => true,
                'label' => $this->translator->trans('Label when out of stock (and back order allowed)', array(), 'Admin.Catalog.Feature'),
            ))
            ->add('available_date', 'PrestaShopBundle\Form\Admin\Type\DatePickerType', array(
                'required' => false,
                'label' => $this->translator->trans('Availability date', array(), 'Admin.Catalog.Feature'),
                'attr' => array('placeholder' => 'YYYY-MM-DD'),
            ))
            ->add('virtual_product', 'PrestaShopBundle\Form\Admin\Product\ProductVirtual', array(
                'required' => false,
                'label' => $this->translator->trans('Does this product have an associated file?', array(), 'Admin.Catalog.Feature'),
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            //Manage out_of_stock field with contextual values/label
            $defaultChoiceLabel = $this->translator->trans('Use default behavior', array(), 'Admin.Catalog.Feature').' (';
            $defaultChoiceLabel .= $this->configuration->get('PS_ORDER_OUT_OF_STOCK') == 1 ?
                $this->translator->trans('Allow orders', array(), 'Admin.Catalog.Feature') :
                $this->translator->trans('Deny orders', array(), 'Admin.Catalog.Feature');
            $defaultChoiceLabel .= ')';

            $form->add('out_of_stock', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' => array(
                    $this->translator->trans('Deny orders', array(), 'Admin.Catalog.Feature') => '0',
                    $this->translator->trans('Allow orders', array(), 'Admin.Catalog.Feature') => '1',
                    $defaultChoiceLabel => '2',
                ),
                'choices_as_values' => true,
                'expanded' => true,
                'required' => false,
                'placeholder' => false,
                'label' => $this->translator->trans('When out of stock', array(), 'Admin.Catalog.Feature'),
            ));

            //Manage out_of_stock field with contextual values/label
            $pack_stock_type = $this->configuration->get('PS_PACK_STOCK_TYPE');
            $defaultChoiceLabel = $this->translator->trans('Default', array(), 'Admin.Global').': ';
            if ($pack_stock_type == 0) {
                $defaultChoiceLabel .= $this->translator->trans('Decrement pack only.', array(), 'Admin.Catalog.Feature');
            } elseif ($pack_stock_type == 1) {
                $defaultChoiceLabel .= $this->translator->trans('Decrement products in pack only.', array(), 'Admin.Catalog.Feature');
            } else {
                $defaultChoiceLabel .= $this->translator->trans('Decrement both.', array(), 'Admin.Catalog.Feature');
            }

            $form->add('pack_stock_type', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' => array(
                    $this->translator->trans('Decrement pack only.', array(), 'Admin.Catalog.Feature') => '0',
                    $this->translator->trans('Decrement products in pack only.', array(), 'Admin.Catalog.Feature') => '1',
                    $this->translator->trans('Decrement both.', array(), 'Admin.Catalog.Feature') => '2',
                    $defaultChoiceLabel => '3',
                ),
                'choices_as_values' => true,
                'expanded' => false,
                'required' => true,
                'placeholder' => false,
                'label' => $this->translator->trans('Pack quantities', array(), 'Admin.Catalog.Feature'),
            ));
        });
    }

    /**
     * {@inheritdoc}
     *
     * Configure options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
        ));
    }
    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'product_quantity';
    }
}
