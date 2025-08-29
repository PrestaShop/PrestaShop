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

use PrestaShop\PrestaShop\Core\Configuration\UploadSizeConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Http\CookieOptions;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\Exception\FieldNotFoundException;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\FormDataProvider;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\GeneralType;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\UploadQuotaType;
use PrestaShopBundle\Form\Exception\DataProviderException;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataError;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataErrorCollection;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Configure > Advanced Parameters > Administration" page display.
 */
class AdministrationController extends PrestaShopAdminController
{
    public function __construct(private readonly UploadSizeConfigurationInterface $uploadSizeConfiguration)
    {
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        #[Autowire(service: 'prestashop.adapter.administration.general.form_handler')]
        FormHandlerInterface $generalFormHandler,
        #[Autowire(service: 'prestashop.adapter.administration.upload_quota.form_handler')]
        FormHandlerInterface $uploadQuotaFormHandler,
        #[Autowire(service: 'prestashop.adapter.administration.notifications.form_handler')]
        FormHandlerInterface $notificationsFormHandler,
    ): Response {
        $generalForm = $generalFormHandler->getForm();
        $uploadQuotaForm = $uploadQuotaFormHandler->getForm();
        $notificationsForm = $notificationsFormHandler->getForm();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/administration.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Administration', [], 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminAdminPreferences'),
            'requireFilterStatus' => false,
            'generalForm' => $generalForm->createView(),
            'uploadQuotaForm' => $uploadQuotaForm->createView(),
            'notificationsForm' => $notificationsForm->createView(),
            'isDebug' => $this->getEnvironment()->isDebug(),
        ]);
    }

    /**
     * Process the Administration general configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_administration')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_administration')]
    public function processGeneralFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.administration.general.form_handler')]
        FormHandlerInterface $generalFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $generalFormHandler,
            'General'
        );
    }

    #[DemoRestricted(redirectRoute: 'admin_administration')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_administration')]
    public function processUploadQuotaFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.administration.upload_quota.form_handler')]
        FormHandlerInterface $uploadQuotaFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $uploadQuotaFormHandler,
            'UploadQuota'
        );
    }

    #[DemoRestricted(redirectRoute: 'admin_administration')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_administration')]
    public function processNotificationsFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.administration.notifications.form_handler')]
        FormHandlerInterface $notificationsFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $notificationsFormHandler,
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
    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName): RedirectResponse
    {
        $this->dispatchHookWithParameters(
            'actionAdminAdministrationControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHookWithParameters('actionAdminAdministrationControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            try {
                $formHandler->save($data);
            } catch (DataProviderException $e) {
                $this->addFlashErrors($this->getErrorMessages($e->getInvalidConfigurationDataErrors()));

                return $this->redirectToRoute('admin_administration');
            }

            $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
        }

        return $this->redirectToRoute('admin_administration');
    }

    /**
     * @param InvalidConfigurationDataErrorCollection $errors
     *
     * @return array<int, string>
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
                    [$this->getFieldLabel($error->getFieldName())],
                    'Admin.Notifications.Error',
                );
            case FormDataProvider::ERROR_COOKIE_LIFETIME_MAX_VALUE_EXCEEDED:
                return $this->trans(
                    '%s is invalid. Please enter an integer lower than %s.',
                    [
                        $this->getFieldLabel($error->getFieldName()),
                        CookieOptions::MAX_COOKIE_VALUE,
                    ],
                    'Admin.Notifications.Error',
                );
            case FormDataProvider::ERROR_COOKIE_SAMESITE_NONE:
                return $this->trans(
                    'The SameSite=None attribute is only available in secure mode.',
                    [],
                    'Admin.Advparameters.Notification'
                );
            case FormDataProvider::ERROR_MAX_SIZE_ATTACHED_FILES:
                return $this->trans(
                    '%s is invalid. Please enter an integer between %s and %s.',
                    [
                        $this->getFieldLabel($error->getFieldName()),
                        0,
                        $this->uploadSizeConfiguration->getMaxUploadSizeInBytes() / 1048576,
                    ],
                    'Admin.Advparameters.Notification'
                );
        }

        return $this->trans(
            '%s is invalid.',
            [
                $this->getFieldLabel($error->getFieldName()),
                CookieOptions::MAX_COOKIE_VALUE,
            ],
            'Admin.Notifications.Error',
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
                    [],
                    'Admin.Advparameters.Feature'
                );
            case UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE:
                return $this->trans(
                    'Maximum size for a downloadable product',
                    [],
                    'Admin.Advparameters.Feature'
                );
            case UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE:
                return $this->trans(
                    'Maximum size for a product\'s image',
                    [],
                    'Admin.Advparameters.Feature'
                );
            case GeneralType::FIELD_FRONT_COOKIE_LIFETIME:
                return $this->trans(
                    'Lifetime of front office cookies',
                    [],
                    'Admin.Advparameters.Feature'
                );
            case GeneralType::FIELD_BACK_COOKIE_LIFETIME:
                return $this->trans(
                    'Lifetime of back office cookies',
                    [],
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
