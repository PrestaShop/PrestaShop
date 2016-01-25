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
        $builder->add('id_product_attribute', FormType\HiddenType::class, array(
            'required' => false,
        ))
        ->add('attribute_reference', FormType\TextType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Reference code', [], 'AdminProducts')
        ))
        ->add('attribute_ean13', FormType\TextType::class, array(
            'required' => false,
            'error_bubbling' => true,
            'label' => $this->translator->trans('EAN-13 or JAN barcode', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,13}$/"),
            )
        ))
        ->add('attribute_isbn', FormType\TextType::class, array(
            'required' => false,
            'label' => $this->translator->trans('ISBN code', [], 'AdminProducts')
        ))
        ->add('attribute_upc', FormType\TextType::class, array(
            'required' => false,
            'label' => $this->translator->trans('UPC barcode', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,12}$/"),
            )
        ))
        ->add('attribute_wholesale_price', FormType\MoneyType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Pre-tax wholesale price', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
        ))
        ->add('attribute_price', FormType\MoneyType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Impact on price (tax excl.)', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
            'attr' => ['class' => 'attribute_priceTE']
        ))
        ->add('attribute_priceTI', FormType\MoneyType::class, array(
            'required' => false,
            'mapped' => false,
            'label' => $this->translator->trans('Impact on price (tax incl.)', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
            'attr' => ['class' => 'attribute_priceTI']
        ))
        ->add('attribute_ecotax', FormType\MoneyType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Ecotax (tax incl.)', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('attribute_weight', FormType\NumberType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Impact on weight', [], 'AdminProducts')
        ))
        ->add('attribute_unity', FormType\MoneyType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Impact on unit price', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
        ))
        ->add('attribute_minimal_quantity', FormType\NumberType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Minimum quantity', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric')),
            )
        ))
        ->add('available_date_attribute', PsFormType\DatePickerType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Availability date', [], 'AdminProducts'),
            'attr' => ['class' => 'date', 'placeholder' => 'YYYY-MM-DD']
        ))
        ->add('attribute_default', FormType\CheckboxType::class, array(
            'label'    => $this->translator->trans('Make this combination the default combination for this product.', [], 'AdminProducts'),
            'required' => false,
        ))
        ->add('attribute_quantity', FormType\NumberType::class, array(
            'required' => true,
            'label' => $this->translator->trans('Quantity', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric')),
            )
        ))
        ->add('id_image_attr', FormType\ChoiceType::class, array(
            'choices'  => array(),
            'required' => false,
            'expanded' => true,
            'multiple' => true,
            'label' => $this->translator->trans('Images of this combination', [], 'AdminProducts'),
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

            $form->add('id_image_attr', FormType\ChoiceType::class, array(
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
