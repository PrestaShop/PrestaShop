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

/**
 * @property AttributeGroup $object
 */
class AdminAttributesGroupsControllerCore extends AdminController
{
    public $bootstrap = true;
    protected $id_attribute;
    protected $position_identifier = 'id_attribute_group';
    protected $attribute_name;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'attribute_group';
        $this->list_id = 'attribute_group';
        $this->identifier = 'id_attribute_group';
        $this->className = 'AttributeGroup';
        $this->lang = true;
        $this->_defaultOrderBy = 'position';

        parent::__construct();

        $this->fields_list = array(
            'id_attribute_group' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
                'filter_key' => 'b!name',
                'align' => 'left',
            ),
            'count_values' => array(
                'title' => $this->trans('Values', array(), 'Admin.Catalog.Feature'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false,
                'search' => false,
            ),
            'position' => array(
                'title' => $this->trans('Position', array(), 'Admin.Global'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Notifications.Info'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Info'),
            ),
        );
        $this->fieldImageSettings = array('name' => 'texture', 'dir' => 'co');
    }

    /**
     * AdminController::renderList() override.
     *
     * @see AdminController::renderList()
     */
    public function renderList()
    {
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderView()
    {
        if (($id = Tools::getValue('id_attribute_group'))) {
            $this->table = 'attribute';
            $this->className = 'Attribute';
            $this->identifier = 'id_attribute';
            $this->position_identifier = 'id_attribute';
            $this->position_group_identifier = 'id_attribute_group';
            $this->list_id = 'attribute_values';
            $this->lang = true;

            $this->context->smarty->assign(array(
                'current' => self::$currentIndex . '&id_attribute_group=' . (int) $id . '&viewattribute_group',
            ));

            if (!Validate::isLoadedObject($obj = new AttributeGroup((int) $id))) {
                $this->errors[] = $this->trans('An error occurred while updating the status for an object.', array(), 'Admin.Catalog.Notification') .
                    ' <b>' . $this->table . '</b> ' .
                    $this->trans('(cannot load object)', array(), 'Admin.Catalog.Notification');

                return;
            }

            $this->attribute_name = $obj->name;
            $this->fields_list = array(
                'id_attribute' => array(
                    'title' => $this->trans('ID', array(), 'Admin.Global'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                ),
                'name' => array(
                    'title' => $this->trans('Value', array(), 'Admin.Catalog.Feature'),
                    'width' => 'auto',
                    'filter_key' => 'b!name',
                ),
            );

            if ($obj->group_type == 'color') {
                $this->fields_list['color'] = array(
                    'title' => $this->trans('Color', array(), 'Admin.Catalog.Feature'),
                    'filter_key' => 'a!color',
                );
            }

            $this->fields_list['position'] = array(
                'title' => $this->trans('Position', array(), 'Admin.Global'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'class' => 'fixed-width-md',
            );

            $this->addRowAction('edit');
            $this->addRowAction('delete');

            $this->_where = 'AND a.`id_attribute_group` = ' . (int) $id;
            $this->_orderBy = 'position';

            self::$currentIndex = self::$currentIndex . '&id_attribute_group=' . (int) $id . '&viewattribute_group';
            $this->processFilter();

            return parent::renderList();
        }
    }

    /**
     * AdminController::renderForm() override.
     *
     * @see AdminController::renderForm()
     */
    public function renderForm()
    {
        $this->table = 'attribute_group';
        $this->identifier = 'id_attribute_group';

        $group_type = array(
            array(
                'id' => 'select',
                'name' => $this->trans('Drop-down list', array(), 'Admin.Global'),
            ),
            array(
                'id' => 'radio',
                'name' => $this->trans('Radio buttons', array(), 'Admin.Global'),
            ),
            array(
                'id' => 'color',
                'name' => $this->trans('Color or texture', array(), 'Admin.Catalog.Feature'),
            ),
        );

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Attributes', array(), 'Admin.Catalog.Feature'),
                'icon' => 'icon-info-sign',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Name', array(), 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->trans('Your internal name for this attribute.', array(), 'Admin.Catalog.Help') . '&nbsp;' . $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info') . ' <>;=#{}',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Public name', array(), 'Admin.Catalog.Feature'),
                    'name' => 'public_name',
                    'lang' => true,
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->trans('The public name for this attribute, displayed to the customers.', array(), 'Admin.Catalog.Help') . '&nbsp;' . $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info') . ' <>;=#{}',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Attribute type', array(), 'Admin.Catalog.Feature'),
                    'name' => 'group_type',
                    'required' => true,
                    'options' => array(
                        'query' => $group_type,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'col' => '2',
                    'hint' => $this->trans('The way the attribute\'s values will be presented to the customers in the product\'s page.', array(), 'Admin.Catalog.Help'),
                ),
            ),
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        return parent::renderForm();
    }

    public function renderFormAttributes()
    {
        $attributes_groups = AttributeGroup::getAttributesGroups($this->context->language->id);

        $this->table = 'attribute';
        $this->identifier = 'id_attribute';

        $this->show_form_cancel_button = true;
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Values', array(), 'Admin.Global'),
                'icon' => 'icon-info-sign',
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->trans('Attribute group', array(), 'Admin.Catalog.Feature'),
                    'name' => 'id_attribute_group',
                    'required' => true,
                    'options' => array(
                        'query' => $attributes_groups,
                        'id' => 'id_attribute_group',
                        'name' => 'name',
                    ),
                    'hint' => $this->trans('Choose the attribute group for this value.', array(), 'Admin.Catalog.Help'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Value', array(), 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info') . ' <>;=#{}',
                ),
            ),
        );

        if (Shop::isFeatureActive()) {
            // We get all associated shops for all attribute groups, because we will disable group shops
            // for attributes that the selected attribute group don't support
            $sql = 'SELECT id_attribute_group, id_shop FROM ' . _DB_PREFIX_ . 'attribute_group_shop';
            $associations = array();
            foreach (Db::getInstance()->executeS($sql) as $row) {
                $associations[$row['id_attribute_group']][] = $row['id_shop'];
            }

            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
                'values' => Shop::getTree(),
            );
        } else {
            $associations = array();
        }

        $this->fields_form['shop_associations'] = json_encode($associations);

        $this->fields_form['input'][] = array(
            'type' => 'color',
            'label' => $this->trans('Color', array(), 'Admin.Catalog.Feature'),
            'name' => 'color',
            'hint' => $this->trans('Choose a color with the color picker, or enter an HTML color (e.g. "lightblue", "#CC6600").', array(), 'Admin.Catalog.Help'),
        );

        $this->fields_form['input'][] = array(
            'type' => 'file',
            'label' => $this->trans('Texture', array(), 'Admin.Catalog.Feature'),
            'name' => 'texture',
            'hint' => array(
                $this->trans('Upload an image file containing the color texture from your computer.', array(), 'Admin.Catalog.Help'),
                $this->trans('This will override the HTML color!', array(), 'Admin.Catalog.Help'),
            ),
        );

        $this->fields_form['input'][] = array(
            'type' => 'current_texture',
            'label' => $this->trans('Current texture', array(), 'Admin.Catalog.Feature'),
            'name' => 'current_texture',
        );

        $this->fields_form['input'][] = array(
            'type' => 'closediv',
            'name' => '',
        );

        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        $this->fields_form['buttons'] = array(
            'save-and-stay' => array(
                'title' => $this->trans('Save then add another value', array(), 'Admin.Catalog.Feature'),
                'name' => 'submitAdd' . $this->table . 'AndStay',
                'type' => 'submit',
                'class' => 'btn btn-default pull-right',
                'icon' => 'process-icon-save',
            ),
        );

        $this->fields_value['id_attribute_group'] = (int) Tools::getValue('id_attribute_group');

        // Override var of Controller
        $this->table = 'attribute';
        $this->className = 'Attribute';
        $this->identifier = 'id_attribute';
        $this->lang = true;
        $this->tpl_folder = 'attributes/';

        // Create object Attribute
        if (!$obj = new Attribute((int) Tools::getValue($this->identifier))) {
            return;
        }

        $str_attributes_groups = '';
        foreach ($attributes_groups as $attribute_group) {
            $str_attributes_groups .= '"' . $attribute_group['id_attribute_group'] . '" : ' . ($attribute_group['group_type'] == 'color' ? '1' : '0') . ', ';
        }

        $image = '../img/' . $this->fieldImageSettings['dir'] . '/' . (int) $obj->id . '.jpg';

        $this->tpl_form_vars = array(
            'strAttributesGroups' => $str_attributes_groups,
            'colorAttributeProperties' => Validate::isLoadedObject($obj) && $obj->isColorAttribute(),
            'imageTextureExists' => file_exists(_PS_IMG_DIR_ . $this->fieldImageSettings['dir'] . '/' . (int) $obj->id . '.jpg'),
            'imageTexture' => $image,
            'imageTextureUrl' => Tools::safeOutput($_SERVER['REQUEST_URI']) . '&deleteImage=1',
        );

        return parent::renderForm();
    }

    /**
     * AdminController::init() override.
     *
     * @see AdminController::init()
     */
    public function init()
    {
        if (Tools::isSubmit('updateattribute')) {
            $this->display = 'editAttributes';
        } elseif (Tools::isSubmit('submitAddattribute')) {
            $this->display = 'editAttributes';
        } elseif (Tools::isSubmit('submitAddattribute_group')) {
            $this->display = 'add';
        }

        parent::init();
    }

    /**
     * Override processAdd to change SaveAndStay button action.
     *
     * @see classes/AdminControllerCore::processUpdate()
     */
    public function processAdd()
    {
        if ($this->table == 'attribute') {
            /** @var AttributeGroup $object */
            $object = new $this->className();
            foreach (Language::getLanguages(false) as $language) {
                if ($object->isAttribute((int) Tools::getValue('id_attribute_group'),
                    Tools::getValue('name_' . $language['id_lang']), $language['id_lang'])) {
                    $this->errors['name_' . $language['id_lang']] = $this->trans('The attribute value "%1$s" already exist for %2$s language',
                        array(
                            Tools::getValue('name_' . $language['id_lang']),
                            $language['name'],
                        ),
                        'Admin.Catalog.Notification');
                }
            }

            if (!empty($this->errors)) {
                return $object;
            }
        }

        $object = parent::processAdd();

        if (Tools::isSubmit('submitAdd' . $this->table . 'AndStay') && !count($this->errors)) {
            if ($this->display == 'add') {
                $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=&conf=3&update' . $this->table . '&token=' . $this->token;
            } else {
                $this->redirect_after = self::$currentIndex . '&id_attribute_group=' . (int) Tools::getValue('id_attribute_group') . '&conf=3&update' . $this->table . '&token=' . $this->token;
            }
        }

        if (count($this->errors)) {
            $this->setTypeAttribute();
        }

        return $object;
    }

    /**
     * Override processUpdate to change SaveAndStay button action.
     *
     * @see classes/AdminControllerCore::processUpdate()
     */
    public function processUpdate()
    {
        $object = parent::processUpdate();

        if (Tools::isSubmit('submitAdd' . $this->table . 'AndStay') && !count($this->errors)) {
            if ($this->display == 'add') {
                $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=&conf=3&update' . $this->table . '&token=' . $this->token;
            } else {
                $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=&id_attribute_group=' . (int) Tools::getValue('id_attribute_group') . '&conf=3&update' . $this->table . '&token=' . $this->token;
            }
        }

        if (count($this->errors)) {
            $this->setTypeAttribute();
        }

        if (Tools::isSubmit('updateattribute') || Tools::isSubmit('deleteattribute') || Tools::isSubmit('submitAddattribute') || Tools::isSubmit('submitBulkdeleteattribute')) {
            Tools::clearColorListCache();
        }

        return $object;
    }

    /**
     * AdminController::initContent() override.
     *
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        if (Combination::isFeatureActive()) {
            if ($this->display == 'edit' || $this->display == 'add') {
                if (!($this->object = $this->loadObject(true))) {
                    return;
                }
                $this->content .= $this->renderForm();
            } elseif ($this->display == 'editAttributes') {
                if (!$this->object = new Attribute((int) Tools::getValue('id_attribute'))) {
                    return;
                }

                $this->content .= $this->renderFormAttributes();
            } elseif ($this->display != 'view' && !$this->ajax) {
                $this->content .= $this->renderList();
                $this->content .= $this->renderOptions();
                /* reset all attributes filter */
                if (!Tools::getValue('submitFilterattribute_group', 0) && !Tools::getIsset('id_attribute_group')) {
                    $this->processResetFilters('attribute_values');
                }
            } elseif ($this->display == 'view' && !$this->ajax) {
                $this->content = $this->renderView();
            }
        } else {
            $adminPerformanceUrl = $this->context->link->getAdminLink('AdminPerformance');
            $url = '<a href="' . $adminPerformanceUrl . '#featuresDetachables">' . $this->trans('Performance', array(), 'Admin.Global') . '</a>';
            $this->displayWarning($this->trans('This feature has been disabled. You can activate it here: %link%.', array('%link%' => $url), 'Admin.Catalog.Notification'));
        }

        $this->context->smarty->assign(array(
            'table' => $this->table,
            'current' => self::$currentIndex,
            'token' => $this->token,
            'content' => $this->content,
        ));
    }

    public function initPageHeaderToolbar()
    {
        if (Combination::isFeatureActive()) {
            if (empty($this->display)) {
                $this->page_header_toolbar_btn['new_attribute_group'] = array(
                    'href' => self::$currentIndex . '&addattribute_group&token=' . $this->token,
                    'desc' => $this->trans('Add new attribute', array(), 'Admin.Catalog.Feature'),
                    'icon' => 'process-icon-new',
                );
                $this->page_header_toolbar_btn['new_value'] = array(
                    'href' => self::$currentIndex . '&updateattribute&id_attribute_group=' . (int) Tools::getValue('id_attribute_group') . '&token=' . $this->token,
                    'desc' => $this->trans('Add new value', array(), 'Admin.Catalog.Feature'),
                    'icon' => 'process-icon-new',
                );
            }
        }

        if ($this->display == 'view') {
            $this->page_header_toolbar_btn['new_value'] = array(
                'href' => self::$currentIndex . '&updateattribute&id_attribute_group=' . (int) Tools::getValue('id_attribute_group') . '&token=' . $this->token,
                'desc' => $this->trans('Add new value', array(), 'Admin.Catalog.Feature'),
                'icon' => 'process-icon-new',
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initToolbar()
    {
        switch ($this->display) {
            // @todo defining default buttons
            case 'add':
            case 'edit':
            case 'editAttributes':
                // Default save button - action dynamically handled in javascript
                $this->toolbar_btn['save'] = array(
                    'href' => '#',
                    'desc' => $this->trans('Save', array(), 'Admin.Actions'),
                );

                if ($this->display == 'editAttributes' && !$this->id_attribute) {
                    $this->toolbar_btn['save-and-stay'] = array(
                        'short' => 'SaveAndStay',
                        'href' => '#',
                        'desc' => $this->trans('Save then add another value', array(), 'Admin.Catalog.Help'),
                        'force_desc' => true,
                    );
                }

                $this->toolbar_btn['back'] = array(
                    'href' => $this->context->link->getAdminLink('AdminAttributesGroups'),
                    'desc' => $this->trans('Back to list', array(), 'Admin.Actions'),
                );
                break;
            case 'view':
                $this->toolbar_btn['newAttributes'] = array(
                    'href' => $this->context->link->getAdminLink('AdminAttributesGroups', true, array(), array('updateattribute' => 1, 'id_attribute_group' => (int) Tools::getValue('id_attribute_group'))),
                    'desc' => $this->trans('Add New Values', array(), 'Admin.Catalog.Feature'),
                    'class' => 'toolbar-new',
                );

                $this->toolbar_btn['back'] = array(
                    'href' => $this->context->link->getAdminLink('AdminAttributesGroups'),
                    'desc' => $this->trans('Back to list', array(), 'Admin.Actions'),
                );
                break;
            default: // list
                $this->toolbar_btn['new'] = array(
                    'href' => $this->context->link->getAdminLink('AdminAttributesGroups', true, array(), array('add' . $this->table => 1)),
                    'desc' => $this->trans('Add New Attributes', array(), 'Admin.Catalog.Feature'),
                );
                if ($this->can_import) {
                    $this->toolbar_btn['import'] = array(
                        'href' => $this->context->link->getAdminLink('AdminImport', true, array(), array('import_type' => 'combinations')),
                        'desc' => $this->trans('Import', array(), 'Admin.Actions'),
                    );
                }
        }
    }

    public function initToolbarTitle()
    {
        $bread_extended = $this->breadcrumbs;

        switch ($this->display) {
            case 'edit':
                $bread_extended[] = $this->trans('Edit New Attribute', array(), 'Admin.Catalog.Feature');
                break;

            case 'add':
                $bread_extended[] = $this->trans('Add New Attribute', array(), 'Admin.Catalog.Feature');
                break;

            case 'view':
                if (Tools::getIsset('viewattribute_group')) {
                    if (($id = Tools::getValue('id_attribute_group'))) {
                        if (Validate::isLoadedObject($obj = new AttributeGroup((int) $id))) {
                            $bread_extended[] = $obj->name[$this->context->employee->id_lang];
                        }
                    }
                } else {
                    $bread_extended[] = $this->attribute_name[$this->context->employee->id_lang];
                }
                break;

            case 'editAttributes':
                if ($this->id_attribute) {
                    if (($id = Tools::getValue('id_attribute_group'))) {
                        if (Validate::isLoadedObject($obj = new AttributeGroup((int) $id))) {
                            $bread_extended[] = '<a href="' . Context::getContext()->link->getAdminLink('AdminAttributesGroups') . '&id_attribute_group=' . $id . '&viewattribute_group">' . $obj->name[$this->context->employee->id_lang] . '</a>';
                        }
                        if (Validate::isLoadedObject($obj = new Attribute((int) $this->id_attribute))) {
                            $bread_extended[] = $this->trans(
                                'Edit: %value%',
                                array(
                                    '%value%' => $obj->name[$this->context->employee->id_lang],
                                    ),
                                'Admin.Catalog.Feature'
                            );
                        }
                    } else {
                        $bread_extended[] = $this->trans('Edit Value', array(), 'Admin.Catalog.Feature');
                    }
                } else {
                    $bread_extended[] = $this->trans('Add New Value', array(), 'Admin.Catalog.Feature');
                }
                break;
        }

        if (count($bread_extended) > 0) {
            $this->addMetaTitle($bread_extended[count($bread_extended) - 1]);
        }

        $this->toolbar_title = $bread_extended;
    }

    public function initProcess()
    {
        $this->setTypeAttribute();

        if (Tools::getIsset('viewattribute_group')) {
            $this->list_id = 'attribute_values';

            if (isset($_POST['submitReset' . $this->list_id])) {
                $this->processResetFilters();
            }
        } else {
            $this->list_id = 'attribute_group';
        }

        parent::initProcess();

        if ($this->table == 'attribute') {
            $this->display = 'editAttributes';
            $this->id_attribute = (int) Tools::getValue('id_attribute');
        }
    }

    protected function setTypeAttribute()
    {
        if (Tools::isSubmit('updateattribute') || Tools::isSubmit('deleteattribute') || Tools::isSubmit('submitAddattribute') || Tools::isSubmit('submitBulkdeleteattribute')) {
            $this->table = 'attribute';
            $this->className = 'Attribute';
            $this->identifier = 'id_attribute';

            if ($this->display == 'edit') {
                $this->display = 'editAttributes';
            }
        }
    }

    public function processPosition()
    {
        if (Tools::getIsset('viewattribute_group')) {
            $object = new Attribute((int) Tools::getValue('id_attribute'));
            self::$currentIndex = self::$currentIndex . '&viewattribute_group';
        } else {
            $object = new AttributeGroup((int) Tools::getValue('id_attribute_group'));
        }

        if (!Validate::isLoadedObject($object)) {
            $this->errors[] = $this->trans('An error occurred while updating the status for an object.', array(), 'Admin.Notifications.Error') .
                ' <b>' . $this->table . '</b> ' . $this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
        } elseif (!$object->updatePosition((int) Tools::getValue('way'), (int) Tools::getValue('position'))) {
            $this->errors[] = $this->trans('Failed to update the position.', array(), 'Admin.Notifications.Error');
        } else {
            $id_identifier_str = ($id_identifier = (int) Tools::getValue($this->identifier)) ? '&' . $this->identifier . '=' . $id_identifier : '';
            $redirect = self::$currentIndex . '&' . $this->table . 'Orderby=position&' . $this->table . 'Orderway=asc&conf=5' . $id_identifier_str . '&token=' . $this->token;
            $this->redirect_after = $redirect;
        }

        return $object;
    }

    /**
     * Call the right method for creating or updating object.
     *
     * @return mixed
     */
    public function processSave()
    {
        if ($this->display == 'add' || $this->display == 'edit') {
            $this->identifier = 'id_attribute_group';
        }

        if (!$this->id_object) {
            return $this->processAdd();
        } else {
            return $this->processUpdate();
        }
    }

    public function postProcess()
    {
        if (!Combination::isFeatureActive()) {
            return;
        }

        if (!Tools::getValue($this->identifier) && Tools::getValue('id_attribute') && !Tools::getValue('attributeOrderby')) {
            // Override var of Controller
            $this->table = 'attribute';
            $this->className = 'Attribute';
            $this->identifier = 'id_attribute';
        }

        /* set location with current index */
        if (Tools::getIsset('id_attribute_group') && Tools::getIsset('viewattribute_group')) {
            self::$currentIndex = self::$currentIndex . '&id_attribute_group=' . Tools::getValue('id_attribute_group', 0) . '&viewattribute_group';
        }

        // If it's an attribute, load object Attribute()
        if (Tools::getValue('updateattribute') || Tools::isSubmit('deleteattribute') || Tools::isSubmit('submitAddattribute')) {
            if (true !== $this->access('edit')) {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');

                return;
            } elseif (!$object = new Attribute((int) Tools::getValue($this->identifier))) {
                $this->errors[] = $this->trans('An error occurred while updating the status for an object.', array(), 'Admin.Notifications.Error') .
                    ' <b>' . $this->table . '</b> ' .
                    $this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');

                return;
            }

            if (Tools::getValue('position') !== false && Tools::getValue('id_attribute')) {
                $_POST['id_attribute_group'] = $object->id_attribute_group;
                if (!$object->updatePosition((int) Tools::getValue('way'), (int) Tools::getValue('position'))) {
                    $this->errors[] = $this->trans('Failed to update the position.', array(), 'Admin.Notifications.Error');
                } else {
                    Tools::redirectAdmin(self::$currentIndex . '&conf=5&token=' . Tools::getAdminTokenLite('AdminAttributesGroups') . '#details_details_' . $object->id_attribute_group);
                }
            } elseif (Tools::isSubmit('deleteattribute') && Tools::getValue('id_attribute')) {
                if (!$object->delete()) {
                    $this->errors[] = $this->trans('Failed to delete the attribute.', array(), 'Admin.Catalog.Notification');
                } else {
                    Tools::redirectAdmin(self::$currentIndex . '&conf=1&token=' . Tools::getAdminTokenLite('AdminAttributesGroups'));
                }
            } elseif (Tools::isSubmit('submitAddattribute')) {
                Hook::exec('actionObjectAttributeAddBefore');
                $this->action = 'save';
                $id_attribute = (int) Tools::getValue('id_attribute');
                // Adding last position to the attribute if not exist
                if ($id_attribute <= 0) {
                    $sql = 'SELECT `position`+1
							FROM `' . _DB_PREFIX_ . 'attribute`
							WHERE `id_attribute_group` = ' . (int) Tools::getValue('id_attribute_group') . '
							ORDER BY position DESC';
                    // set the position of the new group attribute in $_POST for postProcess() method
                    $_POST['position'] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                }
                $_POST['id_parent'] = 0;
                $this->processSave($this->token);
            }

            if (Tools::getValue('id_attribute') && Tools::isSubmit('submitAddattribute') && Tools::getValue('color') && !Tools::getValue('filename')) {
                if (file_exists(_PS_IMG_DIR_ . $this->fieldImageSettings['dir'] . '/' . (int) Tools::getValue('id_attribute') . '.jpg')) {
                    unlink(_PS_IMG_DIR_ . $this->fieldImageSettings['dir'] . '/' . (int) Tools::getValue('id_attribute') . '.jpg');
                }
            }
        } else {
            if (Tools::isSubmit('submitBulkdelete' . $this->table)) {
                if ($this->access('delete')) {
                    if (isset($_POST[$this->list_id . 'Box'])) {
                        /** @var AttributeGroup $object */
                        $object = new $this->className();
                        if ($object->deleteSelection($_POST[$this->list_id . 'Box'])) {
                            AttributeGroup::cleanPositions();
                            Tools::redirectAdmin(self::$currentIndex . '&conf=2' . '&token=' . $this->token);
                        }
                        $this->errors[] = $this->trans('An error occurred while deleting this selection.', array(), 'Admin.Notifications.Error');
                    } else {
                        $this->errors[] = $this->trans('You must select at least one element to delete.', array(), 'Admin.Notifications.Error');
                    }
                } else {
                    $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
                }
                // clean position after delete
                AttributeGroup::cleanPositions();
            } elseif (Tools::isSubmit('submitAdd' . $this->table)) {
                Hook::exec('actionObjectAttributeGroupAddBefore');
                $id_attribute_group = (int) Tools::getValue('id_attribute_group');
                // Adding last position to the attribute if not exist
                if ($id_attribute_group <= 0) {
                    $sql = 'SELECT `position`+1
							FROM `' . _DB_PREFIX_ . 'attribute_group`
							ORDER BY position DESC';
                    // set the position of the new group attribute in $_POST for postProcess() method
                    $_POST['position'] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                }
                // clean \n\r characters
                foreach ($_POST as $key => $value) {
                    if (preg_match('/^name_/Ui', $key)) {
                        $_POST[$key] = str_replace('\n', '', str_replace('\r', '', $value));
                    }
                }
                parent::postProcess();
            } else {
                parent::postProcess();
                if (Tools::isSubmit('delete' . $this->table)) {
                    AttributeGroup::cleanPositions();
                }
            }
        }
    }

    /**
     * AdminController::getList() override.
     *
     * @see AdminController::getList()
     *
     * @param int $id_lang
     * @param string|null $order_by
     * @param string|null $order_way
     * @param int $start
     * @param int|null $limit
     * @param int|bool $id_lang_shop
     *
     * @throws PrestaShopException
     */
    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        if ($this->display == 'view') {
            foreach ($this->_list as &$list) {
                if (file_exists(_PS_IMG_DIR_ . $this->fieldImageSettings['dir'] . '/' . (int) $list['id_attribute'] . '.jpg')) {
                    if (!isset($list['color']) || !is_array($list['color'])) {
                        $list['color'] = array();
                    }
                    $list['color']['texture'] = '../img/' . $this->fieldImageSettings['dir'] . '/' . (int) $list['id_attribute'] . '.jpg';
                }
            }
        } else {
            $nb_items = count($this->_list);
            for ($i = 0; $i < $nb_items; ++$i) {
                $item = &$this->_list[$i];

                $query = new DbQuery();
                $query->select('COUNT(a.id_attribute) as count_values');
                $query->from('attribute', 'a');
                $query->join(Shop::addSqlAssociation('attribute', 'a'));
                $query->where('a.id_attribute_group =' . (int) $item['id_attribute_group']);
                $query->groupBy('attribute_shop.id_shop');
                $query->orderBy('count_values DESC');
                $item['count_values'] = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                unset($query);
            }
        }
    }

    /**
     * Overrides parent to delete items from sublist.
     *
     * @return mixed
     */
    public function processBulkDelete()
    {
        // If we are deleting attributes instead of attribute_groups
        if (Tools::getIsset('attributeBox')) {
            $this->className = 'Attribute';
            $this->table = 'attribute';
            $this->boxes = Tools::getValue($this->table . 'Box');
        }

        $result = parent::processBulkDelete();
        // Restore vars
        $this->className = 'AttributeGroup';
        $this->table = 'attribute_group';

        return $result;
    }

    /* Modify group attribute position */
    public function ajaxProcessUpdateGroupsPositions()
    {
        $way = (int) Tools::getValue('way');
        $id_attribute_group = (int) Tools::getValue('id_attribute_group');
        $positions = Tools::getValue('attribute_group');

        $new_positions = array();
        foreach ($positions as $v) {
            if (count(explode('_', $v)) == 4) {
                $new_positions[] = $v;
            }
        }

        foreach ($new_positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $id_attribute_group) {
                if ($group_attribute = new AttributeGroup((int) $pos[2])) {
                    if (isset($position) && $group_attribute->updatePosition($way, $position)) {
                        echo 'ok position ' . (int) $position . ' for attribute group ' . (int) $pos[2] . '\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update the ' . (int) $id_attribute_group . ' attribute group to position ' . (int) $position . ' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "The (' . (int) $id_attribute_group . ') attribute group cannot be loaded."}';
                }

                break;
            }
        }
    }

    /* Modify attribute position */
    public function ajaxProcessUpdateAttributesPositions()
    {
        $way = (int) Tools::getValue('way');
        $id_attribute = (int) Tools::getValue('id_attribute');
        $id_attribute_group = (int) Tools::getValue('id_attribute_group');
        $positions = Tools::getValue('attribute');

        if (is_array($positions)) {
            foreach ($positions as $position => $value) {
                $pos = explode('_', $value);

                if ((isset($pos[1]) && isset($pos[2])) && (int) $pos[2] === $id_attribute) {
                    if ($attribute = new Attribute((int) $pos[2])) {
                        if (isset($position) && $attribute->updatePosition($way, $position)) {
                            echo 'ok position ' . (int) $position . ' for attribute ' . (int) $pos[2] . '\r\n';
                        } else {
                            echo '{"hasError" : true, "errors" : "Can not update the ' . (int) $id_attribute . ' attribute to position ' . (int) $position . ' "}';
                        }
                    } else {
                        echo '{"hasError" : true, "errors" : "The (' . (int) $id_attribute . ') attribute cannot be loaded"}';
                    }

                    break;
                }
            }
        }
    }
}
