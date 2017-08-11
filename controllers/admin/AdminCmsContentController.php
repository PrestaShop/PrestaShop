<?php
/**
 * 2007-2018 PrestaShop
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

/**
 * @property CMS $object
 */
class AdminCmsContentControllerCore extends AdminController
{
    /** @var object adminCMSCategories() instance */
    protected $admin_cms_categories;

    /** @var object adminCMS() instance */
    protected $admin_cms;

    /** @var object Category() instance for navigation*/
    protected static $category = null;

    public function __construct()
    {
        $this->bootstrap = true;
        /* Get current category */
        $id_cms_category = (int)Tools::getValue('id_cms_category', Tools::getValue('id_cms_category_parent', 1));
        self::$category = new CMSCategory($id_cms_category);
        if (!Validate::isLoadedObject(self::$category)) {
            die('Category cannot be loaded');
        }

        parent::__construct();

        $this->table = 'cms';
        $this->className = 'CMS';
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash'
            )
        );

        $this->admin_cms_categories = new AdminCmsCategoriesController();
        $this->admin_cms_categories->tabAccess = $this->tabAccess;
        $this->admin_cms_categories->init();
        $this->admin_cms = new AdminCmsController();
        $this->admin_cms->tabAccess = $this->tabAccess;
        $this->admin_cms->init();
        $this->context->controller = $this;
    }

    /**
     * Return current category
     *
     * @return object
     */
    public static function getCurrentCMSCategory()
    {
        return self::$category;
    }

    public function initProcess()
    {
        if (((Tools::isSubmit('submitAddcms_category') || Tools::isSubmit('submitAddcms_categoryAndStay')) && count($this->admin_cms_categories->errors))
            || Tools::isSubmit('updatecms_category')
            || Tools::isSubmit('addcms_category')) {
            $this->display = 'edit_category';
        } elseif (((Tools::isSubmit('submitAddcms') || Tools::isSubmit('submitAddcmsAndStay')) && count($this->admin_cms->errors))
            || Tools::isSubmit('updatecms')
            || Tools::isSubmit('addcms')) {
            $this->display = 'edit_page';
        } else {
            $this->display = 'list';
        }
    }

    public function initContent()
    {
        $id_cms_category = (int)Tools::getValue('id_cms_category');

        if (!$id_cms_category) {
            $id_cms_category = 1;
        }

        if ($this->display == 'list') {
            $this->page_header_toolbar_btn['new_cms_category'] = array(
                'href' => self::$currentIndex.'&addcms_category&token='.$this->token,
                'desc' => $this->trans('Add new page category', array(), 'Admin.Design.Help'),
                'icon' => 'process-icon-new'
            );
            $this->page_header_toolbar_btn['new_cms_page'] = array(
                'href' => self::$currentIndex.'&addcms&id_cms_category='.(int)$id_cms_category.'&token='.$this->token,
                'desc' => $this->trans('Add new page', array(), 'Admin.Design.Help'),
                'icon' => 'process-icon-new'
            );
        }

        $this->page_header_toolbar_title = implode(' '.Configuration::get('PS_NAVIGATION_PIPE').' ', $this->toolbar_title);

        if (is_array($this->page_header_toolbar_btn)
            && $this->page_header_toolbar_btn instanceof Traversable
            || trim($this->page_header_toolbar_title) != '') {
            $this->show_page_header_toolbar = true;
        }

        $this->admin_cms_categories->token = $this->token;
        $this->admin_cms->token = $this->token;

        if ($this->display == 'edit_category') {
            $this->content .= $this->admin_cms_categories->renderForm();
        } elseif ($this->display == 'edit_page') {
            $this->content .= $this->admin_cms->renderForm();
        } elseif ($this->display == 'list') {
            $id_cms_category = (int)Tools::getValue('id_cms_category');
            if (!$id_cms_category) {
                $id_cms_category = 1;
            }

            // CMS categories breadcrumb
            $cms_tabs = array('cms_category', 'cms');
            // Cleaning links
            $cat_bar_index = self::$currentIndex;
            foreach ($cms_tabs as $tab) {
                if (Tools::getValue($tab.'Orderby') && Tools::getValue($tab.'Orderway')) {
                    $cat_bar_index = preg_replace('/&'.$tab.'Orderby=([a-z _]*)&'.$tab.'Orderway=([a-z]*)/i', '', self::$currentIndex);
                }
            }
            $this->context->smarty->assign(array(
                'cms_breadcrumb' => Tools::getPath($cat_bar_index, $id_cms_category, '', '', 'cms'),
            ));

            $this->content .= $this->admin_cms_categories->renderList();
            $this->admin_cms->id_cms_category = $id_cms_category;
            $this->content .= $this->admin_cms->renderList();
        }

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'title' => $this->page_header_toolbar_title,
            'toolbar_btn' => $this->page_header_toolbar_btn,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
            'page_header_toolbar_title' => $this->toolbar_title,
        ));
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = is_array($this->breadcrumbs) ? array_unique($this->breadcrumbs) : array($this->breadcrumbs);

        $id_cms_category = (int)Tools::getValue('id_cms_category');
        if ($id_cms_category && $id_cms_category !==1) {
            $cms_category = new CMSCategory($id_cms_category);
        }
        $id_cms_page = Tools::getValue('id_cms');


        if ($this->display == 'edit_category') {
            if (Tools::getValue('addcms_category') !== false) {
                $this->toolbar_title[] = $this->trans('Add new category', array(), 'Admin.Design.Feature');
            } else {
                if (isset($cms_category)) {
                    $this->toolbar_title[] = $this->trans('Edit category: %name%', array('%name%' => $cms_category->name[$this->context->employee->id_lang]), 'Admin.Design.Feature');
                }
            }
        } elseif ($this->display == 'edit_page') {
            if (Tools::getValue('addcms') !== false) {
                $this->toolbar_title[] = $this->trans('Add new page', array(), 'Admin.Design.Feature');
            } elseif ($id_cms_page) {
                $cms_page = new CMS($id_cms_page);
                $this->toolbar_title[] = $this->trans('Edit page: %meta_title%', array('%meta_title%' => $cms_page->meta_title[$this->context->employee->id_lang]), 'Admin.Design.Feature');
            }
        } elseif ($this->display == 'list' && isset($cms_category)) {
            $this->toolbar_title[] = $this->trans('Category: %category%', array('%category%' => $cms_category->name[$this->context->employee->id_lang]), 'Admin.Design.Feature');
        }
    }

    public function postProcess()
    {
        $this->admin_cms->postProcess();
        $this->admin_cms_categories->postProcess();

        parent::postProcess();

        if (isset($this->admin_cms->errors)) {
            $this->errors = array_merge($this->errors, $this->admin_cms->errors);
        }

        if (isset($this->admin_cms_categories->errors)) {
            $this->errors = array_merge($this->errors, $this->admin_cms_categories->errors);
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
    }

    public function ajaxProcessUpdateCmsPositions()
    {
        if ($this->access('edit')) {
            $id_cms = (int)Tools::getValue('id_cms');
            $id_category = (int)Tools::getValue('id_cms_category');
            $way = (int)Tools::getValue('way');
            $positions = Tools::getValue('cms');
            if (is_array($positions)) {
                foreach ($positions as $key => $value) {
                    $pos = explode('_', $value);
                    if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_category && $pos[2] == $id_cms)) {
                        $position = $key;
                        break;
                    }
                }
            }
            $cms = new CMS($id_cms);
            if (Validate::isLoadedObject($cms)) {
                if (isset($position) && $cms->updatePosition($way, $position)) {
                    die(true);
                } else {
                    die('{"hasError" : true, "errors" : "Can not update cms position"}');
                }
            } else {
                die('{"hasError" : true, "errors" : "This cms can not be loaded"}');
            }
        }
    }

    public function ajaxProcessUpdateCmsCategoriesPositions()
    {
        if ($this->access('edit')) {
            $id_cms_category_to_move = (int)Tools::getValue('id_cms_category_to_move');
            $id_cms_category_parent = (int)Tools::getValue('id_cms_category_parent');
            $way = (int)Tools::getValue('way');
            $positions = Tools::getValue('cms_category');
            if (is_array($positions)) {
                foreach ($positions as $key => $value) {
                    $pos = explode('_', $value);
                    if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_cms_category_parent && $pos[2] == $id_cms_category_to_move)) {
                        $position = $key;
                        break;
                    }
                }
            }
            $cms_category = new CMSCategory($id_cms_category_to_move);
            if (Validate::isLoadedObject($cms_category)) {
                if (isset($position) && $cms_category->updatePosition($way, $position)) {
                    die(true);
                } else {
                    die('{"hasError" : true, "errors" : "Can not update cms categories position"}');
                }
            } else {
                die('{"hasError" : true, "errors" : "This cms category can not be loaded"}');
            }
        }
    }

    public function ajaxProcessPublishCMS()
    {
        if ($this->access('edit')) {
            if ($id_cms = (int)Tools::getValue('id_cms')) {
                $bo_cms_url = _PS_BASE_URL_.__PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/index.php?tab=AdminCmsContent&id_cms='.(int)$id_cms.'&updatecms&token='.$this->token;

                if (Tools::getValue('redirect')) {
                    die($bo_cms_url);
                }

                $cms = new CMS((int)(Tools::getValue('id_cms')));
                if (!Validate::isLoadedObject($cms)) {
                    die('error: invalid id');
                }

                $cms->active = 1;
                if ($cms->save()) {
                    die($bo_cms_url);
                } else {
                    die('error: saving');
                }
            } else {
                die('error: parameters');
            }
        }
    }
}
