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

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\FormHelper;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

class BulkCombinationPriceType extends TranslatorAwareType
{
    /**
     * @var string
     */
    private $defaultCurrencyIsoCode;

    /**
     * @var string
     */
    private $weightUnit;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var TaxComputer
     */
    private $taxComputer;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        string $defaultCurrencyIsoCode,
        string $weightUnit,
        ProductRepository $productRepository,
        TaxComputer $taxComputer
    ) {
        parent::__construct($translator, $locales);
        $this->defaultCurrencyIsoCode = $defaultCurrencyIsoCode;
        $this->weightUnit = $weightUnit;
        $this->productRepository = $productRepository;
        $this->taxComputer = $taxComputer;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wholesale_price', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Cost price (tax excl.)', 'Admin.Catalog.Feature'),
                'attr' => ['data-display-price-precision' => FormHelper::DEFAULT_PRICE_PRECISION],
                'currency' => $this->defaultCurrencyIsoCode,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                    new PositiveOrZero(),
                ],
                'disabling_switch' => true,
                'disabled_value' => 0,
                'modify_all_shops' => true,
            ])
            ->add('price_tax_excluded', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Impact on price (tax excl.)', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('If the price of this combination is different from the initial retail price, enter the value of the impact (negative or positive).', 'Admin.Catalog.Help'),
                'attr' => ['data-display-price-precision' => FormHelper::DEFAULT_PRICE_PRECISION],
                'currency' => $this->defaultCurrencyIsoCode,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
                'disabling_switch' => true,
                'disabled_value' => 0,
                'modify_all_shops' => true,
            ])
            ->add('price_tax_included', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Impact on price (tax incl.)', 'Admin.Catalog.Feature'),
                'attr' => ['data-display-price-precision' => FormHelper::DEFAULT_PRICE_PRECISION],
                'currency' => $this->defaultCurrencyIsoCode,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
                'disabling_switch' => true,
                'disabled_value' => 0,
            ])
            ->add('unit_price', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Impact on price per unit (tax excl.)', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('If the price per unit of this combination is different from the initial price per unit, enter the value of the impact (negative or positive).', 'Admin.Catalog.Feature'),
                'attr' => ['data-display-price-precision' => FormHelper::DEFAULT_PRICE_PRECISION],
                'currency' => $this->defaultCurrencyIsoCode,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
                'disabling_switch' => true,
                'disabled_value' => 0,
                'modify_all_shops' => true,
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
                'disabling_switch' => true,
                'disabled_value' => 0,
                'modify_all_shops' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Retail price', 'Admin.Catalog.Feature'),
        ]);

        $resolver
            ->setRequired([
                'product_id',
                'country_id',
                'shop_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setAllowedTypes('country_id', 'int')
            ->setAllowedTypes('shop_id', 'int')
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr'] += [
            'data-rate' => (float) (string) $this->getRate($options['product_id'], $options['country_id'], $options['shop_id']) / 100,
        ];
    }

    private function getRate(int $productId, int $countryId, int $shopId): DecimalNumber
    {
        return $this->taxComputer->getTaxRate(
            $this->productRepository->getProductTaxRulesGroupId(new ProductId($productId), new ShopId($shopId)),
            new CountryId($countryId)
        );
    }
}
