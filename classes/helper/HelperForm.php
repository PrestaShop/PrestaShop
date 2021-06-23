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
 use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

/**
 * @since 1.5.0
 */
class HelperFormCore extends Helper
{
    public $id;
    public $first_call = true;

    /** @var array of forms fields */
    protected $fields_form = [];

    /** @var array values of form fields */
    public $fields_value = [];
    public $name_controller = '';

    /** @var string if not null, a title will be added on that list */
    public $title = null;

    /** @var string Used to override default 'submitAdd' parameter in form action attribute */
    public $submit_action;

    public $token;
    public $languages = null;
    public $default_form_language = null;
    public $allow_employee_form_lang = null;
    public $show_cancel_button = false;
    public $back_url = '#';

    public function __construct()
    {
        $this->base_folder = 'helpers/form/';
        $this->base_tpl = 'form.tpl';
        parent::__construct();
    }

    public function generateForm($fields_form)
    {
        $this->fields_form = $fields_form;

        return $this->generate();
    }

    public function generate()
    {
        $this->tpl = $this->createTemplate($this->base_tpl);
        if (null === $this->submit_action) {
            $this->submit_action = 'submitAdd' . $this->table;
        }

        $categories = true;
        $color = true;
        $date = true;
        $tinymce = true;
        $textarea_autosize = true;
        $file = true;
        $translator = Context::getContext()->getTranslator();

        $default_switch_labels = [
            'active_on' => $translator->trans('Yes', [], 'Admin.Global'),
            'active_off' => $translator->trans('No', [], 'Admin.Global'),
        ];

        $default_switch_values = [
            [
                'id' => 'active_off',
                'value' => 0,
                'label' => $default_switch_labels['active_off'],
            ],
            [
                'id' => 'active_on',
                'value' => 1,
                'label' => $default_switch_labels['active_on'],
            ],
        ];

        foreach ($this->fields_form as $fieldset_key => &$fieldset) {
            if (isset($fieldset['form']['tabs'])) {
                $tabs[] = $fieldset['form']['tabs'];
            }

            if (isset($fieldset['form']['input'])) {
                foreach ($fieldset['form']['input'] as $key => &$params) {
                    // If the condition is not met, the field will not be displayed
                    if (isset($params['condition']) && !$params['condition']) {
                        unset($this->fields_form[$fieldset_key]['form']['input'][$key]);
                    }
                    switch ($params['type']) {
                        case 'switch':
                            $switch_values = $params['values'];
                            if (!empty($params['values'])) {
                                foreach ($switch_values as $k => $value) {
                                    if (!isset($value['label'])) {
                                        $default_key = (int) $value['value'] ? 1 : 0;
                                        $defautl_label = $default_switch_labels[$value['id']] ?? $default_switch_values[$default_key]['label'];
                                        $this->fields_form[$fieldset_key]['form']['input'][$key]['values'][$k]['label'] = $defautl_label;
                                    }
                                }
                            } else {
                                $this->fields_form[$fieldset_key]['form']['input'][$key]['values'] = $default_switch_values;
                            }
                            break;

                        case 'select':
                            $field_name = (string) $params['name'];
                            // If multiple select check that 'name' field is suffixed with '[]'
                            if (isset($params['multiple']) && $params['multiple'] && stripos($field_name, '[]') === false) {
                                $params['name'] .= '[]';
                            }

                            break;

                        case 'categories':
                            if ($categories) {
                                if (!isset($params['tree']['id'])) {
                                    throw new PrestaShopException('Id must be filled for categories tree');
                                }

                                $tree = new HelperTreeCategories($params['tree']['id'], isset($params['tree']['title']) ? $params['tree']['title'] : null);

                                if (isset($params['name'])) {
                                    $tree->setInputName($params['name']);
                                }

                                if (isset($params['tree']['selected_categories'])) {
                                    $tree->setSelectedCategories($params['tree']['selected_categories']);
                                }

                                if (isset($params['tree']['disabled_categories'])) {
                                    $tree->setDisabledCategories($params['tree']['disabled_categories']);
                                }

                                if (isset($params['tree']['root_category'])) {
                                    $tree->setRootCategory($params['tree']['root_category']);
                                }

                                if (isset($params['tree']['use_search'])) {
                                    $tree->setUseSearch($params['tree']['use_search']);
                                }

                                if (isset($params['tree']['use_checkbox'])) {
                                    $tree->setUseCheckBox($params['tree']['use_checkbox']);
                                }

                                if (isset($params['tree']['set_data'])) {
                                    $tree->setData($params['tree']['set_data']);
                                }

                                $this->context->smarty->assign('categories_tree', $tree->render());
                                $categories = false;
                            }

                            break;

                        case 'file':
                            $uploader = new HelperUploader();
                            $uploader->setId(isset($params['id']) ? $params['id'] : null);
                            $uploader->setName($params['name']);
                            $uploader->setUrl(isset($params['url']) ? $params['url'] : null);
                            $uploader->setMultiple(isset($params['multiple']) ? $params['multiple'] : false);
                            $uploader->setUseAjax(isset($params['ajax']) ? $params['ajax'] : false);
                            $uploader->setMaxFiles(isset($params['max_files']) ? $params['max_files'] : null);

                            if (isset($params['files']) && $params['files']) {
                                $uploader->setFiles($params['files']);
                            } elseif (isset($params['image']) && $params['image']) { // Use for retrocompatibility
                                $uploader->setFiles([
                                    0 => [
                                        'type' => HelperUploader::TYPE_IMAGE,
                                        'image' => isset($params['image']) ? $params['image'] : null,
                                        'size' => isset($params['size']) ? $params['size'] : null,
                                        'delete_url' => isset($params['delete_url']) ? $params['delete_url'] : null,
                                    ],
                                ]);
                            }

                            if (isset($params['file']) && $params['file']) { // Use for retrocompatibility
                                $uploader->setFiles([
                                    0 => [
                                        'type' => HelperUploader::TYPE_FILE,
                                        'size' => isset($params['size']) ? $params['size'] : null,
                                        'delete_url' => isset($params['delete_url']) ? $params['delete_url'] : null,
                                        'download_url' => isset($params['file']) ? $params['file'] : null,
                                    ],
                                ]);
                            }

                            if (isset($params['thumb']) && $params['thumb']) { // Use for retrocompatibility
                                $uploader->setFiles([
                                    0 => [
                                        'type' => HelperUploader::TYPE_IMAGE,
                                        'image' => isset($params['thumb']) ? '<img src="' . $params['thumb'] . '" alt="' . (isset($params['title']) ? $params['title'] : '') . '" title="' . (isset($params['title']) ? $params['title'] : '') . '" />' : null,
                                    ],
                                ]);
                            }

                            $uploader->setTitle(isset($params['title']) ? $params['title'] : null);
                            $params['file'] = $uploader->render();

                            break;

                        case 'color':
                            if ($color) {
                                // Added JS file
                                $this->context->controller->addJqueryPlugin('colorpicker');
                                $this->tpl_vars['color'] = true;
                                $color = false;
                            }

                            break;

                        case 'date':
                            if ($date) {
                                $this->context->controller->addJqueryUI('ui.datepicker');
                                $date = false;
                            }

                            break;

                        case 'textarea':
                            if ($tinymce) {
                                $iso = $this->context->language->iso_code;
                                $this->tpl_vars['iso'] = file_exists(_PS_CORE_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en';
                                $this->tpl_vars['path_css'] = _THEME_CSS_DIR_;
                                $this->tpl_vars['ad'] = __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_);
                                $this->tpl_vars['tinymce'] = true;

                                $this->context->controller->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
                                $this->context->controller->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
                                $tinymce = false;
                            }

                            if ($textarea_autosize) {
                                $this->context->controller->addJqueryPlugin('autosize');
                                $textarea_autosize = false;
                            }

                            break;

                        case 'shop':
                            $disable_shops = isset($params['disable_shared']) ? $params['disable_shared'] : false;
                            $params['html'] = $this->renderAssoShop($disable_shops);
                            if (Shop::getTotalShops(false) == 1) {
                                if ((isset($this->fields_form[$fieldset_key]['form']['force']) && !$this->fields_form[$fieldset_key]['form']['force']) || !isset($this->fields_form[$fieldset_key]['form']['force'])) {
                                    unset($this->fields_form[$fieldset_key]['form']['input'][$key]);
                                }
                            }

                            break;
                    }
                }
            }
        }

        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        $this->tpl->assign([
            'title' => $this->title,
            'toolbar_btn' => $this->toolbar_btn,
            'show_toolbar' => $this->show_toolbar,
            'toolbar_scroll' => $this->toolbar_scroll,
            'submit_action' => $this->submit_action,
            'firstCall' => $this->first_call,
            'current' => $this->currentIndex,
            'token' => $this->token,
            'table' => $this->table,
            'identifier' => $this->identifier,
            'name_controller' => $this->name_controller,
            'languages' => $this->languages,
            'current_id_lang' => $this->context->language->id,
            'defaultFormLanguage' => $this->default_form_language,
            'allowEmployeeFormLang' => $this->allow_employee_form_lang,
            'form_id' => $this->id,
            'tabs' => (isset($tabs)) ? $tabs : null,
            'fields' => $this->fields_form,
            'fields_value' => $this->fields_value,
            'required_fields' => $this->getFieldsRequired(),
            'vat_number' => $moduleManager->isInstalled('vatnumber') && file_exists(_PS_MODULE_DIR_ . 'vatnumber/ajax.php'),
            'module_dir' => _MODULE_DIR_,
            'base_url' => $this->context->shop->getBaseURL(),
            'contains_states' => (isset($this->fields_value['id_country'], $this->fields_value['id_state'])) ? Country::containsStates($this->fields_value['id_country']) : null,
            'dni_required' => (isset($this->fields_value['id_country'], $this->fields_value['dni'])) ? Address::dniRequired($this->fields_value['id_country']) : null,
            'show_cancel_button' => $this->show_cancel_button,
            'back_url' => $this->back_url,
        ]);

        return parent::generate();
    }

