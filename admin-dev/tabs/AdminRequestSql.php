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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminRequestSql extends AdminTab
{

	public function __construct()
	{
		$this->table = 'request_sql';
		$this->className = 'RequestSql';
		$this->edit = true;
		$this->delete = true;
		$this->view = true;
		$this->export = true;

		$this->fieldsDisplay = array(
			'id_request_sql' => array('title' => $this->l('ID'), 'width' => 25),
			'name' => array('title' => $this->l('Name'), 'width' => 300),
			'sql' => array('title' => $this->l('Request'), 'width' => 500)
		);
		parent::__construct();
	}

	public function displayList()
	{
		return parent::displayList();
	}

	public function displayTop()
	{
		echo '<div class="hint clear" style="display:block;">
			&nbsp;<b>'.$this->l('How to create a new sql query?').'</b>
			<br />
			<ul>
				<li>'.$this->l('Click "Add new".').'<br /></li>
				<li>'.$this->l('Fill in the fields and click "Save".').'</li>
				<li>'.$this->l('You can then view the query results by clicking on the tab: ').' <img src="../img/admin/details.gif"></li>
				<li>'.$this->l('You can then export the query results as a file. Csv file by clicking on the tab: ').' <img src="../img/admin/export.gif"></li>
			</ul>
		</div><br />
		<div class="warn"><img src="../img/admin/warn2.png">'.$this->l('Warning: when saving the query, only the request type "SELECT" are allowed.').
		'</div>';

		if (isset($_GET['maxsize']))
			echo '<div class="error"><img src="../img/admin/error2.png">'.
			$this->l('The file is too large and can not be downloaded. Please use the clause "LIMIT" in this query.').'</div>';
	}

	public function displayForm($isMainTab = true)
	{
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<div class="warn"><img src="../img/admin/warn2.png">'.$this->l('Warning: when saving the query, only the request type "SELECT" are allowed.').
		'</div>
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

		return parent::postProcess();
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

	public function viewRequest_sql()
	{
		if (!($obj = $this->loadObject(true)))
			return;
		echo '<h2>'.$obj->name.'</h2>';

		if ($results = Db::getInstance()->ExecuteS($obj->sql))
		{
			$tab_key = array();
			foreach (array_keys($results[0]) as $key)
				$tab_key[] = $key;
			echo '
			<table cellpadding="0" cellspacing="0" class="table" id="viewRequestSql">
				<tr>';
				foreach ($tab_key as $key_name)
					echo '<th align="center">'.$key_name.'</th>';
			echo '
				</tr>';
				$request_sql = new RequestSql();
				$attributes = $request_sql->attributes;
				foreach ($results as $result)
				{
					echo '<tr>';
					foreach ($tab_key as $name)
					{
						if (!isset($attributes[$name]))
							echo '<td>'.Tools::safeOutput($result[$name]).'</td>';
						else
							echo '<td>'.$attributes[$name].'</td>';
					}
					echo '</tr>';
				}
			echo '
			</table>
			<script type="text/javascript">
				$(function(){
					var width = $("#viewRequestSql").width();
					if (width > 990){
						$("#viewRequestSql").css("display","block").css("overflow-x","scroll");
					}
				});
			</script>';
		}
		echo '<br /><br /><a href="'.((Tools::getValue('back')) ? Tools::getValue('back') : self::$currentIndex.'&token='.$this->token).'"><img src="../img/admin/arrow2.gif" /> '.((Tools::getValue('back')) ? $this->l('Back') : $this->l('Back to list')).'</a><br />';
	}

	/**
	* Override displayListContent method for add a button "export"
	*/
	public function displayListContent($token = null)
	{
		/* Display results in a table
		 *
		 * align  : determine value alignment
		 * prefix : displayed before value
		 * suffix : displayed after value
		 * image  : object image
		 * icon   : icon determined by values
		 * active : allow to toggle status
		 */
		$id_category = 1; // default categ

		$irow = 0;
		if ($this->_list && isset($this->fieldsDisplay['position']))
		{
			$positions = array_map(create_function('$elem', 'return (int)($elem[\'position\']);'), $this->_list);
			sort($positions);
		}
		if ($this->_list)
		{
			$isCms = false;
			if (preg_match('/cms/Ui', $this->identifier))
				$isCms = true;
			$keyToGet = 'id_'.($isCms ? 'cms_' : '').'category'.(in_array($this->identifier, array('id_category', 'id_cms_category')) ? '_parent' : '');
			foreach ($this->_list as $tr)
			{
				$id = $tr[$this->identifier];
				echo '<tr'.(array_key_exists($this->identifier,$this->identifiersDnd) ? ' id="tr_'.(($id_category = (int)(Tools::getValue('id_'.($isCms ? 'cms_' : '').'category', '1'))) ? $id_category : '').'_'.$id.'_'.$tr['position'].'"' : '').($irow++ % 2 ? ' class="alt_row"' : '').' '.((isset($tr['color']) AND $this->colorOnBackground) ? 'style="background-color: '.$tr['color'].'"' : '').'>
							<td class="center">';
				if ($this->delete AND (!isset($this->_listSkipDelete) OR !in_array($id, $this->_listSkipDelete)))
					echo '<input type="checkbox" name="'.$this->table.'Box[]" value="'.$id.'" class="noborder" />';
				echo '</td>';
				foreach ($this->fieldsDisplay as $key => $params)
				{
					$tmp = explode('!', $key);
					$key = isset($tmp[1]) ? $tmp[1] : $tmp[0];
					echo '
					<td '.(isset($params['position']) ? ' id="td_'.(isset($id_category) AND $id_category ? $id_category : 0).'_'.$id.'"' : '').' class="'.((!isset($this->noLink) || !$this->noLink) ? 'pointer' : '').((isset($params['position']) && $this->_orderBy == 'position')? ' dragHandle' : ''). (isset($params['align']) ? ' '.$params['align'] : '').'" ';
					if (!isset($params['position']) && (!isset($this->noLink) || !$this->noLink))
						echo ' onclick="document.location = \''.self::$currentIndex.'&'.$this->identifier.'='.$id.($this->view? '&view' : '&update').$this->table.'&token='.($token!=NULL ? $token : $this->token).'\'">'.(isset($params['prefix']) ? $params['prefix'] : '');
					else
						echo '>';
					if (isset($params['active']) && isset($tr[$key]))
					    $this->_displayEnableLink($token, $id, $tr[$key], $params['active'], Tools::getValue('id_category'), Tools::getValue('id_product'));
					else if (isset($params['activeVisu']) && isset($tr[$key]))
						echo '<img src="../img/admin/'.($tr[$key] ? 'enabled.gif' : 'disabled.gif').'"
						alt="'.($tr[$key] ? $this->l('Enabled') : $this->l('Disabled')).'" title="'.($tr[$key] ? $this->l('Enabled') : $this->l('Disabled')).'" />';
					else if (isset($params['position']))
					{
						if ($this->_orderBy == 'position' && $this->_orderWay != 'DESC')
						{
							echo '<a'.(!($tr[$key] != $positions[sizeof($positions) - 1]) ? ' style="display: none;"' : '').' href="'.self::$currentIndex.
									'&'.$keyToGet.'='.(int)($id_category).'&'.$this->identifiersDnd[$this->identifier].'='.$id.'
									&way=1&position='.(int)($tr['position'] + 1).'&token='.($token!=null ? $token : $this->token).'">
									<img src="../img/admin/'.($this->_orderWay == 'ASC' ? 'down' : 'up').'.gif"
									alt="'.$this->l('Down').'" title="'.$this->l('Down').'" /></a>';

							echo '<a'.(!($tr[$key] != $positions[0]) ? ' style="display: none;"' : '').' href="'.self::$currentIndex.
									'&'.$keyToGet.'='.(int)($id_category).'&'.$this->identifiersDnd[$this->identifier].'='.$id.'
									&way=0&position='.(int)($tr['position'] - 1).'&token='.($token!=NULL ? $token : $this->token).'">
									<img src="../img/admin/'.($this->_orderWay == 'ASC' ? 'up' : 'down').'.gif"
									alt="'.$this->l('Up').'" title="'.$this->l('Up').'" /></a>';						}
						else
							echo (int)($tr[$key] + 1);
					}
					else if (isset($tr[$key]))
					{
						$echo = $tr[$key];

						echo isset($params['callback']) ? call_user_func_array(array((isset($params['callback_object'])) ? $params['callback_object'] : $this->className, $params['callback']), array($echo, $tr)) : $echo;
					}
					else
						echo '--';

					echo (isset($params['suffix']) ? $params['suffix'] : '').
					'</td>';
				}

				if ($this->shopLinkType)
				{
					$name = (Tools::strlen($tr['shop_name']) > 15) ? Tools::substr($tr['shop_name'], 0, 15).'...' : $tr['shop_name'];
					echo '<td class="center" '.(($name != $tr['shop_name']) ? 'title="'.$tr['shop_name'].'"' : '').'>'.$name.'</td>';
				}

				if ($this->edit || $this->delete || ($this->view && $this->view !== 'noActionColumn'))
				{
					echo '<td class="center" style="white-space: nowrap;">';
					if ($this->export)
                        $this->_displayExportLink($token, $id);
					if ($this->view)
                        $this->_displayViewLink($token, $id);
					if ($this->edit)
					    $this->_displayEditLink($token, $id);
					if ($this->delete && (!isset($this->_listSkipDelete) || !in_array($id, $this->_listSkipDelete)))
					    $this->_displayDeleteLink($token, $id);
					if ($this->duplicate)
                        $this->_displayDuplicate($token, $id);
					echo '</td>';
				}
				echo '</tr>';
			}
		}
	}

	protected function _displayExportLink($token = null, $id)
	{
		$_cacheLang['export'] = $this->l('export');
    	echo '
			<a href="requestSql.php?id_request_sql='.$id.'">
			<img src="../img/admin/export.gif" alt="'.$_cacheLang['export'].'" title="'.$_cacheLang['export'].'" /></a>';
	}
}


