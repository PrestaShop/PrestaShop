<?php
/**
 * 2007-2018 PrestaShop
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
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller responsible of "Sell > Orders > Invoices" page
 */
class InvoicesController extends FrameworkBundleAdminController
{
    /**
     * Show order preferences page
     *
     * @param Request $request
     *
     * @Template("@PrestaShop/Admin/Sell/Order/Invoices/invoices.html.twig")
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @return array Template parameters
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $byDateForm = $this->get('prestashop.admin.order.invoices.by_date.form_handler')->getForm();
        $byStatusForm = $this->get('prestashop.admin.order.invoices.by_status.form_handler')->getForm();
        $optionsForm = $this->get('prestashop.admin.order.invoices.options.form_handler')->getForm();

        return [
            'layoutTitle' => $this->trans('Invoices', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'byDateForm' => $byDateForm->createView(),
            'byStatusForm' => $byStatusForm->createView(),
            'optionsForm' => $optionsForm->createView(),
        ];
    }

    /**
     * Action that generates invoices PDF by date interval
     *
     * @param Request $request
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @return RedirectResponse
     */
    public function generatePdfByDateAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.order.invoices.by_date.form_handler');

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($errors = $formHandler->save($form->getData())) {
                $this->flashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_order_invoices');
    }

    /**
     * Action that generates invoices PDF by order status
     *
     * @param Request $request
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')~'_')", message="Access denied.")

     * @return RedirectResponse
     */
    public function generatePdfByStatusAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.order.invoices.by_status.form_handler');

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($errors = $formHandler->save($form->getData())) {
                $this->flashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_order_invoices');
    }

    /**
     * Process the Invoice Options configuration form.
     *
     * @param Request $request
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @return RedirectResponse
     */
    public function processAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.order.invoices.options.form_handler');

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($errors = $formHandler->save($form->getData())) {
                $this->flashErrors($errors);
            } else {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            }
        }

        return $this->redirectToRoute('admin_order_invoices');
    }
}
