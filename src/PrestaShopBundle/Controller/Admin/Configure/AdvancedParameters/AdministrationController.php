<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Administration" page display.
 */
class AdministrationController extends FrameworkBundleAdminController
{
    const CONTROLLER_NAME = 'AdminAdminPreferences';

    /**
     * Show Administration page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return Response
     */
    public function indexAction()
    {
        $generalForm = $this->getGeneralFormHandler()->getForm();
        $uploadQuotaForm = $this->getUploadQuotaFormHandler()->getForm();
        $notificationsForm = $this->getNotificationsFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/administration.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Administration', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminAdminPreferences'),
            'requireFilterStatus' => false,
            'generalForm' => $generalForm->createView(),
            'uploadQuotaForm' => $uploadQuotaForm->createView(),
            'notificationsForm' => $notificationsForm->createView(),
        ]);
    }

    /**
     * Process the Performance Smarty configuration form.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))", message="You do not have permission to update this.", redirectRoute="admin_administration")
     * @DemoRestricted(redirectRoute="admin_administration")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processGeneralFormAction(Request $request)
    {
        $this->dispatchHook(
            'actionAdminAdminPreferencesControllerPostProcessBefore',
            ['controller' => $this]
        );

        return $this->processForm(
            $request,
            $this->getGeneralFormHandler()
        );
    }

    /**
     * Process the Performance Smarty configuration form.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))", message="You do not have permission to update this.", redirectRoute="admin_administration")
     * @DemoRestricted(redirectRoute="admin_administration")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processUploadQuotaFormAction(Request $request)
    {
        $this->dispatchHook(
            'actionAdminAdminPreferencesControllerPostProcessBefore',
            ['controller' => $this]
        );

        return $this->processForm(
            $request,
            $this->getUploadQuotaFormHandler()
        );
    }

    /**
     * Process the Performance Smarty configuration form.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))", message="You do not have permission to update this.", redirectRoute="admin_administration")
     * @DemoRestricted(redirectRoute="admin_administration")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processNotificationsFormAction(Request $request)
    {
        $this->dispatchHook(
            'actionAdminAdminPreferencesControllerPostProcessBefore',
            ['controller' => $this]
        );

        return $this->processForm(
            $request,
            $this->getNotificationsFormHandler()
        );
    }

    /**
     * Process the Performance configuration form.
     *
     * @param Request $request
     * @param FormHandlerInterface $formHandler
     *
     * @return RedirectResponse
     */
    protected function processForm(Request $request, FormHandlerInterface $formHandler)
    {
        $this->dispatchHook('actionAdminAdminPreferencesControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_administration');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getGeneralFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.adapter.administration.general.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getUploadQuotaFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.adapter.administration.upload_quota.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getNotificationsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.adapter.administration.notifications.form_handler');
    }
}
