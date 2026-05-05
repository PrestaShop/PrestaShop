<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\BulkDeleteStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\BulkUpdateStoreStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\DeleteStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\ToggleStoreStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotDeleteStoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotToggleStoreStatusException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Store\Query\GetStoreForEditing;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface as IdentifiableFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\StoreFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\BulkActionsTrait;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * StoreController handles the "Shop Parameters > Contact > Stores" page.
 */
class StoreController extends PrestaShopAdminController
{
    use BulkActionsTrait;

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        StoreFilters $storeFilters,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.store')]
        GridFactoryInterface $storeGridFactory,
        #[Autowire(service: 'prestashop.admin.stores.contact_details_form_handler')]
        FormHandlerInterface $contactDetailsFormHandler,
    ): Response {
        $storeGrid = $storeGridFactory->getGrid($storeFilters);
        $contactDetailsForm = $contactDetailsFormHandler->getForm();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Stores/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'storeGrid' => $this->presentGrid($storeGrid),
            'contactDetailsForm' => $contactDetailsForm->createView(),
            'layoutHeaderToolbarBtn' => [
                'add_store' => [
                    'href' => $this->generateUrl('admin_stores_add'),
                    'desc' => $this->trans('Add new store', [], 'Admin.Shopparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
        ]);
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_stores_index')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.store_form_builder')]
        FormBuilderInterface $storeFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.store_form_handler')]
        IdentifiableFormHandlerInterface $storeFormHandler,
    ): Response {
        $storeForm = $storeFormBuilder->getForm();
        $storeForm->handleRequest($request);

        try {
            $result = $storeFormHandler->handle($storeForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_stores_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Stores/create.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'storeForm' => $storeForm->createView(),
            'layoutTitle' => $this->trans('New store', [], 'Admin.Navigation.Menu'),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_stores_index')]
    public function editAction(
        int $storeId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.store_form_builder')]
        FormBuilderInterface $storeFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.store_form_handler')]
        IdentifiableFormHandlerInterface $storeFormHandler,
    ): Response {
        try {
            $storeForm = $storeFormBuilder->getFormFor($storeId);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_stores_index');
        }

        $storeForm->handleRequest($request);

        try {
            $result = $storeFormHandler->handleFor($storeId, $storeForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_stores_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        /** @var \PrestaShop\PrestaShop\Core\Domain\Store\QueryResult\StoreForEditing $storeForEditing */
        $storeForEditing = $this->dispatchQuery(new GetStoreForEditing($storeId));
        $storeImage = $storeForEditing->getStoreImage();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Stores/edit.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'storeForm' => $storeForm->createView(),
            'storeId' => $storeId,
            'storeImage' => $storeImage,
            'layoutTitle' => $this->trans('Edit store', [], 'Admin.Navigation.Menu'),
        ]);
    }

    #[AdminSecurity(
        "is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
        redirectRoute: 'admin_stores_index'
    )]
    public function saveContactDetailsAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.stores.contact_details_form_handler')]
        FormHandlerInterface $contactDetailsFormHandler,
    ): RedirectResponse {
        $contactDetailsForm = $contactDetailsFormHandler->getForm();
        $contactDetailsForm->handleRequest($request);

        if ($contactDetailsForm->isSubmitted()) {
            $errors = $contactDetailsFormHandler->save($contactDetailsForm->getData());

            if (!empty($errors)) {
                $this->addFlashErrors($errors);
            } else {
                $this->addFlash(
                    'success',
                    $this->trans('The settings have been successfully updated.', [], 'Admin.Notifications.Success')
                );
            }
        }

        return $this->redirectToRoute('admin_stores_index');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function toggleStatusAction(int $storeId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new ToggleStoreStatusCommand($storeId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_stores_index');
    }

    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function deleteAction(int $storeId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteStoreCommand($storeId));

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_stores_index');
    }

    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        try {
            $this->dispatchCommand(new BulkDeleteStoreCommand($this->getBulkActionIds($request, 'store_bulk')));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_stores_index');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function bulkEnableAction(Request $request): RedirectResponse
    {
        return $this->bulkUpdateStatus($request, true);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function bulkDisableAction(Request $request): RedirectResponse
    {
        return $this->bulkUpdateStatus($request, false);
    }

    private function bulkUpdateStatus(Request $request, bool $newStatus): RedirectResponse
    {
        try {
            $this->dispatchCommand(new BulkUpdateStoreStatusCommand(
                $newStatus,
                $this->getBulkActionIds($request, 'store_bulk')
            ));

            $this->addFlash(
                'success',
                $this->trans('The status of the selection has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_stores_index');
    }

    /**
     * @return array<class-string<StoreException>, string|array<StoreException::*, string>>
     */
    private function getErrorMessages(): array
    {
        return [
            CannotToggleStoreStatusException::class => $this->trans(
                'An error occurred while updating the status.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotDeleteStoreException::class => [
                CannotDeleteStoreException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    [],
                    'Admin.Notifications.Error'
                ),
                CannotDeleteStoreException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            StoreNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            StoreConstraintException::class => [
                StoreConstraintException::STATE_COUNTRY_MISMATCH => $this->trans(
                    'You\'ve selected a state for a country that does not contain states.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
                StoreConstraintException::MISSING_COORDINATE => $this->trans(
                    'Latitude and longitude are required.',
                    [],
                    'Admin.Shopparameters.Notification'
                ),
            ],
        ];
    }
}
