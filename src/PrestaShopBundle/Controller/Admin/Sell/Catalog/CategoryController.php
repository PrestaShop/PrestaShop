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

use PrestaShop\PrestaShop\Core\Domain\Product\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\Exception\CategoryException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Catalog\Category\CategoryType;
use Symfony\Component\HttpFoundation\Request;

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
                    $data['id_parent']
                );

                if (isset($data['active'])) {
                    $command->setIsActive($data['active']);
                }

                if (isset($data['description'])) {
                    $command->setDescriptions($data['description']);
                }

                if (isset($data['meta_title'])) {
                    $command->setMetaTitles($data['meta_title']);
                }

                if (isset($data['meta_description'])) {
                    $command->setMetaDescriptions($data['meta_description']);
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
}
