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

namespace PrestaShopBundle\Bridge\AdminController;

use PrestaShopBundle\Bridge\AdminController\Action\HeaderToolbarAction;
use PrestaShopBundle\Security\Admin\Employee;
use Shop;

/**
 * This object holds the configuration of a Controller that is being migrated horizontally.
 * Its properties reflect the properties of a legacy PrestaShop controller.
 */
class ControllerConfiguration
{
    /**
     * @var int|null
     */
    public $tabId;

    /**
     * ClassName of related object model. E.g. "Feature", "Category", "Product" etc.
     *
     * @var string|null
     */
    public $objectModelClassName;

    /**
     * @var string|null
     */
    public $legacyControllerName;

    /**
     * @var string|null
     */
    public $legacyCurrentIndex;

    /**
     * @var string|null
     */
    public $positionIdentifier;

    /**
     * @var string|null
     */
    public $tableName;

    /**
     * @var string|null
     */
    public $token;

    /**
     * @var Employee|null
     */
    public $user;

    /**
     * @var array
     */
    public $metaTitle = [];

    /**
     * @var array
     */
    public $breadcrumbs = [];

    /**
     * @var bool
     */
    public $liteDisplay = false;

    /**
     * @var string
     */
    public $display = 'list';

    /**
     * @var bool
     */
    public $showPageHeaderToolbar = true;

    /**
     * @var string
     */
    public $pageHeaderToolbarTitle = '';

    /**
     * @var array
     */
    public $pageHeaderToolbarButton = [];

    /**
     * @var array
     */
    public $toolbarTitle = [];

    /**
     * @var bool
     */
    public $displayHeader = true;

    /**
     * @var bool
     */
    public $displayHeaderJavascript = true;

    /**
     * @var bool
     */
    public $displayFooter = true;

    /**
     * @var bool
     */
    public $bootstrap = true;

    /**
     * @var array
     */
    public $cssFiles = [];

    /**
     * @var array
     */
    public $jsFiles = [];

    /**
     * @var string|null
     */
    public $templateFolder;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @var array
     */
    public $warnings = [];

    /**
     * @var array
     */
    public $confirmations = [];

    /**
     * @var array
     */
    public $informations = [];

    /**
     * @var bool
     */
    public $json = false;

    /**
     * @var string
     */
    public $template = 'content.tpl';

    /**
     * @var array
     */
    public $templateVars = [];

    /**
     * @var array
     */
    public $modals = [];

    /**
     * This parameter is needed by legacy helper shop, we can't remove it.
     *
     * @var int
     */
    public $multiShopContext = Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP;

    /**
     * This parameter is needed by legacy helper shop, so we can't remove it.
     *
     * @var bool
     */
    public $multiShopContextGroup = true;

    /**
     * Adds toolbar action to the page
     *
     * @param HeaderToolbarAction $action
     *
     * @return ControllerConfiguration
     */
    public function addToolbarAction(HeaderToolbarAction $action): ControllerConfiguration
    {
        $this->pageHeaderToolbarButton[$action->getLabel()] = $action->getConfig();

        return $this;
    }
}
