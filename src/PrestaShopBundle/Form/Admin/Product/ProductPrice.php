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
use PrestaShopBundle\Form\Admin\Product\ProductSpecificPrice;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class is responsible to generate the product price form
 */
class ProductPrice extends CommonAbstractType
{
    private $translator;
    private $tax_rules;
    private $tax_rules_rates;
    private $configuration;
    private $eco_tax_rate;
    private $customerDataprovider;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $taxDataProvider
     * @param object $router
     * @param object $shopContextAdapter
     * @param object $countryDataprovider
     * @param object $currencyDataprovider
     * @param object $groupDataprovider
     * @param object $legacyContext
     * @param object $customerDataprovider
     */
    public function __construct($translator, $taxDataProvider, $router, $shopContextAdapter, $countryDataprovider, $currencyDataprovider, $groupDataprovider, $legacyContext, $customerDataprovider)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->configuration = $this->getConfiguration();
        $this->shopContextAdapter = $shopContextAdapter;
        $this->countryDataprovider = $countryDataprovider;
        $this->currencyDataprovider = $currencyDataprovider;
        $this->groupDataprovider = $groupDataprovider;
        $this->customerDataprovider = $customerDataprovider;
        $this->legacyContext = $legacyContext;
        $this->tax_rules_rates = $taxDataProvider->getTaxRulesGroupWithRates();
        $this->eco_tax_rate = $taxDataProvider->getProductEcotaxRate();
        $this->currency = $legacyContext->getContext()->currency;
        $this->tax_rules = $this->formatDataChoicesList(
            $taxDataProvider->getTaxRulesGroups(true),
            'id_tax_rules_group'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->tax_rules = array_merge(
            [$this->translator->trans('No tax', [], 'Admin.Catalog.Feature') => 0],
            $this->tax_rules
        );
        $builder->add(
            'price',
            FormType\MoneyType::class,
            [
                'required' => false,
                'label' => $this->translator->trans('Price (tax excl.)', [], 'Admin.Catalog.Feature'),
                'attr' => ['data-display-price-precision' => self::PRESTASHOP_DECIMALS],
                'currency' => $this->currency->iso_code,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'float'])
                ]
            ]
        )
            ->add(
                'price_ttc',
                FormType\MoneyType::class,
                [
                    'required' => false,
                    'mapped' => false,
                    'label' => $this->translator->trans('Price (tax incl.)', [], 'Admin.Catalog.Feature'),
                    'currency' => $this->currency->iso_code,
                ]
            )
            ->add(
                'ecotax',
                FormType\MoneyType::class,
                [
                    'required' => false,
                    'label' => $this->translator->trans('Ecotax (tax incl.)', [], 'Admin.Catalog.Feature'),
                    'currency' => $this->currency->iso_code,
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Type(['type' => 'float'])
                    ],
                    'attr' => ['data-eco-tax-rate' => $this->eco_tax_rate],
                ]
            )
            ->add(
                'id_tax_rules_group',
                FormType\ChoiceType::class,
                [
                    'choices' => $this->tax_rules,
                    'required' => true,
                    'choice_attr' => function ($val) {
                        return [
                            'data-rates' => implode(',', $this->tax_rules_rates[$val]['rates']),
                            'data-computation-method' => $this->tax_rules_rates[$val]['computation_method'],
                        ];
                    },
                    'attr' => [
                        'data-toggle' => 'select2',
                        'data-minimumResultsForSearch' => '7',
                    ],
                    'label' => $this->translator->trans('Tax rule', [], 'Admin.Catalog.Feature'),
                ]
            )
            ->add(
                'on_sale',
                FormType\CheckboxType::class,
                [
                    'required' => false,
                    'label' => $this->translator->trans(
                        'Display the "On sale!" flag on the product page, and on product listings.',
                        [],
                        'Admin.Catalog.Feature'
                    ),
                ]
            )
            ->add(
                'wholesale_price',
                FormType\MoneyType::class,
                [
                    'required' => false,
                    'label' => $this->translator->trans('Price (tax excl.)', [], 'Admin.Catalog.Feature'),
                    'currency' => $this->currency->iso_code,
                ]
            )
            ->add(
                'unit_price',
                FormType\MoneyType::class,
                [
                    'required' => false,
                    'label' => $this->translator->trans('Price per unit (tax excl.)', [], 'Admin.Catalog.Feature'),
                    'currency' => $this->currency->iso_code,
                ]
            )
            ->add(
                'unity',
                FormType\TextType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => $this->translator->trans('Per kilo, per litre', [], 'Admin.Catalog.Help')]
                ]
            )
            ->add('specific_price',
                ProductSpecificPrice::class,
                [
                    'id_product' => $options['id_product'],
                ]
            )
            ->add(
                'specificPricePriorityToAll',
                FormType\CheckboxType::class,
                [
                    'required' => false,
                    'label' => $this->translator->trans('Apply to all products', [], 'Admin.Catalog.Feature'),
                ]
            );

        //generates fields for price priority
        $specificPricePriorityChoices = [
            $this->translator->trans('Shop', [], 'Admin.Global') => 'id_shop',
            $this->translator->trans('Currency', [], 'Admin.Global') => 'id_currency',
            $this->translator->trans('Country', [], 'Admin.Global') => 'id_country',
            $this->translator->trans('Group', [], 'Admin.Global') => 'id_group',
        ];


        for ($i=0, $iMax = count($specificPricePriorityChoices); $i < $iMax; $i++) {
            $builder->add(
                'specificPricePriority_' . $i,
                FormType\ChoiceType::class,
                [
                    'choices' => $specificPricePriorityChoices,
                    'required' => true
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'id_product' => 1,
        ]);
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'product_price';
    }
}
