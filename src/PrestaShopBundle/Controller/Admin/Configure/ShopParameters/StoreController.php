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
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends FrameworkBundleAdminController
{
    use BulkActionsTrait;

    /**
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        StoreFilters $storeFilters
    ) {
        $storeGridFactory = $this->get('prestashop.core.grid.grid_factory.store');
        $storeGrid = $storeGridFactory->getGrid($storeFilters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Stores/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'storeGrid' => $this->presentGrid($storeGrid),
            // @todo: uncomment when add action is implemented
            //'layoutHeaderToolbarBtn' => [
            //'add_store' => [
            //'href' => $this->generateUrl('admin_stores_add'),
            //'desc' => $this->trans('Add new store', 'Admin.Shopparameters.Feature'),
            //'icon' => 'add_circle_outline',
            //],
            //],
        ]);
    }

    /**
     * @param int $storeId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
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
     * @param int $storeId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
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
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
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
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function bulkEnableAction(Request $request): RedirectResponse
    {
        return $this->bulkUpdateStatus($request, true);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function bulkDisableAction(Request $request): RedirectResponse
    {
        return $this->bulkUpdateStatus($request, false);
    }

    /**
     * @param Request $request
     * @param bool $newStatus
     *
     * @return RedirectResponse
     */
    private function bulkUpdateStatus(Request $request, bool $newStatus): RedirectResponse
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
