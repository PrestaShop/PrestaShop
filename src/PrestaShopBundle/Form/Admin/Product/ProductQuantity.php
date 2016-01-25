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
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Product\ProductVirtual;
use PrestaShopBundle\Form\Admin\Type as PsFormType;
use Symfony\Component\Form\Extension\Core\Type as FormType;

/**
 * This form class is responsible to generate the product quantity form
 */
class ProductQuantity extends CommonAbstractType
{
    private $router;
    private $translator;
    private $configuration;

    /**
     * Constructor
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
        $builder->add('attributes', FormType\TextType::class, array(
            'attr' =>  [
                'class' => 'tokenfield',
                'data-limit' => 20,
                'data-minLength' => 1,
                'placeholder' => $this->translator->trans('Type something...', [], 'AdminProducts'),
                'data-prefetch' => $this->router->generate('admin_attribute_get_all'),
                'data-action' => $this->router->generate('admin_attribute_generator'),
            ],
            'label' =>  $this->translator->trans('Create combinations', [], 'AdminProducts')
        ))
            ->add('advanced_stock_management', FormType\CheckboxType::class, array(
                'required' => false,
                'label' => $this->translator->trans('I want to use the advanced stock management system for this product.', [], 'AdminProducts'),
            ))
            ->add('pack_stock_type', FormType\ChoiceType::class) //see eventListener for details
            ->add('depends_on_stock', FormType\ChoiceType::class, array(
                'choices'  => array(
                    1 => $this->translator->trans('The available quantities for the current product and its combinations are based on the stock in your warehouse (using the advanced stock management system). ', [], 'AdminProducts'),
                    0 => $this->translator->trans('I want to specify available quantities manually.', [], 'AdminProducts'),
                ),
                'expanded' => true,
                'required' => true,
                'multiple' => false,
            ))
            ->add('qty_0', FormType\NumberType::class, array(
                'required' => true,
                'label' => $this->translator->trans('Quantity', [], 'AdminProducts'),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Type(array('type' => 'numeric')),
                ),
            ))
            ->add('combinations', FormType\CollectionType::class, array(
                'entry_type' => new ProductCombination(
                    $this->translator,
                    $this->legacyContext
                ),
                'allow_add' => true,
                'allow_delete' => true
            ))
            ->add('out_of_stock', FormType\ChoiceType::class) //see eventListener for details
            ->add('minimal_quantity', FormType\NumberType::class, array(
                'required' => true,
                'label' => $this->translator->trans('Minimum quantity', [], 'AdminProducts'),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Type(array('type' => 'numeric')),
                ),
            ))
            ->add('available_now', new TranslateType(FormType\TextType::class, array(), $this->locales, true), array(
                'label' =>  $this->translator->trans('Displayed text when in-stock', [], 'AdminProducts')
            ))
            ->add('available_later', new TranslateType(FormType\TextType::class, array(), $this->locales, true), array(
                'label' =>  $this->translator->trans('Displayed text when backordering is allowed', [], 'AdminProducts')
            ))
            ->add('available_date', PsFormType\DatePickerType::class, array(
                'required' => false,
                'label' => $this->translator->trans('Availability date:', [], 'AdminProducts'),
                'attr' => ['placeholder' => 'YYYY-MM-DD']
            ))
            ->add('virtual_product', new ProductVirtual($this->translator, $this->legacyContext), array(
                'required' => false,
                'label' => $this->translator->trans('Does this product have an associated file?', [], 'AdminProducts'),
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            //Manage out_of_stock field with contextual values/label
            $defaultChoiceLabel = $this->translator->trans('Default', [], 'AdminProducts').' (';
            $defaultChoiceLabel .= $this->configuration->get('PS_ORDER_OUT_OF_STOCK') == 1 ?
                $this->translator->trans('Allow orders', [], 'AdminProducts') :
                $this->translator->trans('Deny orders', [], 'AdminProducts');
            $defaultChoiceLabel .= ')';

            $form->add('out_of_stock', FormType\ChoiceType::class, array(
                'choices'  => array(
                    '0' => $this->translator->trans('Deny orders', [], 'AdminProducts'),
                    '1' => $this->translator->trans('Allow orders', [], 'AdminProducts'),
                    '2' => $defaultChoiceLabel,
                ),
                'expanded' => true,
                'required' => false,
                'placeholder' => false,
                'label' => $this->translator->trans('When out of stock', [], 'AdminProducts')
            ));

            //Manage out_of_stock field with contextual values/label
            $pack_stock_type = $this->configuration->get('PS_PACK_STOCK_TYPE');
            $defaultChoiceLabel = $this->translator->trans('Default', [], 'AdminProducts').': ';
            if ($pack_stock_type == 0) {
                $defaultChoiceLabel .= $this->translator->trans('Decrement pack only.', [], 'AdminProducts');
            } elseif ($pack_stock_type == 1) {
                $defaultChoiceLabel .= $this->translator->trans('Decrement products in pack only.', [], 'AdminProducts');
            } else {
                $defaultChoiceLabel .= $this->translator->trans('Decrement both.', [], 'AdminProducts');
            }

            $form->add('pack_stock_type', FormType\ChoiceType::class, array(
                'choices'  => array(
                    '0' => $this->translator->trans('Decrement pack only.', [], 'AdminProducts'),
                    '1' => $this->translator->trans('Decrement products in pack only.', [], 'AdminProducts'),
                    '2' => $this->translator->trans('Decrement both.', [], 'AdminProducts'),
                    '3' => $defaultChoiceLabel,
                ),
                'expanded' => false,
                'required' => true,
                'placeholder' => false,
                'label' => $this->translator->trans('Pack quantities', [], 'AdminProducts')
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
        return 'product_quantity';
    }
}
