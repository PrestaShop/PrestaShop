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
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction as ReductionVO;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
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
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class SpecificPriceType extends TranslatorAwareType
{
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
     * @var string
     */
    private $defaultCurrencyIso;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $configurableFormChoiceProvider;

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
        ConfigurableFormChoiceProviderInterface $configurableFormChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->currencyByIdChoiceProvider = $currencyByIdChoiceProvider;
        $this->countryByIdChoiceProvider = $countryByIdChoiceProvider;
        $this->groupByIdChoiceProvider = $groupByIdChoiceProvider;
        $this->shopByIdChoiceProvider = $shopByIdChoiceProvider;
        $this->taxInclusionChoiceProvider = $taxInclusionChoiceProvider;
        $this->defaultCurrencyIso = $defaultCurrencyIso;
        $this->configurableFormChoiceProvider = $configurableFormChoiceProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // product_id is required for create action
            ->add('product_id', HiddenType::class)
            ->add('currency_id', ChoiceType::class, [
                'label' => $this->trans('Currency', 'Admin.Global'),
                'required' => false,
                'placeholder' => false,
                'choices' => $this->getModifiedCurrencyChoices(),
            ])
            ->add('country_id', ChoiceType::class, [
                'label' => $this->trans('Country', 'Admin.Global'),
                'required' => false,
                'placeholder' => false,
                'choices' => $this->getModifiedCountryChoices(),
            ])
            ->add('group_id', ChoiceType::class, [
                'label' => $this->trans('Group', 'Admin.Global'),
                'required' => false,
                'placeholder' => false,
                'choices' => $this->getModifiedGroupChoices(),
            ])
            ->add('customer_id', EntitySearchInputType::class, [
                'label' => $this->trans('Customer', 'Admin.Global'),
                //@todo: provide options to search customer and add js side if needed
            ])
            ->add('combinationId', ChoiceType::class, [
                'label' => $this->trans('Combination', 'Admin.Global'),
                'required' => false,
                'placeholder' => false,
                'choices' => $this->configurableFormChoiceProvider->getChoices(
                    ['product_id' => $builder->getData()['product_id']]
                ),
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
            //@todo: handle field removal when % reduction type is selected
            ->add('include_tax', ChoiceType::class, [
                'placeholder' => false,
                'required' => false,
                'choices' => $this->taxInclusionChoiceProvider->getChoices(),
            ])
        ;

        //@todo: handle multishop. Check if we need this?
        //@todo: Also do we need both shop and shop group? check how old page behaved, AddProductSpecificPriceCommand has both values.
//        if ($this->isMultishopEnabled) {
//            $builder->add('id_shop', ChoiceType::class, [
//                'required' => false,
//                'placeholder' => false,
//                'choices' => $this->shopByIdChoiceProvider->getChoices(),
//            ]);
//        }
    }

    //@todo: all bellow getModified{fooBar} methods might be worth moving to some reusable services (used in CatalogPriceRuleType too)

    /**
     * Prepends 'All currencies' option with id of 0 to currency choices
     *
     * @return array<string, int>
     */
    private function getModifiedCurrencyChoices(): array
    {
        return array_merge(
            [$this->trans('All currencies', 'Admin.Global') => 0],
            $this->currencyByIdChoiceProvider->getChoices()
        );
    }

    /**
     * Prepends 'All countries' option with id of 0 to country choices
     *
     * @return array<string, int>
     */
    private function getModifiedCountryChoices(): array
    {
        return array_merge(
            [$this->trans('All countries', 'Admin.Global') => 0],
            $this->countryByIdChoiceProvider->getChoices()
        );
    }

    /**
     * Prepends 'All groups' option with id of 0 to group choices
     *
     * @return array<string, int>
     */
    private function getModifiedGroupChoices(): array
    {
        return array_merge(
            [$this->trans('All groups', 'Admin.Global') => 0],
            $this->groupByIdChoiceProvider->getChoices()
        );
    }
}
