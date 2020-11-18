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

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShopBundle\Form\Admin\Category\SimpleCategory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin controller for the Category pages.
 */
class CategoryController extends FrameworkBundleAdminController
{
    /**
     * Process Ajax Form to add a simple category (name and parent category).
     *
     * @param Request $request
     *
     * @return string
     */
    public function addSimpleCategoryFormAction(Request $request)
    {
        $response = new JsonResponse();
        $commandBus = $this->get('prestashop.core.command_bus');
        $tools = $this->get('prestashop.adapter.tools');
        $shopContext = $this->get('prestashop.adapter.shop.context');
        $shopList = $shopContext->getShops(false, true);
        $currentIdShop = $shopContext->getContextShopID();
        $defaultLanguageId = $this->get('prestashop.adapter.legacy.configuration')->getInt('PS_LANG_DEFAULT');

        $form = $this->createFormBuilder()
            ->add('category', SimpleCategory::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $localizedName = [
                $defaultLanguageId => $data['category']['name'],
            ];

            $command = new AddCategoryCommand(
                $localizedName,
                [$defaultLanguageId => $tools->linkRewrite($data['category']['name'])],
                true,
                (int) $data['category']['id_parent']
            );

            $command->setAssociatedShopIds($currentIdShop ? [$currentIdShop => $currentIdShop] : $shopList);

            try {
                /** @var CategoryId $categoryId */
                $categoryId = $commandBus->handle($command);

                if ($categoryId->getValue()) {
                    $response->setData(
                        [
                            'category' => [
                                'id' => $categoryId->getValue(),
                                'id_parent' => $data['category']['id_parent'],
                                'name' => $localizedName,
                            ],
                        ]
                    );

                    if ($request->query->has('id_product')) {
                        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
                        $product = $productAdapter->getProduct($request->query->get('id_product'));
                        $product->addToCategories($categoryId->getValue());
                        $product->save();
                    }
                }
            } catch (CategoryException $e) {
                // @todo error handling should be implemented.
            }
        } else {
            $response->setStatusCode(400);
            $response->setData($this->getFormErrorsForJS($form));
        }

        return $response;
    }

    /**
     * Get Categories formatted like ajax_product_file.php.
     *
     * @param $limit
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getAjaxCategoriesAction($limit, Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException('Should be ajax request.');
        }

        return new JsonResponse(
            $this->get('prestashop.adapter.data_provider.category')->getAjaxCategories($request->get('query'), $limit, true)
        );
    }
}
