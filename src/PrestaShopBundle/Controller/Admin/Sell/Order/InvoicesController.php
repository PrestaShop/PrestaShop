<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use DateTime;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\PDF\InvoicePdfGenerator;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Order\OrderInvoiceDataProviderInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible of "Sell > Orders > Invoices" page.
 */
class InvoicesController extends PrestaShopAdminController
{
    /**
     * Show order preferences page.
     *
     * @param Request $request
     *
     * @return Response Template parameters
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.order.invoices.by_date.form_handler')] FormHandlerInterface $byDateForm,
        #[Autowire(service: 'prestashop.admin.order.invoices.by_status.form_handler')] FormHandlerInterface $byStatusForm,
        #[Autowire(service: 'prestashop.admin.order.invoices.options.form_handler')] FormHandlerInterface $optionsForm,
    ): Response {
        $legacyController = $request->attributes->get('_legacy_controller');

        return $this->render('@PrestaShop/Admin/Sell/Order/Invoices/invoices.html.twig', [
            'layoutTitle' => $this->trans('Invoices', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'generateByDateForm' => $byDateForm->getForm()->createView(),
            'generateByStatusForm' => $byStatusForm->getForm()->createView(),
            'invoiceOptionsForm' => $optionsForm->getForm()->createView(),
        ]);
    }

    /**
     * Action that generates invoices PDF by date interval.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function generatePdfByDateAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.order.invoices.by_date.pdf_form_handler')] FormHandlerInterface $formHandler,
        #[Autowire(service: 'prestashop.adapter.data_provider.order_invoice')] OrderInvoiceDataProviderInterface $orderInvoiceDataProvider,
        #[Autowire(service: 'prestashop.adapter.pdf.generator.single_invoice')] InvoicePdfGenerator $invoicePdfGenerator,
    ) {
        // Process the invoice filters and use the submitted data after validation
        $form = $this->processForm($formHandler, $request);
        if (null !== $form && $form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Get invoices by the submitted date interval
            $invoices = $orderInvoiceDataProvider->getByDateInterval(new DateTime($data['date_from']), new DateTime($data['date_to']));

            // Generate the PDF content for the selected invoice IDs
            $invoiceIds = array_map(static fn (OrderInvoice $invoice): int => (int) $invoice->id, $invoices);
            $generatedPdf = $invoicePdfGenerator->generatePDFForResponse($invoiceIds);

            // Send the generated document as a PDF attachment
            $response = new Response($generatedPdf->getContent());
            $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $generatedPdf->getFileName());
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        // Show the invoice page when no valid filter form was submitted
        return $this->redirectToRoute('admin_order_invoices');
    }

    /**
     * Action that generates invoices PDF by order status.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function generatePdfByStatusAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.order.invoices.by_status.pdf_form_handler')] FormHandlerInterface $formHandler,
        #[Autowire(service: 'prestashop.adapter.data_provider.order_invoice')] OrderInvoiceDataProviderInterface $orderInvoiceDataProvider,
        #[Autowire(service: 'prestashop.adapter.pdf.generator.single_invoice')] InvoicePdfGenerator $invoicePdfGenerator,
    ) {
        // Process the invoice filters and use the submitted data after validation
        $form = $this->processForm($formHandler, $request);
        if (null !== $form && $form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Collect invoices for each selected order status
            $invoices = [];
            foreach ($data['order_states'] as $orderStateId) {
                $invoices = array_merge($invoices, $orderInvoiceDataProvider->getByStatus($orderStateId));
            }

            // Report when none of the selected statuses have invoices
            if (empty($invoices)) {
                $this->addFlash('error', $this->trans('No invoice has been found for this status.', [], 'Admin.Orderscustomers.Notification'));

                return $this->redirectToRoute('admin_order_invoices');
            }

            // Generate the PDF content for the selected invoice IDs
            $invoiceIds = array_map(static fn (OrderInvoice $invoice): int => (int) $invoice->id, $invoices);
            $generatedPdf = $invoicePdfGenerator->generatePDFForResponse($invoiceIds);

            // Send the generated document as a PDF attachment
            $response = new Response($generatedPdf->getContent());
            $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $generatedPdf->getFileName());
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        // Show the invoice page when no valid filter form was submitted
        return $this->redirectToRoute('admin_order_invoices');
    }

    /**
     * Process the Invoice Options configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'Access denied.', redirectRoute: 'admin_order_invoices')]
    public function processAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.order.invoices.options.form_handler')] FormHandlerInterface $formHandler,
    ) {
        if ($this->processForm($formHandler, $request)) {
            $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
        }

        return $this->redirectToRoute('admin_order_invoices');
    }

    /**
     * Processes the form in a generic way.
     *
     * @param FormHandlerInterface $formHandler
     * @param Request $request
     *
     * @return FormInterface|null The processed form, or null if validation or saving failed
     */
    private function processForm(FormHandlerInterface $formHandler, Request $request): ?FormInterface
    {
        // Bind request data to the form
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        // Validate submitted data before saving and running the form save hook
        if ($form->isSubmitted()) {
            if ($errors = $formHandler->save($form->getData())) {
                $this->addFlashErrors($errors);

                return null;
            }
        }

        return $form;
    }

    /**
     * Generates PDF of given invoice ID.
     *
     * @param int $invoiceId
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function generatePdfByIdAction(
        int $invoiceId,
        #[Autowire(service: 'prestashop.adapter.pdf.generator.single_invoice')] InvoicePdfGenerator $invoicePdfGenerator
    ): Response {
        $generatedPdf = $invoicePdfGenerator->generatePDFForResponse([$invoiceId]);

        $response = new Response($generatedPdf->getContent());
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $generatedPdf->getFileName()
        );
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
