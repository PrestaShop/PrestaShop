<?php
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

/**
 * @property Feature $object
 */
class AdminFeaturesControllerCore extends AdminController
{
    public $bootstrap = true;
    protected $position_identifier = 'id_feature';
    protected $feature_name;

    public function __construct()
    {
        $this->table = 'feature';
        $this->className = 'Feature';
        $this->list_id = 'feature';
        $this->identifier = 'id_feature';
        $this->lang = true;

        parent::__construct();

        $this->fields_list = [
            'id_feature' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Admin.Global'),
                'width' => 'auto',
                'filter_key' => 'b!name',
            ],
            'value' => [
                'title' => $this->trans('Values', [], 'Admin.Global'),
                'orderby' => false,
                'search' => false,
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'position' => [
                'title' => $this->trans('Position', [], 'Admin.Global'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'position' => 'position',
            ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
            ],
        ];
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

    /**
     * Change object type to feature value (use when processing a feature value).
     */
    protected function setTypeValue()
    {
        $this->table = 'feature_value';
        $this->className = 'FeatureValue';
        $this->identifier = 'id_feature_value';
    }

    /**
     * Change object type to feature (use when processing a feature).
     */
    protected function setTypeFeature()
    {
        $this->table = 'feature';
        $this->className = 'Feature';
        $this->identifier = 'id_feature';
    }

    public function renderView()
    {
        if (($id = (int) Tools::getValue('id_feature'))) {
            $this->setTypeValue();
            $this->list_id = 'feature_value';
            $this->lang = true;

            // Action for list
            $this->addRowAction('edit');
            $this->addRowAction('delete');

            if (!Validate::isLoadedObject($obj = new Feature((int) $id))) {
                $this->errors[] = $this->trans('An error occurred while updating the status for an object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b> ' . $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');

                return;
            }

            $this->feature_name = $obj->name;
            $this->toolbar_title = $this->feature_name[$this->context->employee->id_lang];
            $this->fields_list = [
                'id_feature_value' => [
                    'title' => $this->trans('ID', [], 'Admin.Global'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                ],
                'value' => [
                    'title' => $this->trans('Value', [], 'Admin.Global'),
                ],
            ];

            $this->_where = sprintf('AND `id_feature` = %d', (int) $id);
            self::$currentIndex = self::$currentIndex . '&id_feature=' . (int) $id . '&viewfeature';
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
        $this->toolbar_title = $this->trans('Add a new feature', [], 'Admin.Catalog.Feature');
        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Feature', [], 'Admin.Catalog.Feature'),
                'icon' => 'icon-info-sign',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'size' => 33,
                    'hint' => $this->trans('Invalid characters:', [], 'Admin.Notifications.Info') . ' <>;=#{}',
                    'required' => true,
                ],
            ],
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->trans('Shop association', [], 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        $this->fields_form['submit'] = [
            'title' => $this->trans('Save', [], 'Admin.Actions'),
        ];

        return parent::renderForm();
    }

    public function initPageHeaderToolbar()
    {
        if (Feature::isFeatureActive()) {
            if (empty($this->display)) {
                $this->page_header_toolbar_btn['new_feature'] = [
                    'href' => self::$currentIndex . '&addfeature&token=' . $this->token,
                    'desc' => $this->trans('Add new feature', [], 'Admin.Catalog.Feature'),
                    'icon' => 'process-icon-new',
                ];

                $this->page_header_toolbar_btn['new_feature_value'] = [
                    'href' => self::$currentIndex . '&addfeature_value&id_feature=' . (int) Tools::getValue('id_feature') . '&token=' . $this->token,
                    'desc' => $this->trans('Add new feature value', [], 'Admin.Catalog.Help'),
                    'icon' => 'process-icon-new',
                ];
            }
        }

        if ($this->display == 'view') {
            $this->page_header_toolbar_btn['new_feature_value'] = [
                'href' => self::$currentIndex . '&addfeature_value&id_feature=' . (int) Tools::getValue('id_feature') . '&token=' . $this->token,
                'desc' => $this->trans('Add new feature value', [], 'Admin.Catalog.Help'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * AdminController::initToolbar() override.
     *
     * @see AdminController::initToolbar()
     */
    public function initToolbar()
    {
        switch ($this->display) {
            case 'editFeatureValue':
            case 'add':
            case 'edit':
                $this->toolbar_btn['save'] = [
                    'href' => '#',
                    'desc' => $this->trans('Save', [], 'Admin.Actions'),
                ];

                if ($this->display == 'editFeatureValue') {
                    $this->toolbar_btn['save-and-stay'] = [
                        'short' => 'SaveAndStay',
                        'href' => '#',
                        'desc' => $this->trans('Save and add another value', [], 'Admin.Catalog.Help'),
                        'force_desc' => true,
                    ];
                }

                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex . '&token=' . $this->token;
                }

                $this->toolbar_btn['back'] = [
                    'href' => $back,
                    'desc' => $this->trans('Back to the list', [], 'Admin.Catalog.Help'),
                ];

                break;
            case 'view':
                $this->toolbar_btn['newAttributes'] = [
                    'href' => self::$currentIndex . '&addfeature_value&id_feature=' . (int) Tools::getValue('id_feature') . '&token=' . $this->token,
                    'desc' => $this->trans('Add new feature values', [], 'Admin.Catalog.Help'),
                ];
                $this->toolbar_btn['back'] = [
                    'href' => self::$currentIndex . '&token=' . $this->token,
                    'desc' => $this->trans('Back to the list', [], 'Admin.Catalog.Help'),
                ];

                break;
            default:
                parent::initToolbar();
        }
    }

    public function initToolbarTitle()
    {
        $bread_extended = $this->breadcrumbs;

        switch ($this->display) {
            case 'edit':
                $bread_extended[] = $this->trans('Edit New Feature', [], 'Admin.Catalog.Feature');
                $this->addMetaTitle($bread_extended[count($bread_extended) - 1]);

                break;

            case 'add':
                $bread_extended[] = $this->trans('Add New Feature', [], 'Admin.Catalog.Feature');
                $this->addMetaTitle($bread_extended[count($bread_extended) - 1]);

                break;

            case 'view':
                $bread_extended[] = $this->feature_name[$this->context->employee->id_lang] ?? null;
                $this->addMetaTitle($bread_extended[count($bread_extended) - 1]);

                break;

            case 'editFeatureValue':
                if (Tools::getValue('id_feature_value')) {
                    if (($id = (int) Tools::getValue('id_feature'))) {
                        if (Validate::isLoadedObject($obj = new Feature((int) $id))) {
                            $bread_extended[] = '<a href="' . Context::getContext()->link->getAdminLink('AdminFeatures') . '&id_feature=' . $id . '&viewfeature">' . $obj->name[$this->context->employee->id_lang] . '</a>';
                        }

                        if (Validate::isLoadedObject($obj = new FeatureValue((int) Tools::getValue('id_feature_value')))) {
                            $bread_extended[] = $this->trans('Edit: %value%', ['%value%' => $obj->value[$this->context->employee->id_lang]], 'Admin.Catalog.Feature');
                        }
                    } else {
                        $bread_extended[] = $this->trans('Edit Value', [], 'Admin.Catalog.Feature');
                    }
                } else {
                    $bread_extended[] = $this->trans('Add New Value', [], 'Admin.Catalog.Feature');
                }

                if (count($bread_extended) > 0) {
                    $this->addMetaTitle($bread_extended[count($bread_extended) - 1]);
                }

                break;
        }

        $this->toolbar_title = $bread_extended;
    }

    /**
     * AdminController::renderForm() override.
     *
     * @see AdminController::renderForm()
     */
    public function initFormFeatureValue()
    {
        $this->setTypeValue();

        $this->fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->trans('Feature value', [], 'Admin.Catalog.Feature'),
                'icon' => 'icon-info-sign',
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->trans('Feature', [], 'Admin.Catalog.Feature'),
                    'name' => 'id_feature',
                    'options' => [
                        'query' => Feature::getFeatures($this->context->language->id),
                        'id' => 'id_feature',
                        'name' => 'name',
                    ],
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Value', [], 'Admin.Global'),
                    'name' => 'value',
                    'lang' => true,
                    'size' => 33,
                    'hint' => $this->trans('Invalid characters:', [], 'Admin.Notifications.Info') . ' <>;=#{}',
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
            'buttons' => [
                'save-and-stay' => [
                    'title' => $this->trans('Save then add another value', [], 'Admin.Catalog.Feature'),
                    'name' => 'submitAdd' . $this->table . 'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ],
            ],
        ];

        $this->fields_value['id_feature'] = (int) Tools::getValue('id_feature');

        // Create Object FeatureValue
        $feature_value = new FeatureValue(Tools::getValue('id_feature_value'));

        $this->tpl_vars = [
            'feature_value' => $feature_value,
        ];

        $this->getlanguages();
        $helper = new HelperForm();
        $helper->show_cancel_button = true;

        $back = Tools::safeOutput(Tools::getValue('back', ''));
        if (empty($back)) {
            $back = self::$currentIndex . '&token=' . $this->token;
        }
        if (!Validate::isCleanHtml($back)) {
            die(Tools::displayError());
        }

        $helper->back_url = $back;
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;
        $helper->override_folder = 'feature_value/';
        $helper->id = $feature_value->id;
        $helper->toolbar_scroll = false;
        $helper->tpl_vars = $this->tpl_vars;
        $helper->languages = $this->_languages;
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        $helper->fields_value = $this->getFieldsValue($feature_value);
        $helper->toolbar_btn = $this->toolbar_btn;
        $helper->title = $this->trans('Add a new feature value', [], 'Admin.Catalog.Feature');
        $this->content .= $helper->generateForm($this->fields_form);
    }

    /**
     * AdminController::initContent() override.
     *
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        if (Feature::isFeatureActive()) {
            if ($this->display == 'edit' || $this->display == 'add') {
                if (!$this->loadObject(true)) {
                    return;
                }
                $this->content .= $this->renderForm();
            } elseif ($this->display == 'view') {
                // Some controllers use the view action without an object
                if ($this->className) {
                    $this->loadObject(true);
                }
                $this->content .= $this->renderView();
            } elseif ($this->display == 'editFeatureValue') {
                if (!$this->object = new FeatureValue((int) Tools::getValue('id_feature_value'))) {
                    return;
                }
                $this->content .= $this->initFormFeatureValue();
            } elseif ($this->display != 'view' && !$this->ajax) {
                // If a feature value was saved, we need to reset the values to display the list
                $this->setTypeFeature();
                $this->content .= $this->renderList();
                /* reset all attributes filter */
                if (!Tools::getValue('submitFilterfeature_value', 0) && !Tools::getIsset('id_feature_value')) {
                    $this->processResetFilters('feature_value');
                }
            }
        } else {
            $adminPerformanceUrl = $this->context->link->getAdminLink('AdminPerformance');
            $url = '<a href="' . $adminPerformanceUrl . '#featuresDetachables">' . $this->trans('Performance', [], 'Admin.Global') . '</a>';
            $this->displayWarning($this->trans('This feature has been disabled. You can activate it here: %url%.', ['%url%' => $url], 'Admin.Catalog.Notification'));
        }

        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
    }

    public function initProcess()
    {
        // Are we working on feature values?
        if ((Tools::getValue('id_feature_value') && !Tools::getValue('id_feature'))
            || Tools::isSubmit('deletefeature_value')
            || Tools::isSubmit('submitAddfeature_value')
            || Tools::isSubmit('addfeature_value')
            || Tools::isSubmit('updatefeature_value')
            || Tools::isSubmit('submitBulkdeletefeature_value')) {
            $this->setTypeValue();
        }

        if (Tools::getIsset('viewfeature')) {
            $this->list_id = 'feature_value';

            if (isset($_POST['submitReset' . $this->list_id])) {
                $this->processResetFilters();
            }
        } else {
            $this->list_id = 'feature';
            $this->_defaultOrderBy = 'position';
            $this->_defaultOrderWay = 'ASC';
        }

        parent::initProcess();
    }

    public function postProcess()
    {
        if (!Feature::isFeatureActive()) {
            return;
        }

        /* set location with current index */
        if (Tools::getIsset('id_feature') && Tools::getIsset('viewfeature')) {
            self::$currentIndex = self::$currentIndex . '&id_feature=' . Tools::getValue('id_feature', 0) . '&viewfeature';
        }

        if ($this->table == 'feature_value' && ($this->action == 'save' || $this->action == 'delete' || $this->action == 'bulkDelete')) {
            Hook::exec(
                'displayFeatureValuePostProcess',
                ['errors' => &$this->errors]
            );
        } // send errors as reference to allow displayFeatureValuePostProcess to stop saving process
        else {
            Hook::exec(
                'displayFeaturePostProcess',
                ['errors' => &$this->errors]
            );
        } // send errors as reference to allow displayFeaturePostProcess to stop saving process

        parent::postProcess();

        if ($this->table == 'feature_value' && ($this->display == 'edit' || $this->display == 'add')) {
            $this->display = 'editFeatureValue';
        }
    }

    /**
     * Override processAdd to change SaveAndStay button action.
     *
     * @see classes/AdminControllerCore::processAdd()
     */
    public function processAdd()
    {
        $object = parent::processAdd();

        if (Tools::isSubmit('submitAdd' . $this->table . 'AndStay') && !count($this->errors)) {
            if ($this->table == 'feature_value' && ($this->display == 'edit' || $this->display == 'add')) {
                $this->redirect_after = self::$currentIndex . '&addfeature_value&id_feature=' . (int) Tools::getValue('id_feature') . '&conf=3&token=' . $this->token;
            } else {
                $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=&conf=3&update' . $this->table . '&token=' . $this->token;
            }
        } elseif (Tools::isSubmit('submitAdd' . $this->table . 'AndStay') && count($this->errors)) {
            $this->display = 'editFeatureValue';
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
            $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=&conf=3&update' . $this->table . '&token=' . $this->token;
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
        if ($this->table == 'feature') {
            $id_feature = (int) Tools::getValue('id_feature');
            // Adding last position to the feature if not exist
            if ($id_feature <= 0) {
                $sql = 'SELECT `position`+1
						FROM `' . _DB_PREFIX_ . 'feature`
						ORDER BY position DESC';
                // set the position of the new feature in $_POST for postProcess() method
                $_POST['position'] = Db::getInstance()->getValue($sql);
            }
            // clean \n\r characters
            foreach ($_POST as $key => $value) {
                if (preg_match('/^name_/Ui', $key)) {
                    $_POST[$key] = str_replace('\n', '', str_replace('\r', '', $value));
                }
            }
        }

        return parent::processSave();
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
        if ($this->table == 'feature_value') {
            $this->_where .= ' AND (a.custom = 0 OR a.custom IS NULL)';
        }

        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        if ($this->table == 'feature') {
            $nb_items = count($this->_list);
            for ($i = 0; $i < $nb_items; ++$i) {
                $item = &$this->_list[$i];

                $query = new DbQuery();
                $query->select('COUNT(fv.id_feature_value) as count_values');
                $query->from('feature_value', 'fv');
                $query->where('fv.id_feature =' . (int) $item['id_feature']);
                $query->where('(fv.custom=0 OR fv.custom IS NULL)');
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                $item['value'] = (int) $res;
                unset($query);
            }
        }
    }

    public function ajaxProcessUpdatePositions()
    {
        if ($this->access('edit')) {
            $way = (int) Tools::getValue('way');
            $id_feature = (int) Tools::getValue('id');
            $positions = Tools::getValue('feature');

            $new_positions = [];
            foreach ($positions as $v) {
                if (!empty($v)) {
                    $new_positions[] = $v;
                }
            }

            foreach ($new_positions as $position => $value) {
                $pos = explode('_', $value);

                if (isset($pos[2]) && (int) $pos[2] === $id_feature) {
                    if ($feature = new Feature((int) $pos[2])) {
                        if (isset($position) && $feature->updatePosition($way, $position, $id_feature)) {
                            echo 'ok position ' . (int) $position . ' for feature ' . (int) $pos[1] . '\r\n';
                        } else {
                            echo '{"hasError" : true, "errors" : "Can not update feature ' . (int) $id_feature . ' to position ' . (int) $position . ' "}';
                        }
                    } else {
                        echo '{"hasError" : true, "errors" : "This feature (' . (int) $id_feature . ') can t be loaded"}';
                    }

                    break;
                }
            }
        }
    }
}
