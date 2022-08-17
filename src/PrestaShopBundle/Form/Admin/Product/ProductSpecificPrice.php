<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Product;

use Currency;
use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Adapter\Customer\CustomerDataProvider;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\TypeaheadCustomerCollectionType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This form class is responsible to generate the basic product specific prices form.
 */
class ProductSpecificPrice extends CommonAbstractType
{
    /**
     * @var LegacyContext
     */
    public $context;
    /**
     * @var array
     */
    private $countries;
    /**
     * @var array
     */
    private $currencies;
    /**
     * @var Currency
     */
    public $currency;
    /**
     * @var CustomerDataProvider
     */
    public $customerDataProvider;
    /**
     * @var array
     */
    private $groups;
    /**
     * @var array<int|array>
     */
    public $locales;
    /**
     * @var Router
     */
    public $router;
    /**
     * @var array
     */
    public $shops;
    /**
     * @var TranslatorInterface
     */
    public $translator;

    /**
     * Constructor.
     *
     * @param Router $router
     * @param TranslatorInterface $translator
     * @param Context $shopContextAdapter
     * @param CountryDataProvider $countryDataprovider
     * @param CurrencyDataProviderInterface $currencyDataprovider
     * @param GroupDataProvider $groupDataprovider
     * @param LegacyContext $legacyContext
     * @param CustomerDataProvider $customerDataProvider
     */
    public function __construct(
        $router,
        $translator,
        $shopContextAdapter,
        $countryDataprovider,
        $currencyDataprovider,
        $groupDataprovider,
        $legacyContext,
        $customerDataProvider
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
        $this->currencies = $this->formatDataChoicesList(
            $currencyDataprovider->getCurrencies(),
            'id_currency'
        );
        $this->groups = $this->formatDataChoicesList(
            $groupDataprovider->getGroups($this->locales[0]['id_lang']),
            'id_group'
        );
        $this->currency = $legacyContext->getContext()->currency;
        $this->customerDataProvider = $customerDataProvider;
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
                [
                    'required' => false,
                ]
            );
        } else {
            $builder->add(
                'sp_id_shop',
                FormType\ChoiceType::class,
                [
                    'choices' => $this->shops,
                    'required' => false,
                    'label' => false,
                    'placeholder' => $this->translator->trans('All shops', [], 'Admin.Global'),
                ]
            );
        }

        $builder->add(
            'sp_id_currency',
            FormType\ChoiceType::class,
            [
                'choices' => $this->currencies,
                'required' => false,
                'label' => false,
                'attr' => [
                    'data-toggle' => 'select2',
                    'data-minimumResultsForSearch' => '7',
                ],
                'placeholder' => $this->translator->trans('All currencies', [], 'Admin.Global'),
            ]
        )
            ->add(
                'sp_id_country',
                FormType\ChoiceType::class,
                [
                    'choices' => $this->countries,
                    'required' => false,
                    'label' => false,
                    'attr' => [
                        'data-toggle' => 'select2',
                        'data-minimumResultsForSearch' => '7',
                    ],
                    'placeholder' => $this->translator->trans('All countries', [], 'Admin.Global'),
                ]
            )
            ->add(
                'sp_id_group',
                FormType\ChoiceType::class,
                [
                    'choices' => $this->groups,
                    'required' => false,
                    'label' => false,
                    'attr' => [
                        'data-toggle' => 'select2',
                        'data-minimumResultsForSearch' => '7',
                    ],
                    'placeholder' => $this->translator->trans('All groups', [], 'Admin.Global'),
                ]
            )
            ->add(
                'sp_id_customer',
                TypeaheadCustomerCollectionType::class,
                [
                    // "%QUERY" is appended to url in order to avoid "%" sign being encoded into "%25",
                    // it used as a placeholder to replace with actual query in JS
                    'remote_url' => $this->router->generate('admin_customers_search', ['sf2' => 1]) . '&customer_search=%QUERY',
                    'mapping_value' => 'id_customer',
                    'mapping_name' => 'fullname_and_email',
                    'placeholder' => $this->translator->trans('All customers', [], 'Admin.Global'),
                    'template_collection' => '<div class="media-body"><div class="label">%s</div><i class="material-icons delete">clear</i></div>',
                    'limit' => 1,
                    'required' => false,
                    'label' => $this->translator->trans('Add customer', [], 'Admin.Catalog.Feature'),
                ]
            )
            ->add(
                'sp_id_product_attribute',
                FormType\ChoiceType::class,
                [
                    'choices' => [],
                    'required' => false,
                    'placeholder' => $this->translator->trans('Apply to all combinations', [], 'Admin.Catalog.Feature'),
                    'label' => $this->translator->trans('Combinations', [], 'Admin.Catalog.Feature'),
                    'attr' => [
                        'data-action' => $this->router->generate('admin_get_product_combinations', ['idProduct' => $options['id_product']]),
                        // used to force selected select option after options have been loaded
                        'data-selected-attribute' => (array_keys($options, 'selected_product_attribute')) ? $options['selected_product_attribute'] : '0',
                    ],
                ]
            )
            ->add(
                'sp_from',
                DatePickerType::class,
                [
                    'required' => false,
                    'label' => $this->translator->trans('Available from', [], 'Admin.Catalog.Feature'),
                    'attr' => ['placeholder' => 'YYYY-MM-DD'],
                ]
            )
            ->add(
                'sp_to',
                DatePickerType::class,
                [
                    'required' => false,
                    'label' => $this->translator->trans('to', [], 'Admin.Global'),
                    'attr' => ['placeholder' => 'YYYY-MM-DD'],
                ]
            )
            ->add(
                'sp_from_quantity',
                FormType\NumberType::class,
                [
                    'required' => false,
                    'label' => $this->translator->trans('Starting at', [], 'Admin.Catalog.Feature'),
                    'constraints' => [
                        new Assert\Type(['type' => 'numeric']),
                    ],
                    'unit' => $this->translator->trans('Unit(s)', [], 'Admin.Catalog.Feature'),
                ]
            )
            ->add(
                'sp_price',
                FormType\MoneyType::class,
                [
                    'required' => false,
                    'label' => $this->translator->trans('Product price (tax excl.)', [], 'Admin.Catalog.Feature'),
                    'attr' => ['class' => 'price'],
                    'currency' => $this->currency->iso_code,
                    'disabled' => true,
                ]
            )
            ->add(
                'leave_bprice',
                FormType\CheckboxType::class,
                [
                    'label' => $this->translator->trans('Leave initial price', [], 'Admin.Catalog.Feature'),
                    'required' => false,
                ]
            )
            ->add(
                'sp_reduction',
                FormType\NumberType::class,
                [
                    'label' => $this->translator->trans('Reduction', [], 'Admin.Catalog.Feature'),
                    'required' => false,
                ]
            )
            ->add(
                'sp_reduction_type',
                FormType\ChoiceType::class,
                [
                    'label' => $this->translator->trans('Reduction type', [], 'Admin.Catalog.Feature'),
                    'choices' => [
                        $this->currency->getSign() => 'amount',
                        $this->translator->trans('%', [], 'Admin.Global') => 'percentage',
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'sp_reduction_tax',
                FormType\ChoiceType::class,
                [
                    'label' => $this->translator->trans('Reduction tax', [], 'Admin.Catalog.Feature'),
                    'choices' => [
                        $this->translator->trans('Tax excluded', [], 'Admin.Catalog.Feature') => '0',
                        $this->translator->trans('Tax included', [], 'Admin.Catalog.Feature') => '1',
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'save',
                FormType\ButtonType::class,
                [
                    'label' => $this->translator->trans('Apply', [], 'Admin.Actions'),
                    'attr' => ['class' => 'btn-outline-primary js-save'],
                ]
            )
            ->add(
                'cancel',
                FormType\ButtonType::class,
                [
                    'label' => $this->translator->trans('Cancel', [], 'Admin.Actions'),
                    'attr' => ['class' => 'btn-outline-secondary js-cancel'],
                ]
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
                [
                    'choices' => [$data['sp_id_product_attribute'] => ''],
                    'required' => false,
                ]
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'id_product' => 1, // 1 is default value for new form
            'selected_product_attribute' => '0', // used to force selected select option after options have been loaded
        ]);
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
