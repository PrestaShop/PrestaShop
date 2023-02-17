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
use PrestaShop\PrestaShop\Adapter\Shop\Url\ProductPreviewProvider;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkUpdateProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductsPositionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductPositionException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\DuplicateFeatureValueAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\InvalidAssociatedFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductIsEnabled;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ProductGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\AdminFilter;
use PrestaShopBundle\Entity\ProductDownload;
use PrestaShopBundle\Form\Admin\Sell\Product\Category\CategoryFilterType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Security\Voter\PageVoter;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Admin controller for the Product pages using the Symfony architecture:
 * - product list (display, search)
 * - product form (creation, edition)
 * - ...
 *
 * Some component displayed in this form are based on ajax request which might implemented
 * in another Controller.
 *
 * This controller is a re-migration of the initial ProductController which was the first
 * one to be migrated but doesn't meet the standards of the recently migrated controller.
 * The retro-compatibility is dropped for the legacy Admin pages, the former hook are no longer
 * managed for backward compatibility, new hooks need to be used in the modules, migration process
 * is detailed in the devdoc. (@todo add devdoc link when ready?)
 */
class ProductController extends FrameworkBundleAdminController
{
    /**
     * Used to validate connected user authorizations.
     */
    private const PRODUCT_CONTROLLER_PERMISSION = 'ADMINPRODUCTS_';

