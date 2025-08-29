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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ApiClientSettings;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\DeleteApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\EditApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\GenerateApiClientSecretCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\CannotAddApiClientException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\CannotUpdateApiClientException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Query\GetApiClientForEditing;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\QueryResult\EditableApiClient;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\CreatedApiClient;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface as ConfigurationFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ApiClientFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages the "Configure > Advanced Parameters > Admin API" page.
 */
class AdminAPIController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('delete', request.get('_legacy_controller')) || is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        ApiClientFilters $apiClientFilters,
        #[Autowire(service: 'prestashop.adapter.admin_api.form_handler')]
        ConfigurationFormHandlerInterface $formHandler,
        #[Autowire(service: 'prestashop.core.grid.factory.api_client')]
        GridFactoryInterface $gridFactory,
    ): Response {
        return $this->renderIndex($apiClientFilters, $formHandler->getForm(), $gridFactory);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function processConfigurationAction(
        ApiClientFilters $apiClientFilters,
        Request $request,
        #[Autowire(service: 'prestashop.adapter.admin_api.form_handler')]
        ConfigurationFormHandlerInterface $formHandler,
        #[Autowire(service: 'prestashop.core.grid.factory.api_client')]
        GridFactoryInterface $gridFactory,
    ): Response {
        $configurationForm = $formHandler->getForm();
        $configurationForm->handleRequest($request);

        if ($configurationForm->isSubmitted() && $configurationForm->isValid()) {
            $data = $configurationForm->getData();
            $configurationErrors = $formHandler->save($data);

            if (empty($configurationErrors)) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_api_index');
            }

            $this->addFlashErrors($configurationErrors);
        }

        return $this->renderIndex($apiClientFilters, $configurationForm, $gridFactory);
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_api_index')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.api_client_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.api_client_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        if ($this->isAdminAPIMultistoreDisabled()) {
            return $this->redirectToRoute('admin_api_index');
        }

        $apiClientForm = $formBuilder->getForm();
        $apiClientForm->handleRequest($request);

        try {
            $handlerResult = $formHandler->handle($apiClientForm);
            if (null !== $handlerResult->getIdentifiableObjectId()) {
                /** @var CreatedApiClient $createdApiClient */
                $createdApiClient = $handlerResult->getIdentifiableObjectId();
                $this->displayTemporarySecret(
                    $this->trans('The API Client and client secret have been generated successfully.', [], 'Admin.Notifications.Success'),
                    $createdApiClient->getSecret()
                );

                return $this->redirectToRoute('admin_api_clients_edit', ['apiClientId' => $createdApiClient->getApiClientId()->getValue()]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/AdminAPI/ApiClient/create.html.twig',
            [
                'layoutTitle' => $this->trans('New API Client', [], 'Admin.Navigation.Menu'),
                'apiClientForm' => $apiClientForm->createView(),
            ]
        );
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_api_index')]
    public function editAction(
        Request $request,
        int $apiClientId,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.api_client_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.api_client_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        if ($this->isAdminAPIMultistoreDisabled()) {
            return $this->redirectToRoute('admin_api_index');
        }

        $apiClientForm = $formBuilder->getFormFor($apiClientId);
        $apiClientForm->handleRequest($request);

        try {
            $handlerResult = $formHandler->handleFor($apiClientId, $apiClientForm);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_api_clients_edit', ['apiClientId' => $apiClientId]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $formData = $apiClientForm->getData();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/AdminAPI/ApiClient/edit.html.twig', [
            'layoutTitle' => $this->trans('Editing API Client "%name%"', ['%name%' => $formData['client_name']], 'Admin.Navigation.Menu'),
            'apiClientForm' => $apiClientForm->createView(),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_api_clients_edit')]
    public function regenerateSecretAction(int $apiClientId): RedirectResponse
    {
        try {
            $newSecret = $this->dispatchCommand(new GenerateApiClientSecretCommand($apiClientId));
            $this->displayTemporarySecret(
                $this->trans('Your new client secret has been generated successfully. Your former client secret is now obsolete.', [], 'Admin.Notifications.Success'),
                $newSecret
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_api_clients_edit', ['apiClientId' => $apiClientId]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_api_index')]
    public function toggleStatusAction(int $apiClientId): JsonResponse
    {
        /** @var EditableApiClient $editableApiClient */
        $editableApiClient = $this->dispatchQuery(new GetApiClientForEditing($apiClientId));

        try {
            $command = new EditApiClientCommand($apiClientId);
            $command->setEnabled(!$editableApiClient->isEnabled());
            $this->dispatchCommand($command);
        } catch (Exception $e) {
            return $this->json([
                'status' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages()),
            ]);
        }

        return $this->json([
            'status' => true,
            'message' => $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'),
        ]);
    }

    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to delete this.', redirectRoute: 'admin_api_index')]
    public function deleteAction(int $apiClientId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteApiClientCommand($apiClientId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_api_index');
    }

    private function renderIndex(ApiClientFilters $apiClientFilters, FormInterface $configurationForm, GridFactoryInterface $gridFactory): Response
    {
        $apiClientGrid = $gridFactory->getGrid($apiClientFilters);
        $isAdminAPIMultistoreDisabled = $this->isAdminAPIMultistoreDisabled();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/AdminAPI/index.html.twig', [
            'apiClientGrid' => $this->presentGrid($apiClientGrid),
            'help_link' => $this->generateSidebarLink('AdminAdminAPI'),
            'layoutTitle' => $this->trans('Admin API', [], 'Admin.Navigation.Menu'),
            'layoutHeaderToolbarBtn' => $this->getApiClientsToolbarButtons(),
            'isAdminAPIMultistoreDisabled' => $isAdminAPIMultistoreDisabled,
            'configurationForm' => $configurationForm,
            'enableSidebar' => true,
        ]);
    }

    private function displayTemporarySecret(string $successMessage, string $secret): void
    {
        $this->addFlash(
            'info',
            sprintf(
                '%s <strong>%s</strong>',
                $successMessage,
                $this->trans('This secret value will only be displayed once. Don\'t forget to make a copy in a secure location.', [], 'Admin.Notifications.Info'),
            )
        );

        // Pass generated secret via flash message
        $this->addFlash('client_secret', $secret);
    }

    /**
     * @return array
     */
    private function getApiClientsToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['addApiClient'] = [
            'href' => $this->generateUrl('admin_api_clients_create'),
            'desc' => $this->trans('Add new API Client', [], 'Admin.Actions'),
            'icon' => 'add_circle_outline',
            'class' => 'btn-primary',
        ];

        return $toolbarButtons;
    }

    /**
     * Provides translated error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            ApiClientNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            ApiClientConstraintException::class => [
                ApiClientConstraintException::CLIENT_ID_ALREADY_USED => $this->trans(
                    'This value for "%field%" is already used and must be unique.',
                    ['%field%' => $this->trans('Client ID', [], 'Admin.Advparameters.Feature')],
                    'Admin.Notifications.Error',
                ),
                ApiClientConstraintException::CLIENT_NAME_ALREADY_USED => $this->trans(
                    'This value for "%field%" is already used and must be unique.',
                    ['%field%' => $this->trans('Client Name', [], 'Admin.Advparameters.Feature')],
                    'Admin.Notifications.Error',
                ),
                ApiClientConstraintException::INVALID_CLIENT_ID => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Client ID', [], 'Admin.Advparameters.Feature'))],
                    'Admin.Notifications.Error',
                ),
                ApiClientConstraintException::INVALID_CLIENT_NAME => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Client Name', [], 'Admin.Advparameters.Feature'))],
                    'Admin.Notifications.Error',
                ),
                ApiClientConstraintException::INVALID_DESCRIPTION => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Description', [], 'Admin.Global'))],
                    'Admin.Notifications.Error',
                ),
                ApiClientConstraintException::CLIENT_ID_TOO_LARGE => $this->trans(
                    'The field "%field%" cannot be longer than %limit% characters.',
                    [
                        '%field%' => $this->trans('Client ID', [], 'Admin.Advparameters.Feature'),
                        '%limit%' => ApiClientSettings::MAX_CLIENT_ID_LENGTH,
                    ],
                    'Admin.Notifications.Error',
                ),
                ApiClientConstraintException::CLIENT_NAME_TOO_LARGE => $this->trans(
                    'The field "%field%" cannot be longer than %limit% characters.',
                    [
                        '%field%' => $this->trans('Client Name', [], 'Admin.Advparameters.Feature'),
                        '%limit%' => ApiClientSettings::MAX_CLIENT_NAME_LENGTH,
                    ],
                    'Admin.Notifications.Error',
                ),
                ApiClientConstraintException::DESCRIPTION_TOO_LARGE => $this->trans(
                    'The field "%field%" cannot be longer than %limit% characters.',
                    [
                        '%field%' => $this->trans('Description', [], 'Admin.Global'),
                        '%limit%' => ApiClientSettings::MAX_DESCRIPTION_LENGTH,
                    ],
                    'Admin.Notifications.Error',
                ),
            ],
            CannotAddApiClientException::class => $this->trans(
                'An error occurred while creating the API Client.',
                [],
                'Admin.Advparameters.Notification'
            ),
            CannotUpdateApiClientException::class => $this->trans(
                'An error occurred while creating the API Client.',
                [],
                'Admin.Advparameters.Notification'
            ),
        ];
    }

    private function isAdminAPIMultistoreDisabled(): bool
    {
        return !$this->getFeatureFlagStateChecker()->isEnabled(FeatureFlagSettings::FEATURE_FLAG_ADMIN_API_MULTISTORE)
            && $this->getShopContext()->isMultiShopEnabled();
    }
}
