<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
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
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function generatePdfByDateAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.order.invoices.by_date.form_handler')] FormHandlerInterface $formHandler
    ) {
        $this->processForm($formHandler, $request);

        return $this->redirectToRoute('admin_order_invoices');
    }

    /**
     * Action that generates invoices PDF by order status.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function generatePdfByStatusAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.order.invoices.by_status.form_handler')] FormHandlerInterface $formHandler
    ) {
        $this->processForm($formHandler, $request);

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
     * @return bool false if an error occurred, true otherwise
     */
    private function processForm(FormHandlerInterface $formHandler, Request $request)
    {
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($errors = $formHandler->save($form->getData())) {
                $this->addFlashErrors($errors);

                return false;
            }
        }

        return true;
    }

    /**
     * Generates PDF of given invoice ID.
     *
     * @param int $invoiceId
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function generatePdfByIdAction(
        int $invoiceId,
        #[Autowire(service: 'prestashop.adapter.pdf.generator.single_invoice')] PDFGeneratorInterface $invoicePdfGenerator
    ) {
        $invoicePdfGenerator->generatePDF([$invoiceId]);

        // When using legacy generator,
        // we want to be sure that displaying PDF is the last thing this controller will do
        die;
    }
}
