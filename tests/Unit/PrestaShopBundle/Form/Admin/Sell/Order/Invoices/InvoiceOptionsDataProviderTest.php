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

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\ConfigurationErrorInterface;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\InvoicesConfigurationError;
use PrestaShopBundle\Form\Admin\Sell\Order\Invoices\InvoiceOptionsDataProvider;
use PrestaShopBundle\Form\Admin\Sell\Order\Invoices\InvoiceOptionsType;
use PrestaShopBundle\Form\Exception\DataProviderException;

class InvoiceOptionsDataProviderTest extends TestCase
{
    /**
     * Tests that exception is thrown if invoice number is incorrect
     */
    public function testValidateFailsOnIncorrectInvoiceNumber(): void
    {
        $dataConfiguration = $this->getMockBuilder(DataConfigurationInterface::class)->getMock();
        $invoiceOptionsDataProvider = new InvoiceOptionsDataProvider($dataConfiguration, 5);
        $data = [
            InvoiceOptionsType::FIELD_INVOICE_NUMBER => 4,
        ];
        $exceptionThrown = false;
        $configurationError = new InvoicesConfigurationError(InvoicesConfigurationError::ERROR_INCORRECT_INVOICE_NUMBER, InvoiceOptionsType::FIELD_INVOICE_NUMBER);
        try {
            $invoiceOptionsDataProvider->setData($data);
        } catch (DataProviderException $e) {
            $exceptionThrown = true;
            $this->assertContainsEquals($configurationError, $e->getConfigurationErrors());
        }

        if (!$exceptionThrown) {
            $this->fail('Expected exception DataProviderException was not thrown');
        }
    }

    /**
     * Tests that all 3 text fields throw an exception if you pass HTML
     *
     * @dataProvider getInvoiceNoHtmlFields
     *
     * @param string $field
     */
    public function testValidateFailsOnHtmlTags(string $field): void
    {
        $dataConfiguration = $this->getMockBuilder(DataConfigurationInterface::class)->getMock();
        $invoiceOptionsDataProvider = new InvoiceOptionsDataProvider($dataConfiguration, 5);
        $data = [
            $field => [
                1 => 'text',
                2 => '<html>',
            ],
        ];

        $configurationError = new InvoicesConfigurationError(ConfigurationErrorInterface::ERROR_CONTAINS_HTML_TAGS, $field, 2);

        $exceptionThrown = false;
        try {
            $invoiceOptionsDataProvider->setData($data);
        } catch (DataProviderException $e) {
            $exceptionThrown = true;
            $this->assertContainsEquals($configurationError, $e->getConfigurationErrors());
        }

        if (!$exceptionThrown) {
            $this->fail('Expected exception DataProviderException was not thrown');
        }
    }

    /**
     * Tests that no exceptions are thrown if data is correct
     *
     * @dataProvider getCorrectInvoiceData
     *
     * @doesNotPerformAssertions
     *
     * @param array $data
     */
    public function testValidatePassesWithCorrectData(array $data): void
    {
        $dataConfiguration = $this->getMockBuilder(DataConfigurationInterface::class)->getMock();
        $invoiceOptionsDataProvider = new InvoiceOptionsDataProvider($dataConfiguration, 5);
        $invoiceOptionsDataProvider->setData($data);
    }

    public function getInvoiceNoHtmlFields(): array
    {
        return [
            [InvoiceOptionsType::FIELD_INVOICE_PREFIX],
            [InvoiceOptionsType::FIELD_LEGAL_FREE_TEXT],
            [InvoiceOptionsType::FIELD_FOOTER_TEXT],
        ];
    }

    public function getCorrectInvoiceData(): array
    {
        return [
            [
                [
                    InvoiceOptionsType::FIELD_INVOICE_PREFIX => [
                        1 => 'data',
                    ],
                    InvoiceOptionsType::FIELD_LEGAL_FREE_TEXT => [
                        1 => 'data',
                    ],
                    InvoiceOptionsType::FIELD_FOOTER_TEXT => [
                        1 => 'data',
                    ],
                    InvoiceOptionsType::FIELD_INVOICE_NUMBER => 6,
                ],
            ],
            [
                [
                    InvoiceOptionsType::FIELD_INVOICE_PREFIX => [
                        1 => 'data',
                    ],
                    InvoiceOptionsType::FIELD_LEGAL_FREE_TEXT => [
                        1 => 'data',
                    ],
                    InvoiceOptionsType::FIELD_FOOTER_TEXT => [
                        1 => 'data',
                    ],
                    InvoiceOptionsType::FIELD_INVOICE_NUMBER => 0,
                ],
            ],
        ];
    }
}
