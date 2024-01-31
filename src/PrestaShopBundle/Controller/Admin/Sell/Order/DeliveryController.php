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

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin controller for the Order Delivery.
 */
class DeliveryController extends FrameworkBundleAdminController
{
    /**
     * Main page for Delivery slips.
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('create', request.get('_legacy_controller')) || is_granted('delete', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function slipAction(Request $request): Response
    {
        /** @var FormHandlerInterface $formHandler */
        $formHandler = $this->get('prestashop.adapter.order.delivery.slip.options.form_handler');
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
                    $this->trans('Update successful', 'Admin.Notifications.Success')
                );
            } else {
                $this->flashErrors($errors);
            }

            return $this->redirectToRoute('admin_order_delivery_slip');
        }

        return $this->render('@PrestaShop/Admin/Sell/Order/Delivery/slip.html.twig', [
            'optionsForm' => $form->createView(),
            'pdfForm' => $this->get('prestashop.adapter.order.delivery.slip.pdf.form_handler')->getForm()->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('Delivery slips', 'Admin.Navigation.Menu'),
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
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('create', request.get('_legacy_controller')) || is_granted('delete', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function generatePdfAction(Request $request)
    {
        /** @var FormHandlerInterface $formHandler */
        $formHandler = $this->get('prestashop.adapter.order.delivery.slip.pdf.form_handler');
        /** @var Form $form */
        $form = $formHandler->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $formHandler->save($form->getData());
            if (empty($errors)) {
                $pdf = $form->getData();

                return $this->redirect(
                    $this->get('prestashop.adapter.legacy.context')->getAdminLink(
                        'AdminPdf',
                        true,
                        [
                            'date_from' => $pdf['date_from'],
                            'date_to' => $pdf['date_to'],
                            'submitAction' => 'generateDeliverySlipsPDF',
                        ]
                    )
                );
            }
        }

        if (!empty($errors)) {
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('admin_order_delivery_slip');
    }
}
