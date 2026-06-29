<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\AddQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\BulkDeleteQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\DeleteQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\ToggleQuickAccessNewWindowCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\BulkQuickAccessException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\CannotAddQuickAccessException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\CannotDeleteQuickAccessException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\CannotUpdateQuickAccessException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\QuickAccessConstraintException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\QuickAccessException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\QuickAccessNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject\QuickAccessId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\QuickAccess\QuickAccessGenerator;
use PrestaShop\PrestaShop\Core\Search\Filters\QuickAccessFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Configure > Advanced Parameters > Quick Access" page.
 */
class QuickAccessController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        QuickAccessFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.quick_access')]
        GridFactoryInterface $quickAccessGridFactory
    ): Response {
        $quickAccessGrid = $quickAccessGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/QuickAccess/index.html.twig', [
            'quickAccessGrid' => $this->presentGrid($quickAccessGrid),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_quick_accesses_index')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.quick_access_form_builder')]
        FormBuilderInterface $quickAccessFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.quick_access_form_handler')]
        FormHandlerInterface $quickAccessFormHandler
    ): Response {
        try {
            $quickAccessForm = $quickAccessFormBuilder->getForm();
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_quick_accesses_index');
        }

        try {
            $quickAccessForm->handleRequest($request);
            $result = $quickAccessFormHandler->handle($quickAccessForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_quick_accesses_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/QuickAccess/create.html.twig', [
            'quickAccessForm' => $quickAccessForm->createView(),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('New quick access', [], 'Admin.Navigation.Menu'),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_quick_accesses_index')]
    public function editAction(
        int $quickAccessId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.quick_access_form_builder')]
        FormBuilderInterface $quickAccessFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.quick_access_form_handler')]
        FormHandlerInterface $quickAccessFormHandler
    ): Response {
        try {
            $quickAccessForm = $quickAccessFormBuilder->getFormFor($quickAccessId);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_quick_accesses_index');
        }

        try {
            $quickAccessForm->handleRequest($request);
            $result = $quickAccessFormHandler->handleFor($quickAccessId, $quickAccessForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_quick_accesses_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof QuickAccessNotFoundException) {
                return $this->redirectToRoute('admin_quick_accesses_index');
            }
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/QuickAccess/edit.html.twig', [
            'quickAccessForm' => $quickAccessForm->createView(),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('Editing quick access', [], 'Admin.Navigation.Menu'),
        ]);
    }

    #[DemoRestricted(redirectRoute: 'admin_quick_accesses_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_quick_accesses_index')]
    public function deleteAction(int $quickAccessId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteQuickAccessCommand($quickAccessId));
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (QuickAccessException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_quick_accesses_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_quick_accesses_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_quick_accesses_index')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $quickAccessIds = $request->request->all('quick_access_bulk');

        if (empty($quickAccessIds)) {
            $this->addFlash('info', $this->trans('You must select at least one element to delete.', [], 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_quick_accesses_index');
        }

        try {
            $this->dispatchCommand(new BulkDeleteQuickAccessCommand($quickAccessIds));
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (BulkQuickAccessException $e) {
            $this->addFlashErrors($this->getBulkErrorMessages($e));
        } catch (QuickAccessException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_quick_accesses_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_quick_accesses_index')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function ajaxAddQuickLinkAction(
        Request $request,
        LanguageContext $languageContext,
        QuickAccessGenerator $quickAccessGenerator,
    ): JsonResponse {
        try {
            /** @var QuickAccessId $createdId */
            $createdId = $this->dispatchCommand(new AddQuickAccessCommand(
                [$languageContext->getId() => $request->request->getString('name')],
                $request->request->getString('url'),
                $request->request->getBoolean('new_window'),
            ));
        } catch (QuickAccessException $e) {
            return new JsonResponse([
                'has_errors' => true,
                0 => $this->getErrorMessageForException($e, $this->getErrorMessages()),
            ]);
        }

        $quickAccesses = $quickAccessGenerator->getTokenizedQuickAccesses();
        foreach ($quickAccesses as &$row) {
            $row['active'] = ($row['id_quick_access'] === $createdId->getValue());
        }

        return new JsonResponse($quickAccesses);
    }

    #[DemoRestricted(redirectRoute: 'admin_quick_accesses_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_quick_accesses_index')]
    public function ajaxDeleteQuickLinkAction(
        Request $request,
        QuickAccessGenerator $quickAccessGenerator,
    ): JsonResponse {
        try {
            $this->dispatchCommand(new DeleteQuickAccessCommand($request->request->getInt('id_quick_access')));
        } catch (QuickAccessException $e) {
            return new JsonResponse([
                'has_errors' => true,
                0 => $this->getErrorMessageForException($e, $this->getErrorMessages()),
            ]);
        }

        $quickAccesses = $quickAccessGenerator->getTokenizedQuickAccesses();
        foreach ($quickAccesses as &$row) {
            $row['active'] = false;
        }

        return new JsonResponse($quickAccesses);
    }

    #[DemoRestricted(redirectRoute: 'admin_quick_accesses_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_quick_accesses_index')]
    public function toggleNewWindowAction(int $quickAccessId): JsonResponse
    {
        try {
            $this->dispatchCommand(new ToggleQuickAccessNewWindowCommand($quickAccessId));
            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'),
            ];
        } catch (QuickAccessException $e) {
            $response = [
                'status' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages()),
            ];
        }

        return new JsonResponse($response);
    }

    private function getBulkErrorMessages(BulkQuickAccessException $e): array
    {
        $errors = [];
        foreach ($e->getExceptions() as $exception) {
            $errors[] = $this->getErrorMessageForException($exception, $this->getErrorMessages());
        }

        return $errors;
    }

    private function getErrorMessages(): array
    {
        return [
            QuickAccessNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            CannotAddQuickAccessException::class => $this->trans(
                'An error occurred while creating the object.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotUpdateQuickAccessException::class => $this->trans(
                'An error occurred while updating the object.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotDeleteQuickAccessException::class => $this->trans(
                'An error occurred while deleting the object.',
                [],
                'Admin.Notifications.Error'
            ),
            QuickAccessConstraintException::class => [
                QuickAccessConstraintException::LINK_ALREADY_EXISTS => $this->trans(
                    'A quick access link to this URL already exists.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
            ],
        ];
    }
}
