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
 * @property WebserviceKey $object
 */
class AdminWebserviceControllerCore extends AdminController
{
    /** this will be filled later */
    public $fields_form = ['webservice form'];
    protected $toolbar_scroll = false;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'webservice_account';
        $this->className = 'WebserviceKey';
        $this->lang = false;
        $this->edit = true;
        $this->delete = true;
        $this->id_lang_default = Configuration::get('PS_LANG_DEFAULT');

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->fields_list = [
            'key' => [
                'title' => $this->trans('Key', [], 'Admin.Advparameters.Feature'),
                'class' => 'fixed-width-md',
                'filter_key' => 'a!key',
            ],
            'description' => [
                'title' => $this->trans('Key description', [], 'Admin.Advparameters.Feature'),
                'align' => 'left',
                'orderby' => false,
            ],
            'active' => [
                'title' => $this->trans('Enabled', [], 'Admin.Global'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'class' => 'fixed-width-xs',
            ],
        ];

        $this->fields_options = [
                'general' => [
                    'title' => $this->trans('Configuration', [], 'Admin.Global'),
                    'fields' => [
                        'PS_WEBSERVICE' => ['title' => $this->trans('Enable PrestaShop\'s webservice', [], 'Admin.Advparameters.Feature'),
                            'desc' => $this->trans('Before activating the webservice, you must be sure to: ', [], 'Admin.Advparameters.Help') .
                                                '<ol>
													<li>' . $this->trans('Check that URL rewriting is available on this server.', [], 'Admin.Advparameters.Help') . '</li>
													<li>' . $this->trans('Check that the five methods GET, POST, PUT, DELETE and HEAD are supported by this server.', [], 'Admin.Advparameters.Help') . '</li>
												</ol>',
                            'cast' => 'intval',
                            'type' => 'bool', ],
                    ],
                    'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
                ],
            ];

        if (!defined('_PS_HOST_MODE_')) {
            $this->fields_options['general']['fields']['PS_WEBSERVICE_CGI_HOST'] = [
                'title' => $this->trans('Enable CGI mode for PHP', [], 'Admin.Advparameters.Feature'),
                'desc' => $this->trans('Before choosing "Yes", check that PHP is not configured as an Apache module on your server.', [], 'Admin.Advparameters.Help'),
                'cast' => 'intval',
                'type' => 'bool',
            ];
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_webservice'] = [
                'href' => self::$currentIndex . '&addwebservice_account&token=' . $this->token,
                'desc' => $this->trans('Add new webservice key', [], 'Admin.Advparameters.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    protected function processUpdateOptions()
    {
        parent::processUpdateOptions();
        Tools::generateHtaccess();
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Webservice Accounts', [], 'Admin.Advparameters.Feature'),
                'icon' => 'icon-lock',
            ],
            'input' => [
                [
                    'type' => 'textbutton',
                    'label' => $this->trans('Key', [], 'Admin.Advparameters.Feature'),
                    'name' => 'key',
                    'id' => 'code',
                    'required' => true,
                    'hint' => $this->trans('Webservice account key.', [], 'Admin.Advparameters.Feature'),
                    'button' => [
                        'label' => $this->trans('Generate!', [], 'Admin.Advparameters.Feature'),
                        'attributes' => [
                            'onclick' => 'gencode(32)',
                        ],
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Key description', [], 'Admin.Advparameters.Feature'),
                    'name' => 'description',
                    'rows' => 3,
                    'cols' => 110,
                    'hint' => $this->trans('Quick description of the key: who it is for, what permissions it has, etc.', [], 'Admin.Advparameters.Help'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Status', [], 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', [], 'Admin.Global'),
                        ],
                    ],
                ],
                [
                    'type' => 'resources',
                    'label' => $this->trans('Permissions', [], 'Admin.Advparameters.Feature'),
                    'name' => 'resources',
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

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $ressources = WebserviceRequest::getResources();
        $permissions = WebserviceKey::getPermissionForAccount($obj->key);

        $this->tpl_form_vars = [
            'ressources' => $ressources,
            'permissions' => $permissions,
        ];

        return parent::renderForm();
    }

    public function initContent()
    {
        if ($this->display != 'add' && $this->display != 'edit') {
            $this->checkForWarning();
        }

        parent::initContent();
    }

    /**
     * Function used to render the options for this controller.
     */
    public function renderOptions()
    {
        if ($this->fields_options && is_array($this->fields_options)) {
            $helper = new HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->toolbar_scroll = true;
            $helper->toolbar_btn = ['save' => [
                                'href' => '#',
                                'desc' => $this->trans('Save', [], 'Admin.Actions'),
                            ]];
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            $options = $helper->generateOptions($this->fields_options);

            return $options;
        }
    }

    public function initProcess()
    {
        parent::initProcess();
        // This is a composite page, we don't want the "options" display mode
        if ($this->display == 'options') {
            $this->display = '';
        }
    }

    public function postProcess()
    {
        if (Tools::getValue('key') && strlen(Tools::getValue('key')) < 32) {
            $this->errors[] = $this->trans('Key length must be 32 character long.', [], 'Admin.Advparameters.Notification');
        }
        if (WebserviceKey::keyExists(Tools::getValue('key')) && !Tools::getValue('id_webservice_account')) {
            $this->errors[] = $this->trans('This key already exists.', [], 'Admin.Advparameters.Notification');
        }

        return parent::postProcess();
    }

    protected function afterAdd($object)
    {
        Tools::generateHtaccess();
        WebserviceKey::setPermissionForAccount($object->id, Tools::getValue('resources', []));
    }

    protected function afterUpdate($object)
    {
        Tools::generateHtaccess();
        WebserviceKey::setPermissionForAccount($object->id, Tools::getValue('resources', []));
    }

    public function checkForWarning()
    {
        if (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') === false) {
            $this->warnings[] = $this->trans('To avoid operating problems, please use an Apache server.', [], 'Admin.Advparameters.Notification');
            if (function_exists('apache_get_modules')) {
                $apache_modules = apache_get_modules();
                if (!in_array('mod_auth_basic', $apache_modules)) {
                    $this->warnings[] = $this->trans('Please activate the \'mod_auth_basic\' Apache module to allow authentication of PrestaShop\'s webservice.', [], 'Admin.Advparameters.Notification');
                }
                if (!in_array('mod_rewrite', $apache_modules)) {
                    $this->warnings[] = $this->trans('Please activate the \'mod_rewrite\' Apache module to allow the PrestaShop webservice.', [], 'Admin.Advparameters.Notification');
                }
            } else {
                $this->warnings[] = $this->trans('We could not check to see if basic authentication and rewrite extensions have been activated. Please manually check if they\'ve been activated in order to use the PrestaShop webservice.', [], 'Admin.Advparameters.Notification');
            }
        }
        if (!extension_loaded('SimpleXML')) {
            $this->warnings[] = $this->trans('Please activate the \'SimpleXML\' PHP extension to allow testing of PrestaShop\'s webservice.', [], 'Admin.Advparameters.Notification');
        }
        if (!configuration::get('PS_SSL_ENABLED')) {
            $this->warnings[] = $this->trans('It is preferable to use SSL (https:) for webservice calls, as it avoids the "man in the middle" type security issues.', [], 'Admin.Advparameters.Notification');
        }

        foreach ($this->_list as $k => $item) {
            if ($item['is_module'] && $item['class_name'] && $item['module_name'] &&
                ($instance = Module::getInstanceByName($item['module_name'])) &&
                !$instance->useNormalPermissionBehaviour()) {
                unset($this->_list[$k]);
            }
        }

        $this->renderList();
    }
}
