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

/* @phpstan-ignore-next-line */
use Feature;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use PrestaShopBundle\Bridge\AdminController\FrameworkBridgeControllerInterface;
use PrestaShopBundle\Bridge\AdminController\FrameworkBridgeControllerListTrait;
use PrestaShopBundle\Bridge\AdminController\FrameworkBridgeControllerTrait;
use PrestaShopBundle\Bridge\Helper\Listing\HelperBridge\FeatureHelperListBridge;
use PrestaShopBundle\Bridge\Helper\Listing\HelperListConfiguration;
use PrestaShopBundle\Bridge\Smarty\FrameworkControllerSmartyTrait;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
/* @phpstan-ignore-next-line */
use Tools;

/**
 * @deprecated do not use this controller, it is not maintained and will eventually be removed.
 *
 * This controller is only left for a reference of how horizontal migration approach worked.
 * Horizontal migration was cancelled as ineffective, but some parts of it can still be reused.
 * So this should be cleaned up when it is decided which parts can be left and which can still be useful.
 */
class HorizontalFeatureController extends FrameworkBundleAdminController implements FrameworkBridgeControllerInterface
{
    use FrameworkBridgeControllerTrait;
    use FrameworkBridgeControllerListTrait;
    use FrameworkControllerSmartyTrait;

    /**
     * @deprecated
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $this->setHeaderToolbarActions();

        $helperListConfiguration = $this->buildListConfiguration(
            'id_feature',
            // @todo: position update is still handled by legacy ajax controller action. Need to handle in dedicated PR
            'position',
            $request->attributes->get('_route'),
            'id_feature'
        );

        $this->setListFields($helperListConfiguration);
        $this->setListActions($helperListConfiguration);
        $this->processFilters($request, $helperListConfiguration);

        return $this->renderSmarty($this->getHelperListBridge()->generateList($helperListConfiguration));
    }

    /**
     * @return ControllerConfiguration
     */
    public function getControllerConfiguration(): ControllerConfiguration
    {
        return $this->buildControllerConfiguration(
            'feature',
            /* @phpstan-ignore-next-line */
            Feature::class,
            'AdminFeatures'
        );
    }

    /**
     * @return FeatureHelperListBridge
     */
    private function getHelperListBridge(): FeatureHelperListBridge
    {
        return $this->get('prestashop.bridge.helper.listing.helper_bridge.feature_helper_list_bridge');
    }

    /**
     * @return void
     */
    private function setHeaderToolbarActions(): void
    {
        $controllerConfiguration = $this->getControllerConfiguration();
        $index = $controllerConfiguration->legacyCurrentIndex;
        $token = $controllerConfiguration->token;

        $controllerConfiguration
            ->addHeaderToolbarAction('new_feature', [
                'href' => $this->generateUrl('admin_features_add'),
                'desc' => $this->trans('Add new feature', 'Admin.Catalog.Feature'),
                'icon' => 'process-icon-new',
            ])
            ->addHeaderToolbarAction('new_feature_value', [
                /* @phpstan-ignore-next-line */
                'href' => $index . '&addfeature_value&id_feature=' . (int) Tools::getValue('id_feature') . '&token=' . $token,
                'desc' => $this->trans('Add new feature value', 'Admin.Catalog.Help'),
                'icon' => 'process-icon-new',
            ])
        ;
    }

    /**
     * Build actions for list.
     *
     * @return void
     */
    private function setListActions(HelperListConfiguration $helperListConfiguration): void
    {
        $helperListConfiguration
            ->addRowAction('view')
            ->addRowAction('edit')
            ->addRowAction('delete')
            ->addToolbarAction('new', [
                'href' => $this->generateUrl('admin_features_add'),
                'desc' => $this->trans('Add new', 'Admin.Actions'),
            ])
            ->addBulkAction('delete', [
                'text' => $this->trans('Delete selected', 'Admin.Actions'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', 'Admin.Notifications.Warning'),
            ])
        ;
    }

    /**
     * @param HelperListConfiguration $helperListConfiguration
     */
    private function setListFields(HelperListConfiguration $helperListConfiguration): void
    {
        $helperListConfiguration->setFieldsList([
            'id_feature' => [
                'title' => $this->trans('ID', 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Name', 'Admin.Global'),
                'width' => 'auto',
                'filter_key' => 'b!name',
            ],
            'value' => [
                'title' => $this->trans('Values', 'Admin.Global'),
                'orderby' => false,
                'search' => false,
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'position' => [
                'title' => $this->trans('Position', 'Admin.Global'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'position' => 'position',
            ],
        ]);
    }
}
