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

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
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
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $emailConfigurationForm = $this->getEmailConfigurationFormHandler()->getForm();
        $extensionChecker = $this->get('prestashop.core.configuration.php_extension_checker');

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Email/email.html.twig', [
            'emailConfigurationForm' => $emailConfigurationForm->createView(),
            'isOpenSslExtensionLoaded' => $extensionChecker->loaded('openssl'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Process email configuration saving
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
     * Get email configuration form handler
     */
    protected function getEmailConfigurationFormHandler()
    {
        return $this->get('prestashop.admin.email_configuration.form_handler');
    }
}
