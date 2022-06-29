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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShopBundle\Bridge\AdminController\Action\HeaderToolbarAction;
use PrestaShopBundle\Bridge\AdminController\Action\ListBulkAction;
use PrestaShopBundle\Bridge\AdminController\Action\ListHeaderToolbarAction;
use PrestaShopBundle\Bridge\AdminController\Action\ListRowAction;
use PrestaShopBundle\Bridge\AdminController\AdminControllerTrait;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use PrestaShopBundle\Bridge\AdminController\Field\Field;
use PrestaShopBundle\Bridge\AdminController\LegacyControllerBridgeInterface;
use PrestaShopBundle\Bridge\AdminController\LegacyListControllerBridgeInterface;
use PrestaShopBundle\Bridge\Helper\HelperListConfiguration;
use PrestaShopBundle\Bridge\Smarty\SmartyTrait;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupController extends FrameworkBundleAdminController implements LegacyControllerBridgeInterface, LegacyListControllerBridgeInterface
{
    use SmartyTrait;
    use AdminControllerTrait;
    /**
     * @var ControllerConfiguration
     */
    public $controllerConfiguration;

    /**
     * This parameter is needed by legacy hook, so we can't remove it.
     *
     * @var string
     */
    public $php_self;

    /**
     * This parameter is needed by legacy helper shop, so we can't remove it.
     *
     * @var bool
     */
    public $multishop_context_group = true;

    /**
     * This parameter is needed by legacy helper shop, we can't remove it.
     *
     * @var int
     */
    public $multishop_context;

    public function indexAction(Request $request): Response
    {
        $this->addAction(new HeaderToolbarAction('new_feature', [
            'href' => $this->controllerConfiguration->legacyCurrentIndex . '&addgroup&token=' . $this->controllerConfiguration->token,
            'desc' => $this->trans('Add new group', 'Admin.Catalog.Feature'),
            'icon' => 'process-icon-new',
        ]));

        $helperListConfiguration = $this->get('prestashop.core.bridge.helper_list_configuration_factory')->create(
        //@todo: getTable, getClassName, configuration, identifier, position identifier - all of these must be defiend in controller, so its a bit redundant,
        //  maybe we could introduce some abstract/trait method to reuse this if every controller has to implement getTable, getClassName etc.
            $this->getTable(),
            $this->getClassName(),
            $this->controllerConfiguration,
            $this->getIdentifier(),
            $this->getPositionIdentifier(),
            'name',
            true
        );
        $this->setListFields($helperListConfiguration);
        $this->buildActionList($helperListConfiguration);

        if ($request->request->has('submitResetgroup')) {
            $this->getResetFiltersHelper()->resetFilters($helperListConfiguration, $request);
        }

        $this->getFiltersHelper()->processFilter(
            $request,
            $helperListConfiguration
        );

        return $this->renderSmarty(
            $this->getHelperListBridge()->generateList(
                $helperListConfiguration
            )
        );
    }

    public function getTable(): string
    {
        return 'group';
    }

    public function getClassName(): string
    {
        return 'Group';
    }

    public function getPositionIdentifier(): ?string
    {
        return null;
    }

    public function getIdentifier(): string
    {
        return 'id_group';
    }

    /**
     * Build actions for list.
     *
     * @return void
     *
     * @throws \Exception
     */
    private function buildActionList(HelperListConfiguration $helperListConfiguration): void
    {
        $this->addActionList(new ListHeaderToolbarAction('new', [
            'href' => $this->controllerConfiguration->legacyCurrentIndex . '&addgroup&token=' . $this->controllerConfiguration->token,
            'desc' => $this->trans('Add new group', 'Admin.Shopparameters.Feature'),
            'icon' => 'process-icon-new',
        ]), $helperListConfiguration);

        $this->addActionList(new ListRowAction('edit'), $helperListConfiguration);
        $this->addActionList(new ListRowAction('delete'), $helperListConfiguration);

        $this->addActionList(new ListBulkAction('delete', [
            'text' => $this->trans('Delete selected', 'Admin.Actions'),
            'confirm' => $this->trans('Delete selected items?', 'Admin.Notifications.Warning'),
            'icon' => 'icon-trash',
        ]), $helperListConfiguration);
    }

    /**
     * Define fields in the list.
     *
     * @return void
     */
    private function setListFields(HelperListConfiguration $helperListConfiguration): void
    {
        $this->addListField(new Field('id_group', [
            'title' => $this->trans('ID', 'Admin.Global'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
        ]), $helperListConfiguration);
        $this->addListField(new Field('name', [
            'title' => $this->trans('Group name', 'Admin.Shopparameters.Feature'),
            'filter_key' => 'b!name',
        ]), $helperListConfiguration);
        $this->addListField(new Field('reduction', [
            'title' => $this->trans('Discount (%)', 'Admin.Shopparameters.Feature'),
            'align' => 'right',
            'type' => 'percent',
        ]), $helperListConfiguration);
        $this->addListField(new Field('nb', [
            'title' => $this->trans('Members', 'Admin.Shopparameters.Feature'),
            'align' => 'center',
            'havingFilter' => true,
        ]), $helperListConfiguration);
        $this->addListField(new Field('show_prices', [
            'title' => $this->trans('Show prices', 'Admin.Shopparameters.Feature'),
            'align' => 'center',
            'type' => 'bool',
            'orderby' => false,
        ]), $helperListConfiguration);
        $this->addListField(new Field('date_add', [
            'title' => $this->trans('Creation date', 'Admin.Shopparameters.Feature'),
            'type' => 'date',
            'align' => 'right',
        ]), $helperListConfiguration);
    }
}
