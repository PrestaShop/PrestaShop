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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Address;
use AddressFormat;
use Carrier;
use Configuration;
use Context;
use Currency;
use Customer;
use Hook;
use Language;
use Mail;
use Order;
use OrderState;
use PDF;
use PrestaShop\PrestaShop\Adapter\MailTemplate\MailPartialTemplateRenderer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\SendOrderConfirmationEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\SendOrderConfirmationEmailHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use State;
use Tools;
use Validate;

#[AsCommandHandler]
class SendOrderConfirmationEmailHandler implements SendOrderConfirmationEmailHandlerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var MailPartialTemplateRenderer
     */
    private $mailPartialTemplateRenderer;

    /**
     * @var LocaleRepository
     */
    private $localeRepository;

    /**
     * @param TranslatorInterface $translator
     * @param MailPartialTemplateRenderer $mailPartialTemplateRenderer
     */
    public function __construct(
        TranslatorInterface $translator,
        MailPartialTemplateRenderer $mailPartialTemplateRenderer,
        LocaleRepository $localeRepository
    ) {
        $this->translator = $translator;
        $this->mailPartialTemplateRenderer = $mailPartialTemplateRenderer;
        $this->localeRepository = $localeRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CustomerMessageException
     * @throws OrderNotFoundException
     */
    public function handle(SendOrderConfirmationEmailCommand $command): void
    {
        // FIXED THINGS
        // Possibility to reuse the command and resend confirmation
        // Customer data from context
        // Order date add correctly

        // Initialize order, customer and some basic data
        $order = new Order($command->getOrderId()->getValue());
        $customer = new Customer((int) $order->id_customer);
        if (!Validate::isLoadedObject($customer) || !Validate::isEmail($customer->email)) {
            return; // throw some exception?
        }

        // Initialize currency and language
        $orderLanguage = new Language((int) $order->id_lang);
        $orderLocale = $this->localeRepository->getLocale($orderLanguage->getLocale());
        $orderCurrency = new Currency((int) $order->id_currency);

        $order_status = new OrderState((int) $order->current_state, (int) $order->id_lang);
        if (!Validate::isLoadedObject($order_status)) {
            return; // throw some exception?
        }

        // Assign basic customer data
        $emailVariables = [
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{email}' => $customer->email,
            '{order_name}' => $order->getUniqReference(),
            '{id_order}' => $order->id,
            '{date}' => Tools::displayDate(date('Y-m-d H:i:s', strtotime($order->date_add)), true),
            '{payment}' => Tools::substr($order->payment, 0, 255) . ($order->hasBeenPaid() ? '' : '&nbsp;' . $this->translator->trans('(waiting for validation)', [], 'Emails.Body')),
            '{recycled_packaging_label}' => $order->recyclable ? $this->translator->trans('Yes', [], 'Shop.Theme.Global') : $this->translator->trans('No', [], 'Shop.Theme.Global'),
        ];

        // Initialize data about addresses
        $invoice = new Address((int) $order->id_address_invoice);
        $delivery = new Address((int) $order->id_address_delivery);
        $delivery_state = $delivery->id_state ? new State((int) $delivery->id_state) : false;
        $invoice_state = $invoice->id_state ? new State((int) $invoice->id_state) : false;

        // Assign data related to addresses
        $emailVariables = array_merge($emailVariables, [
            '{delivery_block_txt}' => $this->_getFormatedAddress($delivery, AddressFormat::FORMAT_NEW_LINE),
            '{invoice_block_txt}' => $this->_getFormatedAddress($invoice, AddressFormat::FORMAT_NEW_LINE),
            '{delivery_block_html}' => $this->_getFormatedAddress($delivery, '<br />', [
                'firstname' => '<span style="font-weight:bold;">%s</span>',
                'lastname' => '<span style="font-weight:bold;">%s</span>',
            ]),
            '{invoice_block_html}' => $this->_getFormatedAddress($invoice, '<br />', [
                'firstname' => '<span style="font-weight:bold;">%s</span>',
                'lastname' => '<span style="font-weight:bold;">%s</span>',
            ]),
            '{delivery_company}' => $delivery->company,
            '{delivery_firstname}' => $delivery->firstname,
            '{delivery_lastname}' => $delivery->lastname,
            '{delivery_address1}' => $delivery->address1,
            '{delivery_address2}' => $delivery->address2,
            '{delivery_city}' => $delivery->city,
            '{delivery_postal_code}' => $delivery->postcode,
            '{delivery_country}' => $delivery->country,
            '{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',
            '{delivery_phone}' => ($delivery->phone) ? $delivery->phone : $delivery->phone_mobile,
            '{delivery_other}' => $delivery->other,
            '{invoice_company}' => $invoice->company,
            '{invoice_vat_number}' => $invoice->vat_number,
            '{invoice_firstname}' => $invoice->firstname,
            '{invoice_lastname}' => $invoice->lastname,
            '{invoice_address2}' => $invoice->address2,
            '{invoice_address1}' => $invoice->address1,
            '{invoice_city}' => $invoice->city,
            '{invoice_postal_code}' => $invoice->postcode,
            '{invoice_country}' => $invoice->country,
            '{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',
            '{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,
            '{invoice_other}' => $invoice->other,
        ]);

        // Generate product list
        $product_var_tpl_list = [];
        $virtual_product = true;
        foreach ($order->getProducts() as $product) {

            // Basic product information
            $product_var_tpl = [
                'id_product' => $product['product_id'],
                'id_product_attribute' => $product['product_attribute_id'],
                'reference' => $product['product_reference'],
                'name' => $product['product_name'],
                'price' => $orderLocale->formatPrice($product['total_price'], $orderCurrency->iso_code),
                'quantity' => $product['product_quantity'],
                'customization' => [],
                'unit_price' => '',
            ];

            $product_var_tpl_list[] = $product_var_tpl;

            // Check if is not a virtual product for the displaying of shipping
            if (!$product['is_virtual']) {
                $virtual_product &= false;
            }
        }

        // Generate cart rule list
        $cart_rules_list = [];
        foreach ($order->getCartRules() as $cartRule) {
            $cart_rules_list[] = [
                'voucher_name' => $cartRule['name'],
                'voucher_reduction' => ($cartRule['value'] != 0.00 ? '-' : '') . ($order->getTaxCalculationMethod() == PS_TAX_EXC
                    ? $orderLocale->formatPrice($cartRule['tax_excl'], $orderCurrency->iso_code)
                    : $orderLocale->formatPrice($cartRule['value_tax_excl'], $orderCurrency->iso_code)
                ),
            ];
        }

        // Render and assign product lists and discount lists
        $product_list_txt = '';
        $product_list_html = '';
        $cart_rules_list_txt = '';
        $cart_rules_list_html = '';
        if (!empty($product_var_tpl_list) > 0) {
            $product_list_txt = $this->mailPartialTemplateRenderer->render('order_conf_product_list.txt', $orderLanguage, $product_var_tpl_list);
            $product_list_html = $this->mailPartialTemplateRenderer->render('order_conf_product_list.tpl', $orderLanguage, $product_var_tpl_list);
        }
        if (!empty($cart_rules_list) > 0) {
            $cart_rules_list_txt = $this->mailPartialTemplateRenderer->render('order_conf_cart_rules.txt', $orderLanguage, $cart_rules_list);
            $cart_rules_list_html = $this->mailPartialTemplateRenderer->render('order_conf_cart_rules.tpl', $orderLanguage, $cart_rules_list);
        }
        $emailVariables = array_merge($emailVariables, [
            '{products}' => $product_list_html,
            '{products_txt}' => $product_list_txt,
            '{discounts}' => $cart_rules_list_html,
            '{discounts_txt}' => $cart_rules_list_txt,
        ]);

        // Assign carrier data
        $carrier = $order->id_carrier ? new Carrier($order->id_carrier) : false;
        $emailVariables = array_merge($emailVariables, [
            '{carrier}' => ($virtual_product || !isset($carrier->name)) ? $this->translator->trans('No carrier', [], 'Admin.Payment.Notification') : $carrier->name,
        ]);

        // Assign totals
        $emailVariables = array_merge($emailVariables, [
            '{total_paid}' => $orderLocale->formatPrice($order->total_paid, $orderCurrency->iso_code),
            '{total_shipping_tax_excl}' => $orderLocale->formatPrice($order->total_shipping_tax_excl, $orderCurrency->iso_code),
            '{total_shipping_tax_incl}' => $orderLocale->formatPrice($order->total_shipping_tax_incl, $orderCurrency->iso_code),
            '{total_tax_paid}' => $orderLocale->formatPrice(($order->total_paid_tax_incl - $order->total_paid_tax_excl), $orderCurrency->iso_code),
        ]);
        if ($order->getTaxCalculationMethod() == PS_TAX_EXC) {
            $emailVariables = array_merge($emailVariables, [
                '{total_products}' => $orderLocale->formatPrice($order->total_products, $orderCurrency->iso_code),
                '{total_discounts}' => $orderLocale->formatPrice($order->total_discounts_tax_excl, $orderCurrency->iso_code),
                '{total_shipping}' => $orderLocale->formatPrice($order->total_shipping_tax_excl, $orderCurrency->iso_code),
                '{total_wrapping}' => $orderLocale->formatPrice($order->total_wrapping_tax_excl, $orderCurrency->iso_code),
            ]);
        } else {
            $emailVariables = array_merge($emailVariables, [
                '{total_products}' => $orderLocale->formatPrice($order->total_products_wt, $orderCurrency->iso_code),
                '{total_discounts}' => $orderLocale->formatPrice($order->total_discounts, $orderCurrency->iso_code),
                '{total_shipping}' => $orderLocale->formatPrice($order->total_shipping, $orderCurrency->iso_code),
                '{total_wrapping}' => $orderLocale->formatPrice($order->total_wrapping, $orderCurrency->iso_code),
            ]);
        }

        // Join PDF invoice if configured for current order status
        if ((int) Configuration::get('PS_INVOICE') && $order_status->invoice && $order->invoice_number) {

            // Assign order language into context
            Context::getContext()->language = $orderLanguage;
            Context::getContext()->getTranslator()->setLocale($orderLanguage->locale);

            // Get invoices and render it
            $order_invoice_list = $order->getInvoicesCollection();
            Hook::exec('actionPDFInvoiceRender', ['order_invoice_list' => $order_invoice_list]);
            $pdf = new PDF($order_invoice_list, PDF::TEMPLATE_INVOICE, Context::getContext()->smarty);
            $attachedInvoice['content'] = $pdf->render(false);
            $attachedInvoice['name'] = $pdf->getFilename();
            $attachedInvoice['mime'] = 'application/pdf';
        } else {
            $attachedInvoice = null;
        }

        if (!empty($command->getExtraVariables())) {
            $data = array_merge($emailVariables, $command->getExtraVariables());
        }

        Mail::Send(
            (int) $order->id_lang,
            'order_conf',
            $this->translator->trans(
                'Order confirmation',
                [],
                'Emails.Subject',
                $orderLanguage->locale
            ),
            $emailVariables,
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname,
            null,
            null,
            $attachedInvoice,
            null,
            _PS_MAIL_DIR_,
            false,
            (int) $order->id_shop
        );
    }

    /**
     * @param Address $the_address that needs to be txt formatted
     * @param string $line_sep
     * @param array $fields_style
     *
     * @return string the txt formated address block
     */
    protected function _getFormatedAddress(Address $the_address, $line_sep, $fields_style = [])
    {
        return AddressFormat::generateAddress($the_address, ['avoid' => []], $line_sep, ' ', $fields_style);
    }
}
