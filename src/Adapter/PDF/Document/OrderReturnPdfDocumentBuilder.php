<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF\Document;

use Address;
use AddressFormat;
use Configuration;
use Context;
use Hook;
use Order;
use OrderReturn;
use PDF;
use PrestaShop\PrestaShop\Adapter\PDF\PdfDocumentCommonDataBuilder;
use PrestaShop\PrestaShop\Adapter\PDF\PdfTemplateResolver;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentBuilderInterface;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentInterface;
use Shop;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

/**
 * Builds an order return PDF document (see legacy classes/pdf/HTMLTemplateOrderReturn.php).
 *
 * @internal
 */
final class OrderReturnPdfDocumentBuilder implements PdfDocumentBuilderInterface
{
    public function __construct(
        private readonly PdfTemplateResolver $templateResolver,
        private readonly PdfDocumentCommonDataBuilder $commonDataBuilder,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function supports(string $type): bool
    {
        return PDF::TEMPLATE_ORDER_RETURN === $type;
    }

    public function getBulkFilename(): string
    {
        // Legacy quirk kept as-is: HTMLTemplateOrderReturn::getBulkFilename() returns the same
        // filename as the invoice bulk PDF ('invoices.pdf'), not an order-return-specific name.
        return 'invoices.pdf';
    }

    public function build($object, bool $bulkMode): PdfDocumentInterface
    {
        /** @var OrderReturn $orderReturn */
        $orderReturn = $object;
        $order = new Order((int) $orderReturn->id_order);
        $shop = new Shop((int) $order->id_shop);

        $languageId = (int) Context::getContext()->language->id;
        $prefix = Configuration::get('PS_RETURN_PREFIX', $languageId);
        $titleFormat = $this->translator->trans('%1$s%2$06d', [], 'Shop.Pdf');
        $title = sprintf($titleFormat, $prefix, $orderReturn->id);
        $date = Tools::displayDate($order->invoice_date);

        $header = $this->templateResolver->render(
            'header',
            $this->commonDataBuilder->buildHeaderData($shop, $title, $date, $this->translator->trans('Order return', [], 'Shop.Pdf'))
        );
        $footer = $this->templateResolver->render(
            'footer',
            // Unlike Invoice, HTMLTemplateOrderReturn never overrides HTMLTemplate::$available_in_your_account,
            // whose default is `true`.
            $this->commonDataBuilder->buildFooterData($shop, true)
        );
        $pagination = $this->templateResolver->render('pagination', []);
        $content = $this->getContent($order, $orderReturn, $shop);

        $filename = Configuration::get('PS_RETURN_PREFIX', $languageId, null, (int) $order->id_shop) . sprintf('%06d', $orderReturn->id) . '.pdf';

        return new GenericPdfDocument($header, $footer, $pagination, $content, $filename);
    }

    private function getContent(Order $order, OrderReturn $orderReturn, Shop $shop): string
    {
        $deliveryAddress = new Address((int) $order->id_address_delivery);
        $formattedDeliveryAddress = AddressFormat::generateAddress($deliveryAddress, [], '<br />', ' ');

        $formattedInvoiceAddress = '';
        if ($order->id_address_delivery != $order->id_address_invoice) {
            $invoiceAddress = new Address((int) $order->id_address_invoice);
            $formattedInvoiceAddress = AddressFormat::generateAddress($invoiceAddress, [], '<br />', ' ');
        }

        $variables = [
            'order_return' => $orderReturn,
            'order_return_number_formatted' => sprintf('%06d', $orderReturn->id),
            'order_return_date_formatted' => Tools::displayDate($orderReturn->date_add),
            'return_nb_days' => (int) Configuration::get('PS_ORDER_RETURN_NB_DAYS'),
            'products' => OrderReturn::getOrdersReturnProducts((int) $orderReturn->id, $order),
            'delivery_address' => $formattedDeliveryAddress,
            'invoice_address' => $formattedInvoiceAddress,
            'shop_address' => AddressFormat::generateAddress($shop->getAddress(), [], '<br />', ' '),
            'hook_display_pdf' => Hook::exec('displayPDFOrderReturn', ['object' => $orderReturn]),
        ];

        $tpls = [
            'style_tab' => $this->templateResolver->render('style-tab', $variables),
            'addresses_tab' => $this->templateResolver->render('order-return/addresses-tab', $variables),
            'summary_tab' => $this->templateResolver->render('order-return/summary-tab', $variables),
            'product_tab' => $this->templateResolver->render('order-return/product-tab', $variables),
            'conditions_tab' => $this->templateResolver->render('order-return/conditions-tab', $variables),
        ];

        return $this->templateResolver->render('order-return/order-return', array_merge($variables, $tpls));
    }
}
