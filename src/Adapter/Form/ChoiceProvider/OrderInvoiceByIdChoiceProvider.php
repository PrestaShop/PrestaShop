<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Currency;
use Order;
use OrderInvoice;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

final class OrderInvoiceByIdChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * @param LocaleInterface $locale
     */
    public function __construct(LocaleInterface $locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices(array $options): array
    {
        $order = new Order($options['id_order']);
        $invoices = $order->getInvoicesCollection();
        $labelFormat = isset($options['display_total']) && false !== $options['display_total'] ? '%s - %s' : '%s';

        $choices = [];

        /** @var OrderInvoice $invoice */
        foreach ($invoices as $invoice) {
            $invoiceLabel = sprintf(
                $labelFormat,
                $invoice->getInvoiceNumberFormatted($options['id_lang'], $order->id_shop),
                $this->locale->formatPrice($invoice->total_paid_tax_incl, Currency::getIsoCodeById($invoice->getOrder()->id_currency))
            );
            $choices[$invoiceLabel] = $invoice->id;
        }

        return $choices;
    }
}
