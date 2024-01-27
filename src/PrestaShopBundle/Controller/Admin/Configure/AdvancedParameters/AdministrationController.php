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

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\ErrorMessage\Factory\ConfigurationErrorFactory;
use PrestaShopBundle\Form\Exception\FormDataProviderException;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Configure > Advanced Parameters > Administration" page display.
 */
class AdministrationController extends FrameworkBundleAdminController
{
    public const CONTROLLER_NAME = 'AdminAdminPreferences';

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
        $isDebug = $this->get('prestashop.adapter.environment')->isDebug();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/administration.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Administration', 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminAdminPreferences'),
            'requireFilterStatus' => false,
            'generalForm' => $generalForm->createView(),
            'uploadQuotaForm' => $uploadQuotaForm->createView(),
            'notificationsForm' => $notificationsForm->createView(),
            'isDebug' => $isDebug,
        ]);
    }

    /**
     * Process the Administration general configuration form.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_administration")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_administration')]
    public function processGeneralFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getGeneralFormHandler(),
            'General'
        );
    }

    /**
     * Process the Administration upload quota configuration form.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_administration")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_administration')]
    public function processUploadQuotaFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getUploadQuotaFormHandler(),
            'UploadQuota'
        );
    }

    /**
     * Process the Administration notifications configuration form.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_administration"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_administration')]
    public function processNotificationsFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getNotificationsFormHandler(),
            'Notifications'
        );
    }

    /**
     * Process the Administration configuration form.
     *
     * @param Request $request
     * @param FormHandlerInterface $formHandler
     * @param string $hookName
     *
     * @return RedirectResponse
     */
    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName)
    {
        $this->dispatchHook(
            'actionAdminAdministrationControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHook('actionAdminAdministrationControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            try {
                $formHandler->save($data);
            } catch (FormDataProviderException $e) {
                $errorMessageFactory = $this->get(ConfigurationErrorFactory::class);
                $this->flashErrors($errorMessageFactory->getErrorMessages($e->getInvalidConfigurationDataErrors(), $form));

                return $this->redirectToRoute('admin_administration');
            }

            $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
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
