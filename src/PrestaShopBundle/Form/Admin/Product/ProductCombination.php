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
        $builder->add('id_product_attribute', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
            'required' => false,
        ))
        ->add('attribute_reference', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'label' => $this->translator->trans('Reference', [], 'AdminProducts')
        ))
        ->add('attribute_ean13', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'error_bubbling' => true,
            'label' => $this->translator->trans('EAN-13 or JAN barcode', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,13}$/"),
            )
        ))
        ->add('attribute_isbn', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'label' => $this->translator->trans('ISBN code', [], 'AdminProducts')
        ))
        ->add('attribute_upc', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'label' => $this->translator->trans('UPC barcode', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,12}$/"),
            )
        ))
        ->add('attribute_wholesale_price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Cost price', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
        ))
        ->add('attribute_price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Impact on price (tax excl.)', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
            'attr' => ['class' => 'attribute_priceTE']
        ))
        ->add('attribute_priceTI', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'mapped' => false,
            'label' => $this->translator->trans('Impact on price (tax incl.)', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
            'attr' => ['class' => 'attribute_priceTI']
        ))
        ->add('attribute_ecotax', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Ecotax', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('attribute_weight', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'required' => false,
            'label' => $this->translator->trans('Impact on weight', [], 'AdminProducts')
        ))
        ->add('attribute_unity', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Impact on unit price', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
        ))
        ->add('attribute_minimal_quantity', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'required' => false,
            'label' => $this->translator->trans('Minimum quantity', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric')),
            )
        ))
        ->add('available_date_attribute', 'PrestaShopBundle\Form\Admin\Type\DatePickerType', array(
            'required' => false,
            'label' => $this->translator->trans('Availability date', [], 'AdminProducts'),
            'attr' => ['class' => 'date', 'placeholder' => 'YYYY-MM-DD']
        ))
        ->add('attribute_default', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
            'label'    => $this->translator->trans('Set as default combination', [], 'AdminProducts'),
            'required' => false,
        ))
        ->add('attribute_quantity', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'required' => true,
            'label' => $this->translator->trans('Quantity', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric')),
            )
        ))
        ->add('id_image_attr', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices'  => array(),
            'choices_as_values' => true,
            'required' => false,
            'expanded' => true,
            'multiple' => true,
            'label' => $this->translator->trans('Select images of this combination:', [], 'AdminProducts'),
            'attr' => array('class' => 'images'),
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
                'choices_as_values' => true,
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
