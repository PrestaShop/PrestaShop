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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use PrestaShopBundle\Form\Admin\Type as PsFormType;

/**
 * This form class is responsible to generate the product combination form
 */
class ProductCombination extends CommonAbstractType
{
    private $translator;
    private $contextLegacy;
    private $configuration;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $legacyContext
     */
    public function __construct($translator, $legacyContext)
    {
        $this->translator = $translator;
        $this->contextLegacy = $legacyContext->getContext();
        $this->configuration = $this->getConfiguration();
        $this->currency = $this->contextLegacy->currency;
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $is_stock_management = $this->configuration->get('PS_STOCK_MANAGEMENT');

        $builder->add('id_product_attribute', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
            'required' => false,
        ))
        ->add('attribute_reference', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'label' => $this->translator->trans('Reference', array(), 'Admin.Global')
        ))
        ->add('attribute_ean13', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'error_bubbling' => true,
            'label' => $this->translator->trans('EAN-13 or JAN barcode', array(), 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,13}$/"),
            )
        ))
        ->add('attribute_isbn', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'label' => $this->translator->trans('ISBN code', array(), 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\Regex("/^[0-9-]{0,32}$/"),
            ),
        ))
        ->add('attribute_upc', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'label' => $this->translator->trans('UPC barcode', array(), 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,12}$/"),
            )
        ))
        ->add('attribute_wholesale_price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Cost price', array(), 'Admin.Catalog.Feature'),
            'currency' => $this->currency->iso_code,
            'attr' => ['class' => 'attribute_wholesale_price']
        ))
        ->add('attribute_price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Impact on price (tax excl.)', array(), 'Admin.Catalog.Feature'),
            'currency' => $this->currency->iso_code,
            'attr' => ['class' => 'attribute_priceTE']
        ))
        ->add('attribute_priceTI', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'mapped' => false,
            'label' => $this->translator->trans('Impact on price (tax incl.)', array(), 'Admin.Catalog.Feature'),
            'currency' => $this->currency->iso_code,
            'attr' => ['class' => 'attribute_priceTI']
        ))
        ->add('attribute_ecotax', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Ecotax', array(), 'Admin.Catalog.Feature'),
            'currency' => $this->currency->iso_code,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('attribute_weight', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'required' => false,
            'label' => $this->translator->trans('Impact on weight', array(), 'Admin.Catalog.Feature')
        ))
        ->add('attribute_unity', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Impact on price per unit (tax excl.)', array(), 'Admin.Catalog.Feature'),
            'currency' => $this->currency->iso_code,
            'attr' => ['class' => 'attribute_unity'],
        ))
        ->add('attribute_minimal_quantity', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'required' => false,
            'label' => $this->translator->trans('Min. quantity for sale', array(), 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric')),
            )
        ))
        ->add('attribute_low_stock_threshold', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'label' => $this->translator->trans('Low stock level', array(), 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\Type(array('type' => 'numeric')),
            ),
            'attr' => array(
                'placeholder' => $this->translator->trans(
                    'Leave empty to disable',
                    array(),
                    'Admin.Catalog.Help'
                ),
            ),
        ))
        ->add('attribute_low_stock_alert', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
            'label' => $this->translator->trans('Send me an email when the quantity is below or equals this level', array(), 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\Type(array('type' => 'bool')),
            ),
        ))
        ->add('available_date_attribute', 'PrestaShopBundle\Form\Admin\Type\DatePickerType', array(
            'required' => false,
            'label' => $this->translator->trans('Availability date', array(), 'Admin.Catalog.Feature'),
            'attr' => ['class' => 'date', 'placeholder' => 'YYYY-MM-DD']
        ))
        ->add('attribute_default', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
            'label'    => $this->translator->trans('Set as default combination', array(), 'Admin.Catalog.Feature'),
            'required' => false,
            'attr' => array('class' => 'attribute_default_checkbox'),
        ));
        if ($is_stock_management) {
            $builder->add(
                'attribute_quantity',
                'Symfony\Component\Form\Extension\Core\Type\NumberType',
                array(
                    'required'    => true,
                    'label'       => $this->translator->trans('Quantity', array(), 'Admin.Catalog.Feature'),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Type(array('type' => 'numeric')),
                    )
                )
            );
        }
        $builder->add('id_image_attr', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices'  => array(),
            'required' => false,
            'expanded' => true,
            'multiple' => true,
            'label' => $this->translator->trans('Select images of this combination:', array(), 'Admin.Catalog.Feature'),
            'attr' => array('class' => 'images'),
        ))
        ->add('final_price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Final price', array(), 'Admin.Catalog.Feature'),
            'currency' => $this->currency->iso_code,
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $choices = [];
            if (!empty($data['id_image_attr'])) {
                foreach ($data['id_image_attr'] as $id) {
                    $choices[$id] = $id;
                }
            }

            $form->add('id_image_attr', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' => $choices,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ));
        });
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'product_combination';
    }
}
