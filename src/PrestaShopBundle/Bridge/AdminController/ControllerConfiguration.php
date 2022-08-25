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

use PrestaShopBundle\Security\Admin\Employee;
use Shop;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This object holds the configuration of a Controller that is being migrated horizontally.
 * Its properties reflect the properties of a legacy PrestaShop controller.
 */
class ControllerConfiguration
{
    /**
     * Identifier of tab related to this configuration (tab is considered a single page and all of them are registered in database)
     *
     * @var int
     */
    public $tabId;

    /**
     * ClassName of related object model. E.g. "Feature", "Category", "Product" etc.
     *
     * @var string
     */
    public $objectModelClassName;

    /**
     * The name of related AdminController (e.g. AdminFeatures)
     *
     * @var string
     */
    public $legacyControllerName;

    /**
     * Url referring to related legacy page (e.g. http://prestashop.local/admin-dev/index.php?controller=AdminFeatures&token=fooBar)
     *
     * @var string
     */
    public $legacyCurrentIndex;

    /**
     * Name of field by which position is supposed to be identified (usually it is the id field e.g. id_feature)
     *
     * @var string|null
     */
    public $positionIdentifierKey;

    /**
     * Name of the related database table
     *
     * @var string
     */
    public $tableName;

    /**
     * Security token
     *
     * @var string|null
     */
    public $token;

    /**
     * Meta title of single language if it is a string
     * or array of localized meta title values where index is the id of the language
     *
     * @var string|array<int, string>
     */
    public $metaTitle = [];

    /**
     * Array of parent tab names up to a current tab
     *
     * @var array<int, string>
     */
    public $breadcrumbs = [];

    /**
     * Defines if lite display should be used (lite display doesn't show header and footer)
     *
     * @var bool
     */
    public $liteDisplay = false;

    /**
     * Provides information about the type of displayed page (e.g. list, form, view, edit, options)
     *
     * @var string
     *
     * @see \AdminController::$display
     */
    public $displayType = 'list';

    /**
     * Controls page header toolbar visibility
     *
     * @var bool
     */
    public $showPageHeaderToolbar = true;

    /**
     * Title of the page shown in page header toolbar
     *
     * @var string
     */
    public $pageHeaderToolbarTitle = '';

    /**
     * Action buttons rendered in page header (e.g. button to Add new feature)
     *
     * @var array<string, array{href?: string, desc?: string, icon?: string, class?: string}>
     */
    public $pageHeaderToolbarButtons = [];

    /**
     * @todo: this is duplicote from HelperListConfiguration. Should be removed here as its unused
     *
     * @var array
     */
    public $toolbarButtons = [];

    /**
     * @var string[]|string
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
     * @var string
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
     * @var Employee
     */
    private $user;

    /**
     * @param Employee $user the current user
     */
    public function __construct(
        Employee $user,
        int $tabId,
        string $objectModelClassName,
        string $legacyControllerName,
        string $tableName,
        string $templateFolder
    ) {
        $this->user = $user;
        $this->tabId = $tabId;
        $this->objectModelClassName = $objectModelClassName;
        $this->legacyControllerName = $legacyControllerName;
        $this->tableName = $tableName;
        $this->templateFolder = $templateFolder;
    }

    /**
     * Adds toolbar action to the page
     *
     * @param string $label
     * @param array<string, mixed> $config
     *
     * @return ControllerConfiguration
     */
    public function addHeaderToolbarAction(string $label, array $config): self
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setDefined(['href', 'desc', 'icon', 'class'])
            ->setDefaults(['class' => ''])
            ->setAllowedTypes('class', ['string'])
            ->setAllowedTypes('href', ['string'])
            ->setAllowedTypes('desc', ['string'])
        ;

        $this->pageHeaderToolbarButtons[$label] = $optionsResolver->resolve($config);

        return $this;
    }

    /**
     * @return Employee
     */
    public function getUser(): Employee
    {
        return $this->user;
    }
}