    /**
     * Shows products listing.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param ProductFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, ProductFilters $filters): Response
    {
        if ($this->shouldRedirectToV1()) {
            return $this->redirectToRoute('admin_product_catalog');
        }

        $productGridFactory = $this->get('prestashop.core.grid.factory.product');
        $productGrid = $productGridFactory->getGrid($filters);

        $filteredCategoryId = null;
        if (isset($filters->getFilters()['id_category'])) {
            $filteredCategoryId = (int) $filters->getFilters()['id_category'];
        }
        $categoriesForm = $this->createForm(CategoryFilterType::class, $filteredCategoryId, [
            'action' => $this->generateUrl('admin_products_grid_category_filter'),
        ]);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/index.html.twig', [
            'categoryFilterForm' => $categoriesForm->createView(),
            'productGrid' => $this->presentGrid($productGrid),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getProductToolbarButtons(),
            'help_link' => $this->generateSidebarLink('AdminProducts'),
        ]);
    }

    /**
     * Process Grid search, but we need to add the category filter which is handled independently.
     *
     * @param Request $request
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('read', request.get('_legacy_controller'))")
     *
     * @return RedirectResponse
     */
    public function searchGridAction(Request $request)
    {
        /** @var GridDefinitionFactoryInterface $definitionFactory */
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.product');

        $filterId = ProductGridDefinitionFactory::GRID_ID;

        $adminFilter = $this->getGridAdminFilter();
        if (isset($adminFilter)) {
            $currentFilters = json_decode($adminFilter->getFilter(), true);
            if (!empty($currentFilters['filters']['id_category'])) {
                $request->query->add([
                    'product[filters][id_category]' => $currentFilters['filters']['id_category'],
                ]);
            }
        }

        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $definitionFactory,
            $request,
            $filterId,
            'admin_products_v2_index',
            ['product[filters][id_category]']
        );
    }

    /**
     * Reset filters for the grid only (category is kept, it can be cleared via another dedicated action)
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('read', request.get('_legacy_controller'))")
     *
     * @return JsonResponse
     */
    public function resetGridSearchAction(): JsonResponse
    {
        $adminFilter = $this->getGridAdminFilter();
        if (isset($adminFilter)) {
            $adminFiltersRepository = $this->get('prestashop.core.admin.admin_filter.repository');
            $currentFilters = json_decode($adminFilter->getFilter(), true);

            // This reset action only reset the filters from the Grid, we keep the filter by category if it was present (we still reset to page 1 though)
            if (!empty($currentFilters['filters']['id_category'])) {
                $adminFilter->setFilter(json_encode([
                    'filters' => [
                        'id_category' => $currentFilters['filters']['id_category'],
                    ],
                    'offset' => 0,
                ]));
                $adminFiltersRepository->updateFilter($adminFilter);
            } else {
                $adminFiltersRepository->unsetFilters($adminFilter);
            }
        }

        return new JsonResponse();
    }

    /**
     * Apply the category filter and redirect to list on first page.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('read', request.get('_legacy_controller'))")
     *
     * @return RedirectResponse
     */
    public function gridCategoryFilterAction(Request $request): RedirectResponse
    {
        $filteredCategoryId = $request->request->get('category_filter');
        $adminFilter = $this->getGridAdminFilter();
        if (isset($adminFilter)) {
            $adminFiltersRepository = $this->get('prestashop.core.admin.admin_filter.repository');
            $currentFilters = json_decode($adminFilter->getFilter(), true);
            if (empty($filteredCategoryId)) {
                unset($currentFilters['filters']['id_category']);
            } else {
                $currentFilters['filters']['id_category'] = $filteredCategoryId;
            }
            $currentFilters['offset'] = 0;
            $adminFilter->setFilter(json_encode($currentFilters));
            $adminFiltersRepository->updateFilter($adminFilter);
        }

        return $this->redirectToRoute('admin_products_v2_index');
    }

    /**
     * @AdminSecurity("is_granted('read', 'AdminProducts')")
     *
     * @return Response
     */
    public function lightListAction(ProductFilters $filters, Request $request): Response
    {
        $gridFactory = $this->get('prestashop.core.grid.factory.product_light');
        $grid = $gridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/light_list.html.twig', [
            'lightDisplay' => $request->query->has('liteDisplaying'),
            'productLightGrid' => $this->presentGrid($grid),
        ]);
    }

    /**
     * The redirection URL is generation thanks to the ProductPreviewProvider however it can't be used in the grid
     * since the LinkRowAction expects a symfony route, so this action is merely used as a proxy for symfony routing
     * and redirects to the appropriate product preview url.
     *
     * @AdminSecurity("is_granted('read', 'AdminProducts')")
     *
     * @return RedirectResponse
     */
    public function previewAction(int $productId): RedirectResponse
    {
        /** @var bool $isEnabled */
        $isEnabled = $this->getQueryBus()->handle(new GetProductIsEnabled((int) $productId));

        /** @var ProductPreviewProvider $previewUrlProvider */
        $previewUrlProvider = $this->get('prestashop.adapter.shop.url.product_preview_provider');
        $previewUrl = $previewUrlProvider->getUrl($productId, $isEnabled);

        return $this->redirect($previewUrl);
    }

    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message="You do not have permission to create this.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        if (!$this->get('prestashop.adapter.shop.context')->isSingleShopContext()) {
            return $this->renderDisableMultistorePage();
        }

        $productForm = $this->getCreateProductFormBuilder()->getForm();

        try {
            $productForm->handleRequest($request);

            $result = $this->getProductFormHandler()->handle($productForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_products_v2_edit', ['productId' => $result->getIdentifiableObjectId()]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->renderCreateProductForm($productForm, $request->query->has('liteDisplaying'));
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to update this.")
     *
     * @param Request $request
     * @param int $productId
     *
     * @return Response
     */
    public function editAction(Request $request, int $productId): Response
    {
        if ($this->shouldRedirectToV1()) {
            return $this->redirectToRoute('admin_product_form', ['id' => $productId]);
        }

        if (!$this->get('prestashop.adapter.shop.context')->isSingleShopContext()) {
            return $this->renderDisableMultistorePage($productId);
        }

        try {
            $productForm = $this->getEditProductFormBuilder()->getFormFor($productId, [], [
                'product_id' => $productId,
                // @todo: patch/partial update doesn't work good for now (especially multiple empty values) so we use POST for now
                // 'method' => Request::METHOD_PATCH,
                'method' => Request::METHOD_POST,
            ]);
        } catch (ShopAssociationNotFound $e) {
            return $this->renderMissingAssociation($productId);
        } catch (ProductNotFoundException $e) {
            $this->addFlash(
                'warning',
                $this->trans('The product you are trying to access doesn\'t exist', 'Admin.Catalog.Notification')
            );

            return $this->redirectToRoute('admin_product_catalog');
        }

        try {
            $productForm->handleRequest($request);
            $result = $this->getProductFormHandler()->handleFor($productId, $productForm);

            if ($result->isSubmitted()) {
                if ($result->isValid()) {
                    $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                    return $this->redirectToRoute('admin_products_v2_edit', ['productId' => $productId]);
                } else {
                    // Display root level errors with flash messages
                    foreach ($productForm->getErrors() as $error) {
                        $this->addFlash('error', sprintf(
                            '%s: %s',
                            $error->getOrigin()->getName(),
                            $error->getMessage()
                        ));
                    }
                }
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->renderEditProductForm($productForm, $productId);
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="You do not have permission to delete this.")
     *
     * @param Request $request
     * @param int $productId
     *
     * @return Response
     */
    public function deleteAction(Request $request, int $productId): Response
    {
        try {
            $this->getCommandBus()->handle(new DeleteProductCommand($productId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (ProductException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_products_v2_index');
    }

    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message="You do not have permission to create this.")
     *
     * @param int $productId
     *
     * @return Response
     */
    public function duplicateAction(int $productId): Response
    {
        try {
            /** @var ProductId $newProductId */
            $newProductId = $this->getCommandBus()->handle(new DuplicateProductCommand($productId));
            $this->addFlash(
                'success',
                $this->trans('Successful duplication', 'Admin.Notifications.Success')
            );
        } catch (ProductException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_products_v2_index');
        }

        return $this->redirectToRoute('admin_products_v2_edit', ['productId' => $newProductId->getValue()]);
    }

    /**
     * Toggles product status
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_products_v2_index")
     * @DemoRestricted(redirectRoute="admin_products_v2_index")
     *
     * @param int $productId
     *
     * @return JsonResponse
     */
    public function toggleStatusAction(int $productId): JsonResponse
    {
        if ($this->isDemoModeEnabled()) {
            return $this->json([
                'status' => false,
                'message' => $this->getDemoModeErrorMessage(),
            ]);
        }

        /** @var bool $isEnabled */
        $isEnabled = $this->getQueryBus()->handle(new GetProductIsEnabled((int) $productId));

        try {
            $this->getCommandBus()->handle(
                new UpdateProductStatusCommand((int) $productId, !$isEnabled)
            );
        } catch (ProductException $e) {
            return $this->json([
                'status' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e)),
            ]);
        }

        return $this->json([
            'status' => true,
            'message' => $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success'),
        ]);
    }

    /**
     * Updates product position.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_v2_index",
     *     redirectQueryParamsToKeep={"id_category"},
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_products_v2_index",
     *     redirectQueryParamsToKeep={"id_category"}
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function updatePositionAction(Request $request): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(
                new UpdateProductsPositionsCommand(
                    $request->request->get('positions'),
                    $request->query->getInt('id_category')
                )
            );
            $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
        } catch (CannotUpdateProductPositionException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_products_v2_index');
        }

        return $this->redirectToRoute('admin_products_v2_index');
    }

    /**
     * Delete products in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_v2_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function bulkDeleteAction(Request $request): JsonResponse
    {
        try {
            $this->getCommandBus()->handle(new BulkDeleteProductCommand(
                $this->getProductIdsFromRequest($request))
            );
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            if ($e instanceof BulkProductException) {
                return $this->jsonBulkErrors($e);
            } else {
                return $this->json(['error' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))], Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->json(['success' => true]);
    }

    /**
     * Enable products in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_v2_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function bulkEnableAction(Request $request): JsonResponse
    {
        try {
            $this->getCommandBus()->handle(
                new BulkUpdateProductStatusCommand(
                    $this->getProductIdsFromRequest($request),
                    true
                )
            );
        } catch (Exception $e) {
            if ($e instanceof BulkProductException) {
                return $this->jsonBulkErrors($e);
            } else {
                return $this->json(['error' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))], Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->json(['success' => true]);
    }

    /**
     * Disable products in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_v2_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function bulkDisableAction(Request $request): JsonResponse
    {
        try {
            $this->getCommandBus()->handle(
                new BulkUpdateProductStatusCommand(
                    $this->getProductIdsFromRequest($request),
                    false
                )
            );
        } catch (Exception $e) {
            if ($e instanceof BulkProductException) {
                return $this->jsonBulkErrors($e);
            } else {
                return $this->json(['error' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))], Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->json(['success' => true]);
    }

    /**
     * Duplicate products in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_v2_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function bulkDuplicateAction(Request $request): JsonResponse
    {
        try {
            $this->getCommandBus()->handle(
                new BulkDuplicateProductCommand(
                    $this->getProductIdsFromRequest($request)
                )
            );
        } catch (Exception $e) {
            if ($e instanceof BulkProductException) {
                return $this->jsonBulkErrors($e);
            } else {
                return $this->json(['error' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))], Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->json(['success' => true]);
    }

    /**
     * @param Request $request
     *
     * @return array<int, int>
     */
    private function getProductIdsFromRequest(Request $request): array
    {
        $productIds = $request->request->get('product_bulk');

        if (is_numeric($productIds)) {
            return [(int) $productIds];
        }

        if (!is_array($productIds)) {
            return [];
        }

        foreach ($productIds as $i => $productId) {
            $productIds[$i] = (int) $productId;
        }

        return $productIds;
    }

    /**
     * @return array
     */
    private function getProductToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_products_v2_create'),
            'desc' => $this->trans('New product', 'Admin.Actions'),
            'icon' => 'add_circle_outline',
            'class' => 'btn-primary new-product-button',
            'floating_class' => 'new-product-button',
        ];

        return $toolbarButtons;
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="You do not have permission to read this.")
     *
     * Download the content of the virtual product.
     *
     * @param int $virtualProductFileId
     *
     * @return BinaryFileResponse
     */
    public function downloadVirtualFileAction(int $virtualProductFileId): BinaryFileResponse
    {
        $configuration = $this->get('prestashop.adapter.legacy.configuration');
        $download = $this->getDoctrine()
            ->getRepository(ProductDownload::class)
            ->findOneBy([
                'id' => $virtualProductFileId,
            ]);

        $response = new BinaryFileResponse(
            $configuration->get('_PS_DOWNLOAD_DIR_') . $download->getFilename()
        );

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $download->getDisplayFilename()
        );

        return $response;
    }

    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param string $languageCode
     *
     * @return JsonResponse
     */
    public function searchAssociationsAction(Request $request, string $languageCode): JsonResponse
    {
        $langRepository = $this->get('prestashop.core.admin.lang.repository');
        $lang = $langRepository->getOneByLocaleOrIsoCode($languageCode);
        if (null === $lang) {
            return $this->json([
                'message' => sprintf(
                    'Invalid language code %s was used which matches no existing language in this shop.',
                    $languageCode
                ),
            ], Response::HTTP_BAD_REQUEST);
        }

        $shopId = $this->get('prestashop.adapter.shop.context')->getContextShopID();
        if (empty($shopId)) {
            $shopId = $this->get('prestashop.adapter.legacy.configuration')->getInt('PS_SHOP_DEFAULT');
        }

        try {
            /** @var ProductForAssociation[] $products */
            $products = $this->getQueryBus()->handle(new SearchProductsForAssociation(
                $request->get('query', ''),
                $lang->getId(),
                (int) $shopId,
                (int) $request->get('limit', 20)
            ));
        } catch (ProductConstraintException $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        if (empty($products)) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->formatProductsForAssociation($products));
    }

    /**
     * @param ProductForAssociation[] $productsForAssociation
     *
     * @return array
     */
    private function formatProductsForAssociation(array $productsForAssociation): array
    {
        $productsData = [];
        foreach ($productsForAssociation as $productForAssociation) {
            $productName = $productForAssociation->getName();
            if (!empty($productForAssociation->getReference())) {
                $productName .= sprintf(' (ref: %s)', $productForAssociation->getReference());
            }

            $productsData[] = [
                'id' => $productForAssociation->getProductId(),
                'name' => $productName,
                'image' => $productForAssociation->getImageUrl(),
            ];
        }

        return $productsData;
    }

    /**
     * @param FormInterface $productForm
     *
     * @return Response
     */
    private function renderCreateProductForm(FormInterface $productForm, bool $lightDisplay): Response
    {
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/create.html.twig', [
            'lightDisplay' => $lightDisplay,
            'showContentHeader' => false,
            'productForm' => $productForm->createView(),
            'helpLink' => $this->generateSidebarLink('AdminProducts'),
            'editable' => $this->isGranted(PageVoter::UPDATE, self::PRODUCT_CONTROLLER_PERMISSION),
        ]);
    }

    /**
     * @param FormInterface $productForm
     * @param int $productId
     *
     * @return Response
     */
    private function renderEditProductForm(FormInterface $productForm, int $productId): Response
    {
        $configuration = $this->get('prestashop.adapter.legacy.configuration');
        $categoryTreeFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.category_tree_selector_form_builder');

        $moduleDataProvider = $this->get('prestashop.adapter.data_provider.module');
        $statsModule = $moduleDataProvider->findByName('statsproduct');
        $statsLink = null;
        if (!empty($statsModule['active'])) {
            $statsLink = $this->getAdminLink('AdminStats', ['module' => 'statsproduct', 'id_product' => $productId]);
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/edit.html.twig', [
            'categoryTreeSelectorForm' => $categoryTreeFormBuilder->getForm()->createView(),
            'showContentHeader' => false,
            'productForm' => $productForm->createView(),
            'statsLink' => $statsLink,
            'helpLink' => $this->generateSidebarLink('AdminProducts'),
            'editable' => $this->isGranted(PageVoter::UPDATE, self::PRODUCT_CONTROLLER_PERMISSION),
            'taxEnabled' => (bool) $configuration->get('PS_TAX'),
            'stockEnabled' => (bool) $configuration->get('PS_STOCK_MANAGEMENT'),
            'isMultistoreActive' => $this->get('prestashop.adapter.multistore_feature')->isActive(),
        ]);
    }

    /**
     * Gets creation form builder.
     *
     * @return FormBuilderInterface
     */
    private function getCreateProductFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.create_product_form_builder');
    }

    /**
     * Gets edition form builder.
     *
     * @return FormBuilderInterface
     */
    private function getEditProductFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.edit_product_form_builder');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getProductFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.product_form_handler');
    }

    /**
     * Format the bulk exception into an array of errors returned in a JsonResponse.
     *
     * @param BulkProductException $bulkProductException
     *
     * @return JsonResponse
     */
    private function jsonBulkErrors(BulkProductException $bulkProductException): JsonResponse
    {
        $errors = [];
        foreach ($bulkProductException->getBulkExceptions() as $productId => $productException) {
            $errors[] = $this->trans(
                'Error for product %product_id%: %error_message%',
                'Admin.Catalog.Notification',
                [
                    '%product_id%' => $productId,
                    '%error_message%' => $this->getErrorMessageForException($productException, $this->getErrorMessages($productException)),
                ]
            );
        }

        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
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
        // @todo: all the constraint error messages are missing for now (see ProductConstraintException)
        return [
            CannotDeleteProductException::class => $this->trans(
                'An error occurred while deleting the object.',
                'Admin.Notifications.Error'
            ),
            CannotBulkDeleteProductException::class => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
            ),
            ProductConstraintException::class => [
                ProductConstraintException::INVALID_PRICE => $this->trans(
                    'Product price is invalid',
                    'Admin.Notifications.Error'
                ),
                ProductConstraintException::INVALID_UNIT_PRICE => $this->trans(
                    'Product price per unit is invalid',
                    'Admin.Notifications.Error'
                ),
                ProductConstraintException::INVALID_REDIRECT_TARGET => $this->trans(
                    'When redirecting towards a product you must select a target product.',
                    'Admin.Catalog.Notification'
                ),
                ProductConstraintException::INVALID_ONLINE_DATA => $this->trans(
                    'To put this product online, please enter a name.',
                    'Admin.Catalog.Notification'
                ),
            ],
            DuplicateFeatureValueAssociationException::class => $this->trans(
                'You cannot associate the same feature value more than once.',
                'Admin.Notifications.Error'
            ),
            InvalidAssociatedFeatureException::class => $this->trans(
                'The selected value belongs to another feature.',
                'Admin.Notifications.Error'
            ),
            SpecificPriceConstraintException::class => [
                SpecificPriceConstraintException::DUPLICATE_PRIORITY => $this->trans(
                    'The selected condition must be different in each field to set an order of priority.',
                    'Admin.Notifications.Error'
                ),
            ],
            InvalidProductTypeException::class => [
                InvalidProductTypeException::EXPECTED_NO_EXISTING_PACK_ASSOCIATIONS => $this->trans(
                    'This product cannot be changed into a pack because it is already associated to another pack.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * @param int $productId
     *
     * @return Response
     */
    private function renderMissingAssociation(int $productId): Response
    {
        //@todo this error message should be improved to indicate which shop can/should be used for this product and/or how to associate it to the current context
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/disabled.html.twig', [
            'errorMessage' => $this->trans(
                'This product is not associated with the store selected in the multistore header, please select another one.',
                'Admin.Notifications.Info'
            ),
            'standardPageUrl' => $this->generateUrl(
                !empty($productId) ? 'admin_product_form' : 'admin_product_new',
                !empty($productId) ? ['id' => $productId] : []
            ),
        ]);
    }

    /**
     * @param int|null $productId
     *
     * @return Response
     */
    private function renderDisableMultistorePage(int $productId = null): Response
    {
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/disabled.html.twig', [
            'errorMessage' => $this->trans(
                'This page is only compatible in a single store context. To access the page, please select a store or [1]disable the multistore feature[/1].',
                'Admin.Notifications.Info',
                [
                    '[1]' => sprintf('<a href="%s">', $this->get('router')->generate('admin_preferences')),
                    '[/1]' => '</a>',
                ]
            ),
        ]);
    }

    private function getGridAdminFilter(): ?AdminFilter
    {
        if (null === $this->getUser() || null === $this->getContext()->shop || empty($this->getContext()->shop->id)) {
            return null;
        }

        $adminFiltersRepository = $this->get('prestashop.core.admin.admin_filter.repository');
        $employeeId = $this->getUser()->getId();
        $shopId = $this->getContext()->shop->id;

        return $adminFiltersRepository->findByEmployeeAndFilterId($employeeId, $shopId, ProductGridDefinitionFactory::GRID_ID);
    }

    /**
     * @return bool
     */
    private function shouldRedirectToV1(): bool
    {
        $multistoreFeature = $this->get('prestashop.adapter.multistore_feature');

        if (!$multistoreFeature->isActive()) {
            return $this->get('prestashop.core.admin.feature_flag.repository')->isDisabled(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2);
        }

        return $this->get('prestashop.core.admin.feature_flag.repository')->isDisabled(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2_MULTI_SHOP);
    }
}
