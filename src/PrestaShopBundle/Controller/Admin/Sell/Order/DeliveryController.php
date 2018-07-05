<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PrestaShop\PrestaShop\Core\Form\FormHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;

/**
 * Admin controller for the Order Delivery.
 */
class DeliveryController extends FrameworkBundleAdminController
{
    /**
     * Main page for Delivery slips.
     *
     * @Template("@PrestaShop/Admin/Sell/Order/Delivery/slip.html.twig")
     * @AdminSecurity("is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function slipAction(Request $request)
    {
        /* @var $formHandler FormHandler */
        $formHandler = $this->get('prestashop.adapter.order.delivery.slip.options.form_handler');
        /* @var $form Form */
        $form = $formHandler->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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

        return [
            'optionsForm' => $form->createView(),
            'pdfForm' => $this->get('prestashop.adapter.order.delivery.slip.pdf.form_handler')->getForm()->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('Delivery Slips', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => false,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
        ];
    }

    /**
     * Delivery slips PDF generator.
     *
     * @AdminSecurity("is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function generatePdfAction(Request $request)
    {
        /* @var $formHandler FormHandler */
        $formHandler = $this->get('prestashop.adapter.order.delivery.slip.pdf.form_handler');
        /* @var $form Form */
        $form = $formHandler->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $formHandler->save($form->getData());
            if (empty($errors)) {
                $pdf = $form->get('pdf')->getData();

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
