<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\Reduction;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CurrencyChoiceType;
use PrestaShopBundle\Form\Admin\Type\ShippingInclusionChoiceType;
use PrestaShopBundle\Form\Admin\Type\TaxInclusionChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Responsible for creating form for price reduction
 */
class MinimumAmountType extends TranslatorAwareType implements EventSubscriberInterface
{
    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        CurrencyDataProviderInterface $currencyDataProvider
    ) {
        parent::__construct($translator, $locales);
        $this->currencyDataProvider = $currencyDataProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'adaptValueField',
            FormEvents::PRE_SUBMIT => 'adaptValueField',
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', MoneyType::class, [
                'scale' => $options['scale'],
                'row_attr' => [
                    'class' => 'price-reduction-value',
                ],
                'default_empty_data' => 0,
            ])
            ->add('currency', CurrencyChoiceType::class, [
                'row_attr' => [
                    'class' => 'price-reduction-currency-selector',
                ],
            ])
            ->add('tax_included', TaxInclusionChoiceType::class)
        ;
        // No no jose, this field is not in the design so we drop it
        // $builder->add('shipping_included', ShippingInclusionChoiceType::class);

        $builder->addEventSubscriber($this);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'scale' => 6,
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Discount/FormTheme/minimum_amount.html.twig',
            ])
        ;
    }

    public function adaptValueField(FormEvent $event): void
    {
        $data = $event->getData();
        if (!isset($data['type'])) {
            return;
        }

        $form = $event->getForm();
        $valueField = $form->get('value');
        $options = $valueField->getConfig()->getOptions();

        // It is possible to have different values in same request, but different events, so if/else is essential
        // to make sure the form is built as expected during all events
        $form->add('value', MoneyType::class, [
            'scale' => $options['scale'],
            'currency' => $this->currencyDataProvider->getDefaultCurrencyIsoCode(),
            'attr' => [
                'data-currency' => $this->currencyDataProvider->getDefaultCurrencySymbol(),
            ],
            'row_attr' => [
                'class' => 'price-reduction-value',
            ],
            'default_empty_data' => 0,
        ]);
    }
}
