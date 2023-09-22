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
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryResult\Attribute;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query\GetAttributeGroupList;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryResult\AttributeGroup;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Query\GetProductAttributeGroups;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\BulkDeleteCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\DeleteCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\GenerateProductCombinationsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\BulkCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotGenerateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationIds;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\SearchCombinationsForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\SearchProductCombinations;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationListForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\ProductCombinationsCollection;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
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
        try {
            $combinationForm = $this->getCombinationFormBuilder()->getFormFor($combinationId);
        } catch (CombinationNotFoundException $e) {
            return $this->render(
                '@PrestaShop/Admin/Sell/Catalog/Product/Combination/not_found.html.twig',
                [],
                new Response('', Response::HTTP_NOT_FOUND)
            );
        }

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
            'lightDisplay' => $liteDisplaying,
            'combinationForm' => $combinationForm->createView(),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param string $languageCode
     *
     * @return JsonResponse
     */
    public function searchCombinationsForAssociationAction(
        Request $request,
        string $languageCode
    ): JsonResponse {
        $langRepository = $this->get('prestashop.core.admin.lang.repository');
        $language = $langRepository->getOneByLocaleOrIsoCode($languageCode);
        if (null === $language) {
            return $this->json([
                'message' => sprintf(
                    'Invalid language code %s was used which matches no existing language in this shop.',
                    $languageCode
                ),
            ], Response::HTTP_BAD_REQUEST);
        }

        $shopId = $this->get('prestashop.adapter.shop.context')->getContextShopID();
        if (empty($shopId)) {
            $shopId = $this->getConfiguration()->getInt('PS_SHOP_DEFAULT');
        }

        try {
            /** @var CombinationForAssociation[] $combinationProducts */
            $combinationProducts = $this->getQueryBus()->handle(new SearchCombinationsForAssociation(
                $request->get('query', ''),
                $language->getId(),
                (int) $shopId,
                $request->get('filters', []),
                (int) $request->get('limit', 20)
            ));
        } catch (ProductConstraintException $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        if (empty($combinationProducts)) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->formatCombinationProductsForAssociation($combinationProducts));
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param int $productId
     * @param int|null $shopId
     * @param int|null $languageId
     *
     * @return JsonResponse
     */
    public function searchProductCombinationsAction(
        Request $request,
        int $productId,
        ?int $shopId,
        ?int $languageId
    ): JsonResponse {
        $searchPhrase = $request->query->get('q', '');
        $shopConstraint = $shopId ? ShopConstraint::shop($shopId) : ShopConstraint::allShops();

        /** @var ProductCombinationsCollection $productCombinationsCollection */
        $productCombinationsCollection = $this->getQueryBus()->handle(new SearchProductCombinations(
            $productId,
            $languageId ?: $this->getContextLangId(),
            $shopConstraint,
            $searchPhrase,
            $request->query->getInt('limit', SearchProductCombinations::DEFAULT_RESULTS_LIMIT)
        ));

        return $this->json(['combinations' => $productCombinationsCollection->getProductCombinations()]);
    }

    /**
     * @param CombinationForAssociation[] $combinationsForAssociation
     *
     * @return array<array<string, mixed>>
     */
    protected function formatCombinationProductsForAssociation(array $combinationsForAssociation): array
    {
        $productsData = [];
        foreach ($combinationsForAssociation as $productForAssociation) {
            $productsData[] = [
                'product_id' => $productForAssociation->getProductId(),
                'unique_identifier' => $productForAssociation->getProductId() . '_' . $productForAssociation->getCombinationId(),
                'name' => $productForAssociation->getName(),
                'reference' => $productForAssociation->getReference(),
                'combination_id' => $productForAssociation->getCombinationId(),
                'image' => $productForAssociation->getImageUrl(),
                'quantity' => 1,
            ];
        }

        return $productsData;
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $productId
     *
     * @return Response
     */
    public function bulkEditFormAction(Request $request, int $productId): Response
    {
        $bulkCombinationForm = $this->getBulkCombinationFormBuilder()->getForm([], [
            'product_id' => $productId,
            'country_id' => $this->get('prestashop.adapter.legacy.context')->getCountryId(),
            'shop_id' => $this->getContextShopId(),
            'method' => Request::METHOD_PATCH,
        ]);
        $bulkCombinationForm->handleRequest($request);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/Combination/bulk.html.twig', [
            'bulkCombinationForm' => $bulkCombinationForm->createView(),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param int $productId
     *
     * @return JsonResponse
     */
    public function bulkEditAction(Request $request, int $productId): JsonResponse
    {
        $combinationIds = $request->request->get('combinationIds');
        if (!$combinationIds) {
            return $this->json([
                'error' => $this->getFallbackErrorMessage('', 0, 'Missing combinationIds in request body'),
            ], Response::HTTP_BAD_REQUEST);
        }

        $combinationIds = json_decode($combinationIds);
        $errors = [];
        foreach ($combinationIds as $combinationId) {
            try {
                // PATCH request is required to avoid disabled fields to be forced with null values
                $bulkCombinationForm = $this->getBulkCombinationFormBuilder()->getFormFor($combinationId, [], [
                    'method' => Request::METHOD_PATCH,
                    'product_id' => $productId,
                    'country_id' => $this->get('prestashop.adapter.legacy.context')->getCountryId(),
                    'shop_id' => $this->getContextShopId(),
                ]);
            } catch (CombinationNotFoundException $e) {
                $errors[] = $this->getErrorMessageForException($e, $this->getErrorMessages($e));
                continue;
            }

            try {
                $bulkCombinationForm->handleRequest($request);
                $result = $this->getBulkCombinationFormHandler()->handleFor($combinationId, $bulkCombinationForm);

                if (!$result->isSubmitted()) {
                    return $this->json([
                        'error' => $this->getFallbackErrorMessage('', 0, 'No submitted data'),
                    ], Response::HTTP_BAD_REQUEST);
                }

                if (!$result->isValid()) {
                    // it's the same form for all combinations, so if it is invalid for one, it will be invalid for all of them,
                    // so we return and break the loop
                    return $this->json([
                        'error' => $this->trans('Form contains invalid values', 'Admin.Notifications.Error'),
                        'formErrors' => $this->getFormErrorsForJS($bulkCombinationForm),
                    ], Response::HTTP_BAD_REQUEST);
                }
            } catch (CombinationException $e) {
                $errors[] = $this->getErrorMessageForException($e, $this->getErrorMessages($e));
            }
        }

        if (empty($errors)) {
            return $this->json(['success' => true]);
        }

        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @AdminSecurity("is_granted('read', 'AdminProducts')")
     *
     * Note: role must be hard coded because there is no route associated to this action therefore not
     * _legacy_controller request parameter.
     *
     * It can only be embedded into another view (does not have a route), it is included in this template:
     *
     * src/PrestaShopBundle/Resources/views/Admin/Sell/Catalog/Product/FormTheme/combination.html.twig
     *
     * @param int $productId
     *
     * @return Response
     */
    public function paginatedListAction(int $productId): Response
    {
        $combinationsForm = $this->getCombinationListFormBuilder()->getForm();
        $contextShop = $this->getContext()->shop;

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/Combination/paginated_list.html.twig', [
            'productId' => $productId,
            'combinationLimitChoices' => self::COMBINATIONS_PAGINATION_OPTIONS,
            'combinationsLimit' => ProductCombinationFilters::LIST_LIMIT,
            'combinationsForm' => $combinationsForm->createView(),
            'isMultistoreActive' => $this->get('prestashop.adapter.multistore_feature')->isActive(),
            'shopName' => $contextShop->name,
            'shopId' => $contextShop->id,
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param int $productId
     * @param int|null $shopId
     *
     * @return JsonResponse
     */
    public function getAttributeGroupsAction(int $productId, ?int $shopId): JsonResponse
    {
        /** @var AttributeGroup[] $attributeGroups */
        $attributeGroups = $this->getQueryBus()->handle(new GetProductAttributeGroups(
            $productId,
            $shopId ? ShopConstraint::shop($shopId) : ShopConstraint::allShops()
        ));

        return $this->json($this->formatAttributeGroupsForPresentation($attributeGroups));
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param int|null $shopId
     *
     * @return JsonResponse
     */
    public function getAllAttributeGroupsAction(?int $shopId): JsonResponse
    {
        /** @var AttributeGroup[] $attributeGroups */
        $attributeGroups = $this->getQueryBus()->handle(new GetAttributeGroupList(
            $shopId ? ShopConstraint::shop($shopId) : ShopConstraint::allShops()
        ));

        return $this->json($this->formatAttributeGroupsForPresentation($attributeGroups));
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
            ShopConstraint::shop($combinationFilters->getShopId()),
            $combinationFilters->getLimit(),
            $combinationFilters->getOffset(),
            $combinationFilters->getOrderBy(),
            $combinationFilters->getOrderWay(),
            $combinationFilters->getFilters()
        ));

        return $this->json($this->formatListForPresentation($combinationsList));
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $productId
     * @param ProductCombinationFilters $filters
     *
     * @return JsonResponse
     */
    public function getCombinationIdsAction(int $productId, ProductCombinationFilters $filters): JsonResponse
    {
        $combinationIds = $this->getQueryBus()->handle(new GetCombinationIds(
            $productId,
            $filters->getShopConstraint(),
            $filters->getLimit(),
            $filters->getOffset(),
            $filters->getOrderBy(),
            $filters->getOrderWay(),
            $filters->getFilters()
        ));

        return $this->json(array_map(static function (CombinationId $combinationId): int {
            return $combinationId->getValue();
        }, $combinationIds));
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @param int $combinationId
     * @param int|null $shopId
     *
     * @return JsonResponse
     */
    public function deleteAction(int $combinationId, ?int $shopId): JsonResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteCombinationCommand(
                $combinationId,
                $shopId ? ShopConstraint::shop($shopId) : ShopConstraint::allShops()
            ));
        } catch (Exception $e) {
            return $this->json([
                'error' => $this->getErrorMessageForException($e, $this->getErrorMessages($e)),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'message' => $this->trans('Successful deletion', 'Admin.Notifications.Success'),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param int $productId
     * @param int|null $shopId
     *
     * @return JsonResponse
     */
    public function bulkDeleteAction(Request $request, int $productId, ?int $shopId): JsonResponse
    {
        $combinationIds = $request->request->get('combinationIds');
        if (!$combinationIds) {
            return $this->json([
                'error' => $this->getFallbackErrorMessage('', 0, 'Missing combinationIds in request body'),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->getCommandBus()->handle(new BulkDeleteCombinationCommand(
                $productId,
                json_decode($combinationIds),
                $shopId ? ShopConstraint::shop($shopId) : ShopConstraint::allShops()
            ));
        } catch (Exception $e) {
            if ($e instanceof BulkCombinationException) {
                return $this->jsonBulkErrors($e);
            }

            return $this->json([
                'error' => $this->getErrorMessageForException($e, $this->getErrorMessages($e)),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['success' => true]);
    }

    /**
     * Format the bulk exception into an array of errors returned in a JsonResponse.
     *
     * @param BulkCombinationException $bulkCombinationException
     *
     * @return JsonResponse
     */
    private function jsonBulkErrors(BulkCombinationException $bulkCombinationException): JsonResponse
    {
        $errors = [];
        foreach ($bulkCombinationException->getBulkExceptions() as $productId => $productException) {
            $errors[] = $this->trans(
                'Error for combination %combination_id%: %error_message%',
                'Admin.Catalog.Notification',
                [
                    '%combination_id%' => $productId,
                    '%error_message%' => $this->getErrorMessageForException($productException, $this->getErrorMessages($productException)),
                ]
            );
        }

        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $productId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateCombinationFromListingAction(int $productId, Request $request): JsonResponse
    {
        $combinationsListForm = $this->getCombinationListFormBuilder()->getForm([], [
            'method' => Request::METHOD_PATCH,
        ]);

        try {
            $combinationsListForm->handleRequest($request);
            $result = $this->getCombinationListFormHandler()->handleFor($productId, $combinationsListForm);

            if (!$result->isSubmitted()) {
                return $this->json(['errors' => $this->getFormErrorsForJS($combinationsListForm)], Response::HTTP_BAD_REQUEST);
            } elseif (!$result->isValid()) {
                return $this->json([
                    'errors' => $this->getFormErrorsForJS($combinationsListForm),
                    'formContent' => $this->renderView('@PrestaShop/Admin/Sell/Catalog/Product/Combination/combination_list_form.html.twig', [
                        'combinationsForm' => $combinationsListForm->createView(),
                    ]),
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            return $this->json(
                ['errors' => [$this->getErrorMessageForException($e, $this->getErrorMessages($e))]],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json([
            'message' => $this->trans('Update successful', 'Admin.Notifications.Success'),
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller'))"
     * )
     *
     * @param int $productId
     * @param int|null $shopId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function generateCombinationsAction(int $productId, ?int $shopId, Request $request): JsonResponse
    {
        $requestAttributeGroups = $request->request->all('attributes');
        $attributes = [];
        foreach ($requestAttributeGroups as $attributeGroupId => $requestAttributes) {
            $attributes[(int) $attributeGroupId] = array_map('intval', $requestAttributes);
        }

        try {
            /** @var CombinationId[] $combinationsIds */
            $combinationsIds = $this->getCommandBus()->handle(new GenerateProductCombinationsCommand(
                $productId,
                $attributes,
                $shopId ? ShopConstraint::shop($shopId) : ShopConstraint::allShops()
            ));
        } catch (Exception $e) {
            return $this->json([
                'error' => [
                    $this->getErrorMessageForException($e, $this->getErrorMessages($e)),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'combination_ids' => array_map(function (CombinationId $combinationId) { return $combinationId->getValue(); }, $combinationsIds),
        ]);
    }

    /**
     * @param AttributeGroup[] $attributeGroups
     *
     * @return array<int, array<string, mixed>>
     */
    private function formatAttributeGroupsForPresentation(array $attributeGroups): array
    {
        $contextLangId = $this->getContextLangId();

        $formattedGroups = [];
        foreach ($attributeGroups as $attributeGroup) {
            $attributes = [];
            /** @var Attribute $attribute */
            foreach ($attributeGroup->getAttributes() as $attribute) {
                $attributeNames = $attribute->getLocalizedNames();
                $attributeData = [
                    'id' => $attribute->getAttributeId(),
                    'name' => $attributeNames[$contextLangId] ?? reset($attributeNames),
                ];
                if (null !== $attribute->getColor()) {
                    $attributeData['color'] = $attribute->getColor();
                }
                $attributes[] = $attributeData;
            }

            $publicNames = $attributeGroup->getLocalizedPublicNames();
            $names = $attributeGroup->getLocalizedNames();
            $formattedGroups[] = [
                'id' => $attributeGroup->getAttributeGroupId(),
                'name' => $names[$contextLangId] ?? reset($names),
                'publicName' => $publicNames[$contextLangId] ?? reset($publicNames),
                'attributes' => $attributes,
            ];
        }

        return $formattedGroups;
    }

    /**
     * @param CombinationListForEditing $combinationListForEditing
     *
     * @return array<string, array<int, array<string,bool|int|string|float>>|int>
     */
    private function formatListForPresentation(CombinationListForEditing $combinationListForEditing): array
    {
        $data = [
            'combinations' => [],
            'total' => $combinationListForEditing->getTotalCombinationsCount(),
        ];

        $fallbackImageUrl = $this->getFallbackImageUrl();
        foreach ($combinationListForEditing->getCombinations() as $combination) {
            $data['combinations'][] = [
                'combination_id' => $combination->getCombinationId(),
                'is_selected' => false,
                'name' => $combination->getCombinationName(),
                'reference' => $combination->getReference(),
                'impact_on_price_te' => (string) $combination->getImpactOnPrice(),
                'quantity' => $combination->getQuantity(),
                'is_default' => $combination->isDefault(),
                'image_url' => $combination->getImageUrl() ?: $fallbackImageUrl,
                'eco_tax' => (string) $combination->getEcoTax(),
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    private function getFallbackImageUrl(): string
    {
        $imageUrlFactory = $this->get(ProductImagePathFactory::class);

        return $imageUrlFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT);
    }

    /**
     * @return FormHandlerInterface
     */
    private function getCombinationListFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.combination_list_form_handler');
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
     * @return FormHandlerInterface
     */
    private function getBulkCombinationFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.bulk_combination_form_handler');
    }

    /**
     * @return FormBuilderInterface
     */
    private function getBulkCombinationFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.bulk_combination_form_builder');
    }

    /**
     * @return FormBuilderInterface
     */
    private function getCombinationListFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.combination_list_form_builder');
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
                    [sprintf('"%s"', $this->trans('Minimum order quantity', 'Admin.Catalog.Feature'))]
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
            CombinationNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                'Admin.Notifications.Error'
            ),
            CannotGenerateCombinationException::class => [
                CannotGenerateCombinationException::DIFFERENT_ATTRIBUTES_BETWEEN_SHOPS => $this->trans(
                    'To create combinations for all your stores, the selected attributes must be available on each of them.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
