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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\Reduction;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction as ReductionVO;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ReductionTypeChoiceProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Responsible for creating form for price reduction
 */
class PriceReductionType extends TranslatorAwareType
{
    /**
     * @var EventSubscriberInterface
     */
    private $eventSubscriber;

    /**
     * @var ReductionTypeChoiceProvider
     */
    private $reductionTypeChoiceProvider;

    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        EventSubscriberInterface $eventSubscriber,
        ReductionTypeChoiceProvider $reductionTypeChoiceProvider,
        CurrencyDataProviderInterface $currencyDataProvider
    ) {
        parent::__construct($translator, $locales);
        $this->eventSubscriber = $eventSubscriber;
        $this->reductionTypeChoiceProvider = $reductionTypeChoiceProvider;
        $this->currencyDataProvider = $currencyDataProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', MoneyType::class, [
                'scale' => $options['scale'],
                'currency' => $this->currencyDataProvider->getDefaultCurrencyIsoCode(),
                'attr' => [
                    'data-currency' => $this->currencyDataProvider->getDefaultCurrencySymbol(),
                ],
                'row_attr' => [
                    'class' => 'price-reduction-value',
                ],
                'default_empty_data' => 0,
            ])
            ->add('type', ChoiceType::class, [
                'placeholder' => false,
                'required' => false,
                'choices' => $this->reductionTypeChoiceProvider->getChoices(),
            ])
        ;

        if ($options['currency_select']) {
            $builder->add('currency', CurrencyChoiceType::class);
        }

        $builder->add('include_tax', TaxInclusionChoiceType::class);

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
                'constraints' => [
                    new Reduction([
                        'invalidPercentageValueMessage' => $this->trans(
                            'Reduction value "%value%" is invalid. It must be greater than 0 and maximum %max%.',
                            'Admin.Notifications.Error',
                            ['%max%' => ReductionVO::MAX_ALLOWED_PERCENTAGE . '%']
                        ),
                        'invalidAmountValueMessage' => $this->trans(
                            'Reduction value "%value%" is invalid. It must be greater than 0.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->setAllowedTypes('currency_select', 'bool')
        ;
    }
}
