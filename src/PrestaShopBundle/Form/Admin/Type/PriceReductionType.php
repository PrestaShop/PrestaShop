<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            $builder->add('currency', CurrencyChoiceType::class, [
                'row_attr' => [
                    'class' => 'price-reduction-currency-selector',
                ],
            ]);
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
