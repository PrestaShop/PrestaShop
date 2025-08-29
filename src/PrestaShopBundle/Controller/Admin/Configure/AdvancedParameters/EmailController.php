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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Configuration\PhpExtensionCheckerInterface;
use PrestaShop\PrestaShop\Core\Email\EmailConfigurationTesterInterface;
use PrestaShop\PrestaShop\Core\Email\EmailLogEraserInterface;
use PrestaShop\PrestaShop\Core\Email\MailOption;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\EmailLogsFilter;
use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email\TestEmailSendingType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EmailController is responsible for handling "Configure > Advanced Parameters > E-mail" page.
 */
class EmailController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        EmailLogsFilter $filters,
        PhpExtensionCheckerInterface $phpExtensionChecker,
        #[Autowire(service: 'prestashop.core.grid.factory.email_logs')]
        GridFactoryInterface $emailLogsGridFactory,
        #[Autowire(service: 'prestashop.admin.email_configuration.form_handler')]
        FormHandlerInterface $emailConfigurationFormHandler,
    ): Response {
        $configuration = $this->getConfiguration();

        $emailConfigurationForm = $emailConfigurationFormHandler->getForm();

        $testEmailSendingForm = $this->createForm(TestEmailSendingType::class, [
            'send_email_to' => $configuration->get('PS_SHOP_EMAIL'),
        ]);

        $isEmailLogsEnabled = $configuration->get('PS_LOG_EMAILS');

        if ($isEmailLogsEnabled) {
            $emailLogsGrid = $emailLogsGridFactory->getGrid($filters);
            $presentedEmailLogsGrid = $this->presentGrid($emailLogsGrid);
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Email/index.html.twig', [
            'emailConfigurationForm' => $emailConfigurationForm->createView(),
            'isOpenSslExtensionLoaded' => $phpExtensionChecker->loaded('openssl'),
            'smtpMailMethod' => MailOption::METHOD_SMTP,
            'testEmailSendingForm' => $testEmailSendingForm->createView(),
            'emailLogsGrid' => $presentedEmailLogsGrid ?? null,
            'isEmailLogsEnabled' => $isEmailLogsEnabled,
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('E-mail', [], 'Admin.Navigation.Menu'),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Process email configuration saving.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_emails_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'Access denied.', redirectRoute: 'admin_emails_index')]
    public function saveOptionsAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.email_configuration.form_handler')]
        FormHandlerInterface $emailConfigurationFormHandler,
    ): RedirectResponse {
        $emailConfigurationForm = $emailConfigurationFormHandler->getForm();
        $emailConfigurationForm->handleRequest($request);

        if ($emailConfigurationForm->isSubmitted()) {
            $errors = $emailConfigurationFormHandler->save($emailConfigurationForm->getData());

            if (!empty($errors)) {
                $this->addFlashErrors($errors);
            } else {
                $this->addFlash(
                    'success',
                    $this->trans('The settings have been successfully updated.', [], 'Admin.Notifications.Success')
                );
            }
        }

        return $this->redirectToRoute('admin_emails_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_emails_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'Access denied.', redirectRoute: 'admin_emails_index')]
    public function deleteBulkAction(
        Request $request,
        EmailLogEraserInterface $emailLogEraser,
    ): RedirectResponse {
        $mailLogsToDelete = $request->request->all('email_logs_delete_email_logs');

        $errors = $emailLogEraser->erase($mailLogsToDelete);

        if (!empty($errors)) {
            $this->addFlashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_emails_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_emails_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function deleteAllAction(
        EmailLogEraserInterface $emailLogEraser,
    ): RedirectResponse {
        if ($emailLogEraser->eraseAll()) {
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        }

        return $this->redirectToRoute('admin_emails_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_emails_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function deleteAction(
        int $mailId,
        EmailLogEraserInterface $emailLogEraser,
    ): RedirectResponse {
        $errors = $emailLogEraser->erase([$mailId]);

        if (!empty($errors)) {
            $this->addFlashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_emails_index');
    }

    /**
     * Processes test email sending.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function sendTestAction(
        Request $request,
        EmailConfigurationTesterInterface $emailConfigurationTester,
    ): Response {
        if ($this->isDemoModeEnabled()) {
            return $this->json([
                'errors' => [
                    $this->getDemoModeErrorMessage(),
                ],
            ]);
        }

        if (!in_array(
            $this->getAuthorizationLevel($request->attributes->get('_legacy_controller')),
            [
                Permission::LEVEL_READ,
                Permission::LEVEL_UPDATE,
                Permission::LEVEL_CREATE,
                Permission::LEVEL_DELETE,
            ]
        )) {
            return $this->json([
                'errors' => [
                    $this->trans('Access denied.', [], 'Admin.Notifications.Error'),
                ],
            ]);
        }

        $testEmailSendingForm = $this->createForm(TestEmailSendingType::class);
        $testEmailSendingForm->handleRequest($request);

        $result = [];

        if ($testEmailSendingForm->isSubmitted()) {
            $result['errors'] = $emailConfigurationTester->testConfiguration($testEmailSendingForm->getData());
        }

        return $this->json($result);
    }
}
