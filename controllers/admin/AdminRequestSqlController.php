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
 * @property RequestSql $object
 */
class AdminRequestSqlControllerCore extends AdminController
{
    /**
     * @var array : List of encoding type for a file
     */
    public static $encoding_file = [
        ['value' => 1, 'name' => 'utf-8'],
        ['value' => 2, 'name' => 'iso-8859-1'],
    ];

    /**
     * @deprecated since 1.7.6, to be removed in the next minor
     */
    public function __construct()
    {
        @trigger_error(
            'The AdminRequestSqlController is deprecated and will be removed in the next minor',
            E_USER_DEPRECATED
        );

        $this->bootstrap = true;
        $this->table = 'request_sql';
        $this->className = 'RequestSql';
        $this->lang = false;

        parent::__construct();

        $this->fields_list = [
            'id_request_sql' => ['title' => $this->trans('ID', [], 'Admin.Global'), 'class' => 'fixed-width-xs'],
            'name' => ['title' => $this->trans('SQL query name', [], 'Admin.Advparameters.Feature')],
            'sql' => [
                'title' => $this->trans('SQL query', [], 'Admin.Advparameters.Feature'),
                'filter_key' => 'a!sql',
            ],
        ];

        $this->fields_options = [
            'general' => [
                'title' => $this->trans('Settings', [], 'Admin.Global'),
                'fields' => [
                    'PS_ENCODING_FILE_MANAGER_SQL' => [
                        'title' => $this->trans('Select your default file encoding', [], 'Admin.Advparameters.Feature'),
                        'cast' => 'intval',
                        'type' => 'select',
                        'identifier' => 'value',
                        'list' => self::$encoding_file,
                        'visibility' => Shop::CONTEXT_ALL,
                    ],
                ],
                'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
            ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];
    }

    public function renderOptions()
    {
        // Set toolbar options
        $this->display = 'options';
        $this->show_toolbar = true;
        $this->toolbar_scroll = true;
        $this->initToolbar();

        return parent::renderOptions();
    }

    public function initToolbar()
    {
        if ($this->display == 'view' && $id_request = Tools::getValue('id_request_sql')) {
            $this->toolbar_btn['edit'] = [
                'href' => self::$currentIndex . '&amp;updaterequest_sql&amp;token=' . $this->token . '&amp;id_request_sql=' . (int) $id_request,
                'desc' => $this->trans('Edit this SQL query', [], 'Admin.Advparameters.Feature'),
            ];
        }

        parent::initToolbar();

        if ($this->display == 'options') {
            unset($this->toolbar_btn['new']);
        }
    }

    public function renderList()
    {
        // Set toolbar options
        $this->display = null;
        $this->initToolbar();

        $this->displayWarning($this->trans('When saving the query, only the "SELECT" SQL statement is allowed.', [], 'Admin.Advparameters.Notification'));
        $this->displayInformation('
		<strong>' . $this->trans('How do I create a new SQL query?', [], 'Admin.Advparameters.Help') . '</strong><br />
		<ul>
			<li>' . $this->trans('Click "%add_new_label%".', ['%add_new_label%' => $this->trans('Add new SQL query', [], 'Admin.Advparameters.Feature')], 'Admin.Advparameters.Help') . '</li>
			<li>' . $this->trans('Fill in the fields and click "%save_label%".', ['%save_label%' => $this->trans('Save', [], 'Admin.Actions')], 'Admin.Advparameters.Help') . '</li>
			<li>' . $this->trans('You can then view the query results by clicking on the "%view_label%" action in the dropdown menu', ['%view_label%' => $this->trans('View', [], 'Admin.Global')], 'Admin.Advparameters.Help') . ' <i class="icon-pencil"></i></li>
			<li>' . $this->trans('You can also export the query results as a CSV file by clicking on the "%export_label%" button', ['%export_label%' => $this->trans('Export', [], 'Admin.Actions')], 'Admin.Advparameters.Help') . ' <i class="icon-cloud-upload"></i></li>
		</ul>');

        $this->addRowAction('export');
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('SQL query', [], 'Admin.Advparameters.Feature'),
                'icon' => 'icon-cog',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('SQL query name', [], 'Admin.Advparameters.Feature'),
                    'name' => 'name',
                    'size' => 103,
                    'required' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('SQL query', [], 'Admin.Advparameters.Feature'),
                    'name' => 'sql',
                    'cols' => 100,
                    'rows' => 10,
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];

        $request = new RequestSql();
        $this->tpl_form_vars = ['tables' => $request->getTables()];

        return parent::renderForm();
    }

    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

            return;
        }

