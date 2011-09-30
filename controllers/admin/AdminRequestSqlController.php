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
*  @version  Release: $Revision: 8897 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminRequestSqlControllerCore extends AdminController
{
	private $info = true;
	private $warning = true;

	public function __construct()
	{
		$this->table = 'request_sql';
		$this->className = 'RequestSql';
	 	$this->lang = false;
	 	$this->edit = true;
	 	$this->delete = true;
		$this->view = true;
		$this->export = true;
		$this->requiredDatabase = true;
		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

		$this->fieldsDisplay = array(
			'id_request_sql' => array('title' => $this->l('ID'), 'width' => 25),
			'name' => array('title' => $this->l('Name'), 'width' => 300),
			'sql' => array('title' => $this->l('Request'), 'width' => 500)
		);

		$this->template = 'requestSql.tpl';

		parent::__construct();
	}

	public function postProcess()
	{
		if (!($obj = $this->loadObject(true)))
			return;

		$result = Db::getInstance()->ExecuteS('
			SELECT `id_request_sql`
			FROM `'._DB_PREFIX_.'request_sql`
		');

		if (count($result) === 1)
			foreach ($result as $row)
				$this->_listSkipDelete = array($row['id_request_sql']);

		if (!count($this->_errors))
			parent::postProcess();
	}

	public function viewRequest_sql()
	{
		if (!($obj = $this->loadObject(true)))
			return;
		$view = array();
		//$content = '<h2>'.$obj->name.'</h2>';

		if ($results = Db::getInstance()->ExecuteS($obj->sql))
		{
			foreach (array_keys($results[0]) as $key)
				$tab_key[] = $key;
				
			$view['name'] = $obj->name;
			$view['key'] = $tab_key;
			$view['results'] = $results;
			/*
			$tab_key = array();
			foreach (array_keys($results[0]) as $key)
				$tab_key[] = $key;
			$content .= '
			<table cellpadding="0" cellspacing="0" class="table" id="viewRequestSql">
				<tr>';
				foreach ($tab_key as $key_name)
					$content .= '<th align="center">'.$key_name.'</th>';
				$content .= '
				</tr>';
				$request_sql = new RequestSql();
				$attributes = $request_sql->attributes;
				foreach ($results as $result)
				{
					$content .= '<tr>';
					foreach ($tab_key as $name)
					{
						if (!isset($attributes[$name]))
							$content .= '<td>'.Tools::safeOutput($result[$name]).'</td>';
						else
							$content .= '<td>'.$attributes[$name].'</td>';
					}
					$content .= '</tr>';
				}
			$content .= '</table>';
			*/
		}
		return $view;
	}

	public function _childValidation()
	{
		if (Tools::getValue('submitAdd'.$this->table) && $sql = Tools::getValue('sql'))
		{
			$request_sql = new RequestSql();
			$parser = $request_sql->parsingSql($sql);
			$validate = $request_sql->validateParser($parser, false, $sql);

			if (!$validate || !empty($request_sql->error_sql))
				$this->displayError($request_sql->error_sql);
		}
	}

	public function displayError($e)
	{
		foreach (array_keys($e) as $key)
		{
			switch ($key)
			{
				case 'checkedFrom':
					if (isset($e[$key]['table']))
						$this->_errors[] = Tools::DisplayError($this->l('The Table ').' "'.$e[$key]['table'].'" '.$this->l(' doesn\'t exist.'));
					else if (isset($e[$key]['attribut']))
						$this->_errors[] = Tools::DisplayError($this->l('The attribute ').' "'.
						$e[$key]['attribut'][0].'" '.$this->l(' does not exist in the table: ').$e[$key]['attribut'][1].'.');
					else
						$this->_errors[] = Tools::DisplayError($this->l('Error'));
					break;
				case 'checkedSelect':
					if (isset($e[$key]['table']))
						$this->_errors[] = Tools::DisplayError($this->l('The Table ').' "'.$e[$key]['table'].'" '.$this->l(' doesn\'t exist.'));
					else if (isset($e[$key]['attribut']))
						$this->_errors[] = Tools::DisplayError($this->l('The attribute ').' "'.
						$e[$key]['attribut'][0].'" '.$this->l(' does not exist in the table: ').$e[$key]['attribut'][1].'.');
					else if (isset($e[$key]['*']))
						$this->_errors[] = Tools::DisplayError($this->l('The operand "*" can be used in a nested query.'));
					else
						$this->_errors[] = Tools::DisplayError($this->l('Error'));
					break;
				case 'checkedWhere':
					if (isset($e[$key]['operator']))
						$this->_errors[] = Tools::DisplayError($this->l('The operator ').' "'.$e[$key]['operator'].'" '.$this->l(' used is incorrect.'));
					else if (isset($e[$key]['attribut']))
						$this->_errors[] = Tools::DisplayError($this->l('The attribute ').' "'.
						$e[$key]['attribut'][0].'" '.$this->l(' does not exist in the table: ').$e[$key]['attribut'][1].'.');
					else
						$this->_errors[] = Tools::DisplayError($this->l('Error'));
					break;
				case 'checkedHaving':
					if (isset($e[$key]['operator']))
						$this->_errors[] = Tools::DisplayError($this->l('The operator ').' "'.$e[$key]['operator'].'" '.$this->l(' used is incorrect.'));
					else if (isset($e[$key]['attribut']))
						$this->_errors[] = Tools::DisplayError($this->l('The attribute ').' "'.
						$e[$key]['attribut'][0].'" '.$this->l(' does not exist in the table: ').$e[$key]['attribut'][1].'.');
					else
						$this->_errors[] = Tools::DisplayError($this->l('Error'));
					break;
				case 'checkedOrder':
					if (isset($e[$key]['attribut']))
						$this->_errors[] = Tools::DisplayError($this->l('The attribute ').' "'.
						$e[$key]['attribut'][0].'" '.$this->l(' does not exist in the table: ').$e[$key]['attribut'][1].'.');
					else
						$this->_errors[] = Tools::DisplayError($this->l('Error'));
					break;
				case 'checkedGroupBy':
					if (isset($e[$key]['attribut']))
						$this->_errors[] = Tools::DisplayError($this->l('The attribute ').' "'.
						$e[$key]['attribut'][0].'" '.$this->l(' does not exist in the table: ').$e[$key]['attribut'][1].'.');
					else
						$this->_errors[] = Tools::DisplayError($this->l('Error'));
					break;
				case 'checkedLimit':
						$this->_errors[] = Tools::DisplayError($this->l('The LIMIT clause must contain numeric arguments.'));
					break;
				case 'returnNameTable':
						if (isset($e[$key]['reference']))
							$this->_errors[] = Tools::DisplayError($this->l('The reference ').'"'.
							$e[$key]['reference'][0].'"'.$this->l(' doesn\'t exist in : ').$e[$key]['reference'][1]);
						else
							$this->_errors[] = Tools::DisplayError($this->l('When multiple tables are used, each attribute must be referenced to a table.'));
					break;
				case 'testedRequired':
						$this->_errors[] = Tools::DisplayError($e[$key].' '.$this->l(' doesn\'t exist.'));
					break;
				case 'testedUnauthorized':
						$this->_errors[] = Tools::DisplayError($e[$key].' '.$this->l(' is a unauthorized keyword.'));
					break;
				default:

					break;
			}
		}
	}

	public function displayForm($isMainTab = true)
	{
		$this->content .= parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;

		$this->content .= '
		<form action="'.self::$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/subdomain.gif" /> '.$this->l('Request').'</legend>
				<label>'.$this->l('Name:').' <sup>*</sup></label>
				<div class="margin-form">
					<input type="text" name="name" value="'.$this->getFieldValue($obj, 'name').'" size="103" />
				</div>
				<label>'.$this->l('Request:').' <sup>*</sup></label>
				<div class="margin-form">
					<textarea name="sql" cols="100" rows="10">'.$this->getFieldValue($obj, 'sql').'</textarea>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
	
	public function init()
	{
		if (isset($_GET['view'.$this->table]) && isset($_GET['id_'.$this->table]))
		{
			if ($this->tabAccess['edit'] === '1' || ($this->table == 'employee' AND $this->context->employee->id == Tools::getValue('id_employee')))
				$this->display = 'view';
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		parent::init();
	}

	public function initContent()
	{
		$smarty = $this->context->smarty;
		
		switch ($this->display)
		{
			case 'edit':
				$this->info = false;
				break;
			case 'view':
				$this->info = false;
				$this->warning = false;
				$smarty->assign('view', $this->viewRequest_sql());
				break;
			default:
				$this->display = 'list';
				break;
		}

		$smarty->assign('info', $this->info);
		$smarty->assign('warning', $this->warning);

		parent::initContent();
	}

}


