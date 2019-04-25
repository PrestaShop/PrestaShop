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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring\EmptyCategoryGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring\ProductWithCombinationGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\DisabledProductFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\EmptyCategoryFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\ProductWithCombinationFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\ProductWithoutCombinationFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Monitoring\ProductWithoutImageFilters;
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
     * @param Request $request
     * @param EmptyCategoryFilters $emptyCategoryFilters
     * @param ProductWithCombinationFilters $productWithCombinationFilters
     * @param ProductWithoutCombinationFilters $productWithoutCombinationFilters
     * @param DisabledProductFilters $disabledProductFilters
     * @param ProductWithoutImageFilters $productWithoutImageFilters
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        EmptyCategoryFilters $emptyCategoryFilters,
        ProductWithCombinationFilters $productWithCombinationFilters,
        ProductWithoutCombinationFilters $productWithoutCombinationFilters,
        DisabledProductFilters $disabledProductFilters,
        ProductWithoutImageFilters $productWithoutImageFilters
    ) {
        $deleteCategoriesForm = $this->createForm(DeleteCategoriesType::class);

        $emptyCategoryGrid = $this->getEmptyCategoryGrid($emptyCategoryFilters);
        $productWithCombinationGrid = $this->getProductWithCombinationGrid($productWithCombinationFilters);
        $productWithoutCombinationGrid = $this->getProductWithoutCombinationGrid($productWithoutCombinationFilters);
        $disabledProductGrid = $this->getDisabledProductGrid($disabledProductFilters);
        $productWithoutImageGrid = $this->getProductWithoutImagegrid($productWithoutImageFilters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Monitoring/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'empty_category_grid' => $this->presentGrid($emptyCategoryGrid),
            'deleteCategoriesForm' => $deleteCategoriesForm->createView(),
            'product_with_combination_grid' => $this->presentGrid($productWithCombinationGrid),
            'product_without_combination_grid' => $this->presentGrid($productWithoutCombinationGrid),
            'disabled_product_grid' => $this->presentGrid($disabledProductGrid),
            'product_without_image_grid' => $this->presentGrid($productWithoutImageGrid),
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
        $gridDefinitionFactory = 'prestashop.core.grid.definition.factory.monitoring.empty_category';
        $filterId = EmptyCategoryGridDefinitionFactory::GRID_ID;

        if ($request->request->has(ProductWithCombinationGridDefinitionFactory::GRID_ID)) {
            $gridDefinitionFactory = 'prestashop.core.grid.definition.factory.monitoring.product_with_combination';
            $filterId = ProductWithCombinationGridDefinitionFactory::GRID_ID;
        }

        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get($gridDefinitionFactory),
            $request,
            $filterId,
            'admin_monitoring_index'
        );
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
     * @param ProductWithCombinationFilters $filters
     *
     * @return GridInterface
     */
    private function getProductWithCombinationGrid(ProductWithCombinationFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.grid_factory.product_with_combination');

        return $gridFactory->getGrid($filters);
    }

    /**
     * @param ProductWithoutCombinationFilters $filters
     *
     * @return GridInterface
     */
    private function getProductWithoutCombinationGrid(ProductWithoutCombinationFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.grid_factory.product_without_combination');

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
    private function getProductWithoutImagegrid(ProductWithoutImageFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.grid_factory.product_without_image');

        return $gridFactory->getGrid($filters);
    }
}
