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
class HelperCore
{
    public $currentIndex;
    public $table = 'configuration';
    public $identifier;
    public $token;
    public $toolbar_btn;
    public $ps_help_context;
    public $title;
    public $show_toolbar = true;
    public $context;
    public $toolbar_scroll = false;
    public $bootstrap = false;
    public $className;
    public $name_controller;
    public $shopLink;
    public $allow_employee_form_lang;
    public $multiple_fieldsets;
    public $position_group_identifier;

    /** @var Module|null */
    public $module;

    /** @var string Helper tpl folder */
    public $base_folder;

    /** @var string Controller tpl folder */
    public $override_folder;

    /** @var Smarty_Internal_Template base template object */
    protected $tpl;

    /** @var string base template name */
    public $base_tpl = 'content.tpl';

    public $tpl_vars = [];

    /**
     * @var string
     */
    public $controller_name = '';

    public function __construct()
    {
        $this->context = Context::getContext();
    }

    public function setTpl($tpl)
    {
        $this->tpl = $this->createTemplate($tpl);
    }

    /**
     * Create a template from the override file, else from the base file.
     *
     * @param string $tpl_name filename
     *
     * @return Smarty_Internal_Template
     */
    public function createTemplate($tpl_name)
    {
        if ($this->override_folder) {
            if ($this->context->controller instanceof ModuleAdminController) {
                $override_tpl_path = $this->context->controller->getTemplatePath() . $this->override_folder . $this->base_folder . $tpl_name;
            } elseif ($this->module) {
                $override_tpl_path = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_configure/' . $this->override_folder . $this->base_folder . $tpl_name;
            } else {
                if (file_exists($this->context->smarty->getTemplateDir(1) . $this->override_folder . $this->base_folder . $tpl_name)) {
                    $override_tpl_path = $this->context->smarty->getTemplateDir(1) . $this->override_folder . $this->base_folder . $tpl_name;
                } elseif (file_exists($this->context->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR . $this->override_folder . $this->base_folder . $tpl_name)) {
                    $override_tpl_path = $this->context->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR . $this->override_folder . $this->base_folder . $tpl_name;
                }
            }
        } elseif ($this->module) {
            $override_tpl_path = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_configure/' . $this->base_folder . $tpl_name;
        }

        if (isset($override_tpl_path) && file_exists($override_tpl_path)) {
            return $this->context->smarty->createTemplate($override_tpl_path, $this->context->smarty);
        } else {
            return $this->context->smarty->createTemplate($this->base_folder . $tpl_name, $this->context->smarty);
        }
    }

    /**
     * default behaviour for helper is to return a tpl fetched.
     *
     * @return string
     */
    public function generate()
    {
        $this->tpl->assign($this->tpl_vars);

        return $this->tpl->fetch();
    }

