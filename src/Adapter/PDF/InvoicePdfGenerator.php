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

use Hook;
use OrderInvoice;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorSingleObjectInterface;
use PrestaShop\PrestaShop\Core\PDF\Template\PDFTemplateFactory;
use PrestaShop\PrestaShop\Core\PDF\Template\PDFTemplateInvoiceSmarty;
use PrestaShop\PrestaShop\Core\PDF\Template\PDFTemplateRenderer;
use RuntimeException;
use Validate;

/**
 * Generates invoice by invoice ID. */
final class InvoicePdfGenerator implements PDFGeneratorSingleObjectInterface
{
    private PDFTemplateFactory $templateFactory;
    private PDFTemplateRenderer $pdfTemplateRenderer;

    public function __construct(
        PDFTemplateFactory  $templateFactory,
        PDFTemplateRenderer $pdfTemplateRenderer,
    ) {
        $this->templateFactory = $templateFactory;
        $this->pdfTemplateRenderer = $pdfTemplateRenderer;
    }
    /**
     * {@inheritdoc}
     */
    public function generatePDF(int $objectId): void
    {
        $orderInvoice = new OrderInvoice($objectId);

        if (!Validate::isLoadedObject($orderInvoice)) {
            throw new RuntimeException('The invoice cannot be found within your database.');
        }

        $template = $this->templateFactory->getTemplate(PDFTemplateInvoiceSmarty::TEMPLATE_NAME, ['invoice' => $orderInvoice]);
        $this->pdfTemplateRenderer->render($template);
    }
}
