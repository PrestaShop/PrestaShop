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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use Country;
use Currency;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

class RetailPriceType extends TranslatorAwareType
{
    /**
     * @var Locale
     */
    protected $contextLocale;

    /**
     * @var Currency
     */
    protected $defaultCurrency;

    /**
     * @var FormChoiceProviderInterface|FormChoiceAttributeProviderInterface
     */
    protected $taxRuleGroupChoicesProvider;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var bool
     */
    protected $isTaxEnabled;

    /**
     * @var bool
     */
    protected $isEcotaxEnabled;

    /**
     * @var int
     */
    protected $ecoTaxGroupId;

    /**
     * @var TaxComputer
     */
    protected $taxComputer;

    /**
     * @var int
     */
    protected $contextCountryId;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        Locale $contextLocale,
        Currency $defaultCurrency,
        $taxRuleGroupChoicesProvider,
        RouterInterface $router,
        bool $isTaxEnabled,
        bool $isEcotaxEnabled,
        int $ecoTaxGroupId,
        TaxComputer $taxComputer,
        int $contextCountryId
    ) {
        parent::__construct($translator, $locales);
        $this->contextLocale = $contextLocale;
        $this->defaultCurrency = $defaultCurrency;
        $this->taxRuleGroupChoicesProvider = $taxRuleGroupChoicesProvider;
        $this->router = $router;
        $this->isTaxEnabled = $isTaxEnabled;
        $this->isEcotaxEnabled = $isEcotaxEnabled;
        $this->ecoTaxGroupId = $ecoTaxGroupId;
        $this->taxComputer = $taxComputer;
        $this->contextCountryId = $contextCountryId;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->isTaxEnabled) {
            $ecotaxRate = $this->taxComputer->getTaxRate(new TaxRulesGroupId($this->ecoTaxGroupId), new CountryId($this->contextCountryId));
        } else {
            $ecotaxRate = new DecimalNumber('0');
        }
        $country = new Country($this->contextCountryId);

        $taxRateHelpPlaceholderWithoutState = $this->trans(
            'Tax %1$s : %2$s%%',
            'Admin.Catalog.Feature',
            ['%1$s' => $country->iso_code, '%2$s' => '_TAX_RATE_HELP_PLACEHOLDER_']
        );
        $taxRateHelpPlaceholderWithState = $this->trans(
            'Tax %1$s-%3$s: %2$s%%',
            'Admin.Catalog.Feature',
            ['%1$s' => $country->iso_code, '%2$s' => '_TAX_RATE_HELP_PLACEHOLDER_', '%3s$s' => '_STATE_ISO_CODE_HELP_PLACEHOLDER_']
        );

        $builder
            // @todo we should have DecimalType and MoneyDecimalType it was moved in a separate PR #22162
            ->add('price_tax_excluded', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Retail price (tax excl.)', 'Admin.Catalog.Feature'),
                'attr' => [
                    'data-display-price-precision' => self::PRESTASHOP_DECIMALS,
                    'data-price-specification' => json_encode($this->contextLocale->getPriceSpecification($this->defaultCurrency->iso_code)->toArray()),
                ],
                'row_attr' => [
                    'class' => 'retail-price-tax-excluded',
                ],
                'currency' => $this->defaultCurrency->iso_code,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                    new PositiveOrZero(),
                ],
                'default_empty_data' => 0.0,
                'modify_all_shops' => true,
            ])
            ->add('tax_rules_group_id', ChoiceType::class, [
                'choices' => $this->taxRuleGroupChoicesProvider->getChoices(),
                'disabled' => !$this->isTaxEnabled,
                'required' => false,
                // placeholder false is important to avoid empty option in select input despite required being false
                'placeholder' => false,
                'choice_attr' => $this->taxRuleGroupChoicesProvider->getChoicesAttributes(),
                'attr' => [
                    'data-toggle' => 'select2',
                    'data-minimumResultsForSearch' => '7',
                    'data-tax-enabled' => $this->isTaxEnabled,
                ],
                'row_attr' => [
                    'class' => 'retail-price-tax-rules-group-id',
                ],
                'label' => $this->trans('Tax rule', 'Admin.Catalog.Feature'),
                'help' => !$this->isTaxEnabled ?
                    $this->trans('Tax feature is disabled, it will not affect price tax included.', 'Admin.Catalog.Feature')
                    : '', //we replace help text in js on load when tax is enabled
                'help_attr' => [
                    'class' => 'js-tax-rule-help',
                    'data-place-holder-without-state' => $taxRateHelpPlaceholderWithoutState,
                    'data-place-holder-with-state' => $taxRateHelpPlaceholderWithState,
                    'data-is-tax-enabled' => $this->isTaxEnabled,
                ],
                'modify_all_shops' => true,
            ]);

        if ($this->isEcotaxEnabled) {
            $builder->add('ecotax_tax_excluded', MoneyType::class, [
                'label' => $this->trans('Ecotax (tax excl.)', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                    new PositiveOrZero(),
                ],
                'currency' => $this->defaultCurrency->iso_code,
                'modify_all_shops' => true,
                'attr' => [
                    'data-tax-rate' => (string) $ecotaxRate,
                ],
                'row_attr' => [
                    'class' => 'ecotax-tax-excluded',
                ],
            ]);
        }

        $builder
            ->add('price_tax_included', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Retail price (tax incl.)', 'Admin.Catalog.Feature'),
                'attr' => [
                    'data-display-price-precision' => self::PRESTASHOP_DECIMALS,
                ],
                'row_attr' => [
                    'class' => 'retail-price-tax-included',
                ],
                'currency' => $this->defaultCurrency->iso_code,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                    new PositiveOrZero(),
                ],
                'external_link' => [
                    'text' => $this->trans('[1]Manage tax rules[/1]', 'Admin.Catalog.Feature'),
                    'href' => $this->router->generate('admin_taxes_index'),
                    'align' => 'right',
                ],
                'default_empty_data' => 0.0,
                'modify_all_shops' => true,
            ]);

        if ($this->isEcotaxEnabled) {
            $helpMessage = '';
            if ($this->isTaxEnabled) {
                $helpMessage = $this->trans('Ecotax rate %rate%%', 'Admin.Catalog.Feature', ['%rate%' => $ecotaxRate->round(2)]);
            }
            $builder->add('ecotax_tax_included', MoneyType::class, [
                'label' => $this->trans('Ecotax (tax incl.)', 'Admin.Catalog.Feature'),
                'help' => $helpMessage,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                    new PositiveOrZero(),
                ],
                'currency' => $this->defaultCurrency->iso_code,
                'modify_all_shops' => true,
                'attr' => [
                    'data-tax-rate' => (string) $ecotaxRate,
                ],
                'row_attr' => [
                    'class' => 'ecotax-tax-included',
                ],
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label' => $this->trans('Retail price', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'required' => false,
            'attr' => [
                'class' => 'retail-price-widget',
            ],
            // Ecotax can be removed so there might be extra data in the request during type switching
            'allow_extra_fields' => true,
        ])
            ->setRequired([
                'tax_rules_group_id',
            ]);
    }
}
