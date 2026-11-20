<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use DateTime;
use PrestaShop\PrestaShop\Adapter\Invoice\OrderInvoiceDataProvider;
use PrestaShop\PrestaShop\Adapter\PDF\PDFGenerator;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Component\PdfResponse;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin controller for the Order Delivery.
 */
class DeliveryController extends PrestaShopAdminController
{
    /**
     * Main page for Delivery slips.
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('create', request.get('_legacy_controller')) || is_granted('delete', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function slipAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.order.delivery.slip.options.form_handler')] FormHandlerInterface $formHandler,
        #[Autowire(service: 'prestashop.adapter.order.delivery.slip.pdf.form_handler')] FormHandlerInterface $pdfFormHandler,
    ): Response {
        /** @var Form $form */
        $form = $formHandler->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()
            && $this->isGranted('update', $request->attributes->get('_legacy_controller')
            )) {
            $errors = $formHandler->save($form->getData());
            if (empty($errors)) {
                $this->addFlash(
                    'success',
                    $this->trans('Update successful', [], 'Admin.Notifications.Success')
                );
            } else {
                $this->addFlashErrors($errors);
            }

            return $this->redirectToRoute('admin_order_delivery_slip');
        }

        return $this->render('@PrestaShop/Admin/Sell/Order/Delivery/slip.html.twig', [
            'optionsForm' => $form->createView(),
            'pdfForm' => $pdfFormHandler->getForm()->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('Delivery slips', [], 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
        ]);
    }

    /**
     * Delivery slips PDF generator.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('create', request.get('_legacy_controller')) || is_granted('delete', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function generatePdfAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.order.delivery.slip.pdf.form_handler')] FormHandlerInterface $formHandler,
        #[Autowire(service: 'prestashop.adapter.data_provider.order_invoice')] OrderInvoiceDataProvider $orderInvoiceDataProvider,
        #[Autowire(service: 'prestashop.adapter.pdf.generator.delivery_slip')] PDFGenerator $deliverySlipPdfGenerator,
    ) {
        /** @var Form $form */
        $form = $formHandler->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $errors = $formHandler->save($data);
            if (empty($errors)) {
                return new PdfResponse($deliverySlipPdfGenerator->generatePDFForResponse(
                    $orderInvoiceDataProvider->getByDeliveryDateInterval(new DateTime($data['date_from']), new DateTime($data['date_to']))
                ));
            }
        }

        if (!empty($errors)) {
            $this->addFlashErrors($errors);
        }

        return $this->redirectToRoute('admin_order_delivery_slip');
    }
}
