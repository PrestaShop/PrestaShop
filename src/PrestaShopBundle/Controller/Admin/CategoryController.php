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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AssignProductToCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotAssignProductToCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShopBundle\Form\Admin\Category\SimpleCategory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @deprecated since 8.1 and will be removed in next major.
 *
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
        $commandBus = $this->getCommandBus();
        $tools = $this->get(Tools::class);
        $shopContext = $this->get('prestashop.adapter.shop.context');
        $shopList = $shopContext->getShops(false, true);
        $currentIdShop = $shopContext->getContextShopID();
        $defaultLanguageId = $this->getConfiguration()->getInt('PS_LANG_DEFAULT');

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
                        $assignProductToCategoryCommand = new AssignProductToCategoryCommand(
                            $categoryId->getValue(),
                            $request->query->getInt('id_product')
                        );
                        $commandBus->handle($assignProductToCategoryCommand);
                    }
                }
            } catch (CategoryException $e) {
                // TODO: do some frontend work to display this error message from ajax query
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setData(['error' => $this->getErrorMessageForException($e, $this->getErrorMessages($data['category']['name']))]);
            } catch (ProductException $e) {
                // TODO: do some frontend work to display this error message from ajax query
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setData(['error' => $this->getErrorMessageForException($e, $this->getErrorMessages($data['category']['name']))]);
            }
        } else {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData($this->getFormErrorsForJS($form));
        }

        return $response;
    }

    /**
     * Get Categories formatted like ajax_product_file.php.
     *
     * @param int $limit
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

    /**
     * @param string $categoryName
     *
     * @return array
     */
    private function getErrorMessages(string $categoryName): array
    {
        return [
            CategoryException::class => $this->trans(
                'Category "%s" could not be created.',
                'Admin.Notifications.Error',
                [$categoryName]
            ),
            CannotAssignProductToCategoryException::class => $this->trans(
                'This product could not be assigned to category "%s".',
                'Admin.Notifications.Error',
                [$categoryName]
            ),
        ];
    }
}
