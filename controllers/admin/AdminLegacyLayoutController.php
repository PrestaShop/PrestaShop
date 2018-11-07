<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
class AdminLegacyLayoutControllerCore extends AdminController
{
    public $outPutHtml = '';
    private $headerToolbarBtn = array();
    private $title;
    private $showContentHeader = true;
    private $headerTabContent = '';
    private $enableSidebar = false;
    private $helpLink;

    public function __construct($controllerName = '', $title = '', $headerToolbarBtn = array(), $displayType = '', $showContentHeader = true, $headerTabContent = '', $enableSidebar = false, $helpLink = '')
    {
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

    private function addHeaderToolbarBtn()
    {
        $this->page_header_toolbar_btn = array_merge($this->page_header_toolbar_btn, $this->headerToolbarBtn);
    }

    public function initContent()
    {
        $this->addHeaderToolbarBtn();

        $this->show_page_header_toolbar = (bool) $this->showContentHeader;

        $vars = array(
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
}
