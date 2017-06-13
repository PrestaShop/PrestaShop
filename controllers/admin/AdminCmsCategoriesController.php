<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property CMSCategory $object
 */
class AdminCmsCategoriesControllerCore extends AdminController
{
    /** @var object CMSCategory() instance for navigation*/
    protected $cms_category;

    protected $position_identifier = 'id_cms_category_to_move';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->is_cms = true;
        $this->table = 'cms_category';
        $this->list_id = 'cms_category';
        $this->className = 'CMSCategory';
        $this->lang = true;
        $this->addRowAction('view');
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
        $this->tpl_list_vars['icon'] = 'icon-folder-close';
        $this->tpl_list_vars['title'] = $this->trans('Categories', array(), 'Admin.Catalog.Feature');
        $this->fields_list = array(
        'id_cms_category' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'),
        'name' => array('title' => $this->trans('Name', array(), 'Admin.Global'), 'width' => 'auto', 'callback' => 'hideCMSCategoryPosition', 'callback_object' => 'CMSCategory'),
        'description' => array('title' => $this->trans('Description', array(), 'Admin.Global'), 'maxlength' => 90, 'orderby' => false),
        'position' => array('title' => $this->trans('Position', array(), 'Admin.Global'),'filter_key' => 'position', 'align' => 'center', 'class' => 'fixed-width-sm', 'position' => 'position'),
        'active' => array(
            'title' => $this->trans('Displayed', array(), 'Admin.Global'), 'class' => 'fixed-width-sm', 'active' => 'status',
            'align' => 'center','type' => 'bool', 'orderby' => false
        ));

        // The controller can't be call directly
        // In this case, AdminCmsContentController::getCurrentCMSCategory() is null
        if (!AdminCmsContentController::getCurrentCMSCategory()) {
            $this->redirect_after = '?controller=AdminCmsContent&token='.Tools::getAdminTokenLite('AdminCmsContent');
            $this->redirect();
        }

        $this->cms_category = AdminCmsContentController::getCurrentCMSCategory();
        $this->_where = ' AND `id_parent` = '.(int)$this->cms_category->id;
        $this->_select = 'position ';
    }

    public function getTabSlug()
    {
        return 'ROLE_MOD_TAB_ADMINCMSCONTENT_';
    }

    public function renderList()
    {
        $this->initToolbar();
        $this->_group = 'GROUP BY a.`id_cms_category`';
        if (isset($this->toolbar_btn['new'])) {
            $this->toolbar_btn['new']['href'] .= '&id_parent='.(int)Tools::getValue('id_cms_category');
        }
        return parent::renderList();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $this->action = 'save';
            if ($id_cms_category = (int)Tools::getValue('id_cms_category')) {
                $this->id_object = $id_cms_category;
                if (!CMSCategory::checkBeforeMove($id_cms_category, (int)Tools::getValue('id_parent'))) {
                    $this->errors[] = $this->trans('The page Category cannot be moved here.', array(), 'Admin.Design.Notification');
                    return false;
                }
            }
            $object = parent::postProcess();
            $this->updateAssoShop((int)Tools::getValue('id_cms_category'));
            if ($object !== false) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&id_cms_category='.(int)$object->id.'&token='.Tools::getValue('token'));
            }
            return $object;
        }
        /* Change object statuts (active, inactive) */
        elseif (Tools::isSubmit('statuscms_category') && Tools::getValue($this->identifier)) {
            if ($this->access('edit')) {
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    if ($object->toggleStatus()) {
                        $identifier = ((int)$object->id_parent ? '&id_cms_category='.(int)$object->id_parent : '');
                        Tools::redirectAdmin(self::$currentIndex.'&conf=5'.$identifier.'&token='.Tools::getValue('token'));
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
        /* Delete object */
        elseif (Tools::isSubmit('delete'.$this->table)) {
            if ($this->access('delete')) {
                if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings)) {
                    // check if request at least one object with noZeroObject
                    if (isset($object->noZeroObject) && count($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1) {
                        $this->errors[] = $this->trans('You need at least one object.', array(), 'Admin.Notifications.Error')
                            .' <b>'.$this->table.'</b><br />'.$this->trans('You cannot delete all of the items.', array(), 'Admin.Notifications.Error');
                    } else {
                        $identifier = ((int)$object->id_parent ? '&'.$this->identifier.'='.(int)$object->id_parent : '');
                        if ($this->deleted) {
                            $object->deleted = 1;
                            if ($object->update()) {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.Tools::getValue('token').$identifier);
                            }
                        } elseif ($object->delete()) {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.Tools::getValue('token').$identifier);
                        }
                        $this->errors[] = $this->trans('An error occurred during deletion.', array(), 'Admin.Notifications.Error');
                    }
                } else {
                    $this->errors[] = $this->trans('An error occurred while deleting the object.', array(), 'Admin.Notifications.Error')
                        .' <b>'.$this->table.'</b> '.$this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('position')) {
            $object = new CMSCategory((int)Tools::getValue($this->identifier, Tools::getValue('id_cms_category_to_move', 1)));
            if (!$this->access('edit')) {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            } elseif (!Validate::isLoadedObject($object)) {
                $this->errors[] = $this->trans('An error occurred while updating the status for an object.', array(), 'Admin.Notifications.Error')
                    .' <b>'.$this->table.'</b> '.$this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
            } elseif (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position'))) {
                $this->errors[] = $this->trans('Failed to update the position.', array(), 'Admin.Notifications.Error');
            } else {
                $identifier = ((int)$object->id_parent ? '&'.$this->identifier.'='.(int)$object->id_parent : '');
                $token = Tools::getAdminTokenLite('AdminCmsContent');
                Tools::redirectAdmin(
                    self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.$identifier.'&token='.$token
                );
            }
        }
        /* Delete multiple objects */
        elseif (Tools::getValue('submitDel'.$this->table) || Tools::getValue('submitBulkdelete'.$this->table)) {
            if ($this->access('delete')) {
                if (Tools::isSubmit($this->table.'Box')) {
                    $cms_category = new CMSCategory();
                    $result = true;
                    $result = $cms_category->deleteSelection(Tools::getValue($this->table.'Box'));
                    if ($result) {
                        $cms_category->cleanPositions((int)Tools::getValue('id_cms_category'));
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
        }
        parent::postProcess();
    }

    public function renderForm()
    {
        $this->display = 'edit';
        $this->initToolbar();
        if (!$this->loadObject(true)) {
            return;
        }

        $categories = CMSCategory::getCategories($this->context->language->id, false);
        $html_categories = CMSCategory::recurseCMSCategory($categories, $categories[0][1], 1, $this->getFieldValue($this->object, 'id_parent'), 1);

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('CMS Category', array(), 'Admin.Design.Feature'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Name', array(), 'Admin.Global'),
                    'name' => 'name',
                    'class' => 'copyMeta2friendlyURL',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
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
                // custom template
                array(
                    'type' => 'select_category',
                    'label' => $this->trans('Parent category', array(), 'Admin.Design.Feature'),
                    'name' => 'id_parent',
                    'options' => array(
                        'html' => $html_categories,
                    ),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Description', array(), 'Admin.Global'),
                    'name' => 'description',
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Meta title', array(), 'Admin.Global'),
                    'name' => 'meta_title',
                    'lang' => true,
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
                    'type' => 'text',
                    'label' => $this->trans('Meta keywords', array(), 'Admin.Global'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Friendly URL', array(), 'Admin.Global'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->trans('Only letters and the minus (-) character are allowed.', array(), 'Admin.Catalog.Help')
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
        return parent::renderForm();
    }
}
