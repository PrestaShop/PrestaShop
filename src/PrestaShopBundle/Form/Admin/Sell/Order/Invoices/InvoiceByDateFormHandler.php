<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use DateTime;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\Handler;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Order\OrderInvoiceDataProviderInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class InvoiceByDateFormHandler manages the data manipulated using "By date" form
 * in "Sell > Orders > Invoices" page.
 */
final class InvoiceByDateFormHandler extends Handler
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
        string $form,
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

        // Get invoices by submitted date interval
        $invoiceCollection = $this->orderInvoiceDataProvider->getByDateInterval(
            new DateTime($data['date_from']),
            new DateTime($data['date_to'])
        );

        // Generate PDF out of found invoices
        $this->pdfGenerator->generatePDF($invoiceCollection);

        return [];
    }
}
