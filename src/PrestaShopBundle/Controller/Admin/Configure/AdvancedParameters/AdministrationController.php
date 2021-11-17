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
use PrestaShopBundle\Controller\Exception\FieldNotFoundException;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\FormDataProvider;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\GeneralDataProvider;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\GeneralType;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\UploadQuotaType;
use PrestaShopBundle\Form\Exception\DataProviderException;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataError;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataErrorCollection;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Administration" page display.
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
            'requireAddonsSearch' => true,
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
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))", message="You do not have permission to update this.", redirectRoute="admin_administration")
     * @DemoRestricted(redirectRoute="admin_administration")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
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
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))", message="You do not have permission to update this.", redirectRoute="admin_administration")
     * @DemoRestricted(redirectRoute="admin_administration")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
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
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))", message="You do not have permission to update this.", redirectRoute="admin_administration")
     * @DemoRestricted(redirectRoute="admin_administration")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
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
            } catch (DataProviderException $e) {
                $this->flashErrors($this->getErrorMessages($e->getInvalidConfigurationDataErrors()));

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

    /**
     * @var InvalidConfigurationDataErrorCollection
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
            case FormDataProvider::ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO:
                return $this->trans(
                    '%s is invalid. Please enter an integer greater than or equal to 0.',
                    'Admin.Notifications.Error',
                    [$this->getFieldLabel($error->getFieldName())]
                );
            case FormDataProvider::ERROR_COOKIE_LIFETIME_MAX_VALUE_EXCEEDED:
                return $this->trans(
                    '%s is invalid. Please enter an integer lower than %s.',
                    'Admin.Notifications.Error',
                    [
                        $this->getFieldLabel($error->getFieldName()),
                        GeneralDataProvider::MAX_COOKIE_VALUE,
                    ]
                );
            case FormDataProvider::ERROR_COOKIE_SAMESITE_NONE:
                return $this->trans(
                    'The SameSite=None is only available in secure mode.',
                    'Admin.Advparameters.Notification'
                );
        }

        return $this->trans(
            '%s is invalid.',
            'Admin.Notifications.Error',
            [
                $this->getFieldLabel($error->getFieldName()),
                GeneralDataProvider::MAX_COOKIE_VALUE,
            ]
        );
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    private function getFieldLabel(string $fieldName): string
    {
        /*
         * Reusing same translated string as in UploadQuotaType, ideally I would take strings from there instead
         * Because if somebody changes name in UploadQuotaType it won't be changed here. Not sure how to do that,
         * building the whole form just to retrieve labels sound like an overhead.
         * Maybe move labels to some other service and then retrieve them in both UploadQuotaType and here.
         */
        switch ($fieldName) {
            case UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES:
                return $this->trans(
                    'Maximum size for attached files',
                    'Admin.Advparameters.Feature'
                );
            case UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE:
                return $this->trans(
                    'Maximum size for a downloadable product',
                    'Admin.Advparameters.Feature'
                );
            case UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE:
                return $this->trans(
                    'Maximum size for a product\'s image',
                    'Admin.Advparameters.Feature'
                );
            case GeneralType::FIELD_FRONT_COOKIE_LIFETIME:
                return $this->trans(
                    'Lifetime of front office cookies',
                    'Admin.Advparameters.Feature'
                );
            case GeneralType::FIELD_BACK_COOKIE_LIFETIME:
                return $this->trans(
                    'Lifetime of back office cookies',
                    'Admin.Advparameters.Feature'
                );
        }

        throw new FieldNotFoundException(
            sprintf(
                'Field name for field %s not found',
                $fieldName
            )
        );
    }
}
