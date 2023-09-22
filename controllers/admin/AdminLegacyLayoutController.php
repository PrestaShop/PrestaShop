<?php

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Bridge\Helper\AddFlashMessage;

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
class AdminLegacyLayoutControllerCore extends AdminController
{
    /** @var string */
    public $outPutHtml = '';
    /** @var string[] */
    public $jsRouterMetadata;
    /** @var array */
    protected $headerToolbarBtn = [];
    /** @var string */
    protected $title;
    /** @var bool */
    protected $showContentHeader = true;
    /** @var string */
    protected $headerTabContent = '';
    /**
     * See the $helpLink phpDoc below
     *
     * @var bool
     */
    protected $enableSidebar = false;
    /**
     * The Help Link is used for the 'Help' button in the top right of Back Office pages
     *
     * If $enableSidebar is true, the 'Help' button will download the content available at $helpLink
     * and inject it into the sidebar window
     *
     * If $enableSidebar is false, the 'Help' button is a link that redirects to $helpLink
     *
     * @var string|bool
     */
    protected $helpLink;
    /** @var bool */
    protected $useRegularH1Structure;
    /** @var bool */
    protected $lockedToAllShopContext = false;

    /**
     * @param string $controllerName
     * @param string $title
     * @param array $headerToolbarBtn
     * @param string $displayType
     * @param bool $showContentHeader
     * @param string $headerTabContent
     * @param bool $enableSidebar
     * @param string $helpLink
     * @param string[] $jsRouterMetadata array to provide base_url and security token for JS Router
     * @param string $metaTitle
     * @param bool $useRegularH1Structure allows complex <h1> structure if set to false
     */
    public function __construct(
        $controllerName = '',
        $title = '',
        $headerToolbarBtn = [],
        $displayType = '',
        $showContentHeader = true,
        $headerTabContent = '',
        $enableSidebar = false,
        $helpLink = '',
        $jsRouterMetadata = [],
        $metaTitle = '',
        $useRegularH1Structure = true
    ) {
        // Compatibility with legacy behavior.
        // Some controllers can only be used in "All stores" context.
        // This makes sure that user cannot switch shop contexts
        // when in one of pages (controller) below.
        $controllers = [
            'AdminAccess',
            'AdminFeatureFlag',
            'AdminLanguages',
            'AdminProfiles',
            'AdminSpecificPriceRule',
            'AdminStatuses',
            'AdminSecurity',
            'AdminSecuritySessionEmployee',
            'AdminSecuritySessionCustomer',
            'AdminTranslations',
        ];

        if (in_array($controllerName, $controllers)) {
            $this->multishop_context = Shop::CONTEXT_ALL;
            $this->lockedToAllShopContext = true;
        }

        parent::__construct($controllerName, 'new-theme');

        $this->title = $title;
        $this->meta_title = ($metaTitle !== '') ? $metaTitle : $title;
        $this->display = $displayType;
        $this->bootstrap = true;
        $this->controller_name = $_GET['controller'] = $controllerName;
        $this->id = Tab::getIdFromClassName($this->controller_name);
        $this->headerToolbarBtn = $headerToolbarBtn;
        $this->showContentHeader = $showContentHeader;
        $this->headerTabContent = $headerTabContent;
        $this->enableSidebar = $enableSidebar;
        $this->helpLink = $helpLink;
        $this->php_self = $controllerName;
        $this->className = 'LegacyLayout';
        $this->jsRouterMetadata = $jsRouterMetadata;
        $this->useRegularH1Structure = $useRegularH1Structure;
    }

    /**
     * This helps avoiding handling legacy processes when in Symfony Controllers.
     * Otherwise when using POST action to render form you sometimes get an exception.
     */
    public function initProcess()
    {
    }

    /**
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia(true);
    }

    /**
     * @param bool $disable
     *
     * @return bool
     */
    public function viewAccess($disable = false)
    {
        return true;
    }

    /**
     * Always return true, cause of legacy redirect in layout
     *
     * @return bool
     */
    public function checkAccess()
    {
        return true;
    }

    protected function addHeaderToolbarBtn()
    {
        $this->page_header_toolbar_btn = array_merge($this->page_header_toolbar_btn, $this->headerToolbarBtn);
    }

    /**
     * AdminController::initContent() override.
     *
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        $this->addHeaderToolbarBtn();

        $this->show_page_header_toolbar = (bool) $this->showContentHeader;

        // @todo remove once the product page has been made responsive
        $isProductPage = ('AdminProducts' === $this->controller_name);

        $vars = [
            'maintenance_mode' => !(bool) Configuration::get('PS_SHOP_ENABLE'),
            'maintenance_allow_admins' => (bool) Configuration::get('PS_MAINTENANCE_ALLOW_ADMINS'),
            'debug_mode' => (bool) _PS_MODE_DEV_,
            'headerTabContent' => $this->headerTabContent,
            'content' => '{$content}', //replace content by original smarty tag var
            'enableSidebar' => $this->enableSidebar,
            'lite_display' => $this->lite_display,
            'url_post' => self::$currentIndex . '&token=' . $this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'title' => $this->title ? $this->title : $this->page_header_toolbar_title,
            'toolbar_btn' => $this->page_header_toolbar_btn,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
            'toggle_navigation_url' => $this->context->link->getAdminLink('AdminEmployees', true, [], [
                'action' => 'toggleMenu',
            ]),
            /* base_url and security token for js router. @since 1.7.7 */
            'js_router_metadata' => $this->jsRouterMetadata,
            /* allow complex <h1> structure. @since 1.7.7 */
            'use_regular_h1_structure' => $this->useRegularH1Structure,
            // legacy context selector is hidden on migrated pages when multistore feature is used
            'hideLegacyStoreContextSelector' => $this->isMultistoreEnabled(),
            'locked_to_all_shop_context' => $this->lockedToAllShopContext,
        ];

        if ($this->helpLink === false || !empty($this->helpLink)) {
            $vars['help_link'] = $this->helpLink;
        }

        $this->context->smarty->assign($vars);
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = array_unique($this->breadcrumbs);
        parent::initToolbarTitle();
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
    }

    public function display()
    {
        ob_start();
        parent::display();
        $this->outPutHtml = ob_get_contents();
        ob_end_clean();

        // We push notifications from the legacy controller but only in migrated page for now (that's why it is done here and ont in AdminController)
        // and of course only if the Symfony layout is enabled This will mostly be useful when legacy pages are rendered by Symfony layout
        if ($this->get(FeatureFlagStateCheckerInterface::class)->isEnabled(FeatureFlagSettings::FEATURE_FLAG_SYMFONY_LAYOUT)) {
            foreach (['errors', 'warnings', 'informations', 'confirmations'] as $type) {
                /** @var AddFlashMessage $addFlashMessage */
                $addFlashMessage = $this->get(AddFlashMessage::class);
                foreach ($this->$type as $message) {
                    $addFlashMessage->addMessage($type, $message);
                }
            }
        }
    }
}
