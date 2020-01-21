<?php

/**
 * 2007-2019 PrestaShop SA and Contributors
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
    /** @var bool */
    protected $enableSidebar = false;
    /** @var string */
    protected $helpLink;
    /** @var bool */
    protected $useRegularH1Structure;

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
        // Some controllers can only be used in "All shops" context.
        // This makes sure that user cannot switch shop contexts
        // when in one of pages (controller) below.
        $controllers = ['AdminLanguages', 'AdminProfiles', 'AdminSpecificPriceRule'];

        if (in_array($controllerName, $controllers)) {
            $this->multishop_context = Shop::CONTEXT_ALL;
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

    public function initContent()
    {
        $this->addHeaderToolbarBtn();

        $this->show_page_header_toolbar = (bool) $this->showContentHeader;

        // @todo remove once the product page has been made responsive
        $isProductPage = ('AdminProducts' === $this->controller_name);

        $vars = [
            'viewport_scale' => $isProductPage ? '0.75' : '1',
            'maintenance_mode' => !(bool) Configuration::get('PS_SHOP_ENABLE'),
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

        $this->outPutHtml;
    }
}
