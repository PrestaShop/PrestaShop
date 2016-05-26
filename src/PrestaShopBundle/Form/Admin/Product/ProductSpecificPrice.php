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
use PrestaShopBundle\Form\Admin\Type as PsFormType;
use Symfony\Component\Form\Extension\Core\Type as FormType;

/**
 * This form class is responsible to generate the basic product specific prices form
 */
class ProductSpecificPrice extends CommonAbstractType
{
    private $translator;
    private $locales;
    private $shops;
    private $countries;
    private $currencies;
    private $groups;
    private $customerDataprovider;

    /**
     * Constructor
     *
     * @param object $router
     * @param object $translator
     * @param object $shopContextAdapter
     * @param object $countryDataprovider
     * @param object $currencyDataprovider
     * @param object $groupDataprovider
     * @param object $legacyContext
     */
    public function __construct($router, $translator, $shopContextAdapter, $countryDataprovider, $currencyDataprovider, $groupDataprovider, $legacyContext, $customerDataprovider)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->context = $legacyContext;
        $this->locales = $legacyContext->getLanguages();
        $this->shops = $this->formatDataChoicesList($shopContextAdapter->getShops(), 'id_shop');
        $this->countries = $this->formatDataChoicesList($countryDataprovider->getCountries($this->locales[0]['id_lang']), 'id_country');
        $this->currencies = $this->formatDataChoicesList($currencyDataprovider->getCurrencies(), 'id_currency');
        $this->groups = $this->formatDataChoicesList($groupDataprovider->getGroups($this->locales[0]['id_lang']), 'id_group');
        $this->currency = $legacyContext->getContext()->currency;
        $this->customerDataprovider = $customerDataprovider;
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //If context multi-shop, hide shop selector
        //Else show selector
        if (count($this->shops) == 1) {
            $builder->add('sp_id_shop', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                'required' =>  false,
            ));
        } else {
            $builder->add('sp_id_shop', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' =>  $this->shops,
                'choices_as_values' => true,
                'required' =>  false,
                'label' =>  false,
                'placeholder' => $this->translator->trans('All shops', [], 'AdminProducts'),
            ));
        }

        $builder->add('sp_id_currency', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices' =>  $this->currencies,
            'choices_as_values' => true,
            'required' =>  false,
            'label' =>  false,
            'placeholder' =>  $this->translator->trans('All currencies', [], 'AdminProducts'),
        ))
        ->add('sp_id_country', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices' =>  $this->countries,
            'choices_as_values' => true,
            'required' =>  false,
            'label' =>  false,
            'placeholder' => $this->translator->trans('All countries', [], 'AdminProducts'),
        ))
        ->add('sp_id_group', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices' =>  $this->groups,
            'choices_as_values' => true,
            'required' =>  false,
            'label' =>  false,
            'placeholder' => $this->translator->trans('All groups', [], 'AdminProducts'),
        ))
        ->add('sp_id_customer', 'PrestaShopBundle\Form\Admin\Type\TypeaheadCustomerCollectionType', array(
            'remote_url' => $this->context->getAdminLink('AdminCustomers', true).'&sf2=1&ajax=1&tab=AdminCustomers&action=searchCustomers&customer_search=%QUERY',
            'mapping_value' => 'id_customer',
            'mapping_name' => 'fullname_and_email',
            'placeholder' => $this->translator->trans('All customers', [], 'AdminProducts'),
            'template_collection' => '<div class="title col-md-10">%s</div><button type="button" class="btn btn-danger delete"><i class="material-icons">delete</i></button>',
            'limit' => 1,
            'required' => false,
            'label' => $this->translator->trans('Add customer', [], 'AdminProducts'),
        ))
        ->add('sp_id_product_attribute', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices' =>  [],
            'choices_as_values' => true,
            'required' =>  false,
            'placeholder' => $this->translator->trans('Apply to all combinations', [], 'AdminProducts'),
            'label' => $this->translator->trans('Combination', [], 'AdminProducts'),
            'attr' => ['data-action' =>  $this->router->generate('admin_get_product_combinations')],
        ))
        ->add('sp_from', 'PrestaShopBundle\Form\Admin\Type\DatePickerType', array(
            'required' => false,
            'label' => $this->translator->trans('Available from', [], 'AdminProducts'),
            'attr' => ['placeholder' => 'YYYY-MM-DD HH:II']
        ))
        ->add('sp_to', 'PrestaShopBundle\Form\Admin\Type\DatePickerType', array(
            'required' => false,
            'label' => $this->translator->trans('to', [], 'AdminProducts'),
            'attr' => ['placeholder' => 'YYYY-MM-DD HH:II']
        ))
        ->add('sp_from_quantity', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'required' => false,
            'label' => $this->translator->trans('Starting at', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric')),
            )
        ))
        ->add('sp_price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Product price (tax excl.)', [], 'AdminProducts'),
            'attr' => ['class' => 'price'],
            'currency' => $this->currency->iso_code,
        ))
        ->add('leave_bprice', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
            'label'    => $this->translator->trans('Leave base price:', [], 'AdminProducts'),
            'required' => false,
        ))
        ->add('sp_reduction', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'label' => $this->translator->trans('Reduction', [], 'AdminProducts'),
            'required' => false,
            'currency' => $this->currency->iso_code,
        ))
        ->add('sp_reduction_type', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'label' => $this->translator->trans('Reduction type', [], 'AdminProducts'),
            'choices'  => array(
                '€' => 'amount',
                 $this->translator->trans('%', [], 'AdminProducts') => 'percentage',
            ),
            'choices_as_values' => true,
            'required' => true,
        ))
        ->add('sp_reduction_tax', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'label' => $this->translator->trans('Reduction tax', [], 'AdminProducts'),
            'choices'  => array(
                $this->translator->trans('Tax excluded', [], 'AdminProducts') => '0',
                $this->translator->trans('Tax included', [], 'AdminProducts') => '1',
            ),
            'choices_as_values' => true,
            'required' => true,
        ))
        ->add('save', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
            'label' => $this->translator->trans('Apply', [], 'AdminProducts'),
            'attr' => array('class' => 'btn-primary-outline js-save'),
        ))
        ->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
            'label' => $this->translator->trans('Cancel', [], 'AdminProducts'),
            'attr' => array('class' => 'btn-default-outline js-cancel'),
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (empty($data['sp_id_product_attribute'])) {
                return;
            }

            //bypass SF validation, define submitted value in choice list
            $form->add('sp_id_product_attribute', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' =>  [$data['sp_id_product_attribute'] => ''],
                'choices_as_values' => true,
                'required' =>  false,
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
