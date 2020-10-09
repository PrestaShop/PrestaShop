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
