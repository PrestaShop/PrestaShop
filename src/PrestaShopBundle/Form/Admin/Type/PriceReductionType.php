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

namespace PrestaShopBundle\Form\Admin\Type;

use Currency;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CurrencyByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ReductionTypeChoiceProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Responsible for creating form for price reduction
 */
class PriceReductionType extends CommonAbstractType
{
    /**
     * @var Currency
     */
    private $defaultCurrency;

    /**
     * @var EventSubscriberInterface
     */
    private $eventSubscriber;

    /**
     * @var FormChoiceProviderInterface
     */
    private $taxInclusionChoiceProvider;

    /**
     * @var CurrencyByIdChoiceProvider
     */
    private $currencyByIdChoiceProvider;

    /**
     * @var ReductionTypeChoiceProvider
     */
    private $reductionTypeChoiceProvider;

    public function __construct(
        Currency $defaultCurrency,
        EventSubscriberInterface $eventSubscriber,
        FormChoiceProviderInterface $taxInclusionChoiceProvider,
        CurrencyByIdChoiceProvider $currencyByIdChoiceProvider,
        ReductionTypeChoiceProvider $reductionTypeChoiceProvider
    ) {
        $this->defaultCurrency = $defaultCurrency;
        $this->eventSubscriber = $eventSubscriber;
        $this->taxInclusionChoiceProvider = $taxInclusionChoiceProvider;
        $this->currencyByIdChoiceProvider = $currencyByIdChoiceProvider;
        $this->reductionTypeChoiceProvider = $reductionTypeChoiceProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', MoneyType::class, [
                'scale' => $options['scale'],
                'currency' => $this->defaultCurrency->iso_code,
                'attr' => [
                    'data-currency' => $this->defaultCurrency->symbol,
                ],
                'row_attr' => [
                    'class' => 'price-reduction-value',
                ],
                'default_empty_data' => 0,
            ])
            ->add('type', ChoiceType::class, [
                'placeholder' => false,
                'required' => false,
                'choices' => $this->reductionTypeChoiceProvider->getChoices([
                    'symbol_as_label' => $options['symbol_as_label'],
                ]),
            ])
        ;

        if ($options['currency_select']) {
            $builder->add('currency', ChoiceType::class, [
                'choices' => $this->currencyByIdChoiceProvider->getChoices(),
                'choice_attr' => $this->currencyByIdChoiceProvider->getChoicesAttributes(),
            ]);
        }

        $builder->add('include_tax', ChoiceType::class, [
            'choices' => $this->taxInclusionChoiceProvider->getChoices(),
            'placeholder' => false,
            'required' => false,
            'row_attr' => [
                'class' => 'js-include-tax-row',
            ],
        ]);

        $builder->addEventSubscriber($this->eventSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'scale' => 6,
                'currency_select' => false,
                'symbol_as_label' => true,
            ])
            ->setAllowedTypes('currency_select', 'bool')
            ->setAllowedTypes('symbol_as_label', 'bool')
        ;
    }
}
