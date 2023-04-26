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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\FeatureValueGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Builder\TypedBuilder\FeatureValueFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\FeatureValueFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureValueController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function indexAction(Request $request, FeatureValueFilters $filters): Response
    {
        $featureId = $filters->getFeatureId();
        $featureValueGridFactory = $this->get('prestashop.core.grid.grid_factory.feature_value');
        $grid = $featureValueGridFactory->getGrid($filters, [
            'feature_id' => $featureId,
            'language_id' => $filters->getLanguageId(),
        ]);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/FeatureValue/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'featureValueGrid' => $this->presentGrid($grid),
            // @todo: uncomment when add action is migrated
            //            'layoutHeaderToolbarBtn' => [
            //                'add_feature_value' => [
            //                    'href' => $this->generateUrl('admin_feature_values_add', ['featureId' => $featureId]),
            //                    'desc' => $this->trans('Add new feature value', 'Admin.Catalog.Feature'),
            //                    'icon' => 'add_circle_outline',
            //                ],
            //            ],
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request, FeatureValueFilters $filters): RedirectResponse
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');
        /** @var FeatureValueFiltersBuilder $featureValueFiltersBuilder */
        $featureValueFiltersBuilder = $this->get(FeatureValueFiltersBuilder::class);
        /** @var FeatureValueGridDefinitionFactory $gridDefinitionFactory */
        $gridDefinitionFactory = $this->get(FeatureValueGridDefinitionFactory::class);
        /* @var FeatureValueFilters $filters */
//        $filters = $featureValueFiltersBuilder->buildFilters(new FeatureValueFilters($request->request->all()));

        return $responseBuilder->buildSearchResponse(
            $gridDefinitionFactory,
            $request,
            FeatureValueGridDefinitionFactory::GRID_ID,
            'admin_feature_values_index',
            ['featureId'],
            [
                'feature_id' => $filters->getFeatureId(),
                'language_id' => $filters->getLanguageId(),
            ]
        );
    }
}
