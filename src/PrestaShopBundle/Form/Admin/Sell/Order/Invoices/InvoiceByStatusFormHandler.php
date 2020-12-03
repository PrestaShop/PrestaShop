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

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormHandler;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Order\OrderInvoiceDataProviderInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class InvoiceByStatusFormHandler manages the data manipulated using "By status" form
 * in "Sell > Orders > Invoices" page.
 */
final class InvoiceByStatusFormHandler extends FormHandler
{
    /**
     * @var OrderInvoiceDataProviderInterface
     */
    private $orderInvoiceDataProvider;

    /**
     * @var PDFGeneratorInterface
     */
    private $pdfGenerator;

    /**
     * @param FormBuilderInterface $formBuilder
     * @param HookDispatcherInterface $hookDispatcher
     * @param FormDataProviderInterface $formDataProvider
     * @param array $formTypes
     * @param string $hookName
     * @param OrderInvoiceDataProviderInterface $orderInvoiceDataProvider
     * @param PDFGeneratorInterface $pdfGenerator
     */
    public function __construct(
        FormBuilderInterface $formBuilder,
        HookDispatcherInterface $hookDispatcher,
        FormDataProviderInterface $formDataProvider,
        array $formTypes,
        $hookName,
        OrderInvoiceDataProviderInterface $orderInvoiceDataProvider,
        PDFGeneratorInterface $pdfGenerator
    ) {
        parent::__construct($formBuilder, $hookDispatcher, $formDataProvider, $formTypes, $hookName);
        $this->orderInvoiceDataProvider = $orderInvoiceDataProvider;
        $this->pdfGenerator = $pdfGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        if ($errors = parent::save($data)) {
            return $errors;
        }

        $invoiceCollection = [];

        foreach ($data['generate_by_status']['order_states'] as $orderStateId) {
            // Put invoices for each selected status into one collection
            $invoiceCollection = array_merge(
                $invoiceCollection,
                $this->orderInvoiceDataProvider->getByStatus($orderStateId)
            );
        }

        if (empty($invoiceCollection)) {
            $errors[] = [
                'key' => 'No invoice has been found for this status.',
                'domain' => 'Admin.Orderscustomers.Notification',
                'parameters' => [],
            ];
        } else {
            // Generate PDF out of found invoices
            $this->pdfGenerator->generatePDF($invoiceCollection);
        }

        return $errors;
    }
}
