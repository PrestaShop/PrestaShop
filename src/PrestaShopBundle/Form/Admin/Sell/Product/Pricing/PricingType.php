<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\FormHelper;
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
     * @var string
     */
    private $defaultCurrencyIsoCode;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param string $defaultCurrencyIsoCode
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        string $defaultCurrencyIsoCode
    ) {
        parent::__construct($translator, $locales);
        $this->defaultCurrencyIsoCode = $defaultCurrencyIsoCode;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('retail_price', RetailPriceType::class, [
                'tax_rules_group_id' => $options['tax_rules_group_id'],
            ])
            ->add('wholesale_price', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Cost price', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'label_subtitle' => $this->trans('Cost price (tax excl.)', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('Internal purchase price of the product. You can use it for margin calculations, statistics, and internal reporting. This value is not shown to customers.', 'Admin.Catalog.Help'),
                'attr' => ['data-display-price-precision' => FormHelper::DEFAULT_PRICE_PRECISION],
                'currency' => $this->defaultCurrencyIsoCode,
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
                ],
            ])
            ->add('catalog_price_rules', CatalogPriceRulesType::class)
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