    /**
     * Return true if there are required fields.
     */
    public function getFieldsRequired()
    {
        foreach ($this->fields_form as $fieldset) {
            if (isset($fieldset['form']['input'])) {
                foreach ($fieldset['form']['input'] as $input) {
                    if (!empty($input['required']) && $input['type'] != 'radio') {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Render an area to determinate shop association.
     *
     * @return string
     */
    public function renderAssoShop($disable_shared = false, $template_directory = null)
    {
        if (!Shop::isFeatureActive()) {
            return;
        }

        $assos = [];
        if ((int) $this->id) {
            $sql = 'SELECT `id_shop`, `' . bqSQL($this->identifier) . '`
					FROM `' . _DB_PREFIX_ . bqSQL($this->table) . '_shop`
					WHERE `' . bqSQL($this->identifier) . '` = ' . (int) $this->id;

            foreach (Db::getInstance()->executeS($sql) as $row) {
                $assos[$row['id_shop']] = $row['id_shop'];
            }
        } else {
            switch (Shop::getContext()) {
                case Shop::CONTEXT_SHOP:
                        $assos[Shop::getContextShopID()] = Shop::getContextShopID();

                    break;

                case Shop::CONTEXT_GROUP:
                    foreach (Shop::getShops(false, Shop::getContextShopGroupID(), true) as $id_shop) {
                        $assos[$id_shop] = $id_shop;
                    }

                    break;

                default:
                    foreach (Shop::getShops(false, null, true) as $id_shop) {
                        $assos[$id_shop] = $id_shop;
                    }

                    break;
            }
        }

        $tree = new HelperTreeShops('shop-tree', 'Shops');
        if (isset($template_directory)) {
            $tree->setTemplateDirectory($template_directory);
        }
        $tree->setSelectedShops($assos);
        $tree->setAttribute('table', $this->table);

        return $tree->render();
    }
}
