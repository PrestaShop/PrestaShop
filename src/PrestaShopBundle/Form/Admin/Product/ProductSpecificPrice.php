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
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\TypeaheadCustomerCollectionType;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This form class is responsible to generate the basic product specific prices form.
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
     * Constructor.
     *
     * @param object $router
     * @param object $translator
     * @param object $shopContextAdapter
     * @param object $countryDataprovider
     * @param object $currencyDataprovider
     * @param object $groupDataprovider
     * @param object $legacyContext
     */
    public function __construct(
        $router,
        $translator,
        $shopContextAdapter,
        $countryDataprovider,
        $currencyDataprovider,
        $groupDataprovider,
        $legacyContext,
        $customerDataprovider
    ) {
        $this->router = $router;
        $this->translator = $translator;
        $this->context = $legacyContext;
        $this->locales = $legacyContext->getLanguages();
        $this->shops = $this->formatDataChoicesList($shopContextAdapter->getShops(), 'id_shop');
        $this->countries = $this->formatDataChoicesList(
            $countryDataprovider->getCountries($this->locales[0]['id_lang']),
            'id_country'
        );
        $this->currencies = $this->formatDataChoicesList($currencyDataprovider->getCurrencies(), 'id_currency');
        $this->groups = $this->formatDataChoicesList(
            $groupDataprovider->getGroups($this->locales[0]['id_lang']),
            'id_group'
        );
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
            $builder->add(
                'sp_id_shop',
                FormType\HiddenType::class,
                array(
                    'required' => false,
                )
            );
        } else {
            $builder->add(
                'sp_id_shop',
                FormType\ChoiceType::class,
                array(
                    'choices' => $this->shops,
                    'required' => false,
                    'label' => false,
                    'placeholder' => $this->translator->trans('All shops', array(), 'Admin.Global'),
                )
            );
        }

        $builder->add(
            'sp_id_currency',
            FormType\ChoiceType::class,
            array(
                'choices' => $this->currencies,
                'required' => false,
                'label' => false,
                'attr' => array(
                    'data-toggle' => 'select2',
                    'data-minimumResultsForSearch' => '7',
                ),
                'placeholder' => $this->translator->trans('All currencies', array(), 'Admin.Global'),
            )
        )
        ->add(
            'sp_id_country',
            FormType\ChoiceType::class,
            array(
                'choices' => $this->countries,
                'required' => false,
                'label' => false,
                'attr' => array(
                    'data-toggle' => 'select2',
                    'data-minimumResultsForSearch' => '7',
                ),
                'placeholder' => $this->translator->trans('All countries', array(), 'Admin.Global'),
            )
        )
        ->add(
            'sp_id_group',
            FormType\ChoiceType::class,
            array(
                'choices' => $this->groups,
                'required' => false,
                'label' => false,
                'attr' => array(
                    'data-toggle' => 'select2',
                    'data-minimumResultsForSearch' => '7',
                ),
                'placeholder' => $this->translator->trans('All groups', array(), 'Admin.Global'),
            )
        )
        ->add(
            'sp_id_customer',
            TypeaheadCustomerCollectionType::class,
            array(
                'remote_url' => $this->context->getAdminLink('AdminCustomers', true).'&sf2=1&ajax=1&tab=AdminCustomers&action=searchCustomers&customer_search=%QUERY',
                'mapping_value' => 'id_customer',
                'mapping_name' => 'fullname_and_email',
                'placeholder' => $this->translator->trans('All customers', array(), 'Admin.Global'),
                'template_collection' => '<div class="media-body"><div class="label">%s</div><i class="material-icons delete">clear</i></div>',
                'limit' => 1,
                'required' => false,
                'label' => $this->translator->trans('Add customer', array(), 'Admin.Catalog.Feature'),
            )
        )
        ->add(
            'sp_id_product_attribute',
            FormType\ChoiceType::class,
            array(
                'choices' => array(),
                'required' => false,
                'placeholder' => $this->translator->trans('Apply to all combinations', array(), 'Admin.Catalog.Feature'),
                'label' => $this->translator->trans('Combinations', array(), 'Admin.Catalog.Feature'),
                'attr' => array('data-action' => $this->router->generate('admin_get_product_combinations', ['idProduct' => 1])),
            )
        )
        ->add(
            'sp_from',
            DatePickerType::class,
            array(
                'required' => false,
                'label' => $this->translator->trans('Available from', array(), 'Admin.Catalog.Feature'),
                'attr' => array('placeholder' => 'YYYY-MM-DD'),
            )
        )
        ->add(
            'sp_to',
            DatePickerType::class,
            array(
                'required' => false,
                'label' => $this->translator->trans('to', array(), 'Admin.Global'),
                'attr' => array('placeholder' => 'YYYY-MM-DD'),
            )
        )
        ->add(
            'sp_from_quantity',
            FormType\NumberType::class,
            array(
                'required' => false,
                'label' => $this->translator->trans('Starting at', array(), 'Admin.Catalog.Feature'),
                'constraints' => array(
                    new Assert\Type(array('type' => 'numeric')),
                ),
            )
        )
        ->add(
            'sp_price',
            FormType\MoneyType::class,
            array(
                'required' => false,
                'label' => $this->translator->trans('Product price (tax excl.)', array(), 'Admin.Catalog.Feature'),
                'attr' => array('class' => 'price'),
                'currency' => $this->currency->iso_code,
                'disabled' => true,
            )
        )
        ->add(
            'leave_bprice',
            FormType\CheckboxType::class,
            array(
                'label' => $this->translator->trans('Leave initial price', array(), 'Admin.Catalog.Feature'),
                'required' => false,
            )
        )
        ->add(
            'sp_reduction',
            FormType\MoneyType::class,
            array(
                'label' => $this->translator->trans('Reduction', array(), 'Admin.Catalog.Feature'),
                'required' => false,
                'currency' => $this->currency->iso_code,
            )
        )
        ->add(
            'sp_reduction_type',
            FormType\ChoiceType::class,
            array(
                'label' => $this->translator->trans('Reduction type', array(), 'Admin.Catalog.Feature'),
                'choices' => array(
                    '€' => 'amount',
                    $this->translator->trans('%', array(), 'Admin.Global') => 'percentage',
                ),
                'required' => true,
            )
        )
        ->add(
            'sp_reduction_tax',
            FormType\ChoiceType::class,
            array(
                'label' => $this->translator->trans('Reduction tax', array(), 'Admin.Catalog.Feature'),
                'choices' => array(
                    $this->translator->trans('Tax excluded', array(), 'Admin.Catalog.Feature') => '0',
                    $this->translator->trans('Tax included', array(), 'Admin.Catalog.Feature') => '1',
                ),
                'required' => true,
            )
        )
        ->add(
            'save',
            FormType\ButtonType::class,
            array(
                'label' => $this->translator->trans('Apply', array(), 'Admin.Actions'),
                'attr' => array('class' => 'btn-outline-primary js-save'),
            )
        )
        ->add(
            'cancel',
            FormType\ButtonType::class,
            array(
                'label' => $this->translator->trans('Cancel', array(), 'Admin.Actions'),
                'attr' => array('class' => 'btn-outline-secondary js-cancel'),
            )
        );
        //
        // ResetType can't be used because the product page is wrapped
        // inside a big form: reset a specific price form the "right" way
        // will reset the global form.
        //

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (empty($data['sp_id_product_attribute'])) {
                return;
            }

            $form = $event->getForm();

            //bypass SF validation, define submitted value in choice list
            $form->add(
                'sp_id_product_attribute',
                FormType\ChoiceType::class,
                array(
                    'choices' => array($data['sp_id_product_attribute'] => ''),
                    'required' => false,
                )
            );
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
