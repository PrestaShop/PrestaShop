<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\Handler;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Order\OrderInvoiceDataProviderInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class InvoiceByStatusFormHandler manages the data manipulated using "By status" form
 * in "Sell > Orders > Invoices" page.
 */
final class InvoiceByStatusFormHandler extends Handler
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
     * @param FormFactoryInterface $formFactory
     * @param HookDispatcherInterface $hookDispatcher
     * @param FormDataProviderInterface $formDataProvider
     * @param string $form
     * @param string $hookName
     * @param OrderInvoiceDataProviderInterface $orderInvoiceDataProvider
     * @param PDFGeneratorInterface $pdfGenerator
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        HookDispatcherInterface $hookDispatcher,
        FormDataProviderInterface $formDataProvider,
        $form,
        $hookName,
        OrderInvoiceDataProviderInterface $orderInvoiceDataProvider,
        PDFGeneratorInterface $pdfGenerator
    ) {
        parent::__construct($formFactory, $hookDispatcher, $formDataProvider, $form, $hookName);
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

        foreach ($data['order_states'] as $orderStateId) {
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
