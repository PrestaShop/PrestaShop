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

class AdminGroups extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'group';
	 	$this->className = 'Group';
		$this->lang = true;
	 	$this->edit = true;
	 	$this->view = true;
	 	$this->delete = true;
		
		$this->_select = '
		(SELECT COUNT(jcg.`id_customer`)
		FROM `'._DB_PREFIX_.'customer_group` jcg 
		LEFT JOIN `'._DB_PREFIX_.'customer` jc ON (jc.`id_customer` = jcg.`id_customer`) 
		WHERE jc.`deleted` != 1 
		AND jcg.`id_group` = a.`id_group`) AS nb
		';
		$this->_group = 'GROUP BY a.id_group';
		$this->_listSkipDelete = array(1);

 		$this->fieldsDisplay = array(
		'id_group' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('Name'), 'width' => 80, 'filter_key' => 'b!name'),
		'reduction' => array('title' => $this->l('Discount'), 'width' => 50, 'align' => 'right'),
		'nb' => array('title' => $this->l('Members'), 'width' => 25, 'align' => 'center'),
		'date_add' => array('title' => $this->l('Creation date'), 'width' => 60, 'type' => 'date', 'align' => 'right'));

		parent::__construct();
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;
		$groupReductions = $obj->id ? GroupReduction::getGroupReductions($obj->id, (int)($cookie->id_lang)) : array();
		$categories = Category::getSimpleCategories((int)($cookie->id_lang));

		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/tab-groups.gif" />'.$this->l('Group').'</legend>
				<label>'.$this->l('Name:').' </label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					echo '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:<span class="hint-pointer">&nbsp;</span></span>
					</div>';
				$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'name', 'name');
				$reduction = htmlentities($this->getFieldValue($obj, 'reduction'), ENT_COMPAT, 'UTF-8');
				echo '
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Discount:').' </label>
				<div class="margin-form">
					<input type="text" size="5" name="reduction" value="'.($reduction ? $reduction : '0').'" /> '.$this->l('%').'
					<p>'.$this->l('Will automatically apply this value as a discount on ALL shop\'s products for this group\'s members.').'</p>
				</div>';
		if ($obj->id)
		{
			echo '
				<label>'.$this->l('Current category discount:').'</label>
				<div class="margin-form">';
			if ($groupReductions)
			{
				echo '<table>
						<tr>
							<th>'.$this->l('Category').'</th>
							<th>'.$this->l('Value').'</th>
							<th>'.$this->l('Action').'</th>
						</tr>';
				foreach ($groupReductions AS $groupReduction)
						echo '
						<tr>
							<td>'.Tools::htmlentitiesUTF8($groupReduction['category_name']).'</td>
							<td><input type="hidden" name="gr_id_group_reduction[]" value="'.(int)($groupReduction['id_group_reduction']).'" /><input type="text" name="gr_reduction[]" value="'.($groupReduction['reduction'] * 100).'" /></td>
							<td><a href="'.$currentIndex.'&deleteGroupReduction&id_group_reduction='.(int)($groupReduction['id_group_reduction']).'&id_group='.(int)($obj->id).'&token='.$this->token.'"><img src="" alt="'.$this->l('Delete').'" /></a></td>
						</tr>';
				echo '</table>';
			}
			else
				echo $this->l('No discount');
			echo '	</div>';
		}
		echo '
				<label>'.$this->l('Price display method:').' </label>
				<div class="margin-form">
					<select name="price_display_method">
						<option value="'.PS_TAX_EXC.'"'.((int)($this->getFieldValue($obj, 'price_display_method')) == PS_TAX_EXC ? ' selected="selected"' : '').'>'.$this->l('Tax excluded').'</option>
						<option value="'.PS_TAX_INC.'"'.((int)($this->getFieldValue($obj, 'price_display_method')) == PS_TAX_INC ? ' selected="selected"' : '').'>'.$this->l('Tax included').'</option>
					</select>
					<p>'.$this->l('How the prices are displayed on order summary for this customer group (tax included or excluded).').'</p>
				</div>
				<div class="clear">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form><br />';

		if ($obj->id)
		{
			echo '
			<form action="'.$currentIndex.'&update'.$this->table.'&id_group='.$obj->id.'&token='.$this->token.'" method="post" class="width3">
				<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />
				<fieldset><legend><img src="../img/admin/tab-groups.gif" />'.$this->l('New group discount').'</legend>
					<label>'.$this->l('Category:').' </label>
					<div class="margin-form">
						<select name="id_category">';
				foreach ($categories AS $category)
					echo '	<option value="'.(int)($category['id_category']).'">'.Tools::htmlentitiesUTF8($category['name']).'</option>';
				echo '	</select><sup>*</sup>
					</div>
					<label>'.$this->l('Discount (in %):').' </label>
					<div class="margin-form">
						<input type="text" name="reduction" value="" /><sup>*</sup>
					</div>
					<div class="clear">&nbsp;</div>
					<div class="margin-form">
						<input type="submit" value="'.$this->l('   Add   ').'" name="submitAddGroupReduction" class="button" />
					</div>
					<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
				</fieldset>
			</form>';
		}
	}

	public function viewgroup()
	{
		global $cookie;
		
		$currentIndex = 'index.php?tab=AdminGroups';
		if (!($obj = $this->loadObject(true)))
			return;
		$group = new Group((int)($obj->id));
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		
		echo '
		<fieldset style="width: 400px">
			<div style="float: right"><a href="'.$currentIndex.'&updategroup&id_group='.$obj->id.'&token='.$this->token.'"><img src="../img/admin/edit.gif" /></a></div>
			<span style="font-weight: bold; font-size: 14px;">'.strval($obj->name[(int)($cookie->id_lang)]).'</span>
			<div class="clear">&nbsp;</div>
			'.$this->l('Discount:').' '.(float)($obj->reduction).$this->l('%').'
		</fieldset>
		<div class="clear">&nbsp;</div>';

		$customers = $obj->getCustomers();
		$this->fieldsDisplay = (array(
			'ID' => array('title' => $this->l('ID')),
			'sex' => array('title' => $this->l('Sex')),
			'name' => array('title' => $this->l('Name')),
			'e-mail' => array('title' => $this->l('e-mail')),
			'birthdate' => array('title' => $this->l('Birth date')),
			'register_date' => array('title' => $this->l('Registration date')),
			'orders' => array('title' => $this->l('Orders')),
			'status' => array('title' => $this->l('Status')),
			'actions' => array('title' => $this->l('Actions'))
		));

		if (isset($customers) AND !empty($customers) AND $nbCustomers = sizeof($customers))
		{
			echo '<h2>'.$this->l('Customer members of this group').' ('.$nbCustomers.')</h2>
			<table cellspacing="0" cellpadding="0" class="table widthfull">
				<tr>';
			foreach ($this->fieldsDisplay AS $field)
				echo '<th'.(isset($field['width']) ? 'style="width: '.$field['width'].'"' : '').'>'.$field['title'].'</th>';
			echo '
				</tr>';
			$irow = 0;
			foreach ($customers AS $k => $customer)
			{
				$imgGender = $customer['id_gender'] == 1 ? '<img src="../img/admin/male.gif" alt="'.$this->l('Male').'" />' : ($customer['id_gender'] == 2 ? '<img src="../img/admin/female.gif" alt="'.$this->l('Female').'" />' : '');
				echo '
				<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
					<td>'.$customer['id_customer'].'</td>
					<td class="center">'.$imgGender.'</td>
					<td>'.stripslashes($customer['lastname']).' '.stripslashes($customer['firstname']).'</td>
					<td>'.stripslashes($customer['email']).'<a href="mailto:'.stripslashes($customer['email']).'"> <img src="../img/admin/email_edit.gif" alt="'.$this->l('Write to this customer').'" /></a></td>
					<td>'.Tools::displayDate($customer['birthday'], (int)($cookie->id_lang)).'</td>
					<td>'.Tools::displayDate($customer['date_add'], (int)($cookie->id_lang)).'</td>
					<td>'.Order::getCustomerNbOrders($customer['id_customer']).'</td>
					<td class="center"><img src="../img/admin/'.($customer['active'] ? 'enabled.gif' : 'forbbiden.gif').'" alt="" /></td>
					<td class="center" width="60px">
						<a href="index.php?tab=AdminCustomers&id_customer='.$customer['id_customer'].'&viewcustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee)).'">
						<img src="../img/admin/details.gif" alt="'.$this->l('View orders').'" /></a>
						<a href="index.php?tab=AdminCustomers&id_customer='.$customer['id_customer'].'&addcustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee)).'">
						<img src="../img/admin/edit.gif" alt="'.$this->l('Modify this customer').'" /></a>
						<a href="index.php?tab=AdminCustomers&id_customer='.$customer['id_customer'].'&deletecustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee)).'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');">
						<img src="../img/admin/delete.gif" alt="'.$this->l('Delete this customer').'" /></a>
					</td>
				</tr>';
			}
			echo '</table>';
		}
		else
			echo '<p><img src="../img/admin/information.png" style="float:left;margin-right:5px;" alt="" /> '.$this->l('No user in this group.').'</p>';
	}

	public function postProcess()
	{
		global $currentIndex;
		
		$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;

		if (Tools::isSubmit('deleteGroupReduction'))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (!$id_group_reduction = Tools::getValue('id_group_reduction'))
					$this->_errors[] = Tools::displayError('Invalid group reduction ID');
				else
				{
					$groupReduction = new GroupReduction((int)($id_group_reduction));
					if (!$groupReduction->delete())
						$this->_errors[] = Tools::displayError('An error occurred while deleting the group reduction');
					else
						Tools::redirectAdmin($currentIndex.'&update'.$this->table.'&id_group='.(int)(Tools::getValue('id_group')).'&conf=1&token='.$token);
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		if (Tools::isSubmit('submitAddGroupReduction'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				if (!($obj = $this->loadObject()))
					return;
				$groupReduction = new GroupReduction();
				if (!$id_category = Tools::getValue('id_category') OR !Validate::isUnsignedId($id_category))
					$this->_errors[] = Tools::displayError('Wrong category ID');
				elseif (!$reduction = Tools::getValue('reduction') OR !Validate::isPrice($reduction))
					$this->_errors[] = Tools::displayError('Invalid reduction (must be a percentage)');
				elseif (GroupReduction::doesExist((int)($obj->id), $id_category))
					$this->_errors[] = Tools::displayError('A reduction already exists for this category.');
				else
				{
					$groupReduction->id_category = (int)($id_category);
					$groupReduction->id_group = (int)($obj->id);
					$groupReduction->reduction = (float)($reduction) / 100;
					if (!$groupReduction->add())
						$this->_errors[] = Tools::displayError('An error occurred while adding a category group reduction.');
					else
						Tools::redirectAdmin($currentIndex.'&update'.$this->table.'&id_group='.(int)(Tools::getValue('id_group')).'&conf=3&token='.$this->token);
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		if (Tools::isSubmit('submitAddgroup'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				if (Tools::getValue('reduction') > 100 OR Tools::getValue('reduction') < 0)
					$this->_errors[] = Tools::displayError('Reduction value is incorrect');
				else
				{
					$id_group_reductions = Tools::getValue('gr_id_group_reduction');
					$reductions = Tools::getValue('gr_reduction');
					if ($id_group_reductions)
						foreach ($id_group_reductions AS $key => $id_group_reduction)
							if (!Validate::isUnsignedId($id_group_reductions[$key]) OR !Validate::isPrice($reductions[$key]))
								$this->_errors[] = Tools::displayError();
							else
							{
								$groupReduction = new GroupReduction((int)($id_group_reductions[$key]));
								$groupReduction->reduction = $reductions[$key] / 100;
								if (!$groupReduction->update())
									$this->errors[] = Tools::displayError('Cannot update group reductions');
							}
					if (!sizeof($this->_errors))
						parent::postProcess();
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		elseif (isset($_GET['delete'.$this->table]))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->id == 1)
						$this->_errors[] = Tools::displayError('You cannot delete default group.');
					else
					{
						if ($object->delete())
							Tools::redirectAdmin($currentIndex.'&conf=1&token='.$token);
						$this->_errors[] = Tools::displayError('An error occurred during deletion.');
					}
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else
			parent::postProcess();
	}
}