    /**
     * @param array $root array with the name and ID of the tree root category, if null the Shop's root category will be used
     * @param array $selected_cat array of selected categories
     *
     * @usage
     * Format
     * Array( [0] => 1, [1] => 2)
     * OR
     * Array([1] => Array([id_category] => 1, [name] => Home page))
     *
     * @param string $input_name name of input
     * @param bool $use_radio use radio tree or checkbox tree
     * @param bool $use_search display a find category search box
     * @param array $disabled_categories
     *
     * @return string
     */
    public function renderCategoryTree(
        $root = null,
        $selected_cat = [],
        $input_name = 'categoryBox',
        $use_radio = false,
        $use_search = false,
        $disabled_categories = []
    ) {
        $translator = Context::getContext()->getTranslator();

        $translations = [
            'selected' => $translator->trans('Selected', [], 'Admin.Global'),
            'Collapse all' => $translator->trans('Collapse all', [], 'Admin.Actions'),
            'Expand all' => $translator->trans('Expand all', [], 'Admin.Actions'),
            'Check All' => $translator->trans('Check All', [], 'Admin.Actions'),
            'Uncheck All' => $translator->trans('Uncheck All', [], 'Admin.Actions'),
            'search' => $translator->trans('Find a category', [], 'Admin.Actions'),
        ];

        if (Tools::isSubmit('id_shop')) {
            $id_shop = Tools::getValue('id_shop');
        } elseif (Context::getContext()->shop->id) {
            $id_shop = Context::getContext()->shop->id;
        } elseif (!Shop::isFeatureActive()) {
            $id_shop = Configuration::get('PS_SHOP_DEFAULT');
        } else {
            $id_shop = 0;
        }
        $shop = new Shop($id_shop);
        $root_category = Category::getRootCategory(null, $shop);
        $disabled_categories[] = (int) Configuration::get('PS_ROOT_CATEGORY');
        if (!$root) {
            $root = ['name' => $root_category->name, 'id_category' => $root_category->id];
        }

        if (!$use_radio) {
            $input_name = $input_name . '[]';
        }

        if ($use_search) {
            $this->context->controller->addJs(_PS_JS_DIR_ . 'jquery/plugins/autocomplete/jquery.autocomplete.js');
        }

        $html = '
        <script type="text/javascript">
            var inputName = \'' . addcslashes($input_name, '\'') . '\';' . "\n";
        if (count($selected_cat) > 0) {
            if (isset($selected_cat[0])) {
                $html .= '			var selectedCat = "' . implode(',', array_map('intval', $selected_cat)) . '";' . "\n";
            } else {
                $html .= '			var selectedCat = "' . implode(',', array_map('intval', array_keys($selected_cat))) . '";' . "\n";
            }
        } else {
            $html .= '			var selectedCat = \'\';' . "\n";
        }
        $html .= '			var selectedLabel = \'' . $translations['selected'] . '\';
            var home = \'' . addcslashes($root['name'], '\'') . '\';
            var use_radio = ' . (int) $use_radio . ';';
        $html .= '</script>';

        $html .= '
        <div class="category-filter">
            <a class="btn btn-link" href="#" id="collapse_all"><i class="icon-collapse"></i> ' . $translations['Collapse all'] . '</a>
            <a class="btn btn-link" href="#" id="expand_all"><i class="icon-expand"></i> ' . $translations['Expand all'] . '</a>
            ' . (!$use_radio ? '
                <a class="btn btn-link" href="#" id="check_all"><i class="icon-check"></i> ' . $translations['Check All'] . '</a>
                <a class="btn btn-link" href="#" id="uncheck_all"><i class="icon-check-empty"></i> ' . $translations['Uncheck All'] . '</a>' : '')
            . ($use_search ? '
                <div class="row">
                    <label class="control-label col-lg-6" for="search_cat">' . $translations['search'] . ' :</label>
                    <div class="col-lg-6">
                        <input type="text" name="search_cat" id="search_cat"/>
                    </div>
                </div>' : '')
        . '</div>';

        $home_is_selected = false;
        if (is_array($selected_cat)) {
            foreach ($selected_cat as $cat) {
                if (is_array($cat)) {
                    $disabled = in_array($cat['id_category'], $disabled_categories);
                    if ($cat['id_category'] != $root['id_category']) {
                        $html .= '<input ' . ($disabled ? 'disabled="disabled"' : '') . ' type="hidden" name="' . $input_name . '" value="' . $cat['id_category'] . '" >';
                    } else {
                        $home_is_selected = true;
                    }
                } else {
                    $disabled = in_array($cat, $disabled_categories);
                    if ($cat != $root['id_category']) {
                        $html .= '<input ' . ($disabled ? 'disabled="disabled"' : '') . ' type="hidden" name="' . $input_name . '" value="' . $cat . '" >';
                    } else {
                        $home_is_selected = true;
                    }
                }
            }
        }

        $root_input = '';
        if ($root['id_category'] != (int) Configuration::get('PS_ROOT_CATEGORY') || (Tools::isSubmit('ajax') && Tools::getValue('action') == 'getCategoriesFromRootCategory')) {
            $root_input = '
                <p class="checkbox"><i class="icon-folder-open"></i><label>
                    <input type="' . (!$use_radio ? 'checkbox' : 'radio') . '" name="'
                        . $input_name . '" value="' . $root['id_category'] . '" '
                        . ($home_is_selected ? 'checked' : '') . ' onclick="clickOnCategoryBox($(this));" />'
                    . $root['name'] .
                '</label></p>';
        }
        $html .= '
            <div class="container">
                <div class="well">
                    <ul id="categories-treeview">
                        <li id="' . $root['id_category'] . '" class="hasChildren">
                            <span class="folder">' . $root_input . ' </span>
                            <ul>
                                <li><span class="placeholder">&nbsp;</span></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>';

        if ($use_search) {
            $html .= '<script type="text/javascript">searchCategory();</script>';
        }

        return $html;
    }

    /**
     * Render a form with potentials required fields.
     *
     * @param string $class_name
     * @param string $identifier
     * @param array $table_fields
     *
     * @return string
     */
    public function renderRequiredFields($class_name, $identifier, $table_fields)
    {
        $rules = call_user_func_array([$class_name, 'getValidationRules'], [$class_name]);
        $required_class_fields = [$identifier];
        foreach ($rules['required'] as $required) {
            $required_class_fields[] = $required;
        }

        /** @var ObjectModel $object */
        $object = new $class_name();
        $res = $object->getFieldsRequiredDatabase();

        $required_fields = [];
        foreach ($res as $row) {
            $required_fields[(int) $row['id_required_field']] = $row['field_name'];
        }

        $this->tpl_vars = [
            'table_fields' => $table_fields,
            'irow' => 0,
            'required_class_fields' => $required_class_fields,
            'required_fields' => $required_fields,
            'current' => $this->currentIndex,
            'token' => $this->token,
        ];

        $tpl = $this->createTemplate('helpers/required_fields.tpl');
        $tpl->assign($this->tpl_vars);

        return $tpl->fetch();
    }
}
