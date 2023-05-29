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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use Currency;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\FormHelper;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

class CombinationPriceImpactType extends TranslatorAwareType
{
    /**
     * @var Currency
     */
    protected $defaultCurrency;

    /**
     * @var string
     */
    protected $weightUnit;

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

    /**
     * @var Locale
     */
    protected $contextLocale;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        Currency $defaultCurrency,
        string $weightUnit,
        bool $isTaxEnabled,
        bool $isEcotaxEnabled,
        int $ecoTaxGroupId,
        TaxComputer $taxComputer,
        int $contextCountryId,
        Locale $contextLocale
    ) {
        parent::__construct($translator, $locales);
        $this->defaultCurrency = $defaultCurrency;
        $this->weightUnit = $weightUnit;
        $this->isTaxEnabled = $isTaxEnabled;
        $this->isEcotaxEnabled = $isEcotaxEnabled;
        $this->ecoTaxGroupId = $ecoTaxGroupId;
        $this->taxComputer = $taxComputer;
        $this->contextCountryId = $contextCountryId;
        $this->contextLocale = $contextLocale;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price_tax_excluded', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Impact on price (tax excl.)', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('If the price of this combination is different from the initial retail price, enter the value of the impact (negative or positive).', 'Admin.Catalog.Help'),
                'attr' => [
                    'data-display-price-precision' => FormHelper::DEFAULT_PRICE_PRECISION,
                    'data-price-specification' => json_encode($this->contextLocale->getPriceSpecification($this->defaultCurrency->iso_code)->toArray()),
                ],
                'currency' => $this->defaultCurrency->iso_code,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
                'modify_all_shops' => true,
            ])
            ->add('price_tax_included', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Impact on price (tax incl.)', 'Admin.Catalog.Feature'),
                'attr' => ['data-display-price-precision' => FormHelper::DEFAULT_PRICE_PRECISION],
                'currency' => $this->defaultCurrency->iso_code,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
                'column_breaker' => true,
            ])
        ;

        if ($this->isEcotaxEnabled) {
            if ($this->isTaxEnabled) {
                $ecotaxRate = $this->taxComputer->getTaxRate(new TaxRulesGroupId($this->ecoTaxGroupId), new CountryId($this->contextCountryId));
                $helpMessage = $this->trans('Ecotax rate %rate%%', 'Admin.Catalog.Feature', ['%rate%' => $ecotaxRate->round(2)]);
            } else {
                $ecotaxRate = new DecimalNumber('0');
                $helpMessage = '';
            }

            $builder
                ->add('ecotax_tax_excluded', MoneyType::class, [
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
                ])
                ->add('ecotax_tax_included', MoneyType::class, [
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
                    'column_breaker' => true,
                ])
                ->add('product_ecotax_tax_excluded', HiddenType::class, [
                    'attr' => [
                        'class' => 'product-ecotax-tax-excluded',
                    ],
                ])
            ;
        }

        $builder
            ->add('final_price_tax_excluded', TextPreviewType::class, [
                'required' => false,
                'label' => $this->trans('Final retail price (tax excl.)', 'Admin.Catalog.Feature'),
                'preview_class' => 'final-price-preview',
                'attr' => [
                    'class' => 'final-retail-price final-retail-price-tax-excluded',
                ],
            ])
            ->add('final_price_tax_included', TextPreviewType::class, [
                'required' => false,
                'label' => $this->trans('Final retail price (tax incl.)', 'Admin.Catalog.Feature'),
                'preview_class' => 'final-price-preview',
                'attr' => [
                    'class' => 'final-retail-price final-retail-price-tax-included',
                ],
                'column_breaker' => true,
            ])
            ->add('unit_price_tax_excluded', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Impact on price per unit (tax excl.)', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('If the price per unit of this combination is different from the initial price per unit, enter the value of the impact (negative or positive).', 'Admin.Catalog.Feature'),
                'attr' => ['data-display-price-precision' => FormHelper::DEFAULT_PRICE_PRECISION],
                'currency' => $this->defaultCurrency->iso_code,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
                'modify_all_shops' => true,
            ])
            ->add('unit_price_tax_included', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Impact on price per unit (tax incl.)', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('If the price per unit of this combination is different from the initial price per unit, enter the value of the impact (negative or positive).', 'Admin.Catalog.Feature'),
                'attr' => ['data-display-price-precision' => FormHelper::DEFAULT_PRICE_PRECISION],
                'currency' => $this->defaultCurrency->iso_code,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
                'column_breaker' => true,
            ])
            ->add('weight', NumberType::class, [
                'required' => false,
                'label' => $this->trans('Impact on weight', 'Admin.Catalog.Feature'),
                'unit' => $this->weightUnit,
                'constraints' => [
                    new NotBlank(),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new PositiveOrZero(),
                ],
                'column_breaker' => true,
                'modify_all_shops' => true,
            ])
            ->add('wholesale_price', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Cost price (tax excl.)', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'attr' => ['data-display-price-precision' => FormHelper::DEFAULT_PRICE_PRECISION],
                'currency' => $this->defaultCurrency->iso_code,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                    new PositiveOrZero(),
                ],
                'modify_all_shops' => true,
            ])
            ->add('product_price_tax_excluded', HiddenType::class, [
                'attr' => [
                    'class' => 'product-price-tax-excluded',
                ],
            ])
            ->add('product_tax_rate', HiddenType::class, [
                'attr' => [
                    'class' => 'product-tax-rate',
                ],
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Retail price', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'columns_number' => 3,
        ]);
    }
}
