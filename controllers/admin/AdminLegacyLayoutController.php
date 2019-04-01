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
class AdminLegacyLayoutControllerCore extends AdminController
{
    public $outPutHtml = '';
    protected $headerToolbarBtn = array();
    protected $title;
    protected $showContentHeader = true;
    protected $headerTabContent = '';
    protected $enableSidebar = false;
    protected $helpLink;

    public function __construct($controllerName = '', $title = '', $headerToolbarBtn = array(), $displayType = '', $showContentHeader = true, $headerTabContent = '', $enableSidebar = false, $helpLink = '')
    {
        // Compatibility with legacy behavior.
        // Some controllers can only be used in "All shops" context.
        // This makes sure that user cannot switch shop contexts
        // when in one of pages (controller) below.
        $controllers = ['AdminLanguages', 'AdminProfiles'];

        if (in_array($controllerName, $controllers)) {
            $this->multishop_context = Shop::CONTEXT_ALL;
        }

        parent::__construct($controllerName, 'new-theme');

        $this->title = $title;
        $this->meta_title = $title;
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
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia(true);
    }

    public function viewAccess($disable = false)
    {
        return true;
    }

    //always return true, cause of legacy redirect in layout
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

        $vars = array(
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
        );

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

    /**
     * {@inheritdoc}
     */
    public function addJquery($version = null, $folder = null, $minifier = true)
    {
        // jQuery is already included, so do nothing
        @trigger_error(__FUNCTION__ . 'is deprecated and has no effect in the New Theme since version 1.7.6.0.', E_USER_DEPRECATED);
    }
}
