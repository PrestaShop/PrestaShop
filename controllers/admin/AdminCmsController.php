<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property CMS $object
 */
class AdminCmsControllerCore extends AdminController
{
    protected $category;

    public $id_cms_category;

    protected $position_identifier = 'id_cms';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'cms';
        $this->list_id = 'cms';
        $this->className = 'CMS';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->_orderBy = 'position';

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash'
            )
        );
        $this->fields_list = array(
            'id_cms' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'link_rewrite' => array('title' => $this->trans('URL', array(), 'Admin.Global')),
            'meta_title' => array('title' => $this->trans('Title', array(), 'Admin.Global'), 'filter_key' => 'b!meta_title'),
            'position' => array('title' => $this->trans('Position', array(), 'Admin.Global'),'filter_key' => 'position', 'align' => 'center', 'class' => 'fixed-width-sm', 'position' => 'position'),
            'active' => array('title' => $this->trans('Displayed', array(), 'Admin.Global'), 'align' => 'center', 'active' => 'status', 'class' => 'fixed-width-sm', 'type' => 'bool', 'orderby' => false)
        );

        // The controller can't be call directly
        // In this case, AdminCmsContentController::getCurrentCMSCategory() is null
        if (!AdminCmsContentController::getCurrentCMSCategory()) {
            $this->redirect_after = '?controller=AdminCmsContent&token='.Tools::getAdminTokenLite('AdminCmsContent');
            $this->redirect();
        }

        $this->_category = AdminCmsContentController::getCurrentCMSCategory();
        $this->tpl_list_vars['icon'] = 'icon-folder-close';
        $this->tpl_list_vars['title'] = $this->trans('Pages in category "%name%"', array('%name%' => $this->_category->name[Context::getContext()->employee->id_lang]), 'Admin.Design.Feature');
        $this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'cms_category` c ON (c.`id_cms_category` = a.`id_cms_category`)';
        $this->_select = 'a.position ';
        $this->_where = ' AND c.id_cms_category = '.(int)$this->_category->id;
    }

    public function getTabSlug()
    {
        return 'ROLE_MOD_TAB_ADMINCMSCONTENT_';
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['save-and-preview'] = array(
            'href' => '#',
            'desc' => $this->trans('Save and preview', array(), 'Admin.Actions')
        );
        $this->page_header_toolbar_btn['save-and-stay'] = array(
            'short' => $this->trans('Save and stay', array(), 'Admin.Actions'),
            'href' => '#',
            'desc' => $this->trans('Save and stay', array(), 'Admin.Actions'),
        );

        return parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        if (Validate::isLoadedObject($this->object)) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }

        $this->initToolbar();
        $this->initPageHeaderToolbar();

        $categories = CMSCategory::getCategories($this->context->language->id, false);
        $html_categories = CMSCategory::recurseCMSCategory($categories, $categories[0][1], 1, $this->getFieldValue($this->object, 'id_cms_category'), 1);

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Page'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                // custom template
                array(
                    'type' => 'select_category',
                    'label' => $this->trans('Page Category', array(), 'Admin.Design.Feature'),
                    'name' => 'id_cms_category',
                    'options' => array(
                        'html' => $html_categories,
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Meta title', array(), 'Admin.Global'),
                    'name' => 'meta_title',
                    'id' => 'name', // for copyMeta2friendlyURL compatibility
                    'lang' => true,
                    'required' => true,
                    'class' => 'copyMeta2friendlyURL',
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Meta description', array(), 'Admin.Global'),
                    'name' => 'meta_description',
                    'lang' => true,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->trans('Meta keywords', array(), 'Admin.Global'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'hint' => array(
                        $this->trans('To add "tags" click in the field, write something, and then press "Enter."', array(), 'Admin.Design.Help'),
                        $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Friendly URL', array(), 'Admin.Global'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->trans('Only letters and the hyphen (-) character are allowed.', array(), 'Admin.Design.Feature')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Page content', array(), 'Admin.Design.Feature'),
                    'name' => 'content',
                    'autoload_rte' => true,
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Indexation by search engines', array(), 'Admin.Design.Feature'),
                    'name' => 'indexation',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'indexation_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'indexation_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Displayed', array(), 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            ),
            'buttons' => array(
                'save_and_preview' => array(
                    'name' => 'viewcms',
                    'type' => 'submit',
                    'title' => $this->trans('Save and preview', array(), 'Admin.Actions'),
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-preview'
                )
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            );
        }

        if (Validate::isLoadedObject($this->object)) {
            $this->context->smarty->assign('url_prev', $this->getPreviewUrl($this->object));
        }

        $this->tpl_form_vars = array(
            'active' => $this->object->active,
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
        );
        return parent::renderForm();
    }

    public function renderList()
    {
        $this->_group = 'GROUP BY a.`id_cms`';
        //self::$currentIndex = self::$currentIndex.'&cms';
        $this->position_group_identifier = (int)$this->id_cms_category;

        $this->toolbar_title = $this->trans('Pages in this category', array(), 'Admin.Design.Feature');
        $this->toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&id_cms_category='.(int)$this->id_cms_category.'&token='.$this->token,
            'desc' => $this->trans('Add new', array(), 'Admin.Actions')
        );

        return parent::renderList();
    }

    public function displayList($token = null)
    {
        /* Display list header (filtering, pagination and column names) */
        $this->displayListHeader($token);
        if (!count($this->_list)) {
            echo '<tr><td class="center" colspan="'.(count($this->fields_list) + 2).'">'.$this->trans('No items found', array(), 'Admin.Design.Notification').'</td></tr>';
        }

        /* Show the content of the table */
        $this->displayListContent($token);

        /* Close list table and submit button */
        $this->displayListFooter($token);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('viewcms') && ($id_cms = (int)Tools::getValue('id_cms'))) {
            parent::postProcess();
            if (($cms = new CMS($id_cms, $this->context->language->id)) && Validate::isLoadedObject($cms)) {
                Tools::redirectAdmin(self::$currentIndex.'&id_cms='.$id_cms.'&conf=4&updatecms&token='.Tools::getAdminTokenLite('AdminCmsContent').'&url_preview=1');
            }
        } elseif (Tools::isSubmit('deletecms')) {
            if (Tools::getValue('id_cms') == Configuration::get('PS_CONDITIONS_CMS_ID')) {
                Configuration::updateValue('PS_CONDITIONS', 0);
                Configuration::updateValue('PS_CONDITIONS_CMS_ID', 0);
            }
            $cms = new CMS((int)Tools::getValue('id_cms'));
            $cms->cleanPositions($cms->id_cms_category);
            if (!$cms->delete()) {
                $this->errors[] = $this->trans('An error occurred while deleting the object.', array(), 'Admin.Notifications.Error')
                    .' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&id_cms_category='.$cms->id_cms_category.'&conf=1&token='.Tools::getAdminTokenLite('AdminCmsContent'));
            }
        }/* Delete multiple objects */
        elseif (Tools::getValue('submitDel'.$this->table)) {
            if ($this->access('delete')) {
                if (Tools::isSubmit($this->table.'Box')) {
                    $cms = new CMS();
                    $result = true;
                    $result = $cms->deleteSelection(Tools::getValue($this->table.'Box'));
                    if ($result) {
                        $cms->cleanPositions((int)Tools::getValue('id_cms_category'));
                        $token = Tools::getAdminTokenLite('AdminCmsContent');
                        Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.$token.'&id_cms_category='.(int)Tools::getValue('id_cms_category'));
                    }
                    $this->errors[] = $this->trans('An error occurred while deleting this selection.', array(), 'Admin.Notifications.Error');
                } else {
                    $this->errors[] = $this->trans('You must select at least one element to delete.', array(), 'Admin.Notifications.Error');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitAddcms') || Tools::isSubmit('submitAddcmsAndPreview')) {
            parent::validateRules();
            if (count($this->errors)) {
                return false;
            }
            if (!$id_cms = (int)Tools::getValue('id_cms')) {
                $cms = new CMS();
                $this->copyFromPost($cms, 'cms');
                if (!$cms->add()) {
                    $this->errors[] = $this->trans('An error occurred while creating an object.', array(), 'Admin.Notifications.Error').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                } else {
                    $this->updateAssoShop($cms->id);
                }
            } else {
                $cms = new CMS($id_cms);
                $this->copyFromPost($cms, 'cms');
                if (!$cms->update()) {
                    $this->errors[] = $this->trans('An error occurred while updating an object.', array(), 'Admin.Notifications.Error').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                } else {
                    $this->updateAssoShop($cms->id);
                }
            }
            if (Tools::isSubmit('view'.$this->table)) {
                Tools::redirectAdmin(self::$currentIndex.'&id_cms='.$cms->id.'&conf=4&updatecms&token='.Tools::getAdminTokenLite('AdminCmsContent').'&url_preview=1');
            } elseif (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$cms->id.'&conf=4&update'.$this->table.'&token='.Tools::getAdminTokenLite('AdminCmsContent'));
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&id_cms_category='.$cms->id_cms_category.'&conf=4&token='.Tools::getAdminTokenLite('AdminCmsContent'));
            }
        } elseif (Tools::isSubmit('way') && Tools::isSubmit('id_cms') && (Tools::isSubmit('position'))) {
            /** @var CMS $object */
            if (!$this->access('edit')) {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            } elseif (!Validate::isLoadedObject($object = $this->loadObject())) {
                $this->errors[] = $this->trans('An error occurred while updating the status for an object.', array(), 'Admin.Notifications.Error')
                    .' <b>'.$this->table.'</b> '.$this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
            } elseif (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position'))) {
                $this->errors[] = $this->trans('Failed to update the position.', array(), 'Admin.Notifications.Error');
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=4&id_cms_category='.(int)$object->id_cms_category.'&token='.Tools::getAdminTokenLite('AdminCmsContent'));
            }
        }
        /* Change object statuts (active, inactive) */
        elseif (Tools::isSubmit('statuscms') && Tools::isSubmit($this->identifier)) {
            if ($this->access('edit')) {
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    /** @var CMS $object */
                    if ($object->toggleStatus()) {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=5&id_cms_category='.(int)$object->id_cms_category.'&token='.Tools::getValue('token'));
                    } else {
                        $this->errors[] = $this->trans('An error occurred while updating the status.', array(), 'Admin.Notifications.Error');
                    }
                } else {
                    $this->errors[] = $this->trans('An error occurred while updating the status for an object.', array(), 'Admin.Notifications.Error')
                        .' <b>'.$this->table.'</b> '.$this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        }
        /* Delete multiple CMS content */
        elseif (Tools::isSubmit('submitBulkdeletecms')) {
            if ($this->access('delete')) {
                $this->action = 'bulkdelete';
                $this->boxes = Tools::getValue($this->table.'Box');
                if (is_array($this->boxes) && array_key_exists(0, $this->boxes)) {
                    $firstCms = new CMS((int)$this->boxes[0]);
                    $id_cms_category = (int)$firstCms->id_cms_category;
                    if (!$res = parent::postProcess(true)) {
                        return $res;
                    }
                    Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.Tools::getAdminTokenLite('AdminCmsContent').'&id_cms_category='.$id_cms_category);
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } else {
            parent::postProcess(true);
        }
    }

    public function getPreviewUrl(CMS $cms)
    {
        $preview_url = $this->context->link->getCMSLink($cms, null, null, $this->context->language->id);
        if (!$cms->active) {
            $params = http_build_query(array(
                'adtoken' => Tools::getAdminTokenLite('AdminCmsContent'),
                'ad' => basename(_PS_ADMIN_DIR_),
                'id_employee' => (int)$this->context->employee->id
                )
            );
            $preview_url .= (strpos($preview_url, '?') === false ? '?' : '&').$params;
        }

        return $preview_url;
    }
}
