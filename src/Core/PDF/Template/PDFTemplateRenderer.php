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

use PrestaShop\PrestaShop\Core\PDF\PDFRenderer;

/**
 * This class is responsible for rendering PDF templates in PrestaShop.
 * It takes a PDFTemplate object and uses it as a template that is shown in browser
 */
class PDFTemplateRenderer
{
    protected PDFTemplateFactory $templateFactory;

    protected PDFRenderer $pdfRenderer;

    public function __construct(
        PDFTemplateFactory $templateFactory,
        PDFRenderer        $pdfRenderer
    ) {
        $this->templateFactory = $templateFactory;
        $this->pdfRenderer = $pdfRenderer;
    }

    public function render(PDFTemplate $template, string $display = 'D'): string
    {
        $this->pdfRenderer->startPageGroup();

        /** Data should be assigned to template here */
        $this->pdfRenderer->createHeader($template->getHeader());
        $this->pdfRenderer->createPagination($template->getPagination());
        $this->pdfRenderer->createContent($template->getContent());
        $this->pdfRenderer->writePage();
        // The footer must be added after adding the page, or TCPDF will
        // add the footer for the next page from on the last page of this
        // page group, which could mean the wrong store info is rendered.
        $this->pdfRenderer->createFooter($template->getFooter());


        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }

        return $this->pdfRenderer->render($template->getFileName(), $display);
    }
}
