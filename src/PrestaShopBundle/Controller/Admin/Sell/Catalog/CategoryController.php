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

use PrestaShop\PrestaShop\Core\Domain\Category\Command\AbstractRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Group\DefaultGroups;
use PrestaShop\PrestaShop\Core\Domain\Group\Query\GetDefaultGroups;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Catalog\Category\CategoryType;
use PrestaShopBundle\Form\Admin\Catalog\Category\RootCategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryController handles "Sell > Catalog > Categories" pages.
 */
class CategoryController extends FrameworkBundleAdminController
{
    /**
     * Show "Add new" form and handle form submit.
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

                $this->getCommandBus()->handle($command);

                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_category_add');
            } catch (CategoryException $e) {
                $this->addFlash('error', $this->handleAddException($e));
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/add.html.twig', [
            'layoutTitle' => $this->trans('Add new', 'Admin.Actions'),
            'categoryForm' => $categoryAddForm->createView(),
            'defaultGroups' => $defaultGroups,
        ]);
    }

    /**
     * Show "Add new root category" page & process adding.
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

                $this->getCommandBus()->handle($command);

                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_category_add');
            } catch (CategoryException $e) {
                $this->addFlash('error', $this->handleAddException($e));
            }
        }

        /** @var DefaultGroups $defaultGroups */
        $defaultGroups = $this->getQueryBus()->handle(new GetDefaultGroups());

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/add_root.html.twig', [
            'layoutTitle' => $this->trans('Add new', 'Admin.Actions'),
            'rootCategoryForm' => $rootCategoryForm->createView(),
            'defaultGroups' => $defaultGroups,
        ]);
    }

    /**
     * Show & process category editing.
     *
     * @param int $categoryId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($categoryId, Request $request)
    {
        $categoryId = new CategoryId((int) $categoryId);

        /** @var EditableCategory $editableCategory */
        $editableCategory = $this->getQueryBus()->handle(new GetCategoryForEditing($categoryId));

        if ($editableCategory->isRootCategory()) {
            return $this->redirectToRoute('admin_category_edit_root', ['categoryId' => $categoryId->getValue()]);
        }

        $categoryFormOptions = [
            'id_category' => $categoryId->getValue(),
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
                $command = new EditCategoryCommand($categoryId);

                $this->populateCommandWithFormData($command, $data);

                if (null !== $data['id_parent']) {
                    $command->setParentCategoryId($data['id_parent']);
                }

                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_category_add');
            } catch (CategoryException $e) {
                $this->addFlash('error', $this->handleEditException($e));
            }
        }

        /** @var DefaultGroups $defaultGroups */
        $defaultGroups = $this->getQueryBus()->handle(new GetDefaultGroups());

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/edit.html.twig', [
            'layoutTitle' => $this->trans(
                'Edit: %value%',
                'Admin.Catalog.Feature',
                [
                    '%value%' => $editableCategory->getName()[$this->getContextLangId()],
                ]
            ),
            'editCategoryForm' => $categoryForm->createView(),
            'editableCategory' => $editableCategory,
            'defaultGroups' => $defaultGroups,
        ]);
    }

    /**
     * Show and process category editing.
     *
     * @param int $categoryId
     * @param Request $request
     *
     * @return Response
     */
    public function editRootAction($categoryId, Request $request)
    {
        $categoryId = new CategoryId((int) $categoryId);

        /** @var EditableCategory $editableCategory */
        $editableCategory = $this->getQueryBus()->handle(new GetCategoryForEditing($categoryId));

        if (!$editableCategory->isRootCategory()) {
            return $this->redirectToRoute('admin_category_edit', ['categoryId' => $categoryId->getValue()]);
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
                $command = new EditRootCategoryCommand($categoryId);

                $this->populateCommandWithFormData($command, $data);

                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_category_add');
            } catch (CategoryException $e) {
                $this->addFlash('error', $this->handleEditException($e));
            }
        }

        /** @var DefaultGroups $defaultGroups */
        $defaultGroups = $this->getQueryBus()->handle(new GetDefaultGroups());

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/edit_root.html.twig', [
            'layoutTitle' => $this->trans(
                'Edit: %value%',
                'Admin.Catalog.Feature',
                [
                    '%value%' => $editableCategory->getName()[$this->getContextLangId()],
                ]
            ),
            'editRootCategoryForm' => $rootCategoryForm->createView(),
            'editableCategory' => $editableCategory,
            'defaultGroups' => $defaultGroups,
        ]);
    }

    /**
     * @param AbstractRootCategoryCommand $command
     * @param array $data
     */
    protected function populateCommandWithFormData(AbstractRootCategoryCommand $command, array $data)
    {
        if (null !== $data['description']) {
            $command->setDescription($data['description']);
        }

        if (null !== $data['meta_title']) {
            $command->setMetaTitle($data['meta_title']);
        }

        if (null !== $data['meta_description']) {
            $command->setMetaDescription($data['meta_description']);
        }

        if (null !== $data['meta_keyword']) {
            $command->setMetaKeywords($data['meta_keyword']);
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

        $messages = [
            CategoryNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
        ];

        if (isset($messages[$type])) {
            return $messages[$type];
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

        $messages = [
            CategoryNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
        ];

        if (isset($messages[$type])) {
            return $messages[$type];
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
        $messages = [
            CategoryConstraintException::TOO_MANY_MENU_THUMBNAILS => sprintf(
                '%s %s',
                $this->trans('An error occurred while uploading the image:', 'Admin.Catalog.Notification'),
                $this->trans('You cannot upload more files', 'Admin.Notifications.Error')
            ),
        ];

        if (isset($messages[$e->getCode()])) {
            return $messages[$e->getCode()];
        }

        return $this->trans('Unexpected error occurred', 'Admin.Notifications.Error');
    }
}
