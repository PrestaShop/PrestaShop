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

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Context;
use Hook;
use OrderInvoice;
use PDF;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use RuntimeException;
use Validate;

/**
 * Generates invoice by invoice ID.
 */
final class InvoicePdfGenerator implements PDFGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generatePDF(array $invoiceId): void
    {
        if (count($invoiceId) !== 1) {
            throw new CoreException(sprintf('"%s" supports generating PDF for single invoice only.', self::class));
        }

        $invoiceId = reset($invoiceId);
        $orderInvoice = new OrderInvoice((int) $invoiceId);
        if (!Validate::isLoadedObject($orderInvoice)) {
            throw new RuntimeException('The invoice cannot be found within your database.');
        }

        Hook::exec('actionPDFInvoiceRender', ['order_invoice_list' => [$orderInvoice]]);

        $pdf = new PDF($orderInvoice, PDF::TEMPLATE_INVOICE, Context::getContext()->smarty);
        $pdf->render();
    }
}
