<?php
/*
* 2007-2012 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7465 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminDbControllerCore extends AdminController
{
	public function __construct()
	{
		$this->className = 'Configuration';
		$this->table = 'configuration';

		parent::__construct();

		$this->options = array(
			'database' => array(
				'title' =>	$this->l('Database'),
				'icon' =>	'database_gear',
				'fields' =>	array(
				 	'db_server' => array(
				 		'title' => $this->l('Server:'),
				 		'desc' => $this->l('IP or server name; \'localhost\' will work in most cases'),
				 		'size' => 30,
				 		'type' => 'text',
				 		'required' => true,
				 		'defaultValue' => _DB_SERVER_,
				 		'visibility' => Shop::CONTEXT_ALL
					),
					'db_name' => array(
						'title' => $this->l('Database:'),
						'desc' => $this->l('Database name (e.g., \'prestashop\')'),
						'size' => 30,
						'type' => 'text',
						'required' => true,
						'defaultValue' => _DB_NAME_,
						'visibility' => Shop::CONTEXT_ALL
					),
					'db_prefix' => array(
						'title' => $this->l('Prefix:'),
						'size' => 30,
						'type' => 'text',
						'defaultValue' => _DB_PREFIX_,
						'visibility' => Shop::CONTEXT_ALL
					),
					'db_user' => array(
						'title' => $this->l('User:'),
						'size' => 30,
						'type' => 'text',
						'required' => true,
						'defaultValue' => _DB_USER_,
						'visibility' => Shop::CONTEXT_ALL
					),
					'db_passwd' => array(
						'title' => $this->l('Password:'),
						'size' => 30,
						'type' => 'password',
						'desc' => $this->l('Leave blank if no change'),
						'defaultValue' => _DB_PASSWD_,
						'visibility' => Shop::CONTEXT_ALL
					)
				),
				'submit' => array()
			)
		);

		$this->fieldsDisplay = array (
			'table' => array(
				'title' => $this->l('Table'),
				'type' => 'string',
				'width' => 120
			),
			'table_engine' => array(
				'title' => $this->l('Table Engine'),
				'type' => 'string',
				'width' => 120
			)
		);
	}

	public function initContent()
	{
		$this->warnings[] = $this->l('Be VERY CAREFUL with these settings, as changes may cause your PrestaShop online store to malfunction. For all issues, check the config/settings.inc.php file.');
		$this->display = 'options';
		$this->initToolbar();
		$this->content .= $this->renderOptions();
		$this->content .= $this->renderForm();

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	public function renderForm()
	{
		$engines = array();
		$tab_engines = $this->getEngines();
		foreach ($tab_engines as $key => $engine)
			$engines[]['name'] = $engine;

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('MySQL Engine'),
				'image' => '../img/admin/AdminBackup.gif'
			),
			'input' => array(
				array(
					'type' => 'table',
					'name' => 'table'
				),
				array(
					'type' => 'select',
					'label' => $this->l('Change Engine to'),
					'name' => 'engineType',
					'options' => array(
						'query' => $engines,
						'id' => 'name',
						'name' => 'name'
					)
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		$table_status = $this->getTablesStatus();
		foreach ($table_status as $key => $table)
			if (!preg_match('#^'._DB_PREFIX_.'.*#Ui', $table['Name']))
				unset($table_status[$key]);

		$this->tpl_form_vars = array('table_status' => $table_status);

		$this->show_toolbar = false;

		return parent::renderForm();
	}

	/*
	public function initContent()
	{
		$this->warnings[] = $this->l('Be VERY CAREFUL with these settings, as changes may cause your PrestaShop online store to malfunction. 
			For all issues, check the config/settings.inc.php file.');

		$this->initToolbar();
		$this->content .= $this->renderOptions();

		$table_status = $this->getTablesStatus();
		foreach ($table_status as $key => $table)
			if (!preg_match('#^'._DB_PREFIX_.'.*#Ui', $table['Name']))
				unset($table_status[$key]);

		$this->context->smarty->assign(array(
			'update_url' => self::$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token,
			'table_status' => $table_status,
			'engines' => $this->getEngines(),
			'content' => $this->content,
		));
	}
	*/

	public function postProcess()
	{
		// PrestaShop demo mode
		if (_PS_MODE_DEMO_)
		{
			$this->errors[] = Tools::displayError('This functionnality has been disabled.');
			return;
		}

		if ($this->action == 'update_options')
		{
			foreach ($this->options['database']['fields'] as $field => $values)
				if (isset($values['required']) && $values['required'])
					if (($value = Tools::getValue($field)) == false && (string)$value != '0')
						$this->errors[] = Tools::displayError('field').' <b>'.$values['title'].'</b> '.Tools::displayError('is required.');

			if (!count($this->errors))
			{
				/* Datas are not saved in database but in config/settings.inc.php */
				$settings = array();
			 	foreach ($this->options['database']['fields'] as $k => $data)
					if ($value = Tools::getValue($k))
						$settings['_'.Tools::strtoupper($k).'_'] = $value;

				if (Db::checkConnection(
					isset($settings['_DB_SERVER_']) ? $settings['_DB_SERVER_'] : _DB_SERVER_,
					isset($settings['_DB_USER_']) ? $settings['_DB_USER_'] : _DB_USER_,
					isset($settings['_DB_PASSWD_']) ? $settings['_DB_PASSWD_'] : _DB_PASSWD_,
					isset($settings['_DB_NAME_']) ? $settings['_DB_NAME_'] : _DB_NAME_,
					true
				) == 0)
				{
			 		rewriteSettingsFile(null, null, $settings);
			 		Tools::redirectAdmin(self::$currentIndex.'&conf=6'.'&token='.$this->token);
				}
				else
					$this->errors[] = Tools::displayError('Unable to connect to a database with these identifiers.');
			}
		}

		// Change engine
		if ($this->action == 'save')
		{
			if (!isset($_POST['tablesBox']) || !count($_POST['tablesBox']))
				$this->errors[] = Tools::displayError('You did not select any tables');
			else
			{
				$available_engines = $this->getEngines();
				$tables_status = $this->getTablesStatus();
				$tables_engine = array();

				foreach ($tables_status as $table)
					$tables_engine[$table['Name']] = $table['Engine'];

				$engine_type = pSQL(Tools::getValue('engineType'));

				/* Datas are not saved in database but in config/settings.inc.php */
				$settings = array('_MYSQL_ENGINE_' => $engine_type);
			    rewriteSettingsFile(null, null, $settings);

				foreach ($_POST['tablesBox'] as $table)
				{
					if ($engine_type == $tables_engine[$table])
						$this->errors[] = $table.' '.$this->l('is already in').' '.$engine_type;
					else
						if (!Db::getInstance()->execute('ALTER TABLE `'.bqSQL($table).'` ENGINE=`'.bqSQL($engine_type).'`'))
							$this->errors[] = $this->l('Can\'t change engine for').' '.$table;
				}
				if (!count($this->errors))
			 		Tools::redirectAdmin(self::$currentIndex.'&conf=4'.'&token='.$this->token);
			}
		}
	}

	public function getEngines()
	{
		$engines = Db::getInstance()->executeS('SHOW ENGINES');
		$allowed_engines = array();
		foreach ($engines as $engine)
			if (in_array($engine['Engine'], array('InnoDB', 'MyISAM')) && in_array($engine['Support'], array('DEFAULT', 'YES')))
				$allowed_engines[] = $engine['Engine'];
		return $allowed_engines;
	}

	public function getTablesStatus()
	{
		return Db::getInstance()->executeS('SHOW TABLE STATUS');
	}
}


