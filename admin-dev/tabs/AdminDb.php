<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(PS_ADMIN_DIR.'/tabs/AdminPreferences.php');

class AdminDb extends AdminPreferences
{
	public function __construct()
	{
		$this->className = 'Configuration';
		$this->table = 'configuration';
 	
 		$this->_fieldsDatabase = array(
		'db_server' => array('title' => $this->l('Server:'), 'desc' => $this->l('IP or server name; \'localhost\' will work in most cases'), 'size' => 30, 'type' => 'text', 'required' => true),
		'db_name' => array('title' => $this->l('Database:'), 'desc' => $this->l('Database name (e.g., \'prestashop\')'), 'size' => 30, 'type' => 'text', 'required' => true),
		'db_prefix' => array('title' => $this->l('Prefix:'), 'size' => 30, 'type' => 'text'),
		'db_user' => array('title' => $this->l('User:'), 'size' => 30, 'type' => 'text', 'required' => true),
		'db_passwd' => array('title' => $this->l('Password:'), 'size' => 30, 'type' => 'password', 'desc' => $this->l('Leave blank if no change')));
		parent::__construct();
	}
	
	public function postProcess()
	{
		global $currentIndex;

		if (isset($_POST['submitDatabase'.$this->table]))
		{
		 	if ($this->tabAccess['edit'] === '1')	 	
		 	{
				foreach ($this->_fieldsDatabase AS $field => $values)
					if (isset($values['required']) AND $values['required'])
						if (($value = Tools::getValue($field)) == false AND (string)$value != '0')
							$this->_errors[] = Tools::displayError('field').' <b>'.$values['title'].'</b> '.Tools::displayError('is required.');
	
				if (!sizeof($this->_errors))
				{
					/* Datas are not saved in database but in config/settings.inc.php */
					$settings = array();
				 	foreach ($_POST as $k => $value)
						if ($value)
							$settings['_'.Tools::strtoupper($k).'_'] = $value;
				 	rewriteSettingsFile(NULL, NULL, $settings);
				 	Tools::redirectAdmin($currentIndex.'&conf=6'.'&token='.$this->token);
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		if (Tools::isSubmit('submitEngine'))
		{
			if (!isset($_POST['tablesBox']) OR !sizeof($_POST['tablesBox']))
				$this->_errors[] = Tools::displayError('You do not have select tables');
			else
			{
				$available_engines = $this->_getEngines();
				$tables_status = $this->_getTablesStatus();
				$tables_engine = array();

				foreach ($tables_status AS $table)
					$tables_engine[$table['Name']] = $table['Engine'];
				
				$engineType = pSQL(Tools::getValue('engineType'));
				foreach ($_POST['tablesBox'] AS $table)
				{
					if ($engineType == $tables_engine[$table])
						$this->_errors[] = $table.' '.$this->l('is already in').' '.$engineType;
					else
						if (!Db::getInstance()->Execute('ALTER TABLE '.pSQL($table).' ENGINE='.pSQL($engineType)))
							$this->_errors[] = $this->l('Can\'t change engine for').' '.$table;
						else
							echo '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Engine change of').' '.$table.' '.$this->l('to').' '.$engineType.'</div>';
				}
			}
		}

	}

	public function display()
	{
		global $currentIndex;
		echo $this->displayWarning($this->l('Be VERY CAREFUL with these settings, as changes may cause your PrestaShop online store to malfunction. For all issues, check the config/settings.inc.php file.')).'<br />';
		$this->_displayForm('database', $this->_fieldsDatabase, $this->l('Database'), 'width2', 'database_gear');
		$engines = $this->_getEngines();
		$irow = 0;
		echo '<br /><fieldset class="width2"><legend>'.$this->l('MySQL Engine').'</legend><form name="updateEngine" action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post"><table cellspacing="0" cellpadding="0" class="table width2 clear">
				<tr><th><input type="checkbox" onclick="checkDelBoxes(this.form, \'tablesBox[]\', this.checked)" class="noborder" name="checkme"></th><th>'.$this->l('Table').'</th><th>'.$this->l('Table Engine').'</th></tr>';
		$tables_status = $this->_getTablesStatus();
		foreach ($tables_status AS $table)
		{
			if (!preg_match('/^'._DB_PREFIX_.'.*/Ui', $table['Name']))
				continue;
			echo '<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
						<td class="noborder"><input type="checkbox" name="tablesBox[]" value="'.$table['Name'].'"/></td><td>'.$table['Name'].'</td><td>'.$table['Engine'].'</td>
					</tr>';
		}
		echo '</table><br />
		<label for="dbEngine">'.$this->l('Change Engine to').'</label>
		<div class="margin-form">
			<select name="engineType">';
			foreach ($engines AS $engine)
				echo 	'<option value="'.$engine.'">'.$engine.'</option>';
			echo '</select>
			<input style="margin-left:15px;" class="button" type="submit" value="Submit" name="submitEngine" />
		</div>
		</fieldset>';
	}
	
	private function _getEngines()
	{
		$engines = Db::getInstance()->ExecuteS('SHOW ENGINES');
		$allowed_engines = array();
		foreach ($engines AS $engine)
		{
			if (in_array($engine['Engine'], array('InnoDB', 'MyISAM')) AND in_array($engine['Support'], array('DEFAULT', 'YES')))
				$allowed_engines[] = $engine['Engine'];
		}
		return $allowed_engines;
	}
	
	private function _getTablesStatus()
	{
		return Db::getInstance()->ExecuteS('SHOW TABLE STATUS');
	}
}


