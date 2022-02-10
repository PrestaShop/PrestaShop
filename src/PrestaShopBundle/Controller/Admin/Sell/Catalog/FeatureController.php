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

use ObjectModel;
use PrestaShopBundle\Bridge\AddActionTrait;
use PrestaShopBundle\Bridge\AddActionInterface;
use PrestaShopBundle\Bridge\Controller\ControllerBridgeInterface;
use PrestaShopBundle\Bridge\Controller\ControllerConfiguration;
use PrestaShopBundle\Bridge\Smarty\SmartyTrait;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Tools;
use Validate;

/**
 * Controller responsible for "Sell > Catalog > Attributes & Features > Features" page
 */
class FeatureController extends FrameworkBundleAdminController implements ControllerBridgeInterface, AddActionInterface
{
    use AddActionTrait;
    use SmartyTrait;

    public const DEFAULT_THEME = 'default';
    public const CONTROLLER_NAME_LEGACY = 'AdminFeatures';
    public const POSITION_IDENTIFIER = 'id_feature';
    public const TABLE = 'feature';
    public const LIST_ID = 'feature';
    public const CLASS_NAME = 'Feature';
    public const IDENTIFIER = 'id_feature';

    /**
     * @var string
     */
    public $php_self;

    /**
     * @var ControllerConfiguration
     */
    public $controllerConfiguration;

    //Filters
    /**
     * @var array
     */
    protected $filterList = [];

    /**
     * @var bool
     */
    protected $ajax = false;

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function indexAction(Request $request)
    {
        $this->setListFields();
        $this->buildActionList();

        $helperListConfiguration = $this->get('prestashop.core.bridge.helper_list_configuration_factory')->create([
            'table' => self::TABLE,
            'listId' => self::LIST_ID,
            'className' => self::CLASS_NAME,
            'controllerNameLegacy' => self::CONTROLLER_NAME_LEGACY,
            'identifier' => self::IDENTIFIER,
            'isJoinLanguageTableAuto' => true,
            'defaultOrderBy' => 'position',
            'fieldsList' => $this->controllerConfiguration->fieldsList,
        ]);

        $this->get('prestashop.core.bridge.processor.process_filter')->processFilter(
            $request,
            $helperListConfiguration,
            $this->controllerConfiguration
        );

        $this->get('prestashop.core.bridge.helper_list_bridge')->getList(
            $helperListConfiguration,
            $this->getContext()->language->id
        );

        return $this->renderSmarty(
            $this->get('prestashop.core.bridge.helper_list_bridge')->renderList(
                $this->controllerConfiguration,
                $helperListConfiguration
            ),
            $this->controllerConfiguration
        );
    }

    /**
     * Add a warning message to display at the top of the page.
     *
     * @param string $msg
     */
    protected function displayWarning($msg)
    {
        $this->controllerConfiguration->warnings[] = $msg;
    }

    /**
     * Build action for list interface
     *
     * @return void
     *
     * @throws \Exception
     */
    private function buildActionList(): void
    {
        $this->addAction(self::ACTION_TYPE_HEADER_TOOLBAR, 'new_feature', [
            //Used $this->generateUrl('admin_features_add')
            'href' => $this->controllerConfiguration->currentIndex . '&addfeature&token=' . $this->controllerConfiguration->token,
            'desc' => $this->trans('Add new feature', 'Admin.Catalog.Feature'),
            'icon' => 'process-icon-new',
        ]);
        $this->addAction(self::ACTION_TYPE_HEADER_TOOLBAR, 'new_feature_value', [
            //Used $this->generateUrl('admin_features_add_value')
            'href' => $this->controllerConfiguration->currentIndex . '&addfeature_value&id_feature=' . (int) Tools::getValue('id_feature') . '&token=' . $this->controllerConfiguration->token,
            'desc' => $this->trans('Add new feature value', 'Admin.Catalog.Help'),
            'icon' => 'process-icon-new',
        ]);

        $this->addAction(self::ACTION_TYPE_LIST_HEADER_TOOLBAR, 'new', [
            //Replace by $this->generateUrl('admin_features_add')
            'href' => $this->controllerConfiguration->currentIndex . '&addfeature&token=' . $this->controllerConfiguration->token,
            'desc' => $this->trans('Add new', 'Admin.Actions'),
        ]);

        $this->addAction(self::ACTION_TYPE_ROW, 'view');
        $this->addAction(self::ACTION_TYPE_ROW, 'edit');
        $this->addAction(self::ACTION_TYPE_ROW, 'delete');

        $this->addAction(self::ACTION_TYPE_BULK, 'delete', [
            'text' => $this->trans('Delete selected', 'Admin.Actions'),
            'icon' => 'icon-trash',
            'confirm' => $this->trans('Delete selected items?', 'Admin.Notifications.Warning'),
        ]);
    }

    /**
     * Define fields in the list
     *
     * @return void
     */
    private function setListFields(): void
    {
        $this->controllerConfiguration->fieldsList = [
            'id_feature' => [
                'title' => $this->trans('ID', 'Admin.Global', []),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Name', 'Admin.Global', []),
                'width' => 'auto',
                'filter_key' => 'b!name',
            ],
            'value' => [
                'title' => $this->trans('Values', 'Admin.Global', []),
                'orderby' => false,
                'search' => false,
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'position' => [
                'title' => $this->trans('Position', 'Admin.Global', []),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'position' => 'position',
            ],
        ];
    }
}
