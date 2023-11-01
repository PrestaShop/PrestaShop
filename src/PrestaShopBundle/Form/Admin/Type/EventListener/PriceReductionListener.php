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
