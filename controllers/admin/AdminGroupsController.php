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
 * @property Group $object
 */
class AdminGroupsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'group';
        $this->className = 'Group';
        $this->list_id = 'group';
        $this->lang = true;

        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];

        $groups_to_keep = [
            Configuration::get('PS_UNIDENTIFIED_GROUP'),
            Configuration::get('PS_GUEST_GROUP'),
            Configuration::get('PS_CUSTOMER_GROUP'),
        ];

        $this->fields_list = [
            'id_group' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Group name', [], 'Admin.Shopparameters.Feature'),
                'filter_key' => 'b!name',
            ],
            'reduction' => [
                'title' => $this->trans('Discount (%)', [], 'Admin.Shopparameters.Feature'),
                'align' => 'right',
                'type' => 'percent',
            ],
            'nb' => [
                'title' => $this->trans('Members', [], 'Admin.Shopparameters.Feature'),
                'align' => 'center',
                'havingFilter' => true,
            ],
            'show_prices' => [
                'title' => $this->trans('Show prices', [], 'Admin.Shopparameters.Feature'),
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
            ],
            'date_add' => [
                'title' => $this->trans('Creation date', [], 'Admin.Shopparameters.Feature'),
                'type' => 'date',
                'align' => 'right',
            ],
        ];

        $this->addRowActionSkipList('delete', $groups_to_keep);

        $this->_select .= '(SELECT COUNT(jcg.`id_customer`)
		FROM `' . _DB_PREFIX_ . 'customer_group` jcg
		LEFT JOIN `' . _DB_PREFIX_ . 'customer` jc ON (jc.`id_customer` = jcg.`id_customer`)
		WHERE jc.`deleted` != 1
		' . Shop::addSqlRestriction(Shop::SHARE_CUSTOMER) . '
		AND jcg.`id_group` = a.`id_group`) AS nb';
        $this->_use_found_rows = false;

        $groups = Group::getGroups(Context::getContext()->language->id, true);

        if (Group::isFeatureActive()) {
            $this->fields_options = [
                'general' => [
                    'title' => $this->trans('Default groups options', [], 'Admin.Shopparameters.Feature'),
                    'fields' => [
                        'PS_UNIDENTIFIED_GROUP' => [
                            'title' => $this->trans('Visitors group', [], 'Admin.Shopparameters.Feature'),
                            'desc' => $this->trans('The group defined for your un-identified visitors.', [], 'Admin.Shopparameters.Help'),
                            'cast' => 'intval',
                            'type' => 'select',
                            'list' => $groups,
                            'identifier' => 'id_group',
                        ],
                        'PS_GUEST_GROUP' => [
                            'title' => $this->trans('Guests group', [], 'Admin.Shopparameters.Feature'),
                            'desc' => $this->trans('The group defined for your identified guest customers (used in guest checkout).', [], 'Admin.Shopparameters.Help'),
                            'cast' => 'intval',
                            'type' => 'select',
                            'list' => $groups,
                            'identifier' => 'id_group',
                        ],
                        'PS_CUSTOMER_GROUP' => [
                            'title' => $this->trans('Customers group', [], 'Admin.Shopparameters.Feature'),
                            'desc' => $this->trans('The group defined for your identified registered customers.', [], 'Admin.Shopparameters.Help'),
                            'cast' => 'intval',
                            'type' => 'select',
                            'list' => $groups,
                            'identifier' => 'id_group',
                        ],
                    ],
                    'submit' => [
                        'title' => $this->trans('Save', [], 'Admin.Actions'),
                    ],
                ],
            ];
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin('fancybox');
        $this->addJqueryUi('ui.sortable');
    }

    public function initToolbar()
    {
        if ($this->display == 'add' || $this->display == 'edit') {
            $this->toolbar_btn['save-and-stay'] = [
                'short' => 'SaveAndStay',
                'href' => '#',
                'desc' => $this->trans('Save, then add a category reduction.', [], 'Admin.Shopparameters.Feature'),
                'force_desc' => true,
            ];
        }
        parent::initToolbar();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_group'] = [
                'href' => self::$currentIndex . '&addgroup&token=' . $this->token,
                'desc' => $this->trans('Add new group', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function initProcess()
    {
        $this->id_object = Tools::getValue('id_' . $this->table);

        if (Tools::isSubmit('changeShowPricesVal') && $this->id_object) {
            $this->action = 'change_show_prices_val';
        }

        if (Tools::getIsset('viewgroup')) {
            $this->list_id = 'customer_group';

            if (isset($_POST['submitReset' . $this->list_id])) {
                $this->processResetFilters();
            }

            if (Tools::isSubmit('submitFilter')) {
                self::$currentIndex .= '&id_group=' . (int) Tools::getValue('id_group') . '&viewgroup';
            }
        } else {
            $this->list_id = 'group';
        }

        parent::initProcess();
    }

    public function renderView()
    {
        $this->context = Context::getContext();
        if (!($group = $this->loadObject(true))) {
            return;
        }

        $this->tpl_view_vars = [
            'group' => $group,
            'language' => $this->context->language,
            'customerList' => $this->renderCustomersList($group),
            'categorieReductions' => $this->formatCategoryDiscountList($group->id),
        ];

        return parent::renderView();
    }

    protected function renderCustomersList($group)
    {
        $genders = [0 => '?'];
        $genders_icon = ['default' => 'unknown.gif'];
        foreach (Gender::getGenders() as $gender) {
            /* @var Gender $gender */
            $genders_icon[$gender->id] = '../genders/' . (int) $gender->id . '.jpg';
            $genders[$gender->id] = $gender->name;
        }
        $this->table = 'customer_group';
        $this->lang = false;
        $this->list_id = 'customer_group';
        $this->actions = [];
        $this->addRowAction('edit');
        $this->identifier = 'id_customer';
        $this->bulk_actions = false;
        $this->list_no_link = true;
        $this->explicitSelect = true;

        $this->fields_list = ([
            'id_customer' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'filter_key' => 'c!id_customer',
                'class' => 'fixed-width-xs',
            ],
            'id_gender' => [
                'title' => $this->trans('Social title', [], 'Admin.Global'),
                'icon' => $genders_icon,
                'list' => $genders,
            ],
            'firstname' => [
                'title' => $this->trans('First name', [], 'Admin.Global'),
                'maxlength' => 30,
            ],
            'lastname' => [
                'title' => $this->trans('Last name', [], 'Admin.Global'),
                'maxlength' => 30,
            ],
            'email' => [
                'title' => $this->trans('Email address', [], 'Admin.Global'),
                'filter_key' => 'c!email',
                'orderby' => true,
                'maxlength' => 50,
            ],
            'birthday' => [
                'title' => $this->trans('Date of birth', [], 'Admin.Global'),
                'type' => 'date',
                'class' => 'fixed-width-md',
                'align' => 'center',
            ],
            'date_add' => [
                'title' => $this->trans('Registration date', [], 'Admin.Shopparameters.Feature'),
                'type' => 'date',
                'class' => 'fixed-width-md',
                'align' => 'center',
            ],
            'active' => [
                'title' => $this->trans('Enabled', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'type' => 'bool',
                'search' => false,
                'orderby' => false,
                'filter_key' => 'c!active',
                'callback' => 'printOptinIcon',
            ],
        ]);
        $this->_select = 'c.*, a.id_group';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (a.`id_customer` = c.`id_customer`)';
        $this->_where = 'AND a.`id_group` = ' . (int) $group->id . ' AND c.`deleted` != 1';
        $this->_where .= Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'c');
        self::$currentIndex = self::$currentIndex . '&id_group=' . (int) $group->id . '&viewgroup';

        $this->processFilter();

        return parent::renderList();
    }

    public function printOptinIcon($value, $customer)
    {
        return $value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>';
    }

    public function renderForm()
    {
        if (!($group = $this->loadObject(true))) {
            return;
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Customer group', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-group',
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                    'col' => 4,
                    'hint' => $this->trans('Forbidden characters:', [], 'Admin.Notifications.Info') . ' 0-9!&amp;lt;&amp;gt;,;?=+()@#"�{}_$%:',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Discount', [], 'Admin.Global'),
                    'name' => 'reduction',
                    'suffix' => '%',
                    'col' => 1,
                    'hint' => $this->trans('Automatically apply this value as a discount on all products for members of this customer group.', [], 'Admin.Shopparameters.Help'),
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Price display method', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'price_display_method',
                    'col' => 2,
                    'hint' => $this->trans('How prices are displayed in the order summary for this customer group.', [], 'Admin.Shopparameters.Help'),
                    'options' => [
                        'query' => [
                            [
                                'id_method' => PS_TAX_EXC,
                                'name' => $this->trans('Tax excluded', [], 'Admin.Global'),
                            ],
                            [
                                'id_method' => PS_TAX_INC,
                                'name' => $this->trans('Tax included', [], 'Admin.Global'),
                            ],
                        ],
                        'id' => 'id_method',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Show prices', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'show_prices',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'show_prices_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'show_prices_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', [], 'Admin.Global'),
                        ],
                    ],
                    'hint' => $this->trans('Customers in this group can view prices.', [], 'Admin.Shopparameters.Help'),
                    'desc' => $this->trans('Need to hide prices for all groups? Save time, enable catalog mode in Product Settings instead.', [], 'Admin.Shopparameters.Help'),
                ],
                [
                    'type' => 'group_discount_category',
                    'label' => $this->trans('Category discount', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'reduction',
                    'values' => ($group->id ? $this->formatCategoryDiscountList((int) $group->id) : []),
                ],
                [
                    'type' => 'modules',
                    'label' => $this->trans('Modules authorization', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'auth_modules',
                    'values' => $this->formatModuleListAuth($group->id),
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

        if (Tools::getIsset('addgroup')) {
            $this->fields_value['price_display_method'] = Configuration::get('PRICE_DISPLAY_METHOD');
        }

        $this->fields_value['reduction'] = isset($group->reduction) ? $group->reduction : 0;

        $tree = new HelperTreeCategories('categories-tree');
        $this->tpl_form_vars['categoryTreeView'] = $tree->setRootCategory((int) Category::getRootCategory()->id)->render();

        return parent::renderForm();
    }

    protected function formatCategoryDiscountList($id_group)
    {
        $group_reductions = GroupReduction::getGroupReductions((int) $id_group, $this->context->language->id);
        $category_reductions = [];
        $category_reduction = Tools::getValue('category_reduction');

        foreach ($group_reductions as $category) {
            if (is_array($category_reduction) && array_key_exists($category['id_category'], $category_reduction)) {
                $category['reduction'] = $category_reduction[$category['id_category']];
            }

            $category_reductions[(int) $category['id_category']] = [
                'path' => Tools::getPath(Context::getContext()->link->getAdminLink('AdminCategories'), (int) $category['id_category']),
                'reduction' => (float) $category['reduction'] * 100,
                'id_category' => (int) $category['id_category'],
            ];
        }

        if (is_array($category_reduction)) {
            foreach ($category_reduction as $key => $val) {
                if (!array_key_exists($key, $category_reductions)) {
                    $category_reductions[(int) $key] = [
                        'path' => Tools::getPath(Context::getContext()->link->getAdminLink('AdminCategories'), $key),
                        'reduction' => (float) $val * 100,
                        'id_category' => (int) $key,
                    ];
                }
            }
        }

        return $category_reductions;
    }

    public function formatModuleListAuth($id_group)
    {
        $modules = Module::getModulesInstalled();
        $authorized_modules = '';

        $auth_modules = [];
        $unauth_modules = [];

        $shops = Shop::getContextListShopID();

        if ($id_group) {
            $authorized_modules = Module::getAuthorizedModules($id_group, $shops);
        }

        if (is_array($authorized_modules)) {
            foreach ($modules as $module) {
                $authorized = false;
                foreach ($authorized_modules as $auth_module) {
                    if ($module['id_module'] == $auth_module['id_module']) {
                        $authorized = true;
                    }
                }

                if ($authorized) {
                    $auth_modules[] = $module;
                } else {
                    $unauth_modules[] = $module;
                }
            }
        } else {
            $auth_modules = $modules;
        }
        $auth_modules_tmp = [];
        foreach ($auth_modules as $key => $val) {
            if ($module = Module::getInstanceById($val['id_module'])) {
                $auth_modules_tmp[] = $module;
            }
        }

        $auth_modules = $auth_modules_tmp;

        $unauth_modules_tmp = [];
        foreach ($unauth_modules as $key => $val) {
            if (($tmp_obj = Module::getInstanceById($val['id_module']))) {
                $unauth_modules_tmp[] = $tmp_obj;
            }
        }

        $unauth_modules = $unauth_modules_tmp;

        return ['unauth_modules' => $unauth_modules, 'auth_modules' => $auth_modules];
    }

    public function processSave()
    {
        if (!$this->validateDiscount(Tools::getValue('reduction'))) {
            $this->errors[] = $this->trans('The discount value is incorrect (must be a percentage).', [], 'Admin.Shopparameters.Notification');
        } else {
            $this->updateCategoryReduction();
            $object = parent::processSave();
            $this->updateRestrictions();

            return $object;
        }
    }

    protected function validateDiscount($reduction)
    {
        if (!Validate::isPrice($reduction) || $reduction > 100 || $reduction < 0) {
            return false;
        } else {
            return true;
        }
    }

    public function ajaxProcessAddCategoryReduction()
    {
        $category_reduction = Tools::getValue('category_reduction');
        $id_category = Tools::getValue('id_category'); //no cast validation is done with Validate::isUnsignedId($id_category)

        $result = [];
        if (!Validate::isUnsignedId($id_category)) {
            $result['errors'][] = $this->trans('Wrong category ID.', [], 'Admin.Shopparameters.Notification');
            $result['hasError'] = true;
        } elseif (!$this->validateDiscount($category_reduction)) {
            $result['errors'][] = $this->trans('The discount value is incorrect (must be a percentage).', [], 'Admin.Shopparameters.Notification');
            $result['hasError'] = true;
        } else {
            $result['id_category'] = (int) $id_category;
            $result['catPath'] = Tools::getPath(self::$currentIndex . '?tab=AdminCategories', (int) $id_category);
            $result['discount'] = $category_reduction;
            $result['hasError'] = false;
        }
        die(json_encode($result));
    }

    /**
     * Update (or create) restrictions for modules by group.
     */
    protected function updateRestrictions()
    {
        $id_group = Tools::getValue('id_group');
        $auth_modules = Tools::getValue('modulesBoxAuth');
        $return = true;
        if ($id_group) {
            $shops = Shop::getContextListShopID();
            if (is_array($auth_modules)) {
                $return &= Group::addModulesRestrictions($id_group, $auth_modules, $shops);
            }
        }

        // update module list by hook cache
        Cache::clean(Hook::MODULE_LIST_BY_HOOK_KEY . '*');

        return $return;
    }

    protected function updateCategoryReduction()
    {
        $category_reduction = Tools::getValue('category_reduction');
        Db::getInstance()->execute(
            '
			DELETE FROM `' . _DB_PREFIX_ . 'group_reduction`
			WHERE `id_group` = ' . (int) Tools::getValue('id_group')
        );
        Db::getInstance()->execute(
            '
			DELETE FROM `' . _DB_PREFIX_ . 'product_group_reduction_cache`
			WHERE `id_group` = ' . (int) Tools::getValue('id_group')
        );
        if (is_array($category_reduction) && count($category_reduction)) {
            if (!Configuration::getGlobalValue('PS_GROUP_FEATURE_ACTIVE')) {
                Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', 1);
            }
            foreach ($category_reduction as $cat => $reduction) {
                if (!Validate::isUnsignedId($cat) || !$this->validateDiscount($reduction)) {
                    $this->errors[] = $this->trans('The discount value is incorrect.', [], 'Admin.Shopparameters.Notification');
                } else {
                    $category = new Category((int) $cat);
                    $category->addGroupsIfNoExist((int) Tools::getValue('id_group'));
                    $group_reduction = new GroupReduction();
                    $group_reduction->id_group = (int) Tools::getValue('id_group');
                    $group_reduction->reduction = (float) ($reduction / 100);
                    $group_reduction->id_category = (int) $cat;
                    if (!$group_reduction->save()) {
                        $this->errors[] = $this->trans('You cannot save group reductions.', [], 'Admin.Shopparameters.Notification');
                    }
                }
            }
        }
    }

    /**
     * Toggle show prices flag.
     */
    public function processChangeShowPricesVal()
    {
        $group = new Group($this->id_object);
        if (!Validate::isLoadedObject($group)) {
            $this->errors[] = $this->trans('An error occurred while updating this group.', [], 'Admin.Shopparameters.Notification');
        }
        $update = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'group` SET show_prices = ' . ($group->show_prices ? 0 : 1) . ' WHERE `id_group` = ' . (int) $group->id);
        if (!$update) {
            $this->errors[] = $this->trans('An error occurred while updating this group.', [], 'Admin.Shopparameters.Notification');
        }
        Tools::clearSmartyCache();
        Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
    }

    public function renderList()
    {
        $unidentified = new Group(Configuration::get('PS_UNIDENTIFIED_GROUP'));
        $guest = new Group(Configuration::get('PS_GUEST_GROUP'));
        $default = new Group(Configuration::get('PS_CUSTOMER_GROUP'));

        $unidentified_group_information = $this->trans('%group_name% - All persons without a customer account or customers that are not logged in.', ['%group_name%' => '<b>' . $unidentified->name[$this->context->language->id] . '</b>'], 'Admin.Shopparameters.Help');
        $guest_group_information = $this->trans('%group_name% - All persons who placed an order through Guest Checkout.', ['%group_name%' => '<b>' . $guest->name[$this->context->language->id] . '</b>'], 'Admin.Shopparameters.Help');
        $default_group_information = $this->trans('%group_name% - All persons who created an account on this site.', ['%group_name%' => '<b>' . $default->name[$this->context->language->id] . '</b>'], 'Admin.Shopparameters.Help');

        $this->displayInformation($this->trans('PrestaShop has three default customer groups:', [], 'Admin.Shopparameters.Help'));
        $this->displayInformation($unidentified_group_information);
        $this->displayInformation($guest_group_information);
        $this->displayInformation($default_group_information);

        return parent::renderList();
    }

    public function displayEditLink($token, $id)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');
        if (!array_key_exists('Edit', self::$cache_lang)) {
            self::$cache_lang['Edit'] = $this->trans('Edit', [], 'Admin.Actions');
        }

        $href = self::$currentIndex . '&' . $this->identifier . '=' . $id . '&update' . $this->table . '&token=' . ($token != null ? $token : $this->token);

        if ($this->display == 'view') {
            $href = Context::getContext()->link->getAdminLink('AdminCustomers', true, [], [
                'id_customer' => $id,
                'updatecustomer' => 1,
                'back' => urlencode($href),
            ]);
        }

        $tpl->assign([
            'href' => $href,
            'action' => self::$cache_lang['Edit'],
            'id' => $id,
        ]);

        return $tpl->fetch();
    }
}
