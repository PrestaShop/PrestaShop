<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type\EventListener;

use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PriceReductionListener implements EventSubscriberInterface
{
    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    public function __construct(
        CurrencyDataProviderInterface $currencyDataProvider
    ) {
        $this->currencyDataProvider = $currencyDataProvider;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'adaptReductionField',
            FormEvents::PRE_SUBMIT => 'adaptReductionField',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function adaptReductionField(FormEvent $event): void
    {
        $data = $event->getData();
        if (!isset($data['type'])) {
            return;
        }

        $form = $event->getForm();
        $valueField = $form->get('value');
        $options = $valueField->getConfig()->getOptions();

        if ($data['type'] === Reduction::TYPE_PERCENTAGE) {
            // Change MoneyType into a PercentType
            $form->add('value', PercentType::class, [
                'type' => 'integer',
                'scale' => $options['scale'],
                'attr' => [
                    // We still need the data attribute available to handle switching in JS
                    'data-currency' => $options['attr']['data-currency'],
                ],
                'row_attr' => [
                    // Do not forget the row class which is important for JS
                    'class' => 'price-reduction-value',
                ],
                'default_empty_data' => 0,
            ]);
        // It is possible to have different values in same request, but different events, so if/else is essential
        // to make sure the form is built as expected during all events
        } else {
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
}
