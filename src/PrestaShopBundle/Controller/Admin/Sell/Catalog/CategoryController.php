<?php
/**
 * 2007-2018 PrestaShop
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

use PrestaShop\PrestaShop\Core\Domain\Category\Command\AbstractAddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
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
     * @throws CategoryException
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
                $this->fillCommandWithFormData($command, $data);

                $this->getCommandBus()->handle($command);

                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_category_add');
            } catch (CategoryException $e) {
                //@todo: handle properly
                throw $e;
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/add.html.twig', [
            'layoutTitle' => $this->trans('Add new', 'Admin.Actions'),
            'categoryForm' => $categoryAddForm->createView(),
            'defaultGroups' => $defaultGroups,
        ]);
    }

    /**
     * Show "Add new root category" page & process adding
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addRootAction(Request $request)
    {
        $rootCategoryForm = $this->createForm(RootCategoryType::class);
        $rootCategoryForm->handleRequest($request);

        if ($rootCategoryForm->isSubmitted()) {
            $data = $rootCategoryForm->getData();

            try {
                $command = new AddRootCategoryCommand(
                    $data['name'],
                    $data['link_rewrite'],
                    $data['active']
                );
                $this->fillCommandWithFormData($command, $data);

                $this->getCommandBus()->handle($command);

                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_category_add');
            } catch (CategoryException $e) {
                throw $e; //@todo: handle
            }
        }

        /** @var NamesForDefaultGroups $nameForDefaultGroups */
        $nameForDefaultGroups = $this->getQueryBus()->handle(new GetDefaultGroups());

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/add_root.html.twig', [
            'layoutTitle' => $this->trans('Add new', 'Admin.Actions'),
            'rootCategoryForm' => $rootCategoryForm->createView(),
            'namesForDefaultGroups' => $nameForDefaultGroups,
        ]);
    }

    /**
     * Show & process category editing
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

        /** @var NamesForDefaultGroups $nameForDefaultGroups */
        $nameForDefaultGroups = $this->getQueryBus()->handle(new GetDefaultGroups());

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
            'namesForDefaultGroups' => $nameForDefaultGroups,
        ]);
    }

    /**
     * @param AbstractAddCategoryCommand $command
     * @param array $data
     */
    protected function fillCommandWithFormData(AbstractAddCategoryCommand $command, array $data)
    {
        if (isset($data['description'])) {
            $command->setDescription($data['description']);
        }

        if (isset($data['meta_title'])) {
            $command->setMetaTitle($data['meta_title']);
        }

        if (isset($data['meta_description'])) {
            $command->setMetaDescription($data['meta_description']);
        }

        if (isset($data['meta_keyword'])) {
            $command->setMetaKeywords($data['meta_keyword']);
        }

        if (isset($data['group_association'])) {
            $command->setAssociatedGroupIds($data['group_association']);
        }

        if (isset($data['shop_association'])) {
            $command->setAssociatedShopIds($data['shop_association']);
        }

        if (isset($data['cover_image'])) {
            $command->setCoverImage($data['cover_image']);
        }

        if (isset($data['thumbnail_image'])) {
            $command->setThumbnailImage($data['thumbnail_image']);
        }

        if (isset($data['menu_thumbnail_images'])) {
            $command->setMenuThumbnailImages($data['menu_thumbnail_images']);
        }
    }
}
