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

    public function __construct($controllerName = '', $title = '')
    {
        parent::__construct();

        $this->controller_name = $controllerName;
        $this->id = Tab::getIdFromClassName($this->controller_name);

        if ($title) {
            $this->context->smarty->assign(array('title' => $title));
        }
    }

    public function viewAccess()
    {
        return true;
    }

    public function initContent()
    {
        $this->initPageHeaderToolbar();
        $this->context->smarty->assign(array(
            'content' => '{$content}', //replace content by original smarty tag var
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = array_unique($this->breadcrumbs);
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
