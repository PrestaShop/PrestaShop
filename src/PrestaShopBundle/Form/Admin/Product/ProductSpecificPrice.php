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
use PrestaShopBundle\Form\Admin\Type\TypeaheadCustomerCollectionType;
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
            $builder->add('sp_id_shop', FormType\HiddenType::class, array(
                'required' =>  false,
            ));
        } else {
            $builder->add('sp_id_shop', FormType\ChoiceType::class, array(
                'choices' =>  $this->shops,
                'required' =>  false,
                'label' =>  false,
                'placeholder' => $this->translator->trans('All shops', [], 'AdminProducts'),
                'attr' => [
                    'class' => count($this->shops) >= 1 ? 'hide2' : ''
                ]
            ));
        }

        $builder->add('sp_id_currency', FormType\ChoiceType::class, array(
            'choices' =>  $this->currencies,
            'required' =>  false,
            'label' =>  false,
            'placeholder' =>  $this->translator->trans('All currencies', [], 'AdminProducts'),
        ))
        ->add('sp_id_country', FormType\ChoiceType::class, array(
            'choices' =>  $this->countries,
            'required' =>  false,
            'label' =>  false,
            'placeholder' => $this->translator->trans('All countries', [], 'AdminProducts'),
        ))
        ->add('sp_id_group', FormType\ChoiceType::class, array(
            'choices' =>  $this->groups,
            'required' =>  false,
            'label' =>  false,
            'placeholder' => $this->translator->trans('All groups', [], 'AdminProducts'),
        ))
        ->add('sp_id_customer', new TypeaheadCustomerCollectionType(
            $this->context->getAdminLink('AdminCustomers', true).'&sf2=1&ajax=1&tab=AdminCustomers&action=searchCustomers&customer_search=%QUERY',
            'id_customer',
            'fullname_and_email',
            $this->translator->trans('All customers', [], 'AdminProducts'),
            '<div class="title col-xs-10">%s</div><button type="button" class="btn btn-default delete"><i class="icon-trash"></i></button>',
            $this->customerDataprovider,
            1
        ), array(
            'required' => false,
            'label' => $this->translator->trans('Add product in your pack', [], 'AdminProducts'),
        ))
        ->add('sp_id_product_attribute', FormType\ChoiceType::class, array(
            'choices' =>  [],
            'required' =>  false,
            'placeholder' => $this->translator->trans('Apply to all combinations', [], 'AdminProducts'),
            'label' => $this->translator->trans('Combination:s', [], 'AdminProducts'),
            'attr' => ['data-action' =>  $this->router->generate('admin_get_product_combinations')],
        ))
        ->add('sp_from', PsFormType\DatePickerType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Available from', [], 'AdminProducts'),
            'attr' => ['placeholder' => 'YYYY-MM-DD HH:II']
        ))
        ->add('sp_to', PsFormType\DatePickerType::class, array(
            'required' => false,
            'label' => $this->translator->trans('to', [], 'AdminProducts'),
            'attr' => ['placeholder' => 'YYYY-MM-DD HH:II']
        ))
        ->add('sp_from_quantity', FormType\NumberType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Starting at', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric')),
            )
        ))
        ->add('sp_price', FormType\MoneyType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Product price', [], 'AdminProducts'),
            'attr' => ['class' => 'price'],
            'currency' => $this->currency->iso_code,
        ))
        ->add('leave_bprice', FormType\CheckboxType::class, array(
            'label'    => $this->translator->trans('Leave base price:', [], 'AdminProducts'),
            'required' => false,
        ))
        ->add('sp_reduction', FormType\MoneyType::class, array(
            'label' => $this->translator->trans('Reduction', [], 'AdminProducts'),
            'required' => false,
            'currency' => $this->currency->iso_code,
        ))
        ->add('sp_reduction_type', FormType\ChoiceType::class, array(
            'label' => $this->translator->trans('Reduction type', [], 'AdminProducts'),
            'choices'  => array(
                'amount' => '€',
                'percentage' => $this->translator->trans('%', [], 'AdminProducts'),
            ),
            'required' => true,
        ))
        ->add('sp_reduction_tax', FormType\ChoiceType::class, array(
            'label' => $this->translator->trans('Reduction tax', [], 'AdminProducts'),
            'choices'  => array(
                '0' => $this->translator->trans('Tax excluded', [], 'AdminProducts'),
                '1' => $this->translator->trans('Tax included', [], 'AdminProducts'),
            ),
            'required' => true,
        ))
        ->add('save', FormType\ButtonType::class, array(
            'label' => $this->translator->trans('Save', [], 'AdminProducts'),
            'attr' => array('class' => 'js-save'),
        ))
        ->add('cancel', FormType\ButtonType::class, array(
            'label' => $this->translator->trans('Cancel', [], 'AdminProducts'),
            'attr' => array('class' => 'js-cancel'),
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (empty($data['sp_id_product_attribute'])) {
                return;
            }

            //bypass SF validation, define submitted value in choice list
            $form->add('sp_id_product_attribute', FormType\ChoiceType::class, array(
                'choices' =>  [$data['sp_id_product_attribute'] => ''],
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
