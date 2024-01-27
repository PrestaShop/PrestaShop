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

namespace PrestaShop\PrestaShop\Core\PDF\Template;

use Hook;
use HTMLTemplateInvoice;
use PrestaShop\PrestaShop\Core\PDF\SmartyFactory;

/**
 * PDFTemplateInvoiceSmarty is a class responsible for rendering invoice PDF using Smarty.
 */
class PDFTemplateInvoiceSmarty implements PDFTemplate
{
    private ?HTMLTemplateInvoice $legacyTemplate;

    public const TEMPLATE_NAME = 'invoice';

    public function __construct(private SmartyFactory $smartyFactory)
    {
    }

    public function init(array $data): void
    {
        $invoice = $data['invoice'];
        Hook::exec('actionPDFInvoiceRender', ['order_invoice_list' => [$invoice]]);
        $this->legacyTemplate = new HTMLTemplateInvoice($invoice, $this->smartyFactory->getSmarty());
    }

    public function getHeader(): string
    {
        if (!$this->legacyTemplate) {
            throw new \Exception('You need to use init function first to initialize generator');
        }
        return $this->legacyTemplate->getHeader();
    }

    public function getFooter(): string
    {
        return $this->legacyTemplate->getFooter();
    }

    public function getPagination(): string
    {
        return $this->legacyTemplate->getPagination();
    }
    public function getContent(): string
    {
        return $this->legacyTemplate->getContent();
    }

    public function getFileName(): string
    {
        return $this->legacyTemplate->getFilename();
    }

    public function getTemplateName(): string
    {
        return self::TEMPLATE_NAME;
    }
}
