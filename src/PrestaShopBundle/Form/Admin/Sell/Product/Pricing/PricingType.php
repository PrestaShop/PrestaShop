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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use Currency;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type containing price fields for Pricing tab
 */
class PricingType extends TranslatorAwareType
{
    /**
     * @var Currency
     */
    private $defaultCurrency;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        Currency $defaultCurrency,
        LegacyContext $legacyContext
    ) {
        parent::__construct($translator, $locales);
        $this->defaultCurrency = $defaultCurrency;
        $this->legacyContext = $legacyContext;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * %catalog_price_rule_id% can't be used in this function, because getAdminLink adds uneeded stuff to % while creating url
         * That's why catalog_price_rule_id is used and then string repalced.
         */
        $catalogPriceRuleEditLink = $this->legacyContext->getAdminLink('AdminSpecificPriceRule', true, ['updatespecific_price_rule' => '', 'id_specific_price_rule' => 'catalog_price_rule_id']);
        $catalogPriceRuleIndexLink = $this->legacyContext->getAdminLink('AdminSpecificPriceRule');
        /** Adding % to make link more unique */
        $catalogPriceRuleEditLink = str_replace('catalog_price_rule_id', '%catalog_price_rule_id%', $catalogPriceRuleEditLink);

        $builder
            ->add('retail_price', RetailPriceType::class, [
                'tax_rules_group_id' => $options['tax_rules_group_id'],
            ])
            ->add('wholesale_price', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Cost price', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'label_subtitle' => $this->trans('Cost price (tax excl.)', 'Admin.Catalog.Feature'),
                'attr' => ['data-display-price-precision' => self::PRESTASHOP_DECIMALS],
                'currency' => $this->defaultCurrency->iso_code,
                'modify_all_shops' => true,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                    new PositiveOrZero(),
                ],
            ])
            ->add('unit_price', UnitPriceType::class)
            ->add('summary', PriceSummaryType::class)
            ->add('on_sale', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans(
                    'Display the "On sale!" flag on the product page, and on product listings.',
                    'Admin.Catalog.Feature'
                ),
                'modify_all_shops' => true,
            ])
            ->add('specific_prices', SpecificPricesType::class, [
                'label' => $this->trans('Specific prices', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'label_help_box' => $this->trans('Set specific prices for customers meeting certain conditions.', 'Admin.Catalog.Help'),
            ])
            ->add('show_catalog_price_rules', IconButtonType::class, [
                'icon' => 'visibility',
                'label' => $this->trans('Show catalog price rules', 'Admin.Catalog.Feature'),
                'attr' => [
                    'data-hide-label' => $this->trans('Hide catalog price rules', 'Admin.Catalog.Feature'),
                    'data-show-label' => $this->trans('Show catalog price rules', 'Admin.Catalog.Feature'),
                    'data-show-icon' => 'visibility',
                    'data-hide-icon' => 'visibility_off',
                ],
            ])
            ->add('catalog_price_rules', CatalogPriceRulesType::class, [
                'label' => $this->trans('Catalog price rules', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'attr' => [
                    'data-catalog-price-url' => $catalogPriceRuleEditLink,
                ],
                'external_link' => [
                    'text' => $this->trans('[1]Manage catalog price rules[/1]', 'Admin.Catalog.Feature'),
                    'href' => $catalogPriceRuleIndexLink,
                    'align' => 'left',
                ],
                'row_attr' => [
                    'id' => 'catalog-price-rules-container',
                    'class' => 'd-none',
                ],
            ])
            ->add('priority_management', ProductSpecificPricePriorityType::class, [
                'label' => $this->trans('Priority management', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'label_help_box' => $this->trans('Define which condition should apply first when a customer is eligible for multiple specific prices.', 'Admin.Catalog.Help'),
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => $this->trans('Pricing', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->setRequired([
                'tax_rules_group_id',
            ]);
    }
}
