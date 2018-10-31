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
 * @property RequestSql $object
 */
class AdminRequestSqlControllerCore extends AdminController
{
    /**
     * @var array : List of encoding type for a file
     */
    public static $encoding_file = array(
        array('value' => 1, 'name' => 'utf-8'),
        array('value' => 2, 'name' => 'iso-8859-1'),
    );

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'request_sql';
        $this->className = 'RequestSql';
        $this->lang = false;
        $this->export = true;

        parent::__construct();

        $this->fields_list = array(
            'id_request_sql' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'class' => 'fixed-width-xs'),
            'name' => array('title' => $this->trans('SQL query Name', array(), 'Admin.Advparameters.Feature')),
            'sql' => array(
                'title' => $this->trans('SQL query', array(), 'Admin.Advparameters.Feature'),
                'filter_key' => 'a!sql',
            ),
        );

        $this->fields_options = array(
            'general' => array(
                'title' => $this->trans('Settings', array(), 'Admin.Global'),
                'fields' => array(
                    'PS_ENCODING_FILE_MANAGER_SQL' => array(
                        'title' => $this->trans('Select your default file encoding', array(), 'Admin.Advparameters.Feature'),
                        'cast' => 'intval',
                        'type' => 'select',
                        'identifier' => 'value',
                        'list' => self::$encoding_file,
                        'visibility' => Shop::CONTEXT_ALL,
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ),
        );
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
            $this->toolbar_btn['edit'] = array(
                'href' => self::$currentIndex . '&amp;updaterequest_sql&amp;token=' . $this->token . '&amp;id_request_sql=' . (int) $id_request,
                'desc' => $this->trans('Edit this SQL query', array(), 'Admin.Advparameters.Feature'),
            );
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

        $this->displayWarning($this->trans('When saving the query, only the "SELECT" SQL statement is allowed.', array(), 'Admin.Advparameters.Notification'));
        $this->displayInformation('
		<strong>' . $this->trans('How do I create a new SQL query?', array(), 'Admin.Advparameters.Help') . '</strong><br />
		<ul>
			<li>' . $this->trans('Click "Add New".', array(), 'Admin.Advparameters.Help') . '</li>
			<li>' . $this->trans('Fill in the fields and click "Save".', array(), 'Admin.Advparameters.Help') . '</li>
			<li>' . $this->trans('You can then view the query results by clicking on the Edit action in the dropdown menu', array(), 'Admin.Advparameters.Help') . ' <i class="icon-pencil"></i></li>
			<li>' . $this->trans('You can also export the query results as a CSV file by clicking on the Export button', array(), 'Admin.Advparameters.Help') . ' <i class="icon-cloud-upload"></i></li>
		</ul>');

        $this->addRowAction('export');
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('SQL query', array(), 'Admin.Advparameters.Feature'),
                'icon' => 'icon-cog',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('SQL query name', array(), 'Admin.Advparameters.Feature'),
                    'name' => 'name',
                    'size' => 103,
                    'required' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('SQL query', array(), 'Admin.Advparameters.Feature'),
                    'name' => 'sql',
                    'cols' => 100,
                    'rows' => 10,
                    'required' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            ),
        );

        $request = new RequestSql();
        $this->tpl_form_vars = array('tables' => $request->getTables());

        return parent::renderForm();
    }

    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', array(), 'Admin.Notifications.Error');

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
            die($this->trans('This functionality has been disabled.', array(), 'Admin.Notifications.Error'));
        }
        if ($table = Tools::getValue('table')) {
            $request_sql = new RequestSql();
            $attributes = $request_sql->getAttributesByTable($table);
            foreach ($attributes as $key => $attribute) {
                unset($attributes[$key]['Null']);
                unset($attributes[$key]['Key']);
                unset($attributes[$key]['Default']);
                unset($attributes[$key]['Extra']);
            }
            die(json_encode($attributes));
        }
    }

    public function renderView()
    {
        /** @var RequestSql $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $view = array();
        if ($results = Db::getInstance()->executeS($obj->sql)) {
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

        $this->tpl_view_vars = array(
            'view' => $view,
        );

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
     * @param $token
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

        $tpl->assign(array(
            'href' => self::$currentIndex . '&token=' . $this->token . '&' . $this->identifier . '=' . $id . '&export' . $this->table . '=1',
            'action' => $this->trans('Export', array(), 'Admin.Actions'),
        ));

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

        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_request'] = array(
                'href' => self::$currentIndex . '&addrequest_sql&token=' . $this->token,
                'desc' => $this->trans('Add new SQL query', array(), 'Admin.Advparameters.Feature'),
                'icon' => 'process-icon-new',
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * Genrating a export file.
     */
    public function processExport($textDelimiter = '"')
    {
        $id = Tools::getValue($this->identifier);
        $export_dir = defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ . '/export/' : _PS_ADMIN_DIR_ . '/export/';
        if (!Validate::isFileName($id)) {
            die(Tools::displayError());
        }
        $file = 'request_sql_' . $id . '.csv';
        if ($csv = fopen($export_dir . $file, 'w')) {
            $sql = RequestSql::getRequestSqlById($id);

            if ($sql) {
                $results = Db::getInstance()->executeS($sql[0]['sql']);
                foreach (array_keys($results[0]) as $key) {
                    $tab_key[] = $key;
                    fputs($csv, $key . ';');
                }
                foreach ($results as $result) {
                    fputs($csv, "\n");
                    foreach ($tab_key as $name) {
                        fputs($csv, $textDelimiter . strip_tags($result[$name]) . $textDelimiter . ';');
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
                        $this->errors[] = $this->trans('The file is too large and cannot be downloaded. Please use the LIMIT clause in this query.', array(), 'Admin.Advparameters.Notification');
                    }
                }
            }
        }
    }

    /**
     * Display all errors.
     *
     * @param $e : array of errors
     */
    public function displayError($e)
    {
        foreach (array_keys($e) as $key) {
            switch ($key) {
                case 'checkedFrom':
                    if (isset($e[$key]['table'])) {
                        $this->errors[] = $this->trans(
                            'The "%tablename%" table does not exist.',
                            array(
                                '%tablename%' => $e[$key]['table'],
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($e[$key]['attribut'])) {
                        $this->errors[] = $this->trans(
                                'The "%attribute%" attribute does not exist in the "%table%" table.',
                                array(
                                    '%attribute%' => $e[$key]['attribut'][0],
                                    '%table%' => $e[$key]['attribut'][1],
                                ),
                                'Admin.Advparameters.Notification'
                            );
                    } else {
                        $this->errors[] = $this->trans('Undefined "%s" error', array('checkedForm'), 'Admin.Advparameters.Notification');
                    }
                    break;

                case 'checkedSelect':
                    if (isset($e[$key]['table'])) {
                        $this->errors[] = $this->trans(
                            'The "%tablename%" table does not exist.',
                            array(
                                '%tablename%' => $e[$key]['table'],
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($e[$key]['attribut'])) {
                        $this->errors[] = $this->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            array(
                                '%attribute%' => $e[$key]['attribut'][0],
                                '%table%' => $e[$key]['attribut'][1],
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($e[$key]['*'])) {
                        $this->errors[] = $this->trans('The "*" operator cannot be used in a nested query.', array(), 'Admin.Advparameters.Notification');
                    } else {
                        $this->errors[] = $this->trans('Undefined "%s" error', array('checkedSelect'), 'Admin.Advparameters.Notification');
                    }
                    break;

                case 'checkedWhere':
                    if (isset($e[$key]['operator'])) {
                        $this->errors[] = $this->trans(
                            'The operator "%s" is incorrect.',
                            array(
                                '%operator%' => $e[$key]['operator'],
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($e[$key]['attribut'])) {
                        $this->errors[] = $this->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            array(
                                '%attribute%' => $e[$key]['attribut'][0],
                                '%table%' => $e[$key]['attribut'][1],
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans('Undefined "%s" error', array('checkedWhere'), 'Admin.Advparameters.Notification');
                    }
                    break;

                case 'checkedHaving':
                    if (isset($e[$key]['operator'])) {
                        $this->errors[] = $this->trans(
                            'The "%operator%" operator is incorrect.',
                            array(
                                '%operator%' => $e[$key]['operator'],
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($e[$key]['attribut'])) {
                        $this->errors[] = $this->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            array(
                                '%attribute%' => $e[$key]['attribut'][0],
                                '%table%' => $e[$key]['attribut'][1],
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans('Undefined "%s" error', array('checkedHaving'), 'Admin.Advparameters.Notification');
                    }
                    break;

                case 'checkedOrder':
                    if (isset($e[$key]['attribut'])) {
                        $this->errors[] = $this->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            array(
                                '%attribute%' => $e[$key]['attribut'][0],
                                '%table%' => $e[$key]['attribut'][1],
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans('Undefined "%s" error', array('checkedOrder'), 'Admin.Advparameters.Notification');
                    }
                    break;

                case 'checkedGroupBy':
                    if (isset($e[$key]['attribut'])) {
                        $this->errors[] = $this->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            array(
                                '%attribute%' => $e[$key]['attribut'][0],
                                '%table%' => $e[$key]['attribut'][1],
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans('Undefined "%s" error', array('checkedGroupBy'), 'Admin.Advparameters.Notification');
                    }
                    break;

                case 'checkedLimit':
                    $this->errors[] = $this->trans('The LIMIT clause must contain numeric arguments.', array(), 'Admin.Advparameters.Notification');
                    break;

                case 'returnNameTable':
                    if (isset($e[$key]['reference'])) {
                        $this->errors[] = $this->trans(
                            'The "%reference%" reference does not exist in the "%table%" table.',
                            array(
                                '%reference%' => $e[$key]['reference'][0],
                                '%table%' => $e[$key]['attribut'][1],
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans('When multiple tables are used, each attribute must refer back to a table.', array(), 'Admin.Advparameters.Notification');
                    }
                    break;

                case 'testedRequired':
                    $this->errors[] = $this->trans('"%key%" does not exist.', array('%key%' => $e[$key]), 'Admin.Notifications.Error');
                    break;

                case 'testedUnauthorized':
                    $this->errors[] = $this->trans('"%key%" is an unauthorized keyword.', array('%key%' => $e[$key]), 'Admin.Advparameters.Notification');
                    break;
            }
        }
    }
}
