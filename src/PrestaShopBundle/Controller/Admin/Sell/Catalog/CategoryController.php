<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\Product\Category\Command\ToggleCategoryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\Command\UpdateCategoriesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\Exception\CannotUpdateCategoryStatusException;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\ValueObject\CategoryDeletionMode;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\ValueObject\CategoryStatus;
use PrestaShop\PrestaShop\Core\Search\Filters\CategoryFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Category\DeleteCategoriesType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryController is responsible for "Sell > Catalog > Categories" page.
 */
class CategoryController extends FrameworkBundleAdminController
{
    /**
     * Show categories listing.
     *
     * @param Request $request
     * @param CategoryFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, CategoryFilters $filters)
    {
        $categoryGridFactory = $this->get('prestashop.core.grid.factory.category');
        $categoryGrid = $categoryGridFactory->getGrid($filters);

        $categoriesKpiFactory = $this->get('prestashop.core.kpi_row.factory.categories');

        $currentCategoryId = $filters->getFilters()['id_category_parent'];
        $categoryViewDataProvider = $this->get('prestashop.adapter.category.category_view_data_provider');
        $categoryViewData = $categoryViewDataProvider->getViewData($currentCategoryId);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/categories.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'categoriesGrid' => $this->presentGrid($categoryGrid),
            'categoriesKpi' => $categoriesKpiFactory->build(),
            'layoutHeaderToolbarBtn' => $this->getCategoryToolbarButtons($request),
            'currentCategoryView' => $categoryViewData,
        ]);
    }

    /**
     * Renders category deletion from.
     *
     * @return Response
     */
    public function renderDeleteFormBlockAction()
    {
        $request = $this->get('request_stack')->getMasterRequest();

        $data = [];
        $options = [];
        $isDeleteSubmitted = true;

        if ($request->request->has('categories_bulk')) {
            $data['categories_to_delete'] = $request->request->get('categories_bulk');
            $options['action'] = $this->generateUrl('admin_category_process_bulk_delete');
        } elseif ($request->query->has('id_category_to_delete')) {
            $data['categories_to_delete'] = [$request->query->get('id_category_to_delete')];
            $options['action'] = $this->generateUrl('admin_category_process_delete');
        } else {
            $isDeleteSubmitted = false;
        }

        $deleteCategoriesForm = $this->createForm(DeleteCategoriesType::class, $data, $options);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/Blocks/delete_block.html.twig', [
            'deleteCategoriesForm' => $deleteCategoriesForm->createView(),
            'isDeleteSubmitted' => $isDeleteSubmitted,
        ]);
    }

    /**
     * Toggle category status.
     *
     * @param int $categoryId
     *
     * @return JsonResponse
     */
    public function processStatusToggleAction($categoryId)
    {
        try {
            $command = new ToggleCategoryStatusCommand(new CategoryId($categoryId));

            $this->getCommandBus()->handle($command);

            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success'),
            ];
        } catch (CategoryException $e) {
            $response = [
                'status' => false,
                'message' => $this->handleUpdateStatusException($e),
            ];
        }

        return $this->json($response);
    }

    /**
     * Process bulk action for categories status enabling.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processBulkStatusEnableAction(Request $request)
    {
        $this->updateBulkStatus($request->request->get('categories_bulk'), CategoryStatus::ENABLED);

        return $this->redirectToRoute('admin_category_listing');
    }

    /**
     * Process bulk action for categories status disabling.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processBulkStatusDisableAction(Request $request)
    {
        $this->updateBulkStatus($request->request->get('categories_bulk'), CategoryStatus::DISABLED);

        return $this->redirectToRoute('admin_category_listing');
    }

    /**
     * Processes bulk categories deleting.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processBulkDeleteAction(Request $request)
    {
        $deleteCategoriesForm = $this->createForm(DeleteCategoriesType::class);
        $deleteCategoriesForm->handleRequest($request);

        if ($deleteCategoriesForm->isSubmitted()) {
            $categoriesDeleteData = $deleteCategoriesForm->getData();

            $errors = $this->get('prestashop.adapter.category.category_remover')->removeMultiple(
                $categoriesDeleteData['categories_to_delete'],
                new CategoryDeletionMode($categoriesDeleteData['delete_mode'])
            );

            if (empty($errors)) {
                $this->addFlash(
                    'success',
                    $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
                );
            } else {
                $this->flashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_category_listing');
    }

    /**
     * Process single category deleting.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processDeleteAction(Request $request)
    {
        $deleteCategoriesForm = $this->createForm(DeleteCategoriesType::class);
        $deleteCategoriesForm->handleRequest($request);

        if ($deleteCategoriesForm->isSubmitted()) {
            $categoriesDeleteData = $deleteCategoriesForm->getData();

            $errors = $this->get('prestashop.adapter.category.category_remover')->remove(
                reset($categoriesDeleteData['categories_to_delete']),
                new CategoryDeletionMode($categoriesDeleteData['delete_mode'])
            );

            if (empty($errors)) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful deletion.', 'Admin.Notifications.Success')
                );
            } else {
                $this->flashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_category_listing');
    }

    /**
     * Show category for editing.
     *
     * @param int $categoryId
     *
     * @return Response
     */
    public function editAction($categoryId)
    {
        return $this->redirect(
            $this->getAdminLink('AdminCategories', [
                'id_category' => $categoryId,
                'updatecategory' => 1,
            ])
        );
    }

    /**
     * Export filtered categories.
     *
     * @param CategoryFilters $filters
     *
     * @return Response
     */
    public function exportAction(CategoryFilters $filters)
    {
        $categoriesGridFactory = $this->get('prestashop.core.grid.factory.category');
        $categoriesGrid = $categoriesGridFactory->getGrid($filters);

        $headers = [
            'id_category' => $this->trans('ID', 'Admin.Global'),
            'name' => $this->trans('Name', 'Admin.Global'),
            'description' => $this->trans('Description', 'Admin.Global'),
            'position' => $this->trans('Position', 'Admin.Global'),
            'active' => $this->trans('Displayed', 'Admin.Global'),
        ];

        $data = [];

        foreach ($categoriesGrid->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id_category' => $record['id_category'],
                'name' => $record['name'],
                'description' => $record['description'],
                'position' => $record['position'],
                'active' => $record['active'],
            ];
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('category_' . date('Y-m-d_His') . '.csv')
        ;
    }

    /**
     * Update categories status.
     *
     * @param int[] $categoryIds
     * @param string $newStatus
     */
    protected function updateBulkStatus(array $categoryIds, $newStatus)
    {
        try {
            $command = new UpdateCategoriesStatusCommand(
                $categoryIds,
                new CategoryStatus($newStatus)
            );

            $this->getCommandBus()->handle($command);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CategoryException $e) {
            $this->addFlash('error', $this->handleUpdateStatusException($e));
        }
    }

    /**
     * Handle exception when which occurs when updating category status.
     *
     * @param CategoryException $e
     *
     * @return string Error message
     */
    protected function handleUpdateStatusException(CategoryException $e)
    {
        $type = get_class($e);

        $errors = [
            CategoryNotFoundException::class => sprintf(
                '%s %s',
                $this->trans('An error occurred while updating the status for an object.', 'Admin.Notifications.Error'),
                $this->trans('(cannot load object)', 'Admin.Notifications.Error')
            ),
            CannotUpdateCategoryStatusException::class => $this->trans(
                'An error occurred while updating the status for an object.',
                'Admin.Notifications.Error'
            ),
        ];

        if (isset($errors[$type])) {
            return $errors[$type];
        }

        return $this->trans('Failed to update the status', 'Admin.Notifications.Error');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getCategoryToolbarButtons(Request $request)
    {
        $toolbarButtons = [];

        if ($this->get('prestashop.adapter.feature.multishop')->isActive()) {
            $toolbarButtons['add_root'] = [
                'href' => $this->getAdminLink('AdminCategories', [
                    'addcategoryroot' => 1,
                ]),
                'desc' => $this->trans('Add new root category', 'Admin.Catalog.Feature'),
                'icon' => 'add_circle_outline',
            ];
        }

        $urlParams = [
            'addcategory' => 1,
        ];

        if ($request->query->has('id_category')) {
            $urlParams['id_parent'] = $request->query->get('id_category');
        }

        $toolbarButtons['add'] = [
            'href' => $this->getAdminLink('AdminCategories', $urlParams),
            'desc' => $this->trans('Add new category', 'Admin.Catalog.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }
}