        return parent::postProcess();
    }

    /**
     * method call when ajax request is made with the details row action.
     *
     * @see AdminController::postProcess()
     */
    public function ajaxProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            die($this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error'));
        }
        if ($table = Tools::getValue('table')) {
            $request_sql = new RequestSql();
            $attributes = $request_sql->getAttributesByTable($table);
            foreach ($attributes as $key => $attribute) {
                unset(
                    $attributes[$key]['Null'],
                    $attributes[$key]['Key'],
                    $attributes[$key]['Default'],
                    $attributes[$key]['Extra']
                );
            }
            die(json_encode($attributes));
        }
    }

    /**
     * @return string|void
     *
     * @throws PrestaShopDatabaseException
     */
    public function renderView()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        /** @var RequestSql $obj */
        $view = [];
        if ($results = Db::getInstance()->executeS($obj->sql)) {
            $tab_key = [];
            foreach (array_keys($results[0]) as $key) {
                $tab_key[] = $key;
            }

            $view['name'] = $obj->name;
            $view['key'] = $tab_key;
            $view['results'] = $results;

            $this->toolbar_title = $obj->name;

            $request_sql = new RequestSql();
            $view['attributes'] = $request_sql->attributes;
        } else {
            $view['error'] = true;
        }

        $this->tpl_view_vars = [
            'view' => $view,
        ];

        return parent::renderView();
    }

    public function _childValidation()
    {
        if (Tools::getValue('submitAdd' . $this->table) && $sql = Tools::getValue('sql')) {
            $request_sql = new RequestSql();
            $parser = $request_sql->parsingSql($sql);
            $validate = $request_sql->validateParser($parser, false, $sql);

            if (!$validate || count($request_sql->error_sql)) {
                $this->displayError($request_sql->error_sql);
            }
        }
    }

    /**
     * Display export action link.
     *
     * @param string $token
     * @param int $id
     *
     * @return string
     *
     * @throws Exception
     * @throws SmartyException
     */
    public function displayExportLink($token, $id)
    {
        $tpl = $this->createTemplate('list_action_export.tpl');

        $tpl->assign([
            'href' => self::$currentIndex . '&token=' . $this->token . '&' . $this->identifier . '=' . $id . '&export' . $this->table . '=1',
            'action' => $this->trans('Export', [], 'Admin.Actions'),
        ]);

        return $tpl->fetch();
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::getValue('export' . $this->table)) {
            $this->display = 'export';
            $this->action = 'export';
        }
    }

    public function initContent()
    {
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
        } elseif ($this->display == 'export') {
            $this->processExport();
        } elseif (!$this->ajax) {
            $this->content .= $this->renderList();
            $this->content .= $this->renderOptions();
        }

        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_request'] = [
                'href' => self::$currentIndex . '&addrequest_sql&token=' . $this->token,
                'desc' => $this->trans('Add new SQL query', [], 'Admin.Advparameters.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * Generating an export file.
     */
    public function processExport($textDelimiter = '"')
    {
        $id = Tools::getValue($this->identifier);
        $export_dir = _PS_ADMIN_DIR_ . '/export/';
        if (!Validate::isFileName($id)) {
            die(Tools::displayError('Invalid filename for export.'));
        }
        $file = 'request_sql_' . $id . '.csv';
        if ($csv = fopen($export_dir . $file, 'wb')) {
            $sql = RequestSql::getRequestSqlById($id);

            if ($sql) {
                $results = Db::getInstance()->executeS($sql[0]['sql']);
                $tab_key = [];
                foreach (array_keys($results[0]) as $key) {
                    $tab_key[] = $key;
                    fwrite($csv, $key . ';');
                }
                foreach ($results as $result) {
                    fwrite($csv, "\n");
                    foreach ($tab_key as $name) {
                        fwrite($csv, $textDelimiter . strip_tags($result[$name]) . $textDelimiter . ';');
                    }
                }
                if (file_exists($export_dir . $file)) {
                    $filesize = filesize($export_dir . $file);
                    $upload_max_filesize = Tools::convertBytes(ini_get('upload_max_filesize'));
                    if ($filesize < $upload_max_filesize) {
                        if (Configuration::get('PS_ENCODING_FILE_MANAGER_SQL')) {
                            $charset = Configuration::get('PS_ENCODING_FILE_MANAGER_SQL');
                        } else {
                            $charset = self::$encoding_file[0]['name'];
                        }

                        header('Content-Type: text/csv; charset=' . $charset);
                        header('Cache-Control: no-store, no-cache');
                        header('Content-Disposition: attachment; filename="' . $file . '"');
                        header('Content-Length: ' . $filesize);
                        readfile($export_dir . $file);
                        die();
                    } else {
                        $this->errors[] = $this->trans('The file is too large and cannot be downloaded. Please use the LIMIT clause in this query.', [], 'Admin.Advparameters.Notification');
                    }
                }
            }
        }
    }

    /**
     * Display all errors.
     *
     * @param array $e Array of errors
     */
    public function displayError($e)
    {
        foreach (array_keys($e) as $key) {
            switch ($key) {
                case 'checkedFrom':
                    if (isset($e[$key]['table'])) {
                        $this->errors[] = $this->trans(
                            'The "%tablename%" table does not exist.',
                            [
                                '%tablename%' => $e[$key]['table'],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($e[$key]['attribut'])) {
                        $this->errors[] = $this->trans(
                                'The "%attribute%" attribute does not exist in the "%table%" table.',
                                [
                                    '%attribute%' => $e[$key]['attribut'][0],
                                    '%table%' => $e[$key]['attribut'][1],
                                ],
                                'Admin.Advparameters.Notification'
                            );
                    } else {
                        $this->errors[] = $this->trans('Undefined "%s" error', ['checkedForm'], 'Admin.Advparameters.Notification');
                    }

                    break;

                case 'checkedSelect':
                    if (isset($e[$key]['table'])) {
                        $this->errors[] = $this->trans(
                            'The "%tablename%" table does not exist.',
                            [
                                '%tablename%' => $e[$key]['table'],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($e[$key]['attribut'])) {
                        $this->errors[] = $this->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            [
                                '%attribute%' => $e[$key]['attribut'][0],
                                '%table%' => $e[$key]['attribut'][1],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($e[$key]['*'])) {
                        $this->errors[] = $this->trans('The "*" operator cannot be used in a nested query.', [], 'Admin.Advparameters.Notification');
                    } else {
                        $this->errors[] = $this->trans('Undefined "%s" error', ['checkedSelect'], 'Admin.Advparameters.Notification');
                    }

                    break;

                case 'checkedWhere':
                    if (isset($e[$key]['operator'])) {
                        $this->errors[] = $this->trans(
                            'The operator "%s" is incorrect.',
                            [
                                '%operator%' => $e[$key]['operator'],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($e[$key]['attribut'])) {
                        $this->errors[] = $this->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            [
                                '%attribute%' => $e[$key]['attribut'][0],
                                '%table%' => $e[$key]['attribut'][1],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans('Undefined "%s" error', ['checkedWhere'], 'Admin.Advparameters.Notification');
                    }

                    break;

                case 'checkedHaving':
                    if (isset($e[$key]['operator'])) {
                        $this->errors[] = $this->trans(
                            'The "%operator%" operator is incorrect.',
                            [
                                '%operator%' => $e[$key]['operator'],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($e[$key]['attribut'])) {
                        $this->errors[] = $this->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            [
                                '%attribute%' => $e[$key]['attribut'][0],
                                '%table%' => $e[$key]['attribut'][1],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans('Undefined "%s" error', ['checkedHaving'], 'Admin.Advparameters.Notification');
                    }

                    break;

                case 'checkedOrder':
                    if (isset($e[$key]['attribut'])) {
                        $this->errors[] = $this->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            [
                                '%attribute%' => $e[$key]['attribut'][0],
                                '%table%' => $e[$key]['attribut'][1],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans('Undefined "%s" error', ['checkedOrder'], 'Admin.Advparameters.Notification');
                    }

                    break;

                case 'checkedGroupBy':
                    if (isset($e[$key]['attribut'])) {
                        $this->errors[] = $this->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            [
                                '%attribute%' => $e[$key]['attribut'][0],
                                '%table%' => $e[$key]['attribut'][1],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans('Undefined "%s" error', ['checkedGroupBy'], 'Admin.Advparameters.Notification');
                    }

                    break;

                case 'checkedLimit':
                    $this->errors[] = $this->trans('The LIMIT clause must contain numeric arguments.', [], 'Admin.Advparameters.Notification');

                    break;

                case 'returnNameTable':
                    if (isset($e[$key]['reference'])) {
                        $this->errors[] = $this->trans(
                            'The "%reference%" reference does not exist in the "%table%" table.',
                            [
                                '%reference%' => $e[$key]['reference'][0],
                                '%table%' => $e[$key]['attribut'][1],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans('When multiple tables are used, each attribute must refer back to a table.', [], 'Admin.Advparameters.Notification');
                    }

                    break;

                case 'testedRequired':
                    $this->errors[] = $this->trans('"%key%" does not exist.', ['%key%' => $e[$key]], 'Admin.Notifications.Error');

                    break;

                case 'testedUnauthorized':
                    $this->errors[] = $this->trans('"%key%" is an unauthorized keyword.', ['%key%' => $e[$key]], 'Admin.Advparameters.Notification');

                    break;
            }
        }
    }
}
