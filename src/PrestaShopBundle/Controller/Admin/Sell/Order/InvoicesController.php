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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible of "Sell > Orders > Invoices" page.
 */
class InvoicesController extends FrameworkBundleAdminController
{
    /**
     * Show order preferences page.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response|null Template parameters
     *
     * @throws \Exception
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     */
    public function indexAction(Request $request)
    {
        $byDateForm = $this->get('prestashop.admin.order.invoices.by_date.form_handler')->getForm();
        $byStatusForm = $this->get('prestashop.admin.order.invoices.by_status.form_handler')->getForm();
        $optionsForm = $this->get('prestashop.admin.order.invoices.options.form_handler')->getForm();

        return $this->renderForm($byDateForm, $byStatusForm, $optionsForm, $request);
    }

    /**
     * Action that generates invoices PDF by date interval.
     *
     * @param Request $request
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return Response
     */
    public function generatePdfByDateAction(Request $request)
    {
        $byDateFormHandler = $this->get('prestashop.admin.order.invoices.by_date.form_handler');

        $byStatusForm = $this->get('prestashop.admin.order.invoices.by_status.form_handler')->getForm();
        $optionsForm = $this->get('prestashop.admin.order.invoices.options.form_handler')->getForm();
        $byDateForm = $this->processForm($byDateFormHandler, $request);

        return $this->renderForm(
            $byDateForm,
            $byStatusForm,
            $optionsForm,
            $request
        );
    }

    /**
     * Action that generates invoices PDF by order status.
     *
     * @param Request $request
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return Response
     */
    public function generatePdfByStatusAction(Request $request)
    {
        $byStatusFormHandler = $this->get('prestashop.admin.order.invoices.by_status.form_handler');

        $byDateForm = $this->get('prestashop.admin.order.invoices.by_date.form_handler')->getForm();
        $optionsForm = $this->get('prestashop.admin.order.invoices.options.form_handler')->getForm();
        $byStatusForm = $this->processForm($byStatusFormHandler, $request);

        return $this->renderForm(
            $byDateForm,
            $byStatusForm,
            $optionsForm,
            $request
        );
    }

    /**
     * Process the Invoice Options configuration form.
     *
     * @param Request $request
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return Response
     */
    public function processAction(Request $request)
    {
        $optionsFormHandler = $this->get('prestashop.admin.order.invoices.options.form_handler');
        $byDateForm = $this->get('prestashop.admin.order.invoices.by_date.form_handler')->getForm();
        $byStatusForm = $this->get('prestashop.admin.order.invoices.by_status.form_handler')->getForm();
        $optionsForm = $this->processForm($optionsFormHandler, $request);

        return $this->renderForm($byDateForm, $byStatusForm, $optionsForm, $request);
    }

    /**
     * @param FormInterface $byDateForm
     * @param FormInterface $byStatusForm
     * @param FormInterface $optionsForm
     * @param Request $request
     *
     * @return Response|null
     */
    protected function renderForm(FormInterface $byDateForm, FormInterface $byStatusForm, FormInterface $optionsForm, Request $request): ?Response
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        return $this->render('@PrestaShop/Admin/Sell/Order/Invoices/invoices.html.twig', [
            'layoutTitle' => $this->trans('Invoices', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'generateByDateForm' => $byDateForm->createView(),
            'generateByStatusForm' => $byStatusForm->createView(),
            'invoiceOptionsForm' => $optionsForm->createView(),
        ]);
    }

    /**
     * Processes the form in a generic way.
     *
     * @param FormHandlerInterface $formHandler
     * @param Request $request
     *
     * @return FormInterface
     *
     * @throws \Exception
     */
    private function processForm(FormHandlerInterface $formHandler, Request $request): FormInterface
    {
        $form = $formHandler->getForm();
        $form->submit($request->request->get($form->getName()));

        if ($form->isSubmitted() && $form->isValid()) {
            if ($errors = $formHandler->save($form->getData())) {
                $this->flashErrors($errors);
            } else {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
                $this->redirectToRoute('admin_order_invoices');
            }
        }

        return $form;
    }

    /**
     * Generates PDF of given invoice ID.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param int $invoiceId
     */
    public function generatePdfByIdAction(int $invoiceId)
    {
        $this->get('prestashop.adapter.pdf.generator.single_invoice')->generatePDF([$invoiceId]);

        // When using legacy generator,
        // we want to be sure that displaying PDF is the last thing this controller will do
        die();
    }
}
