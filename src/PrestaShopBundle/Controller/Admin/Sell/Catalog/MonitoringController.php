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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring\DisabledProductGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring\EmptyCategoryGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring\NoQtyProductWithCombinationGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring\NoQtyProductWithoutCombinationGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring\ProductWithoutDescriptionGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring\ProductWithoutImageGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring\ProductWithoutPriceGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\DisabledProductFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\EmptyCategoryFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\NoQtyProductWithCombinationFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\NoQtyProductWithoutCombinationFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\ProductWithoutDescriptionFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\ProductWithoutImageFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\ProductWithoutPriceFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Category\DeleteCategoriesType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Sell > Catalog > Monitoring page
 */
class MonitoringController extends FrameworkBundleAdminController
{
    /**
     * Shows Monitoring listing page
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param EmptyCategoryFilters $emptyCategoryFilters
     * @param NoQtyProductWithCombinationFilters $noQtyProductWithCombinationFilters
     * @param NoQtyProductWithoutCombinationFilters $noQtyProductWithoutCombinationFilters
     * @param DisabledProductFilters $disabledProductFilters
     * @param ProductWithoutImageFilters $productWithoutImageFilters
     * @param ProductWithoutDescriptionFilters $productWithoutDescriptionFilters
     * @param ProductWithoutPriceFilters $productWithoutPriceFilters
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        EmptyCategoryFilters $emptyCategoryFilters,
        NoQtyProductWithCombinationFilters $noQtyProductWithCombinationFilters,
        NoQtyProductWithoutCombinationFilters $noQtyProductWithoutCombinationFilters,
        DisabledProductFilters $disabledProductFilters,
        ProductWithoutImageFilters $productWithoutImageFilters,
        ProductWithoutDescriptionFilters $productWithoutDescriptionFilters,
        ProductWithoutPriceFilters $productWithoutPriceFilters
    ) {
        $deleteCategoryForm = $this->createForm(DeleteCategoriesType::class);

        $emptyCategoryGrid = $this->getEmptyCategoryGrid($emptyCategoryFilters);
        $noQtyProductWithCombinationGrid = $this->getNoQtyProductWithCombinationGrid($noQtyProductWithCombinationFilters);
        $noQtyProductWithoutCombinationGrid = $this->getNoQtyProductWithoutCombinationGrid($noQtyProductWithoutCombinationFilters);
        $disabledProductGrid = $this->getDisabledProductGrid($disabledProductFilters);
        $productWithoutImageGrid = $this->getProductWithoutImageGrid($productWithoutImageFilters);
        $productWithoutDescriptionGrid = $this->getProductWithoutDescriptionGrid($productWithoutDescriptionFilters);
        $productWithoutPriceGrid = $this->getProductWithoutPriceGrid($productWithoutPriceFilters);

        $isShowcaseCardClosed = $this->getQueryBus()->handle(
            new GetShowcaseCardIsClosed($this->getContext()->employee->id, ShowcaseCard::MONITORING_CARD)
        );

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Monitoring/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'emptyCategoryGrid' => $this->presentGrid($emptyCategoryGrid),
            'deleteCategoryForm' => $deleteCategoryForm->createView(),
            'noQtyProductWithCombinationGrid' => $this->presentGrid($noQtyProductWithCombinationGrid),
            'noQtyProductWithoutCombinationGrid' => $this->presentGrid($noQtyProductWithoutCombinationGrid),
            'disabledProductGrid' => $this->presentGrid($disabledProductGrid),
            'productWithoutImageGrid' => $this->presentGrid($productWithoutImageGrid),
            'productWithoutDescriptionGrid' => $this->presentGrid($productWithoutDescriptionGrid),
            'productWithoutPriceGrid' => $this->presentGrid($productWithoutPriceGrid),
            'showcaseCardName' => ShowcaseCard::MONITORING_CARD,
            'isShowcaseCardClosed' => $isShowcaseCardClosed,
        ]);
    }

    /**
     * Provides filters functionality
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $gridIdentifiers = $this->identifySearchableGrid($request);

        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $gridIdentifiers['grid_definition'],
            $request,
            $gridIdentifiers['grid_id'],
            'admin_monitorings_index'
        );
    }

    /**
     * Delete monitoring items in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_monitorings_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        $gridIdentifiers = $this->identifySearchableGrid($request);
        $productIds = $this->getBulkProductsFromRequest($request, $gridIdentifiers);

        try {
            $this->getCommandBus()->handle(new BulkDeleteProductCommand(
                $productIds,
                ShopConstraint::shop($this->getContextShopId())
            ));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, [$e->getMessage()]));
        }

        return $this->redirectToRoute('admin_monitorings_index');
    }

    /**
     * @param Request $request
     * @param array $gridIdentifiers
     *
     * @return array
     */
    private function getBulkProductsFromRequest(Request $request, array $gridIdentifiers): array
    {
        $productIds = $request->request->get(sprintf('%s_%s', $gridIdentifiers['grid_id'], 'monitoring_products_bulk'));

        if (!is_array($productIds)) {
            return [];
        }

        foreach ($productIds as $i => $productId) {
            $productIds[$i] = (int) $productId;
        }

        return $productIds;
    }

