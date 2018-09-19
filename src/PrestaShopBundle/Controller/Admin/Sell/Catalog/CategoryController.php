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

use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Catalog\Category\CategoryType;
use PrestaShopBundle\Form\Admin\Catalog\Category\RootCategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends FrameworkBundleAdminController
{
    public function addAction(Request $request)
    {
        $categoryCreateForm = $this->createForm(CategoryType::class);
        $categoryCreateForm->handleRequest($request);

        if ($categoryCreateForm->isSubmitted()) {
            $data = $categoryCreateForm->getData();

            try {
                $command = new AddCategoryCommand(
                    $data['name'],
                    $data['link_rewrite'],
                    (bool) $data['active'],
                    (int) $data['id_parent']
                );

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

                $this->getCommandBus()->handle($command);

                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_category_add');
            } catch (CategoryException $e) {
                //@todo: handle properly
                throw $e;
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/add.html.twig', [
            'categoryForm' => $categoryCreateForm->createView(),
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

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/add_root.html.twig', [
            'rootCategoryForm' => $rootCategoryForm->createView(),
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
        $categoryId = new CategoryId($categoryId);
        /** @var EditableCategory $editableCategory */
        $editableCategory = $this->getQueryBus()->handle(new GetCategoryForEditing($categoryId));

        $categoryForm = $this->createForm(CategoryType::class, [
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
        ]);
        $categoryForm->handleRequest($request);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/edit.html.twig', [
            'editCategoryForm' => $categoryForm->createView(),
            'editableCategory' => $editableCategory,
        ]);
    }
}
