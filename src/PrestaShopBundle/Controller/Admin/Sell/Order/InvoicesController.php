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
use PrestaShopBundle\Controller\Exception\FieldNotFoundException;
use PrestaShopBundle\Form\Admin\Sell\Order\Invoices\GenerateByDateType;
use PrestaShopBundle\Form\Admin\Sell\Order\Invoices\GenerateByStatusType;
use PrestaShopBundle\Form\Admin\Sell\Order\Invoices\InvoiceByStatusFormHandler;
use PrestaShopBundle\Form\Admin\Sell\Order\Invoices\InvoiceOptionsDataProvider;
use PrestaShopBundle\Form\Admin\Sell\Order\Invoices\InvoiceOptionsType;
use PrestaShopBundle\Form\Admin\Sell\Order\Invoices\InvoicesByDateDataProvider;
use PrestaShopBundle\Form\Admin\Sell\Order\Invoices\InvoicesByStatusDataProvider;
use PrestaShopBundle\Form\Exception\DataProviderException;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataError;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataErrorCollection;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @Template("@PrestaShop/Admin/Sell/Order/Invoices/invoices.html.twig")
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
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
            'generateByDateForm' => $byDateForm->createView(),
            'generateByStatusForm' => $byStatusForm->createView(),
            'invoiceOptionsForm' => $optionsForm->createView(),
        ];
    }

    /**
     * Action that generates invoices PDF by date interval.
     *
     * @param Request $request
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return RedirectResponse
     */
    public function generatePdfByDateAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.order.invoices.by_date.form_handler');
        $this->processForm($formHandler, $request);

        return $this->redirectToRoute('admin_order_invoices');
    }

    /**
     * Action that generates invoices PDF by order status.
     *
     * @param Request $request
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return RedirectResponse
     */
    public function generatePdfByStatusAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.order.invoices.by_status.form_handler');
        $this->processForm($formHandler, $request);

        return $this->redirectToRoute('admin_order_invoices');
    }

    /**
     * Process the Invoice Options configuration form.
     *
     * @param Request $request
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return RedirectResponse
     */
    public function processAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.order.invoices.options.form_handler');

        if ($this->processForm($formHandler, $request)) {
            $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
        }

        return $this->redirectToRoute('admin_order_invoices');
    }

    /**
     * @return array
     *
     * @throws FieldNotFoundException
     *
     * @var InvalidConfigurationDataErrorCollection
     *
     */
    private function getErrorMessages(InvalidConfigurationDataErrorCollection $errors): array
    {
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $this->getErrorMessage($error);
        }

        return $messages;
    }

    /**
     * @param InvalidConfigurationDataError $error
     *
     * @return string
     *
     * @throws FieldNotFoundException
     */
    private function getErrorMessage(InvalidConfigurationDataError $error): string
    {
        switch ($error->getErrorCode()) {
            case InvoicesByDateDataProvider::ERROR_INVALID_DATE_TO:
            case InvoicesByDateDataProvider::ERROR_INVALID_DATE_FROM:
            return $this->trans(
                    'Invalid "%s" date.',
                    'Admin.Orderscustomers.Notification',
                    [
                        $this->getFieldLabel($error->getFieldName())
                    ]
                );
            case InvoicesByDateDataProvider::ERROR_NO_INVOICES_FOUND:
                return $this->trans(
                    'No invoice has been found for this period.',
                    'Admin.Orderscustomers.Notification'
                );
            case InvoicesByStatusDataProvider::ERROR_NO_ORDER_STATE_SELECTED:
                return $this->trans(
                    'You must select at least one order status.',
                    'Admin.Orderscustomers.Notification'
                );
            case InvoiceByStatusFormHandler::ERROR_NO_INVOICES_FOUND_FOR_STATUS:
                return $this->trans(
                    'No invoice has been found for this status.',
                    'Admin.Orderscustomers.Notification'
                );
            case InvoiceOptionsDataProvider::ERROR_INCORRECT_INVOICE_NUMBER:
                return $this->trans(
                    'Invoice number must be greater than the last invoice number, or 0 if you want to keep the current number.',
                    'Admin.Orderscustomers.Notification'
                );
        }

        return $this->trans(
            '%s is invalid.',
            'Admin.Notifications.Error',
            [
                $this->getFieldLabel($error->getFieldName()),
            ]
        );
    }

    /**
     * @param string $fieldName
     *
     * @return string
     *
     * @throws FieldNotFoundException
     */
    private function getFieldLabel(string $fieldName): string
    {
        switch ($fieldName) {
            case GenerateByDateType::FIELD_DATE_FROM:
                return $this->trans(
                    'From',
                    'Admin.Global.Advparameters.Feature'
                );
            case GenerateByDateType::FIELD_DATE_TO:
                return $this->trans(
                    'To',
                    'Admin.Global'
                );
            case GenerateByStatusType::FIELD_ORDER_STATES:
                return $this->trans(
                    'Order statuses',
                    'Admin.Orderscustomers.Feature'
                );
            case InvoiceOptionsType::INVOICE_PREFIX:
                return $this->trans(
                    'Invoice prefix',
                    'Admin.Orderscustomers.Feature'
                );
            case InvoiceOptionsType::LEGAL_FREE_TEXT:
                return $this->trans(
                    'Legal free text',
                    'Admin.Orderscustomers.Feature'
                );
            case InvoiceOptionsType::FOOTER_TEXT:
                return $this->trans(
                    'Footer text',
                    'Admin.Orderscustomers.Feature'
                );
        }

        throw new FieldNotFoundException(
            sprintf(
                'Field name for field %s not found',
                $fieldName
            )
        );
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
        $form->submit($request->request->get($form->getName()));

        if ($form->isSubmitted()) {
            try {
                $formHandler->save($form->getData());
            } catch (DataProviderException $e) {
                $this->flashErrors($this->getErrorMessages($e->getInvalidConfigurationDataErrors()));

                return false;
            }
        }

        return true;
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
