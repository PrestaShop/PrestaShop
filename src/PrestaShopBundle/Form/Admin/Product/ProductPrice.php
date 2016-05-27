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
use Symfony\Component\Form\Extension\Core\Type as FormType;

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
        $builder->add('price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Price - Tax excluded', [], 'AdminProducts'),
            'attr' => ['data-display-price-precision' => $this->configuration->get('_PS_PRICE_DISPLAY_PRECISION_')],
            'currency' => $this->currency->iso_code,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('price_ttc', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'mapped' => false,
            'label' => $this->translator->trans('Price - Tax included', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
        ))
        ->add('ecotax', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Ecotax (tax incl.)', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            ),
            'attr' => ['data-eco-tax-rate' => $this->eco_tax_rate],
        ))
        ->add('id_tax_rules_group', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices' =>  $this->tax_rules,
            'required' => true,
            'choices_as_values' => true,
            'choice_attr' => function ($val) {
                return [
                    'data-rates' => implode(',', $this->tax_rules_rates[$val]['rates']),
                    'data-computation-method' => $this->tax_rules_rates[$val]['computation_method'],
                ];
            },
            'label' => $this->translator->trans('Tax rule', [], 'AdminProducts'),
        ))
        ->add('on_sale', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
            'required' => false,
            'label' => $this->translator->trans('Display the "On sale!" flag on the product page, and on product listings.', [], 'AdminProducts'),
        ))
        ->add('wholesale_price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Price - Tax excluded', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
        ))
        ->add('unit_price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Price per unit - Tax excluded', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
        ))
        ->add('unity', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'attr' => ['placeholder' => $this->translator->trans('Per kilo, per litre', [], 'AdminProducts')]
        ))
        ->add('specific_price', 'PrestaShopBundle\Form\Admin\Product\ProductSpecificPrice')
        ->add('specificPricePriorityToAll', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
            'required' => false,
            'label' => $this->translator->trans('Apply to all products', [], 'AdminProducts'),
        ));

        //generates fields for price priority
        $specificPricePriorityChoices = [
             $this->translator->trans('Shop', [], 'AdminProducts') => 'id_shop',
             $this->translator->trans('Currency', [], 'AdminProducts') => 'id_currency',
             $this->translator->trans('Country', [], 'AdminProducts') => 'id_country',
             $this->translator->trans('Group', [], 'AdminProducts') => 'id_group',
        ];

        for ($i=0; $i < count($specificPricePriorityChoices); $i++) {
            $builder->add('specificPricePriority_'.$i, 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' => $specificPricePriorityChoices,
                'choices_as_values' => true,
                'required' => true
            ));
        }
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
