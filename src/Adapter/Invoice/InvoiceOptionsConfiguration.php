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

namespace PrestaShop\PrestaShop\Adapter\Invoice;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * Class InvoiceOptionsConfiguration is responsible for saving & loading invoice configuration.
 */
final class InvoiceOptionsConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'enable_invoices' => $this->configuration->getBoolean('PS_INVOICE'),
            'enable_tax_breakdown' => $this->configuration->getBoolean('PS_INVOICE_TAXES_BREAKDOWN'),
            'enable_product_images' => $this->configuration->getBoolean('PS_PDF_IMG_INVOICE'),
            'invoice_prefix' => $this->configuration->get('PS_INVOICE_PREFIX'),
            'add_current_year' => $this->configuration->getBoolean('PS_INVOICE_USE_YEAR'),
            'reset_number_annually' => $this->configuration->getBoolean('PS_INVOICE_RESET'),
            'year_position' => $this->configuration->getInt('PS_INVOICE_YEAR_POS'),
            'invoice_number' => $this->configuration->getInt('PS_INVOICE_START_NUMBER'),
            'legal_free_text' => $this->configuration->get('PS_INVOICE_LEGAL_FREE_TEXT'),
            'footer_text' => $this->configuration->get('PS_INVOICE_FREE_TEXT'),
            'invoice_model' => $this->configuration->get('PS_INVOICE_MODEL'),
            'use_disk_cache' => $this->configuration->getBoolean('PS_PDF_USE_CACHE'),
            'invoice_language' => $this->configuration->getBoolean('PS_PDF_LANGUAGE_INVOICE'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_INVOICE', $configuration['enable_invoices']);
            $this->configuration->set('PS_INVOICE_TAXES_BREAKDOWN', $configuration['enable_tax_breakdown']);
            $this->configuration->set('PS_PDF_IMG_INVOICE', $configuration['enable_product_images']);
            $this->configuration->set('PS_INVOICE_PREFIX', $configuration['invoice_prefix']);
            $this->configuration->set('PS_INVOICE_USE_YEAR', $configuration['add_current_year']);
            $this->configuration->set('PS_INVOICE_RESET', $configuration['reset_number_annually']);
            $this->configuration->set('PS_INVOICE_YEAR_POS', $configuration['year_position']);
            $this->configuration->set('PS_INVOICE_START_NUMBER', $configuration['invoice_number']);
            $this->configuration->set('PS_INVOICE_LEGAL_FREE_TEXT', $configuration['legal_free_text']);
            $this->configuration->set('PS_INVOICE_FREE_TEXT', $configuration['footer_text']);
            $this->configuration->set('PS_INVOICE_MODEL', $configuration['invoice_model']);
            $this->configuration->set('PS_PDF_USE_CACHE', $configuration['use_disk_cache']);
            $this->configuration->set('PS_PDF_LANGUAGE_INVOICE', $configuration['invoice_language']);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['enable_invoices'],
            $configuration['enable_tax_breakdown'],
            $configuration['enable_product_images'],
            $configuration['invoice_prefix'],
            $configuration['add_current_year'],
            $configuration['reset_number_annually'],
            $configuration['year_position'],
            $configuration['invoice_number'],
            $configuration['legal_free_text'],
            $configuration['footer_text'],
            $configuration['invoice_model'],
            $configuration['use_disk_cache'],
            $configuration['invoice_language']
        );
    }
}
