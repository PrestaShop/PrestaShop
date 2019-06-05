<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkDeleteCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkDisableCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkEnableCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCoverImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryMenuThumbnailImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\ToggleCategoryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\UpdateCategoryPositionCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteRootCategoryForShopException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotUpdateCategoryStatusException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteImageException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\MenuThumbnailsLimitException;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\MenuThumbnailId;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Search\Filters\CategoryFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Category\DeleteCategoriesType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CategoryController is responsible for "Sell > Catalog > Categories" page.
 */
class CategoryController extends FrameworkBundleAdminController
{
    /**
     * Show categories listing.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param CategoryFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, CategoryFilters $filters)
    {
        $categoryGridFactory = $this->get('prestashop.core.grid.factory.category_decorator');
        $categoryGrid = $categoryGridFactory->getGrid($filters);

        $categoriesKpiFactory = $this->get('prestashop.core.kpi_row.factory.categories');

        $currentCategoryId = $filters->getFilters()['id_category_parent'];
        $categoryViewDataProvider = $this->get('prestashop.adapter.category.category_view_data_provider');
        $categoryViewData = $categoryViewDataProvider->getViewData($currentCategoryId);

        $deleteCategoriesForm = $this->createForm(DeleteCategoriesType::class);

        $showcaseCardIsClosed = $this->getQueryBus()->handle(
            new GetShowcaseCardIsClosed((int) $this->getContext()->employee->id, ShowcaseCard::CATEGORIES_CARD)
        );

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'categoriesGrid' => $this->presentGrid($categoryGrid),
            'categoriesKpi' => $categoriesKpiFactory->build(),
            'layoutHeaderToolbarBtn' => $this->getCategoryToolbarButtons($request),
            'currentCategoryView' => $categoryViewData,
            'deleteCategoriesForm' => $deleteCategoriesForm->createView(),
            'isSingleShopContext' => $this->get('prestashop.adapter.shop.context')->isSingleShopContext(),
            'showcaseCardName' => ShowcaseCard::CATEGORIES_CARD,
            'isShowcaseCardClosed' => $showcaseCardIsClosed,
        ]);
    }

    /**
     * Show "Add new" form and handle form submit.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_categories_index",
     *     message="You do not have permission to create this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $categoryFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.category_form_builder');
        $categoryFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.category_form_handler');

        $parentId = (int) $request->query->get('id_parent', $this->configuration->getInt('PS_HOME_CATEGORY'));

        $categoryForm = $categoryFormBuilder->getForm(['id_parent' => $parentId]);
        $categoryForm->handleRequest($request);

        try {
            $handlerResult = $categoryFormHandler->handle($categoryForm);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_categories_index', [
                    'id_category' => $categoryForm->getData()['id_parent'],
                ]);
            }
        } catch (CategoryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $defaultGroups = $this->get('prestashop.adapter.group.provider.default_groups_provider')->getGroups();

        return $this->render(
            '@PrestaShop/Admin/Sell/Catalog/Categories/create.html.twig',
            [
                'allowMenuThumbnailsUpload' => true,
                'categoryForm' => $categoryForm->createView(),
                'defaultGroups' => $defaultGroups,
                'categoryUrl' => $this->get('prestashop.adapter.shop.url.category_provider')
                ->getUrl(0, '{friendy-url}'),
            ]
        );
    }

    /**
     * Show "Add new root category" page & process adding.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_categories_index",
     *     message="You do not have permission to create this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createRootAction(Request $request)
    {
        $rootCategoryFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.root_category_form_builder');
        $rootCategoryFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.root_category_form_handler');

        $rootCategoryForm = $rootCategoryFormBuilder->getForm();
        $rootCategoryForm->handleRequest($request);

        try {
            $handlerResult = $rootCategoryFormHandler->handle($rootCategoryForm);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_categories_index', [
                    'id_category' => $this->configuration->getInt('PS_ROOT_CATEGORY'),
                ]);
            }
        } catch (CategoryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $defaultGroups = $this->get('prestashop.adapter.group.provider.default_groups_provider')->getGroups();

        return $this->render(
            '@PrestaShop/Admin/Sell/Catalog/Categories/create_root.html.twig',
            [
                'allowMenuThumbnailsUpload' => true,
                'rootCategoryForm' => $rootCategoryForm->createView(),
                'defaultGroups' => $defaultGroups,
                'categoryUrl' => $this->get('prestashop.adapter.shop.url.category_provider')
                ->getUrl(0, '{friendy-url}'),
            ]
        );
    }

    /**
     * Show & process category editing.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_categories_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $categoryId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($categoryId, Request $request)
    {
        try {
            /** @var EditableCategory $editableCategory */
            $editableCategory = $this->getQueryBus()->handle(new GetCategoryForEditing((int) $categoryId));

