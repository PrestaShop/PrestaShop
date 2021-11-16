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
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\InvoiceConfigurationError;
use PrestaShop\PrestaShop\Core\Order\OrderInvoiceDataProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Order\Invoices\GenerateByDateType;
use PrestaShopBundle\Form\Admin\Sell\Order\Invoices\InvoiceOptionsType;
use PrestaShopBundle\Form\Admin\Sell\Order\Invoices\InvoicesByDateDataProvider;
use PrestaShopBundle\Form\Exception\DataProviderException;

class InvoiceByDateDataProviderTest extends TestCase
{
    /**
     * Tests that exception is thrown if invoice number is incorrect
     */
    public function testValidateFailsOnIncorrectInvoiceNumber(): void
    {
        $orderInvoiceDataProvider = $this->getMockBuilder(OrderInvoiceDataProviderInterface::class)->getMock();
        $orderInvoiceDataProvider->method('getByDateInterval')->willReturn(null);
        $orderInvoiceByDateDataProvider = new InvoicesByDateDataProvider($orderInvoiceDataProvider);
        $data = [
            GenerateByDateType::FIELD_DATE_FROM => '2021-05-09',
            GenerateByDateType::FIELD_DATE_TO => '2021-07-09',
        ];
        $exceptionThrown = false;
        $error = new InvoiceConfigurationError(
            InvoiceConfigurationError::ERROR_NO_INVOICES_FOUND,
            GenerateByDateType::FIELD_DATE_TO
        );
        try {
            $orderInvoiceByDateDataProvider->setData($data);
        } catch (DataProviderException $e) {
            $exceptionThrown = true;
            $this->assertContainsEquals($error, $e->getConfigurationErrors());
        }

        if (!$exceptionThrown) {
            $this->fail('Expected exception DataProviderException was not thrown');
        }
    }

    /**
     * Tests that no exceptions are thrown if data is correct
     *
     * @doesNotPerformAssertions
     *
     * @param array $data
     */
    public function testValidatePassesWithCorrectData(): void
    {
        $orderInvoiceDataProvider = $this->getMockBuilder(OrderInvoiceDataProviderInterface::class)->getMock();
        $orderInvoiceDataProvider->method('getByDateInterval')->willReturn(
            ['id_invoice' => 5]
        );
        $orderInvoiceByDateDataProvider = new InvoicesByDateDataProvider($orderInvoiceDataProvider);
        $data = [
            GenerateByDateType::FIELD_DATE_FROM => '2021-05-09',
            GenerateByDateType::FIELD_DATE_TO => '2021-07-09',
        ];
        $orderInvoiceByDateDataProvider->setData($data);
    }

    public function getInvoiceNoHtmlFields(): array
    {
        return [
            [InvoiceOptionsType::FIELD_INVOICE_PREFIX],
            [InvoiceOptionsType::FIELD_LEGAL_FREE_TEXT],
            [InvoiceOptionsType::FIELD_FOOTER_TEXT],
        ];
    }
}
