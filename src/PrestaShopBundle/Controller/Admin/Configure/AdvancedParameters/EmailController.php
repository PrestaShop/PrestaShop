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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Email\MailMethodOption;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email\TestEmailSendingType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EmailController is responsible for handling "Configure > Advanced Parameters > E-mail" page
 */
class EmailController extends FrameworkBundleAdminController
{
    /**
     * Show email configuration page
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $emailConfigurationForm = $this->getEmailConfigurationFormHandler()->getForm();
        $extensionChecker = $this->get('prestashop.core.configuration.php_extension_checker');

        $testEmailSendingForm = $this->createForm(TestEmailSendingType::class);

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Email/email.html.twig', [
            'emailConfigurationForm' => $emailConfigurationForm->createView(),
            'isOpenSslExtensionLoaded' => $extensionChecker->loaded('openssl'),
            'smtpMailMethod' => MailMethodOption::SMTP,
            'testEmailSendingForm' => $testEmailSendingForm->createView(),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Process email configuration saving
     *
     * @DemoRestricted(redirectRoute="admin_email")
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processFormAction(Request $request)
    {
        $formHandler = $this->getEmailConfigurationFormHandler();
        $emailConfigurationForm = $formHandler->getForm();
        $emailConfigurationForm->handleRequest($request);

        if ($emailConfigurationForm->isSubmitted()) {
            $errors = $formHandler->save($emailConfigurationForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_email');
    }

    /**
     * Processes test email sending
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function processTestEmailSendingAction(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            return $this->json([
                'errors' => [
                    $this->getDemoModeErrorMessage(),
                ],
            ]);
        }

        $testEmailSendingForm = $this->createForm(TestEmailSendingType::class);
        $testEmailSendingForm->handleRequest($request);

        $result = [];

        if ($testEmailSendingForm->isSubmitted()) {
            $emailConfigurationTester = $this->get('prestashop.adapter.email.email_configuration_tester');
            $result['errors'] = $emailConfigurationTester->testConfiguration($testEmailSendingForm->getData());
        }

        return $this->json($result);
    }

    /**
     * Get email configuration form handler
     */
    protected function getEmailConfigurationFormHandler()
    {
        return $this->get('prestashop.admin.email_configuration.form_handler');
    }
}
