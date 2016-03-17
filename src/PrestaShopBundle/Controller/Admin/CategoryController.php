<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Admin controller for the Category pages
 */
class CategoryController extends FrameworkBundleAdminController
{
    /**
     * Process Ajax Form to add a simple category (name and parent category)
     *
     * @param Request $request
     *
     * @return string
     */
    public function addSimpleCategoryFormAction(Request $request)
    {
        $response = new JsonResponse();
        $tools = $this->container->get('prestashop.adapter.tools');
        $shopContext = $this->container->get('prestashop.adapter.shop.context');
        $shopList = $shopContext->getShops(false, true);
        $currentIdShop = $shopContext->getContextShopID();

        $form = $this->createFormBuilder()
            ->add('category', 'PrestaShopBundle\Form\Admin\Category\SimpleCategory')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $_POST = [
                'submitAddcategory' => 1,
                'name_1' => $data['category']['name'],
                'id_parent' => $data['category']['id_parent'],
                'link_rewrite_1' => $tools->link_rewrite($data['category']['name']),
                'active' => 1,
                'checkBoxShopAsso_category' => $currentIdShop ? [$currentIdShop => $currentIdShop] : $shopList,
            ];

            $adminCategoryController = $this->container->get('prestashop.adapter.admin.controller.category')->getInstance();
            if ($category = $adminCategoryController->processAdd()) {
                $response->setData(['category' => $category]);
            }
        } else {
            $response->setStatusCode(400);
            $response->setData($this->getFormErrorsForJS($form));
        }

        return $response;
    }
}
