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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DateRange;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\Reduction;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction as ReductionVO;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Customer\SearchedCustomerType;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\ReductionType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class SpecificPriceType extends TranslatorAwareType
{
    /**
     * @var string
     */
    private $defaultCurrencyIso;

    /**
     * @var FormChoiceProviderInterface
     */
    private $currencyByIdChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $countryByIdChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $groupByIdChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $shopByIdChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $taxInclusionChoiceProvider;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $combinationIdChoiceProvider;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var bool
     */
    private $isMultishopEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param string $defaultCurrencyIso
     * @param FormChoiceProviderInterface $currencyByIdChoiceProvider
     * @param FormChoiceProviderInterface $countryByIdChoiceProvider
     * @param FormChoiceProviderInterface $groupByIdChoiceProvider
     * @param FormChoiceProviderInterface $shopByIdChoiceProvider
     * @param FormChoiceProviderInterface $taxInclusionChoiceProvider
     * @param ConfigurableFormChoiceProviderInterface $configurableFormChoiceProvider
     * @param UrlGeneratorInterface $urlGenerator
     * @param bool $isMultishopEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        string $defaultCurrencyIso,
        FormChoiceProviderInterface $currencyByIdChoiceProvider,
        FormChoiceProviderInterface $countryByIdChoiceProvider,
        FormChoiceProviderInterface $groupByIdChoiceProvider,
        FormChoiceProviderInterface $shopByIdChoiceProvider,
        FormChoiceProviderInterface $taxInclusionChoiceProvider,
        ConfigurableFormChoiceProviderInterface $configurableFormChoiceProvider,
        UrlGeneratorInterface $urlGenerator,
        bool $isMultishopEnabled
    ) {
        parent::__construct($translator, $locales);
        $this->currencyByIdChoiceProvider = $currencyByIdChoiceProvider;
        $this->countryByIdChoiceProvider = $countryByIdChoiceProvider;
        $this->groupByIdChoiceProvider = $groupByIdChoiceProvider;
        $this->shopByIdChoiceProvider = $shopByIdChoiceProvider;
        $this->taxInclusionChoiceProvider = $taxInclusionChoiceProvider;
        $this->defaultCurrencyIso = $defaultCurrencyIso;
        $this->combinationIdChoiceProvider = $configurableFormChoiceProvider;
        $this->urlGenerator = $urlGenerator;
        $this->isMultishopEnabled = $isMultishopEnabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!isset($builder->getData()['product_id'])) {
            // product_id is required for create action and to load combinations choices list, but it is not editable
            throw new SpecificPriceException('product_id is required to add/edit specific price.');
        }

        $builder
            ->add('product_id', HiddenType::class)
            ->add('currency_id', ChoiceType::class, [
                'label' => $this->trans('Currency', 'Admin.Global'),
                'placeholder' => $this->trans('All currencies', 'Admin.Global'),
                'choices' => $this->currencyByIdChoiceProvider->getChoices(),
            ])
            ->add('country_id', ChoiceType::class, [
                'label' => $this->trans('Country', 'Admin.Global'),
                'placeholder' => $this->trans('All countries', 'Admin.Global'),
                'choices' => $this->countryByIdChoiceProvider->getChoices(),
            ])
            ->add('group_id', ChoiceType::class, [
                'label' => $this->trans('Group', 'Admin.Global'),
                'required' => false,
                'placeholder' => $this->trans('All groups', 'Admin.Global'),
                'choices' => $this->groupByIdChoiceProvider->getChoices(),
            ])
            ->add('customer', EntitySearchInputType::class, [
                'label' => $this->trans('Customer', 'Admin.Global'),
                'layout' => EntitySearchInputType::LIST_LAYOUT,
                'entry_type' => SearchedCustomerType::class,
                'remove_modal' => null,
                'limit' => 1,
                'remote_url' => $this->urlGenerator->generate('admin_customers_search', ['customer_search' => '__QUERY__']),
                'placeholder' => $this->trans('All Customers', 'Admin.Global'),
                'suggestion_field' => 'fullname_and_email',
            ])
            ->add('combination_id', ChoiceType::class, [
                'label' => $this->trans('Combination', 'Admin.Global'),
                'placeholder' => $this->trans('All combinations', 'Admin.Global'),
                'choices' => $this->combinationIdChoiceProvider->getChoices(['product_id' => $builder->getData()['product_id']]),
            ])
            ->add('from_quantity', NumberType::class, [
                'label' => $this->trans('From quantity', 'Admin.Catalog.Feature'),
                'scale' => 0,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('price', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Retail price (tax excl.)', 'Admin.Catalog.Feature'),
                'attr' => ['data-display-price-precision' => self::PRESTASHOP_DECIMALS],
                'currency' => $this->defaultCurrencyIso,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
                'default_empty_data' => 0.0,
            ])
            ->add('leave_initial_price', CheckboxType::class, [
                'label' => $this->trans('Leave initial price', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('date_range', DateRangeType::class, [
                'label' => $this->trans('Available from', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new DateRange([
                        'message' => $this->trans(
                            'The selected date range is not valid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('reduction', ReductionType::class, [
                'label' => $this->trans('Reduction', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Reduction([
                        'invalidPercentageValueMessage' => $this->trans(
                            'Reduction value "%value%" is invalid. Allowed values from 0 to %max%',
                            'Admin.Notifications.Error',
                            ['%max%' => ReductionVO::MAX_ALLOWED_PERCENTAGE . '%']
                        ),
                        'invalidAmountValueMessage' => $this->trans(
                            'Reduction value "%value%" is invalid. Value cannot be negative',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('include_tax', ChoiceType::class, [
                'row_attr' => [
                    'class' => 'js-include-tax-row',
                ],
                'label' => $this->trans('Reduction with or without taxes', 'Admin.Catalog.Feature'),
                'choices' => $this->taxInclusionChoiceProvider->getChoices(),
            ])
        ;

        if ($this->isMultishopEnabled) {
            $builder->add('shop_id', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => $this->shopByIdChoiceProvider->getChoices(),
            ]);
        }
    }
}
