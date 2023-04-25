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

use PrestaShop\PrestaShop\Core\Search\Filters\FeatureFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureValueController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function indexAction(Request $request, FeatureFilters $filters): Response
    {
        //@todo: render grid
//        $featureGridFactory = $this->get('prestashop.core.grid.grid_factory.feature');
//
//        $showcaseCardIsClosed = $this->getQueryBus()->handle(
//            new GetShowcaseCardIsClosed(
//                (int) $this->getContext()->employee->id,
//                ShowcaseCard::FEATURES_CARD
//            )
//        );

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/FeatureValue/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            //            'featureGrid' => $this->presentGrid($featureGridFactory->getGrid($filters)),
            //            'settingsTipMessage' => $this->getSettingsTipMessage(),
            //            'showcaseCardName' => ShowcaseCard::FEATURES_CARD,
            //            'isShowcaseCardClosed' => $showcaseCardIsClosed,
            //            'layoutHeaderToolbarBtn' => [
            //                'add_feature' => [
            //                    'href' => $this->generateUrl('admin_features_add'),
            //                    'desc' => $this->trans('Add new feature', 'Admin.Catalog.Feature'),
            //                    'icon' => 'add_circle_outline',
            //                ],
            //            ],
        ]);
    }
}
