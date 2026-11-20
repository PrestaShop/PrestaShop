<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use DateTime;
use PrestaShop\PrestaShop\Adapter\PDF\InvoicePdfGenerator;
use PrestaShop\PrestaShop\Adapter\PDF\PDFGenerator;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Order\OrderInvoiceDataProviderInterface;
use PrestaShopBundle\Component\PdfResponse;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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
        #[Autowire(service: 'prestashop.admin.order.invoices.by_date.form_handler')] FormHandlerInterface $formHandler,
        #[Autowire(service: 'prestashop.adapter.data_provider.order_invoice')] OrderInvoiceDataProviderInterface $orderInvoiceDataProvider,
        #[Autowire(service: 'prestashop.adapter.pdf.generator.invoice')] PDFGenerator $invoicePdfGenerator,
    ) {
        $data = $this->getValidatedFormData($formHandler, $request);
        if (null === $data) {
            return $this->redirectToRoute('admin_order_invoices');
        }

        return new PdfResponse($invoicePdfGenerator->generatePDFForResponse(
            $orderInvoiceDataProvider->getByDateInterval(new DateTime($data['date_from']), new DateTime($data['date_to']))
        ));
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
        #[Autowire(service: 'prestashop.admin.order.invoices.by_status.form_handler')] FormHandlerInterface $formHandler,
        #[Autowire(service: 'prestashop.adapter.data_provider.order_invoice')] OrderInvoiceDataProviderInterface $orderInvoiceDataProvider,
        #[Autowire(service: 'prestashop.adapter.pdf.generator.invoice')] PDFGenerator $invoicePdfGenerator,
    ) {
        $data = $this->getValidatedFormData($formHandler, $request);
        if (null === $data) {
            return $this->redirectToRoute('admin_order_invoices');
        }

        $orderInvoices = [];
        foreach ($data['order_states'] as $orderStateId) {
            $orderInvoices = array_merge($orderInvoiceDataProvider->getByStatus((int) $orderStateId), $orderInvoices);
        }

        return new PdfResponse($invoicePdfGenerator->generatePDFForResponse($orderInvoices));
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
        if (null !== $this->getValidatedFormData($formHandler, $request)) {
            $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
        }

        return $this->redirectToRoute('admin_order_invoices');
    }

    /**
     * @return array|null the submitted data once the form handler validated and saved it
     */
    private function getValidatedFormData(FormHandlerInterface $formHandler, Request $request): ?array
    {
        $form = $formHandler->getForm();
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            return null;
        }
        if (!$form->isValid()) {
            $this->addFlashFormErrors($form, true);

            return null;
        }

        $data = $form->getData();
        if ($errors = $formHandler->save($data)) {
            $this->addFlashErrors($errors);

            return null;
        }

        return $data;
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
        return new PdfResponse($invoicePdfGenerator->generatePDFForResponse([$invoiceId]));
    }
}