    /**
     * Parses grid identifying parts from request in order to recognize which grid is being filtered
     *
     * @param Request $request
     *
     * @return array
     */
    private function identifySearchableGrid(Request $request)
    {
        $gridDefinition = 'prestashop.core.grid.definition.factory.monitoring.empty_category';
        $gridId = EmptyCategoryGridDefinitionFactory::GRID_ID;

        $definitionMap = [
            NoQtyProductWithCombinationGridDefinitionFactory::GRID_ID => 'prestashop.core.grid.definition.factory.monitoring.no_qty_product_with_combination',
            NoQtyProductWithoutCombinationGridDefinitionFactory::GRID_ID => 'prestashop.core.grid.definition.factory.monitoring.no_qty_product_without_combination',
            DisabledProductGridDefinitionFactory::GRID_ID => 'prestashop.core.grid.definition.factory.monitoring.disabled_product',
            ProductWithoutImageGridDefinitionFactory::GRID_ID => 'prestashop.core.grid.definition.factory.monitoring.product_without_image',
            ProductWithoutDescriptionGridDefinitionFactory::GRID_ID => 'prestashop.core.grid.definition.factory.monitoring.product_without_description',
            ProductWithoutPriceGridDefinitionFactory::GRID_ID => 'prestashop.core.grid.definition.factory.monitoring.product_without_price',
        ];

        foreach ($definitionMap as $id => $definition) {
            if ($request->request->has($id)) {
                $gridId = $id;
                $gridDefinition = $definition;

                break;
            }
        }

        return [
            'grid_id' => $gridId,
            'grid_definition' => $this->get($gridDefinition),
        ];
    }

    /**
     * @param EmptyCategoryFilters $filters
     *
     * @return GridInterface
     */
    private function getEmptyCategoryGrid(EmptyCategoryFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.grid_factory.empty_category');

        return $gridFactory->getGrid($filters);
    }

    /**
     * @param NoQtyProductWithCombinationFilters $filters
     *
     * @return GridInterface
     */
    private function getNoQtyProductWithCombinationGrid(NoQtyProductWithCombinationFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.grid_factory.no_qty_product_with_combination');

        return $gridFactory->getGrid($filters);
    }

    /**
     * @param NoQtyProductWithoutCombinationFilters $filters
     *
     * @return GridInterface
     */
    private function getNoQtyProductWithoutCombinationGrid(NoQtyProductWithoutCombinationFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.grid_factory.no_qty_product_without_combination');

        return $gridFactory->getGrid($filters);
    }

    /**
     * @param DisabledProductFilters $filters
     *
     * @return GridInterface
     */
    private function getDisabledProductGrid(DisabledProductFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.grid_factory.disabled_product');

        return $gridFactory->getGrid($filters);
    }

    /**
     * @param ProductWithoutImageFilters $filters
     *
     * @return GridInterface
     */
    private function getProductWithoutImageGrid(ProductWithoutImageFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.grid_factory.product_without_image');

        return $gridFactory->getGrid($filters);
    }

    /**
     * @param ProductWithoutDescriptionFilters $filters
     *
     * @return GridInterface
     */
    private function getProductWithoutDescriptionGrid(ProductWithoutDescriptionFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.grid_factory.product_without_description');

        return $gridFactory->getGrid($filters);
    }

    /**
     * @param ProductWithoutPriceFilters $filters
     *
     * @return GridInterface
     */
    private function getProductWithoutPriceGrid(ProductWithoutPriceFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.grid_factory.product_without_price');

        return $gridFactory->getGrid($filters);
    }
}
