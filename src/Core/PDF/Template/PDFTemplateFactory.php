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
use IteratorAggregate;

class PDFTemplateFactory
{
    private IteratorAggregate $templates;

    /**
     * Should be an array of all available templates,
     * probably gotten from the services using tags so modules can inject their own templates or modify existing ones
     */
    public function __construct(IteratorAggregate $templates)
    {
         $this->templates = $templates;
    }

    public function getTemplate(string $templateName, array $data): PDFTemplate
    {
        foreach ($this->templates as $template) {
            if ($templateName === $template->getTemplateName()) {
                Hook::exec('actionPDFInvoiceRender', $data);

                $template->init($data);
                return $template;
            }
        }

        throw new \Exception();
    }
}
