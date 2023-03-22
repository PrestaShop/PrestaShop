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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\BulkDeleteStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\BulkUpdateStoreStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\DeleteStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\ToggleStoreStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotDeleteStoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotToggleStoreStatusException;
use PrestaShop\PrestaShop\Core\Search\Filters\StoreFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Controller\BulkActionsTrait;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends FrameworkBundleAdminController
{
    use BulkActionsTrait;

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        StoreFilters $storeFilters
    ) {
        $storeGridFactory = $this->get('prestashop.core.grid.grid_factory.store');
        $storeGrid = $storeGridFactory->getGrid($storeFilters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Store/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'storeGrid' => $this->presentGrid($storeGrid),
            'layoutHeaderToolbarBtn' => [
                'add_store' => [
                'href' => $this->generateUrl('admin_stores_create'),
                'desc' => $this->trans('Add new store', 'Admin.Shopparameters.Feature'),
                'icon' => 'add_circle_outline',
                ],
            ],
        ]);
    }

    /**
     * Display the Contact creation form.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_stores_index",
     *     message="You do not have permission to add this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $storeFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.store_form_builder');
        $storeForm = $storeFormBuilder->getForm();
        $storeForm->handleRequest($request);

        try {
            $storeFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.store_form_handler');
            $result = $storeFormHandler->handle($storeForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful creation', 'Admin.Notifications.Success')
                );

                return $this->redirectToRoute('admin_contacts_index');
            }
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages($exception))
            );
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Stores/create.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'contactForm' => $storeForm->createView(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New store', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $storeId
     *
     * @return Response
     */
    public function toggleStatusAction(int $storeId): Response
    {
        try {
            $this->getCommandBus()->handle(new ToggleStoreStatusCommand($storeId));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_stores_index');
    }

//    public function bulkEnableStoreAction():

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @param int $storeId
     *
     * @return Response
     */
    public function deleteAction(int $storeId): Response
    {
        try {
            $this->getCommandBus()->handle(new DeleteStoreCommand($storeId));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_stores_index');
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function bulkDeleteAction(Request $request): Response
    {
        try {
            $this->getCommandBus()->handle(new BulkDeleteStoreCommand($this->getBulkActionIds($request, 'store_bulk')));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_stores_index');
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function bulkEnableAction(Request $request): Response
    {
        return $this->bulkUpdateStatus($request, true);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function bulkDisableAction(Request $request): Response
    {
        return $this->bulkUpdateStatus($request, false);
    }

    /**
     * @param Request $request
     * @param bool $newStatus
     *
     * @return Response
     */
    private function bulkUpdateStatus(Request $request, bool $newStatus): Response
    {
        try {
            $this->getCommandBus()->handle(new BulkUpdateStoreStatusCommand(
                $newStatus,
                $this->getBulkActionIds($request, 'store_bulk'))
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_stores_index');
    }

    /**
     * @return array<string, string>
     */
    private function getErrorMessages(): array
    {
        return [
            CannotToggleStoreStatusException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            ),
            CannotDeleteStoreException::class => [
                CannotDeleteStoreException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                CannotDeleteStoreException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
