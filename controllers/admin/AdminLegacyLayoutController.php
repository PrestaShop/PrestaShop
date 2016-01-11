<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class AdminLegacyLayoutControllerCore extends AdminController
{
    public $outPutHtml = '';
    private $headerToolbarBtn = array();
    private $title;
    private $showContentHeader = true;

    public function __construct($controllerName = '', $title = '', $headerToolbarBtn = array(), $displayType = '', $showContentHeader = true)
    {
        parent::__construct();

        $this->title = $title;
        $this->display = $displayType;
        $this->bootstrap = true;
        $this->controller_name = $_GET['controller'] = $controllerName;
        $this->id = Tab::getIdFromClassName($this->controller_name);
        $this->headerToolbarBtn = $headerToolbarBtn;
        $this->showContentHeader = $showContentHeader;
    }

    public function viewAccess()
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
        parent::initToolbar();
        parent::initTabModuleList();
        parent::initPageHeaderToolbar();

        $this->addHeaderToolbarBtn();

        parent::initContent();

        $this->show_page_header_toolbar = (bool) $this->showContentHeader;

        if ($this->title) {
            $this->context->smarty->assign(array('title' => $this->title));
        }

        $this->context->smarty->assign(array(
            'maintenance_mode' => !(bool)Configuration::get('PS_SHOP_ENABLE'),
            'content' => '{$content}', //replace content by original smarty tag var
        ));
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
