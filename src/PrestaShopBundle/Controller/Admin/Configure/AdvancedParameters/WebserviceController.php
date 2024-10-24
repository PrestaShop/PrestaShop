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

use Exception;
use PrestaShop\PrestaShop\Adapter\Webservice\WebserviceKeyEraser;
use PrestaShop\PrestaShop\Adapter\Webservice\WebserviceKeyStatusModifier;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\DuplicateWebserviceKeyException;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\WebserviceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\Key;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface as ConfigurationFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\WebserviceKeyFilters;
use PrestaShop\PrestaShop\Core\Webservice\ServerRequirementsCheckerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Webservice\WebserviceFormDataProvider;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Configure > Advanced Parameters > Webservice" page.
 *
 * @todo: add unit tests
 */
class WebserviceController extends PrestaShopAdminController
{
    private const WEBSERVICE_ENTRY_ENDPOINT = '/api';

    /**
     * Displays the Webservice main page.
     *
     * @param WebserviceKeyFilters $filters - filters for webservice list
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        WebserviceKeyFilters $filters,
        Request $request,
        #[Autowire(service: 'prestashop.adapter.webservice.form_handler')]
        ConfigurationFormHandlerInterface $formHandler,
        #[Autowire(service: 'prestashop.core.grid.factory.webservice_key')]
        GridFactoryInterface $gridFactory,
        ServerRequirementsCheckerInterface $serverRequirementsChecker,
        WebserviceFormDataProvider $webserviceFormDataProvider,
    ): Response {
        return $this->renderPage($request, $filters, $formHandler->getForm(), $gridFactory, $serverRequirementsChecker, $webserviceFormDataProvider);
    }

    /**
     * Shows Webservice Key form and handles its submit
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.webservice_key_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.webservice_key_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response|RedirectResponse {
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        try {
            $result = $formHandler->handle($form);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_webservice_keys_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Webservice/create.html.twig',
            [
                'webserviceKeyForm' => $form->createView(),
                'layoutTitle' => $this->trans('New webservice key', [], 'Admin.Navigation.Menu'),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminWebservice'),
            ]
        );
    }

    /**
     * Redirects to webservice account form where existing webservice account record can be edited.
     *
     * @param int $webserviceKeyId
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function editAction(
        int $webserviceKeyId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.webservice_key_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.webservice_key_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response|RedirectResponse {
        $form = $formBuilder->getFormFor($webserviceKeyId);
        $form->handleRequest($request);

        try {
            $result = $formHandler->handleFor($webserviceKeyId, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_webservice_keys_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Webservice/edit.html.twig',
            [
                'webserviceKeyForm' => $form->createView(),
                'layoutTitle' => $this->trans(
                    'Editing webservice key %key%',
                    [
                        '%key%' => $form->getData()['key'],
                    ],
                    'Admin.Navigation.Menu',
                ),
            ]
        );
    }

    /**
     * Deletes single record.
     *
     * @param int $webserviceKeyId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_webservice_keys_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to delete this.')]
    public function deleteAction(
        int $webserviceKeyId,
        WebserviceKeyEraser $webserviceEraser,
    ): RedirectResponse {
        $errors = $webserviceEraser->erase([$webserviceKeyId]);

        if (!empty($errors)) {
            $this->addFlashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_webservice_keys_index');
    }

    /**
     * Deletes selected records.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_webservice_keys_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to delete this.')]
    public function bulkDeleteAction(
        Request $request,
        WebserviceKeyEraser $webserviceEraser,
    ): RedirectResponse {
        $webserviceToDelete = $request->request->all('webservice_key_bulk_action');
        $errors = $webserviceEraser->erase($webserviceToDelete);

        if (!empty($errors)) {
            $this->addFlashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_webservice_keys_index');
    }

    /**
     * Enables status for selected rows.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_webservice_keys_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.')]
    public function bulkEnableAction(
        Request $request,
        WebserviceKeyStatusModifier $statusModifier,
    ): RedirectResponse {
        $webserviceToEnable = $request->request->all('webservice_key_bulk_action');

        if ($statusModifier->setStatus($webserviceToEnable, true)) {
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_webservice_keys_index');
    }

    /**
     * Disables status for selected rows.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_webservice_keys_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.')]
    public function bulkDisableAction(
        Request $request,
        WebserviceKeyStatusModifier $statusModifier,
    ): RedirectResponse {
        $webserviceToDisable = $request->request->all('webservice_key_bulk_action');

        if ($statusModifier->setStatus($webserviceToDisable, false)) {
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_webservice_keys_index');
    }

    /**
     * Toggles webservice account status.
     *
     * @param int $webserviceKeyId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_webservice_keys_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.')]
    public function toggleStatusAction(
        int $webserviceKeyId,
        WebserviceKeyStatusModifier $statusModifier,
    ): RedirectResponse {
        $errors = $statusModifier->toggleStatus($webserviceKeyId);

        if (!empty($errors)) {
            $this->addFlashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_webservice_keys_index');
    }

    /**
     * Process the Webservice configuration form.
     *
     * @param Request $request
     * @param WebserviceKeyFilters $filters
     *
     * @return Response|RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_webservice_keys_index')]
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.')]
    public function saveSettingsAction(
        Request $request,
        WebserviceKeyFilters $filters,
        #[Autowire(service: 'prestashop.adapter.webservice.form_handler')]
        ConfigurationFormHandlerInterface $formHandler,
        #[Autowire(service: 'prestashop.core.grid.factory.webservice_key')]
        GridFactoryInterface $gridFactory,
        ServerRequirementsCheckerInterface $serverRequirementsChecker,
        WebserviceFormDataProvider $webserviceFormDataProvider,
    ): Response|RedirectResponse {
        $this->dispatchHookWithParameters('actionAdminAdminWebserviceControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $saveErrors = $formHandler->save($form->getData());

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_webservice_keys_index');
            } else {
                $this->addFlashErrors($saveErrors);
            }
        }

        return $this->renderPage($request, $filters, $form, $gridFactory, $serverRequirementsChecker, $webserviceFormDataProvider);
    }

    protected function renderPage(
        Request $request,
        WebserviceKeyFilters $filters,
        FormInterface $form,
        GridFactoryInterface $gridFactory,
        ServerRequirementsCheckerInterface $serverRequirementsChecker,
        WebserviceFormDataProvider $webserviceFormDataProvider,
    ): Response {
        $grid = $gridFactory->getGrid($filters);

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Webservice/index.html.twig',
            [
                'help_link' => $this->generateSidebarLink($request->get('_legacy_controller')),
                'webserviceConfigurationForm' => $form->createView(),
                'grid' => $this->presentGrid($grid),
                'configurationWarnings' => $serverRequirementsChecker->checkForErrors(),
                'webserviceStatus' => $this->getWebServiceStatus($request, $webserviceFormDataProvider),
                'enableSidebar' => true,
            ]
        );
    }

    /**
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            WebserviceConstraintException::class => [
                WebserviceConstraintException::INVALID_KEY => $this->trans(
                    'Key length must be %length% characters long.',
                    [
                        '%length%' => Key::LENGTH,
                    ],
                    'Admin.Advparameters.Notification',
                ),
            ],
            DuplicateWebserviceKeyException::class => $this->trans('This key already exists.', [], 'Admin.Advparameters.Notification'),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array<string, bool|string|null>
     */
    private function getWebServiceStatus(
        Request $request,
        WebserviceFormDataProvider $webserviceFormDataProvider,
    ): array {
        $webserviceConfiguration = $webserviceFormDataProvider->getData();
        $webserviceStatus = [
            'isEnabled' => (bool) $webserviceConfiguration['enable_webservice'],
            'isFunctional' => false,
            'endpoint' => null,
        ];

        if ($webserviceStatus['isEnabled']) {
            $webserviceStatus['endpoint'] = rtrim($request->getSchemeAndHttpHost(), '/');
            $webserviceStatus['endpoint'] .= rtrim($this->getShopContext()->getBaseURI(), '/');
            $webserviceStatus['endpoint'] .= self::WEBSERVICE_ENTRY_ENDPOINT;
            $webserviceStatus['isFunctional'] = $this->checkWebserviceEndpoint($webserviceStatus['endpoint']);
        }

        return $webserviceStatus;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    private function checkWebserviceEndpoint(string $url): bool
    {
        $client = HttpClient::create();
        $statusCode = null;
        try {
            $response = $client->request('GET', $url, [
                'max_redirects' => 5,
            ]);
            $statusCode = $response->getStatusCode();
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return false;
        }

        if ($statusCode >= Response::HTTP_OK && $statusCode < Response::HTTP_MULTIPLE_CHOICES) {
            return true;
        } elseif ($statusCode == Response::HTTP_UNAUTHORIZED) {
            return true;
        }

        return false;
    }
}
