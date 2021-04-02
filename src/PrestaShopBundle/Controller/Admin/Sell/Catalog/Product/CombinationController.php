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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use Exception;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationListForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Product\Combination\CombinationListType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CombinationController extends FrameworkBundleAdminController
{
    /**
     * Options used for the number of combinations per page
     */
    private const COMBINATIONS_PAGINATION_OPTIONS = [ProductCombinationFilters::LIST_LIMIT, 20, 50, 100];

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param int $combinationId
     *
     * @return Response
     */
    public function editAction(Request $request, int $combinationId): Response
    {
        $liteDisplaying = $request->query->has('liteDisplaying');
        $combinationForm = $this->getCombinationFormBuilder()->getFormFor($combinationId);

        try {
            $combinationForm->handleRequest($request);

            $result = $this->getCombinationFormHandler()->handleFor($combinationId, $combinationForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_products_combinations_edit_combination', [
                    'combinationId' => $combinationId,
                    'liteDisplaying' => $liteDisplaying,
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/Combination/edit.html.twig', [
            'combinationForm' => $combinationForm->createView(),
        ]);
    }

    /**
     * Renders combinations list prototype (which contains form inputs submittable by ajax)
     * It can only be embedded into another view (does not have a route)
     *
     * @return Response
     */
    public function listFormAction(): Response
    {
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/Blocks/combinations.html.twig', [
            'combinationLimitChoices' => self::COMBINATIONS_PAGINATION_OPTIONS,
            'combinationsLimit' => ProductCombinationFilters::LIST_LIMIT,
            'combinationsForm' => $this->createForm(CombinationListType::class)->createView(),
            'combinationItemForm' => $this->getCombinationItemFormBuilder()->getForm()->createView(),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param int $productId
     * @param ProductCombinationFilters $combinationFilters
     *
     * @return JsonResponse
     */
    public function getListAction(int $productId, ProductCombinationFilters $combinationFilters): JsonResponse
    {
        $combinationsList = $this->getQueryBus()->handle(new GetEditableCombinationsList(
            $productId,
            $this->getContextLangId(),
            $combinationFilters->getLimit(),
            $combinationFilters->getOffset(),
            $combinationFilters->getOrderBy(),
            $combinationFilters->getOrderWay(),
            $combinationFilters->getFilters()
        ));

        return $this->json($this->formatListResponse($combinationsList));
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $productId
     *
     * @return JsonResponse
     */
    public function getListIdsAction(int $productId): JsonResponse
    {
        /** @var CombinationRepository $repository */
        $repository = $this->get('prestashop.adapter.product.combination.repository.combination_repository');

        $combinationIds = $repository->getCombinationIdsByProductId(new ProductId($productId));
        $data = [];
        foreach ($combinationIds as $combinationId) {
            $data[] = $combinationId->getValue();
        }

        return $this->json($data);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $combinationId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateCombinationFromListingAction(int $combinationId, Request $request): JsonResponse
    {
        $form = $this->getCombinationItemFormBuilder()->getFormFor($combinationId, [], [
            'method' => Request::METHOD_PATCH,
        ]);
        $form->handleRequest($request);

        try {
            $result = $this->getCombinationItemFormHandler()->handleFor($combinationId, $form);

            if (!$result->isValid()) {
                return $this->json(['errors' => $this->getFormErrorsForJS($form)], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            return $this->json(
                ['errors' => [$this->getFallbackErrorMessage(get_class($e), $e->getCode(), $e->getMessage())]],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json([]);
    }

    /**
     * @param CombinationListForEditing $combinationListForEditing
     *
     * @return array<string, array<int, array<string,bool|int|string>>|int>
     */
    private function formatListResponse(CombinationListForEditing $combinationListForEditing): array
    {
        $data = [
            'combinations' => [],
            'total' => $combinationListForEditing->getTotalCombinationsCount(),
        ];
        foreach ($combinationListForEditing->getCombinations() as $combination) {
            $data['combinations'][] = [
                'id' => $combination->getCombinationId(),
                'isSelected' => false,
                'name' => $combination->getCombinationName(),
                'reference' => $combination->getReference(),
                //@todo: don't forget image path when implemented in the query
                'impactOnPrice' => (string) $combination->getImpactOnPrice(),
                'quantity' => $combination->getQuantity(),
                'isDefault' => $combination->isDefault(),
            ];
        }

        return $data;
    }

    /**
     * @return FormHandlerInterface
     */
    private function getCombinationItemFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.combination_item_form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getCombinationFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.combination_form_handler');
    }

    /**
     * @return FormBuilderInterface
     */
    private function getCombinationFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.combination_form_builder');
    }

    /**
     * @return FormBuilderInterface
     */
    private function getCombinationItemFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.combination_item_form_builder');
    }

    /**
     * Gets an error by exception class and its code.
     *
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e): array
    {
        return [
            ProductConstraintException::class => [
                ProductConstraintException::INVALID_LOW_STOCK_THRESHOLD => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Low stock level', 'Admin.Catalog.Feature'))]
                ),
                ProductConstraintException::INVALID_LOW_STOCK_ALERT => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Low stock alert', 'Admin.Catalog.Feature'))]
                ),
                ProductConstraintException::INVALID_AVAILABLE_DATE => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Availability date', 'Admin.Catalog.Feature'))]
                ),
                ProductConstraintException::INVALID_MINIMAL_QUANTITY => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Minimum quantity for sale', 'Admin.Catalog.Feature'))]
                ),
            ],
            ProductStockConstraintException::class => [
                ProductStockConstraintException::INVALID_QUANTITY => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Quantity', 'Admin.Catalog.Feature'))]
                ),
                ProductStockConstraintException::INVALID_LOCATION => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Stock location', 'Admin.Catalog.Feature'))]
                ),
            ],
        ];
    }
}
