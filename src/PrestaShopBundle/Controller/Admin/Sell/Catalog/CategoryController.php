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

use PrestaShop\PrestaShop\Core\Domain\Category\Command\AbstractCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkDeleteCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DisableCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCoverImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryMenuThumbnailImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EnableCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\ToggleCategoryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteRootCategoryForShopException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotUpdateCategoryStatusException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteImageException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\MenuThumbnailsLimitException;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\MenuThumbnailId;
use PrestaShop\PrestaShop\Core\Domain\Group\Query\GetDefaultGroups;
use PrestaShop\PrestaShop\Core\Domain\Group\QueryResult\DefaultGroups;
use PrestaShop\PrestaShop\Core\Search\Filters\CategoryFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Catalog\Category\CategoryType;
use PrestaShopBundle\Form\Admin\Catalog\Category\RootCategoryType;
use PrestaShopBundle\Form\Admin\Sell\Category\DeleteCategoriesType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Security\Voter\PageVoter;
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
     * @AdminSecurity("is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller'))")
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

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'categoriesGrid' => $this->presentGrid($categoryGrid),
            'categoriesKpi' => $categoriesKpiFactory->build(),
            'layoutHeaderToolbarBtn' => $this->getCategoryToolbarButtons($request),
            'currentCategoryView' => $categoryViewData,
            'deleteCategoriesForm' => $deleteCategoriesForm->createView(),
        ]);
    }

    /**
     * Show "Add new" form and handle form submit.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     message="You do not have permission to create this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        /** @var DefaultGroups $defaultGroups */
        $defaultGroups = $this->getQueryBus()->handle(new GetDefaultGroups());

        $emptyCategoryData = [
            'group_association' => [
                $defaultGroups->getVisitorsGroup()->getGroupId()->getValue(),
                $defaultGroups->getGuestsGroup()->getGroupId()->getValue(),
                $defaultGroups->getCustomersGroup()->getGroupId()->getValue(),
            ],
            'shop_association' => [
                $this->getContextShopId(),
            ],
        ];

        $categoryAddForm = $this->createForm(CategoryType::class, $emptyCategoryData);
        $categoryAddForm->handleRequest($request);

        if ($categoryAddForm->isSubmitted()) {
            $data = $categoryAddForm->getData();

            try {
                $command = new AddCategoryCommand(
                    $data['name'],
                    $data['link_rewrite'],
                    (bool) $data['active'],
                    (int) $data['id_parent']
                );
                $this->populateCommandWithFormData($command, $data);

                /** @var CategoryId $categoryId */
                $categoryId = $this->getCommandBus()->handle($command);

                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_category_edit', [
                    'categoryId' => $categoryId->getValue(),
                ]);
            } catch (CategoryException $e) {
                $this->addFlash('error', $this->handleAddException($e));
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/add.html.twig', [
            'allowMenuThumbnailsUpload' => true,
            'categoryForm' => $categoryAddForm->createView(),
            'defaultGroups' => $defaultGroups,
        ]);
    }

    /**
     * Show "Add new root category" page & process adding.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     message="You do not have permission to create this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addRootAction(Request $request)
    {
        /** @var DefaultGroups $defaultGroups */
        $defaultGroups = $this->getQueryBus()->handle(new GetDefaultGroups());

        $emptyCategoryData = [
            'group_association' => [
                $defaultGroups->getVisitorsGroup()->getGroupId()->getValue(),
                $defaultGroups->getGuestsGroup()->getGroupId()->getValue(),
                $defaultGroups->getCustomersGroup()->getGroupId()->getValue(),
            ],
            'shop_association' => [
                $this->getContextShopId(),
            ],
        ];

        $rootCategoryForm = $this->createForm(RootCategoryType::class, $emptyCategoryData);
        $rootCategoryForm->handleRequest($request);

        if ($rootCategoryForm->isSubmitted()) {
            $data = $rootCategoryForm->getData();

            try {
                $command = new AddRootCategoryCommand(
                    $data['name'],
                    $data['link_rewrite'],
                    $data['active']
                );
                $this->populateCommandWithFormData($command, $data);

                /** @var CategoryId $categoryId */
                $categoryId = $this->getCommandBus()->handle($command);

                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_category_edit_root', [
                    'categoryId' => $categoryId->getValue(),
                ]);
            } catch (CategoryException $e) {
                $this->addFlash('error', $this->handleAddException($e));
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/add_root.html.twig', [
            'allowMenuThumbnailsUpload' => true,
            'rootCategoryForm' => $rootCategoryForm->createView(),
            'defaultGroups' => $defaultGroups,
        ]);
    }

    /**
     * Show & process category editing.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
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
        /** @var EditableCategory $editableCategory */
        $editableCategory = $this->getQueryBus()->handle(new GetCategoryForEditing((int) $categoryId));

        if ($editableCategory->isRootCategory()) {
            return $this->redirectToRoute('admin_category_edit_root', ['categoryId' => $categoryId]);
        }

        $categoryFormOptions = [
            'id_category' => (int) $categoryId,
        ];

        $categoryFormData = [
            'name' => $editableCategory->getName(),
            'active' => $editableCategory->isActive(),
            'id_parent' => $editableCategory->getParentId(),
            'description' => $editableCategory->getDescription(),
            'meta_title' => $editableCategory->getMetaTitle(),
            'meta_description' => $editableCategory->getMetaDescription(),
            'meta_keyword' => $editableCategory->getMetaKeywords(),
            'link_rewrite' => $editableCategory->getLinkRewrite(),
            'group_association' => $editableCategory->getGroupAssociationIds(),
            'shop_association' => $editableCategory->getShopAssociationIds(),
        ];

        $categoryForm = $this->createForm(CategoryType::class, $categoryFormData, $categoryFormOptions);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted()) {
            $data = $categoryForm->getData();

            try {
                $command = new EditCategoryCommand((int) $categoryId);

                $this->populateCommandWithFormData($command, $data);

                if (null !== $data['id_parent']) {
                    $command->setParentCategoryId($data['id_parent']);
                }

                $this->getCommandBus()->handle($command);

                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_category_edit', [
                    'categoryId' => $categoryId,
                ]);
            } catch (CategoryException $e) {
                $this->addFlash('error', $this->handleEditException($e));
            }
        }

        /** @var DefaultGroups $defaultGroups */
        $defaultGroups = $this->getQueryBus()->handle(new GetDefaultGroups());

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/edit.html.twig', [
            'allowMenuThumbnailsUpload' => $editableCategory->canContainMoreMenuThumbnails(),
            'maxMenuThumbnails' => count(MenuThumbnailId::ALLOWED_ID_VALUES),
            'contextLangId' => $this->getContextLangId(),
            'editCategoryForm' => $categoryForm->createView(),
            'editableCategory' => $editableCategory,
            'defaultGroups' => $defaultGroups,
        ]);
    }

    /**
     * Show and process category editing.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
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
        /** @var EditableCategory $editableCategory */
        $editableCategory = $this->getQueryBus()->handle(new GetCategoryForEditing((int) $categoryId));

        if (!$editableCategory->isRootCategory()) {
            return $this->redirectToRoute('admin_category_edit', ['categoryId' => $categoryId]);
        }

        $rootCategoryForm = $this->createForm(RootCategoryType::class, [
            'name' => $editableCategory->getName(),
            'active' => $editableCategory->isActive(),
            'description' => $editableCategory->getDescription(),
            'meta_title' => $editableCategory->getMetaTitle(),
            'meta_description' => $editableCategory->getMetaDescription(),
            'meta_keyword' => $editableCategory->getMetaKeywords(),
            'link_rewrite' => $editableCategory->getLinkRewrite(),
            'group_association' => $editableCategory->getGroupAssociationIds(),
            'shop_association' => $editableCategory->getShopAssociationIds(),
        ]);
        $rootCategoryForm->handleRequest($request);

        if ($rootCategoryForm->isSubmitted()) {
            $data = $rootCategoryForm->getData();

            try {
                $command = new EditRootCategoryCommand((int) $categoryId);

                $this->populateCommandWithFormData($command, $data);

                $this->getCommandBus()->handle($command);

                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_category_edit_root', [
                    'categoryId' => $categoryId->getValue(),
                ]);
            } catch (CategoryException $e) {
                $this->addFlash('error', $this->handleEditException($e));
            }
        }

        /** @var DefaultGroups $defaultGroups */
        $defaultGroups = $this->getQueryBus()->handle(new GetDefaultGroups());

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/edit_root.html.twig', [
            'allowMenuThumbnailsUpload' => $editableCategory->canContainMoreMenuThumbnails(),
            'maxMenuThumbnails' => count(MenuThumbnailId::ALLOWED_ID_VALUES),
            'contextLangId' => $this->getContextLangId(),
            'editRootCategoryForm' => $rootCategoryForm->createView(),
            'editableCategory' => $editableCategory,
            'defaultGroups' => $defaultGroups,
        ]);
    }

    /**
     * Deletes category cover image.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
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
                'uri' => $this->generateUrl('admin_category_edit', [
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
            $this->addFlash('error', $this->handleImageDeletingException($e));
        }

        return $this->redirectToRoute('admin_category_edit', [
            'categoryId' => $categoryId,
        ]);
    }

    /**
     * Delete given menu thumbnail for category.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
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
                'uri' => $this->generateUrl('admin_category_edit', [
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
            $this->addFlash('error', $this->handleImageDeletingException($e));
        }

        return $this->redirectToRoute('admin_category_edit', [
            'categoryId' => $categoryId,
        ]);
    }

    /**
     * @param AbstractCategoryCommand $command
     * @param array $data
     */
    protected function populateCommandWithFormData(AbstractCategoryCommand $command, array $data)
    {
        if (null !== $data['description']) {
            $command->setLocalizedDescriptions($data['description']);
        }

        if (null !== $data['meta_title']) {
            $command->setLocalizedMetaTitles($data['meta_title']);
        }

        if (null !== $data['meta_description']) {
            $command->setLocalizedMetaDescriptions($data['meta_description']);
        }

        if (null !== $data['meta_keyword']) {
            $command->setLocalizedMetaKeywords($data['meta_keyword']);
        }

        if (null !== $data['group_association']) {
            $command->setAssociatedGroupIds($data['group_association']);
        }

        if (null !== $data['shop_association']) {
            $command->setAssociatedShopIds($data['shop_association']);
        }

        if (null !== $data['cover_image']) {
            $command->setCoverImage($data['cover_image']);
        }

        if (null !== $data['thumbnail_image']) {
            $command->setThumbnailImage($data['thumbnail_image']);
        }

        if (null !== $data['menu_thumbnail_images']) {
            $command->setMenuThumbnailImages($data['menu_thumbnail_images']);
        }
    }

    /**
     * @param CategoryException $exception
     *
     * @return string User friendly error message for exception
     */
    protected function handleAddException(CategoryException $exception)
    {
        $type = get_class($exception);

        if (CategoryConstraintException::class === $type) {
            return $this->handleConstraintException($exception);
        }

        $errorMessagesForDisplay = [
            CategoryNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
        ];

        if (isset($errorMessagesForDisplay[$type])) {
            return $errorMessagesForDisplay[$type];
        }

        return $this->trans('An error occurred while creating an object.', 'Admin.Notifications.Error');
    }

    /**
     * @param CategoryException $exception
     *
     * @return string User friendly error message for exception
     */
    protected function handleEditException(CategoryException $exception)
    {
        $type = get_class($exception);

        if (CategoryConstraintException::class === $type) {
            return $this->handleConstraintException($exception);
        }

        $errorMessagesForDisplay = [
            CategoryNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
            MenuThumbnailsLimitException::class => sprintf(
                '%s %s',
                $this->trans('An error occurred while uploading the image:', 'Admin.Catalog.Notification'),
                $this->trans('You cannot upload more files', 'Admin.Notifications.Error')
            ),
        ];

        if (isset($errorMessagesForDisplay[$type])) {
            return $errorMessagesForDisplay[$type];
        }

        return $this->trans('An error occurred while updating an object.', 'Admin.Notifications.Error');
    }

    /**
     * @param CategoryConstraintException $e
     *
     * @return string
     */
    protected function handleConstraintException(CategoryConstraintException $e)
    {
        $errorMessagesForDisplay = [
            CategoryConstraintException::TOO_MANY_MENU_THUMBNAILS => sprintf(
                '%s %s',
                $this->trans('An error occurred while uploading the image:', 'Admin.Catalog.Notification'),
                $this->trans('You cannot upload more files', 'Admin.Notifications.Error')
            ),
        ];

        if (isset($errorMessagesForDisplay[$e->getCode()])) {
            return $errorMessagesForDisplay[$e->getCode()];
        }

        return $this->trans('Unexpected error occurred', 'Admin.Notifications.Error');
    }

    /**
     * Toggle category status.
     *
     * @param Request $request
     * @param int $categoryId
     *
     * @return JsonResponse
     */
    public function processStatusToggleAction(Request $request, $categoryId)
    {
        if ($this->isDemoModeEnabled()) {
            return $this->json([
                'status' => false,
                'message' => $this->getDemoModeErrorMessage(),
            ]);
        }

        $authLevel = $this->authorizationLevel($request->attributes->get('_legacy_controller'));

        if (!in_array($authLevel, [PageVoter::LEVEL_UPDATE, PageVoter::LEVEL_DELETE])) {
            return $this->json([
                'status' => false,
                'message' => $this->trans('You do not have permission to update this.', 'Admin.Notifications.Error'),
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
                'message' => $this->handleUpdateStatusException($e),
            ];
        }

        return $this->json($response);
    }

    /**
     * Process bulk action for categories status enabling.
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_category_listing",
     *     message="You do not have permission to update this."
     * )
     * @DemoRestricted(redirectRoute="admin_category_listing")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processBulkStatusEnableAction(Request $request)
    {
        try {
            $categoryIds = array_map(function ($categoryId) {
                return (int) $categoryId;
            }, $request->request->get('categories_bulk'));

            $command = new EnableCategoriesCommand($categoryIds);

            $this->getCommandBus()->handle($command);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CategoryException $e) {
            $this->addFlash('error', $this->handleUpdateStatusException($e));
        }

        return $this->redirectToRoute('admin_category_listing');
    }

    /**
     * Process bulk action for categories status disabling.
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_category_listing",
     *     message="You do not have permission to update this."
     * )
     * @DemoRestricted(redirectRoute="admin_category_listing")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processBulkStatusDisableAction(Request $request)
    {
        try {
            $categoryIds = array_map(function ($categoryId) {
                return (int) $categoryId;
            }, $request->request->get('categories_bulk'));

            $command = new DisableCategoriesCommand($categoryIds);

            $this->getCommandBus()->handle($command);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CategoryException $e) {
            $this->addFlash('error', $this->handleUpdateStatusException($e));
        }

        return $this->redirectToRoute('admin_category_listing');
    }

    /**
     * Processes bulk categories deleting.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_category_listing",
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(redirectRoute="admin_category_listing")
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
                $this->addFlash('error', $this->handleDeleteException($e));
            }
        }

        return $this->redirectToRoute('admin_category_listing');
    }

    /**
     * Process single category deleting.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_category_listing",
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(redirectRoute="admin_category_listing")
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

            try {
                $command = new DeleteCategoryCommand(
                    (int) reset($categoriesDeleteData['categories_to_delete']),
                    $categoriesDeleteData['delete_mode']
                );

                $this->getCommandBus()->handle($command);

                $this->addFlash('success', $this->trans('Successful deletion.', 'Admin.Notifications.Success'));
            } catch (CategoryException $e) {
                $this->addFlash('error', $this->handleDeleteException($e));
            }
        }

        return $this->redirectToRoute('admin_category_listing');
    }

    /**
     * Export filtered categories.
     *
     * @AdminSecurity(
     *     "is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_category_listing",
     *     message="You do not have permission to view this."
     * )
     * @DemoRestricted(redirectRoute="admin_category_listing")
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
     * Handle exception which occurs when updating category status.
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
     * Handle exception which occurred when deleting category.
     *
     * @param CategoryException $e
     *
     * @return string
     */
    protected function handleDeleteException(CategoryException $e)
    {
        $type = get_class($e);

        if (CategoryConstraintException::class === $type) {
            $constraintErrors = [
                CategoryConstraintException::EMPTY_BULK_DELETE_DATA => $this->trans('You must select at least one element to delete.', 'Admin.Notifications.Error'),
            ];

            if (isset($constraintErrors[$e->getCode()])) {
                return $constraintErrors[$e->getCode()];
            }
        }

        $errors = [
            CategoryNotFoundException::class => sprintf(
                '%s %s',
                $this->trans('An error occurred while updating the status for an object.', 'Admin.Notifications.Error'),
                $this->trans('(cannot load object)', 'Admin.Notifications.Error')
            ),
            CannotDeleteRootCategoryForShopException::class => $this->trans(
                'You cannot remove this category because one of your shops uses it as a root category.',
                'Admin.Catalog.Notification'
            ),
        ];

        if (isset($errors[$type])) {
            return $errors[$type];
        }

        return $this->trans('An error occurred while deleting this selection.', 'Admin.Notifications.Error');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getCategoryToolbarButtons(Request $request)
    {
        $toolbarButtons = [];

        if ($this->get('prestashop.adapter.feature.multistore')->isActive()) {
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

    /**
     * Handle exception which occurs when deleting category image (cover, thumbnails).
     *
     * @param CategoryException $e
     *
     * @return string
     */
    private function handleImageDeletingException(CategoryException $e)
    {
        $type = get_class($e);

        $errorMessages = [
            CannotDeleteImageException::class => $this->trans('Unable to delete associated images.', 'Admin.Notifications.Error'),
            CategoryNotFoundException::class => sprintf(
                '%s %s',
                $this->trans('An error occurred while updating the status for an object.', 'Admin.Notifications.Error'),
                $this->trans('(cannot load object)', 'Admin.Notifications.Error')
            ),
        ];

        if (isset($errorMessages[$type])) {
            return $errorMessages[$type];
        }

        return $this->getFallbackErrorMessage($type, $e->getCode());
    }
}