            if ($editableCategory->isRootCategory()) {
                return $this->redirectToRoute('admin_categories_edit_root', ['categoryId' => $categoryId]);
            }
        } catch (CategoryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_categories_index');
        }

        try {
            $categoryFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.category_form_builder');
            $categoryFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.category_form_handler');

            $categoryFormOptions = [
                'id_category' => (int) $categoryId,
            ];

            $categoryForm = $categoryFormBuilder->getFormFor((int) $categoryId, [], $categoryFormOptions);
            $categoryForm->handleRequest($request);

            $handlerResult = $categoryFormHandler->handleFor((int) $categoryId, $categoryForm);

            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_categories_index', [
                    'id_category' => $categoryForm->getData()['id_parent'],
                ]);
            }
        } catch (CategoryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $defaultGroups = $this->get('prestashop.adapter.group.provider.default_groups_provider')->getGroups();

        return $this->render(
            '@PrestaShop/Admin/Sell/Catalog/Categories/edit.html.twig',
            [
                'allowMenuThumbnailsUpload' => $editableCategory->canContainMoreMenuThumbnails(),
                'maxMenuThumbnails' => count(MenuThumbnailId::ALLOWED_ID_VALUES),
                'contextLangId' => $this->getContextLangId(),
                'editCategoryForm' => $categoryForm->createView(),
                'editableCategory' => $editableCategory,
                'defaultGroups' => $defaultGroups,
                'categoryUrl' => $this->get('prestashop.adapter.shop.url.category_provider')
                ->getUrl($categoryId, '{friendy-url}'),
            ]
        );
    }

    /**
     * Show and process category editing.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_categories_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $categoryId
     * @param Request $request
     *
     * @return Response
     */
    public function editRootAction($categoryId, Request $request)
    {
        try {
            /** @var EditableCategory $editableCategory */
            $editableCategory = $this->getQueryBus()->handle(new GetCategoryForEditing((int) $categoryId));

            if (!$editableCategory->isRootCategory()) {
                return $this->redirectToRoute('admin_categories_edit', ['categoryId' => $categoryId]);
            }
        } catch (CategoryNotFoundException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_categories_index');
        }

        $rootCategoryFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.root_category_form_builder');
        $rootCategoryFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.root_category_form_handler');

        try {
            $rootCategoryForm = $rootCategoryFormBuilder->getFormFor((int) $categoryId);
            $rootCategoryForm->handleRequest($request);

            $handlerResult = $rootCategoryFormHandler->handleFor((int) $categoryId, $rootCategoryForm);

            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_categories_index', [
                    'id_category' => $this->configuration->getInt('PS_ROOT_CATEGORY'),
                ]);
            }
        } catch (CategoryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $defaultGroups = $this->get('prestashop.adapter.group.provider.default_groups_provider')->getGroups();

        return $this->render(
            '@PrestaShop/Admin/Sell/Catalog/Categories/edit_root.html.twig',
            [
                'allowMenuThumbnailsUpload' => $editableCategory->canContainMoreMenuThumbnails(),
                'maxMenuThumbnails' => count(MenuThumbnailId::ALLOWED_ID_VALUES),
                'contextLangId' => $this->getContextLangId(),
                'editRootCategoryForm' => $rootCategoryForm->createView(),
                'editableCategory' => $editableCategory,
                'defaultGroups' => $defaultGroups,
                'categoryUrl' => $this->get('prestashop.adapter.shop.url.category_provider')
                ->getUrl($categoryId, '{friendy-url}'),
            ]
        );
    }

    /**
     * Deletes category cover image.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_categories_edit",
     *     redirectQueryParamsToKeep={"categoryId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param Request $request
     * @param int $categoryId
     *
     * @return RedirectResponse
     */
    public function deleteCoverImageAction(Request $request, $categoryId)
    {
        if (!$this->isCsrfTokenValid('delete-cover-image', $request->request->get('_csrf_token'))) {
            return $this->redirectToRoute('admin_security_compromised', [
                'uri' => $this->generateUrl('admin_categories_edit', [
                    'categoryId' => $categoryId,
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        }

        try {
            $this->getCommandBus()->handle(new DeleteCategoryCoverImageCommand((int) $categoryId));

            $this->addFlash(
                'success',
                $this->trans('The image was successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (CategoryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_categories_edit', [
            'categoryId' => $categoryId,
        ]);
    }

    /**
     * Delete given menu thumbnail for category.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_categories_edit",
     *     redirectQueryParamsToKeep={"categoryId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param Request $request
     * @param int $categoryId
     * @param int $menuThumbnailId
     *
     * @return RedirectResponse
     */
    public function deleteMenuThumbnailAction(Request $request, $categoryId, $menuThumbnailId)
    {
        if (!$this->isCsrfTokenValid('delete-menu-thumbnail', $request->request->get('_csrf_token'))) {
            return $this->redirectToRoute('admin_security_compromised', [
                'uri' => $this->generateUrl('admin_categories_edit', [
                    'categoryId' => $categoryId,
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        }

        try {
            $this->getCommandBus()->handle(new DeleteCategoryMenuThumbnailImageCommand(
                (int) $categoryId,
                (int) $menuThumbnailId
            ));

            $this->addFlash(
                'success',
                $this->trans('The image was successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (CategoryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_categories_edit', [
            'categoryId' => $categoryId,
        ]);
    }

    /**
     * Toggle category status.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this."
     * )
     *
     * @param int $categoryId
     *
     * @return JsonResponse
     */
    public function toggleStatusAction($categoryId)
    {
        if ($this->isDemoModeEnabled()) {
            return $this->json([
                'status' => false,
                'message' => $this->getDemoModeErrorMessage(),
            ]);
        }

        try {
            $command = new ToggleCategoryStatusCommand((int) $categoryId);

            $this->getCommandBus()->handle($command);

            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success'),
            ];
        } catch (CategoryException $e) {
            $response = [
                'status' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages()),
            ];
        }

        return $this->json($response);
    }

    /**
     * Process bulk action for categories status enabling.
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_categories_index",
     *     message="You do not have permission to update this."
     * )
     * @DemoRestricted(redirectRoute="admin_categories_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkEnableStatusAction(Request $request)
    {
        try {
            $categoryIds = $this->getBulkCategoriesFromRequest($request);

            $command = new BulkEnableCategoriesCommand($categoryIds);

            $this->getCommandBus()->handle($command);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CategoryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_categories_index');
    }

    /**
     * Process bulk action for categories status disabling.
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_categories_index",
     *     message="You do not have permission to update this."
     * )
     * @DemoRestricted(redirectRoute="admin_categories_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDisableStatusAction(Request $request)
    {
        try {
            $categoryIds = $this->getBulkCategoriesFromRequest($request);

            $command = new BulkDisableCategoriesCommand($categoryIds);

            $this->getCommandBus()->handle($command);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CategoryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_categories_index');
    }

    /**
     * Processes bulk categories deleting.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_categories_index",
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(redirectRoute="admin_categories_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        $deleteCategoriesForm = $this->createForm(DeleteCategoriesType::class);
        $deleteCategoriesForm->handleRequest($request);

        if ($deleteCategoriesForm->isSubmitted()) {
            try {
                $categoriesDeleteData = $deleteCategoriesForm->getData();
                $categoryIds = array_map(function ($categoryId) {
                    return (int) $categoryId;
                }, $categoriesDeleteData['categories_to_delete']);

                $command = new BulkDeleteCategoriesCommand(
                    $categoryIds,
                    $categoriesDeleteData['delete_mode']
                );

                $this->getCommandBus()->handle($command);

                $this->addFlash(
                    'success',
                    $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
                );
            } catch (CategoryException $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
            }
        }

        return $this->redirectToRoute('admin_categories_index');
    }

    /**
     * Process single category deleting.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_categories_index",
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(redirectRoute="admin_categories_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $deleteCategoriesForm = $this->createForm(DeleteCategoriesType::class);
        $deleteCategoriesForm->handleRequest($request);

        if ($deleteCategoriesForm->isSubmitted()) {
            $categoriesDeleteData = $deleteCategoriesForm->getData();

            try {
                $command = new DeleteCategoryCommand(
                    (int) reset($categoriesDeleteData['categories_to_delete']),
                    $categoriesDeleteData['delete_mode']
                );

                $this->getCommandBus()->handle($command);

                $this->addFlash('success', $this->trans('Successful deletion.', 'Admin.Notifications.Success'));
            } catch (CategoryException $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
            }
        }

        return $this->redirectToRoute('admin_categories_index');
    }

    /**
     * Export filtered categories.
     *
     * @AdminSecurity(
     *     "is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_categories_index",
     *     message="You do not have permission to view this."
     * )
     * @DemoRestricted(redirectRoute="admin_categories_index")
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
            ->setFileName('category_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * Updates category position
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_categories_index",
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updatePositionAction(Request $request)
    {
        try {
            $this->getCommandBus()->handle(new UpdateCategoryPositionCommand(
                $request->request->getInt('id_category_to_move'),
                $request->request->getInt('id_category_parent'),
                $request->request->getInt('way'),
                $request->request->get('positions'),
                $request->request->getBoolean('found_first')
            ));
        } catch (CategoryException $e) {
            return $this->json([
                'success' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages()),
            ]);
        }

        return $this->json([
            'success' => true,
            'message' => $this->trans('Successful update.', 'Admin.Notifications.Success'),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getCategoryToolbarButtons(Request $request)
    {
        $toolbarButtons = [];

        if ($this->get('prestashop.adapter.feature.multistore')->isUsed()) {
            $toolbarButtons['add_root'] = [
                'href' => $this->generateUrl('admin_categories_create_root'),
                'desc' => $this->trans('Add new root category', 'Admin.Catalog.Feature'),
                'icon' => 'add_circle_outline',
            ];
        }

        $categoryId = $request->query->get('id_category', $this->configuration->getInt('PS_HOME_CATEGORY'));

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_categories_create', ['id_parent' => $categoryId]),
            'desc' => $this->trans('Add new category', 'Admin.Catalog.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }

    /**
     * Get translated error messsages for category exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            CannotDeleteImageException::class => $this->trans('Unable to delete associated images.', 'Admin.Notifications.Error'),
            CategoryNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
            CategoryConstraintException::class => [
                CategoryConstraintException::EMPTY_BULK_DELETE_DATA => $this->trans('You must select at least one element to delete.', 'Admin.Notifications.Error'),
                CategoryConstraintException::TOO_MANY_MENU_THUMBNAILS => sprintf(
                    '%s %s',
                    $this->trans('An error occurred while uploading the image:', 'Admin.Catalog.Notification'),
                    $this->trans('You cannot upload more files', 'Admin.Notifications.Error')
                ),
            ],
            CannotDeleteRootCategoryForShopException::class => $this->trans(
                'You cannot remove this category because one of your shops uses it as a root category.',
                'Admin.Catalog.Notification'
            ),
            CannotUpdateCategoryStatusException::class => $this->trans(
                'An error occurred while updating the status for an object.',
                'Admin.Notifications.Error'
            ),
            MenuThumbnailsLimitException::class => sprintf(
                '%s %s',
                $this->trans('An error occurred while uploading the image:', 'Admin.Catalog.Notification'),
                $this->trans('You cannot upload more files', 'Admin.Notifications.Error')
            ),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkCategoriesFromRequest(Request $request)
    {
        $categoryIds = $request->request->get('category_id_category');

        if (!is_array($categoryIds)) {
            return [];
        }

        foreach ($categoryIds as $i => $categoryId) {
            $categoryIds[$i] = (int) $categoryId;
        }

        return $categoryIds;
    }
}
