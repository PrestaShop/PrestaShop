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
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\DisabledProductFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\EmptyCategoryFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\NoQtyProductWithCombinationFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\NoQtyProductWithoutCombinationFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\ProductWithoutDescriptionFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\ProductWithoutImageFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\ProductWithoutPriceFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Form\Admin\Sell\Category\DeleteCategoriesType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Sell > Catalog > Monitoring page
 */
class MonitoringController extends PrestaShopAdminController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            NoQtyProductWithCombinationGridDefinitionFactory::GRID_ID => NoQtyProductWithCombinationGridDefinitionFactory::class,
            NoQtyProductWithoutCombinationGridDefinitionFactory::GRID_ID => NoQtyProductWithoutCombinationGridDefinitionFactory::class,
            DisabledProductGridDefinitionFactory::GRID_ID => DisabledProductGridDefinitionFactory::class,
            ProductWithoutImageGridDefinitionFactory::GRID_ID => ProductWithoutImageGridDefinitionFactory::class,
            ProductWithoutDescriptionGridDefinitionFactory::GRID_ID => ProductWithoutDescriptionGridDefinitionFactory::class,
            ProductWithoutPriceGridDefinitionFactory::GRID_ID => ProductWithoutPriceGridDefinitionFactory::class,
            EmptyCategoryGridDefinitionFactory::GRID_ID => EmptyCategoryGridDefinitionFactory::class,
        ]);
    }

    /**
     * Shows Monitoring listing page
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
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.empty_category')]
        GridFactoryInterface $emptyCategoryGrid,
        EmptyCategoryFilters $emptyCategoryFilters,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.no_qty_product_with_combination')]
        GridFactoryInterface $noQtyProductWithCombinationGrid,
        NoQtyProductWithCombinationFilters $noQtyProductWithCombinationFilters,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.no_qty_product_without_combination')]
        GridFactoryInterface $noQtyProductWithoutCombinationGrid,
        NoQtyProductWithoutCombinationFilters $noQtyProductWithoutCombinationFilters,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.disabled_product')]
        GridFactoryInterface $disabledProductGrid,
        DisabledProductFilters $disabledProductFilters,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.product_without_image')]
        GridFactoryInterface $productWithoutImageGrid,
        ProductWithoutImageFilters $productWithoutImageFilters,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.product_without_description')]
        GridFactoryInterface $productWithoutDescriptionGrid,
        ProductWithoutDescriptionFilters $productWithoutDescriptionFilters,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.product_without_price')]
        GridFactoryInterface $productWithoutPriceGrid,
        ProductWithoutPriceFilters $productWithoutPriceFilters
    ): Response {
        $deleteCategoryForm = $this->createForm(DeleteCategoriesType::class);

        $emptyCategoryGrid = $emptyCategoryGrid->getGrid($emptyCategoryFilters);
        $noQtyProductWithCombinationGrid = $noQtyProductWithCombinationGrid->getGrid($noQtyProductWithCombinationFilters);
        $noQtyProductWithoutCombinationGrid = $noQtyProductWithoutCombinationGrid->getGrid($noQtyProductWithoutCombinationFilters);
        $disabledProductGrid = $disabledProductGrid->getGrid($disabledProductFilters);
        $productWithoutImageGrid = $productWithoutImageGrid->getGrid($productWithoutImageFilters);
        $productWithoutDescriptionGrid = $productWithoutDescriptionGrid->getGrid($productWithoutDescriptionFilters);
        $productWithoutPriceGrid = $productWithoutPriceGrid->getGrid($productWithoutPriceFilters);

        $isShowcaseCardClosed = $this->dispatchQuery(
            new GetShowcaseCardIsClosed($this->getEmployeeContext()->getEmployee()->getId(), ShowcaseCard::MONITORING_CARD)
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
            'layoutTitle' => $this->trans('Monitoring', [], 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Provides filters functionality
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function searchAction(Request $request): RedirectResponse
    {
        $gridIdentifiers = $this->identifySearchableGrid($request);

        return $this->buildSearchResponse(
            $gridIdentifiers['grid_definition'],
            $request,
            $gridIdentifiers['grid_id'],
            'admin_monitorings_index'
        );
    }

    /**
     * Delete monitoring items in bulk action.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_monitorings_index', message: 'You do not have permission to delete this.')]
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        $gridIdentifiers = $this->identifySearchableGrid($request);
        $productIds = $this->getBulkProductsFromRequest($request, $gridIdentifiers);

        try {
            $this->dispatchCommand(new BulkDeleteProductCommand(
                $productIds,
                ShopConstraint::shop($this->getShopContext()->getId())
            ));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, [$e::class => $e->getMessage()]));
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
        $productIds = $request->request->all(sprintf('%s_%s', $gridIdentifiers['grid_id'], 'monitoring_products_bulk'));

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
        $gridId = EmptyCategoryGridDefinitionFactory::GRID_ID;

        $definitionMap = [
            NoQtyProductWithCombinationGridDefinitionFactory::GRID_ID,
            NoQtyProductWithoutCombinationGridDefinitionFactory::GRID_ID,
            DisabledProductGridDefinitionFactory::GRID_ID,
            ProductWithoutImageGridDefinitionFactory::GRID_ID,
            ProductWithoutDescriptionGridDefinitionFactory::GRID_ID,
            ProductWithoutPriceGridDefinitionFactory::GRID_ID,
        ];

        foreach ($definitionMap as $id) {
            if ($request->request->has($id)) {
                $gridId = $id;
                break;
            }
        }

        return [
            'grid_id' => $gridId,
            'grid_definition' => $this->container->get($gridId),
        ];
    }
}
