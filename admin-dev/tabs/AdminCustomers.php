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

class AdminCustomers extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'customer';
	 	$this->className = 'Customer';
	 	$this->lang = false;
	 	$this->edit = true;
	 	$this->view = true;
	 	$this->delete = true;
		$this->deleted = true;
		$this->requiredDatabase = true;
		
		$this->_select = '(YEAR(CURRENT_DATE)-YEAR(`birthday`)) - (RIGHT(CURRENT_DATE, 5)<RIGHT(`birthday`, 5)) as age, (
			SELECT c.date_add FROM '._DB_PREFIX_.'guest g
			LEFT JOIN '._DB_PREFIX_.'connections c ON c.id_guest = g.id_guest
			WHERE g.id_customer = a.id_customer
			ORDER BY c.date_add DESC
			LIMIT 1
		) as connect';
		$genders = array(1 => $this->l('M'), 2 => $this->l('F'), 9 => $this->l('?'));
 		$this->fieldsDisplay = array(
		'id_customer' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'id_gender' => array('title' => $this->l('Gender'), 'width' => 25, 'align' => 'center', 'icon' => array(1 => 'male.gif', 2 => 'female.gif', 'default' => 'unknown.gif'), 'orderby' => false, 'type' => 'select', 'select' => $genders, 'filter_key' => 'a!id_gender'),
		'lastname' => array('title' => $this->l('Last Name'), 'width' => 80),
		'firstname' => array('title' => $this->l('First name'), 'width' => 60),
		'email' => array('title' => $this->l('E-mail address'), 'width' => 120, 'maxlength' => 19),
		'age' => array('title' => $this->l('Age'), 'width' => 30, 'search' => false),
		'active' => array('title' => $this->l('Enabled'), 'width' => 25, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false),
		'newsletter' => array('title' => $this->l('News.'), 'width' => 25, 'align' => 'center', 'type' => 'bool', 'icon' => array(0 => 'disabled.gif', 1 => 'enabled.gif'), 'orderby' => false),
		'optin' => array('title' => $this->l('Opt.'), 'width' => 25, 'align' => 'center', 'type' => 'bool', 'icon' => array(0 => 'disabled.gif', 1 => 'enabled.gif'), 'orderby' => false),
		'date_add' => array('title' => $this->l('Registration'), 'width' => 30, 'type' => 'date', 'align' => 'right'),
		'connect' => array('title' => $this->l('Connection'), 'width' => 60, 'type' => 'datetime', 'search' => false));

		$this->optionTitle = $this->l('Customers options');
		$this->_fieldsOptions = array(
			'PS_PASSWD_TIME_FRONT' => array('title' => $this->l('Regenerate password:'), 'desc' => $this->l('Security minimum time to wait to regenerate the password'), 'cast' => 'intval', 'size' => 5, 'type' => 'text', 'suffix' => ' '.$this->l('minutes'))
		);

		parent::__construct();
	}
	
	public function postProcess()
	{
		global $currentIndex;
		
		if (Tools::isSubmit('submitDel'.$this->table) OR Tools::isSubmit('delete'.$this->table))
		{
			$deleteForm = '
			<form action="'.htmlentities($_SERVER['REQUEST_URI']).'" method="post">
				<fieldset><legend>'.$this->l('How do you want to delete your customer(s)?').'</legend>
					'.$this->l('You have two ways to delete a customer, please choose what you want to do.').'
					<p>
						<input type="radio" name="deleteMode" value="real" id="deleteMode_real" />
						<label for="deleteMode_real" style="float:none">'.$this->l('I want to delete my customer(s) for real, all data will be removed from the database. A customer with the same e-mail address will be able to register again.').'</label>
					</p>
					<p>
						<input type="radio" name="deleteMode" value="deleted" id="deleteMode_deleted" />
						<label for="deleteMode_deleted" style="float:none">'.$this->l('I don\'t want my customer(s) to register again. The customer(s) will be removed from this list but all data will be kept in the database.').'</label>
					</p>';
			foreach ($_POST as $key => $value)
				if (is_array($value))
					foreach ($value as $val)
						$deleteForm .= '<input type="hidden" name="'.htmlentities($key).'[]" value="'.htmlentities($val).'" />';
				else
					$deleteForm .= '<input type="hidden" name="'.htmlentities($key).'" value="'.htmlentities($value).'" />';
			$deleteForm .= '	<br /><input type="submit" class="button" value="'.$this->l('   Delete   ').'" />
				</fieldset>
			</form>
			<div class="clear">&nbsp;</div>';
		}
		
		if (Tools::getValue('submitAdd'.$this->table))
		{
		 	$groupList = Tools::getValue('groupBox');
		 	
		 	/* Checking fields validity */
			$this->validateRules();
			if (!sizeof($this->_errors))
			{
				$id = (int)(Tools::getValue('id_'.$this->table));
				if (isset($id) AND !empty($id))
				{
					if ($this->tabAccess['edit'] !== '1')
						$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
					else
					{
						$object = new $this->className($id);
						if (Validate::isLoadedObject($object))
						{
							$customer_email = strval(Tools::getValue('email'));
							
							// check if e-mail already used
							if ($customer_email != $object->email)
							{
								$customer = new Customer();
								$customer->getByEmail($customer_email);
								if ($customer->id)
									$this->_errors[] = Tools::displayError('An account already exists for this e-mail address:').' '.$customer_email;
							}
							
							if (!is_array($groupList) OR sizeof($groupList) == 0)
								$this->_errors[] = Tools::displayError('Customer must be in at least one group.');
							else
								if (!in_array(Tools::getValue('id_default_group'), $groupList))
									$this->_errors[] = Tools::displayError('Default customer group must be selected in group box.');
							
							// Updating customer's group
							if (!sizeof($this->_errors))
							{
								$object->cleanGroups();
								if (is_array($groupList) AND sizeof($groupList) > 0)
									$object->addGroups($groupList);
							}
						}
						else
							$this->_errors[] = Tools::displayError('An error occurred while loading object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
					}
				}
				else
				{
					if ($this->tabAccess['add'] === '1')
					{
						$object = new $this->className();
						$this->copyFromPost($object, $this->table);
						if (!$object->add())
							$this->_errors[] = Tools::displayError('An error occurred while creating object.').' <b>'.$this->table.' ('.mysql_error().')</b>';
						elseif (($_POST[$this->identifier] = $object->id /* voluntary */) AND $this->postImage($object->id) AND !sizeof($this->_errors) AND $this->_redirect)
						{
							// Add Associated groups
							$group_list = Tools::getValue('groupBox');
							if (is_array($group_list) && sizeof($group_list) > 0)
								$object->addGroups($group_list, true);
							$parent_id = (int)(Tools::getValue('id_parent', 1));
							// Save and stay on same form
							if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
								Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=3&update'.$this->table.'&token='.$this->token);
							// Save and back to parent
							if (Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent'))
								Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=3&token='.$this->token);
							// Default behavior (save and back)
							Tools::redirectAdmin($currentIndex.($parent_id ? '&'.$this->identifier.'='.$object->id : '').'&conf=3&token='.$this->token);
						}
					}
					else
						$this->_errors[] = Tools::displayError('You do not have permission to add here.');
				}
			}
		}
		elseif (Tools::isSubmit('delete'.$this->table) AND $this->tabAccess['delete'] === '1')
		{
			switch (Tools::getValue('deleteMode'))
			{
				case 'real':
					$this->deleted = false;
					Discount::deleteByIdCustomer((int)(Tools::getValue('id_customer')));
					break;
				case 'deleted':
					$this->deleted = true;
					break;
				default:
					echo $deleteForm;
					if (isset($_POST['delete'.$this->table]))
						unset($_POST['delete'.$this->table]);
					if (isset($_GET['delete'.$this->table]))
						unset($_GET['delete'.$this->table]);
					break;
			}
		}
		elseif (Tools::isSubmit('submitDel'.$this->table) AND $this->tabAccess['delete'] === '1')
		{
			switch (Tools::getValue('deleteMode'))
			{
				case 'real':
					$this->deleted = false;
					foreach (Tools::getValue('customerBox') as $id_customer)
						Discount::deleteByIdCustomer((int)($id_customer));
					break;
				case 'deleted':
					$this->deleted = true;
					break;
				default:
					echo $deleteForm;
					if (isset($_POST['submitDel'.$this->table]))
						unset($_POST['submitDel'.$this->table]);
					if (isset($_GET['submitDel'.$this->table]))
						unset($_GET['submitDel'.$this->table]);
					break;
			}
		}
		elseif (Tools::isSubmit('submitGuestToCustomer') AND Tools::getValue('id_customer'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$customer = new Customer((int)Tools::getValue('id_customer'));
				if (!Validate::isLoadedObject($customer))
					$this->_errors[] = Tools::displayError('This customer does not exist.');
				if ($customer->transformToCustomer(Tools::getValue('id_lang', Configuration::get('PS_LANG_DEFAULT'))))
					Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$customer->id.'&conf=3&token='.$this->token);
				else
					$this->_errors[] = Tools::displayError('An error occurred while updating customer.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		return parent::postProcess();
	}

	public function viewcustomer()
	{
		global $currentIndex, $cookie, $link;

		$irow = 0;
		$configurations = Configuration::getMultiple(array('PS_LANG_DEFAULT', 'PS_CURRENCY_DEFAULT'));
		$defaultLanguage = (int)($configurations['PS_LANG_DEFAULT']);
		$defaultCurrency = (int)($configurations['PS_CURRENCY_DEFAULT']);
		if (!($customer = $this->loadObject()))
			return;
		$customerStats = $customer->getStats();
		$addresses = $customer->getAddresses($defaultLanguage);
		$products = $customer->getBoughtProducts();
		$discounts = Discount::getCustomerDiscounts($defaultLanguage, $customer->id, false, false);
		$orders = Order::getCustomerOrders($customer->id);
		$carts = Cart::getCustomerCarts($customer->id);
		$groups = $customer->getGroups();
		$messages = CustomerThread::getCustomerMessages($customer->id);
		$referrers = Referrer::getReferrers($customer->id);
		if ($totalCustomer = Db::getInstance()->getValue('SELECT SUM(total_paid_real) FROM '._DB_PREFIX_.'orders WHERE id_customer = '.$customer->id.' AND valid = 1'))
		{
			Db::getInstance()->getValue('SELECT SQL_CALC_FOUND_ROWS COUNT(*) FROM '._DB_PREFIX_.'orders WHERE valid = 1 GROUP BY id_customer HAVING SUM(total_paid_real) > '.$totalCustomer);
			$countBetterCustomers = (int)Db::getInstance()->getValue('SELECT FOUND_ROWS()') + 1;
		}
		else
			$countBetterCustomers = '-';

		echo '
		<fieldset style="width:400px;float: left"><div style="float: right"><a href="'.$currentIndex.'&addcustomer&id_customer='.$customer->id.'&token='.$this->token.'"><img src="../img/admin/edit.gif" /></a></div>
			<span style="font-weight: bold; font-size: 14px;">'.$customer->firstname.' '.$customer->lastname.'</span>
			<img src="../img/admin/'.($customer->id_gender == 2 ? 'female' : ($customer->id_gender == 1 ? 'male' : 'unknown')).'.gif" style="margin-bottom: 5px" /><br />
			<a href="mailto:'.$customer->email.'" style="text-decoration: underline; color: blue">'.$customer->email.'</a><br /><br />
			'.$this->l('ID:').' '.sprintf('%06d', $customer->id).'<br />
			'.$this->l('Registration date:').' '.Tools::displayDate($customer->date_add, (int)($cookie->id_lang), true).'<br />
			'.$this->l('Last visit:').' '.($customerStats['last_visit'] ? Tools::displayDate($customerStats['last_visit'], (int)($cookie->id_lang), true) : $this->l('never')).'<br />
			'.($countBetterCustomers != '-' ? $this->l('Rank: #').' '.(int)$countBetterCustomers.'<br />' : '').'
		</fieldset>
		<fieldset style="width:300px;float:left;margin-left:50px">
			<div style="float: right">
				<a href="'.$currentIndex.'&addcustomer&id_customer='.$customer->id.'&token='.$this->token.'"><img src="../img/admin/edit.gif" /></a>
			</div>
			'.$this->l('Newsletter:').' '.($customer->newsletter ? '<img src="../img/admin/enabled.gif" />' : '<img src="../img/admin/disabled.gif" />').'<br />
			'.$this->l('Opt-in:').' '.($customer->optin ? '<img src="../img/admin/enabled.gif" />' : '<img src="../img/admin/disabled.gif" />').'<br />
			'.$this->l('Age:').' '.$customerStats['age'].' '.((!empty($customer->birthday['age'])) ? '('.Tools::displayDate($customer->birthday, (int)($cookie->id_lang)).')' : $this->l('unknown')).'<br /><br />
			'.$this->l('Last update:').' '.Tools::displayDate($customer->date_upd, (int)($cookie->id_lang), true).'<br />
			'.$this->l('Status:').' '.($customer->active ? '<img src="../img/admin/enabled.gif" />' : '<img src="../img/admin/disabled.gif" />');
		if ($customer->isGuest())
			echo '
		    <div>
		  	  '.$this->l('This customer is registered as').' <b>'.$this->l('guest').'</b>
		  	  <form method="POST" action="index.php?tab=AdminCustomers&id_customer='.(int)$customer->id.'&token='.Tools::getAdminTokenLite('AdminCustomers').'">
		  	  	<input type="hidden" name="id_lang" value="'.(int)(sizeof($orders) ? $orders[0]['id_lang'] : Configuration::get('PS_LANG_DEFAULT')).'" />
		  	  	<p class="center"><input class="button" type="submit" name="submitGuestToCustomer" value="'.$this->l('Transform to customer').'" /></p>
		  	  	'.$this->l('This feature generates a random password and sends an e-mail to the customer').'
		  	  </form>
		    </div>
		    ';
		echo '
		</fieldset>
		<div class="clear">&nbsp;</div>';
		
		echo '<fieldset style="height:190px"><legend><img src="../img/admin/cms.gif" /> '.$this->l('Add a private note').'</legend>
			<p>'.$this->l('This note will be displayed to all the employees but not to the customer.').'</p>
			<form action="ajax.php" method="post" onsubmit="saveCustomerNote();return false;" id="customer_note">
				<textarea name="note" id="noteContent" style="width:600px;height:100px" onkeydown="$(\'#submitCustomerNote\').removeAttr(\'disabled\');">'.Tools::htmlentitiesUTF8($customer->note).'</textarea><br />
				<input type="submit" id="submitCustomerNote" class="button" value="'.$this->l('   Save   ').'" style="float:left;margin-top:5px" disabled="disabled" />
				<span id="note_feedback" style="float:left;margin:10px 0 0 10px"></span>
			</form>
		</fieldset>
		<div class="clear">&nbsp;</div>
		<script type="text/javascript">
			function saveCustomerNote()
			{
				$("#note_feedback").html("<img src=\"../img/loader.gif\" />").show();
				var noteContent = $("#noteContent").val();
				$.post("ajax.php", {submitCustomerNote:1,id_customer:'.(int)$customer->id.',note:noteContent}, function (r) {
					$("#note_feedback").html("").hide();
					if (r == "ok")
					{
						$("#note_feedback").html("<b style=\"color:green\">'.addslashes($this->l('Your note has been saved')).'</b>").fadeIn(400);
						$("#submitCustomerNote").attr("disabled", "disabled");
					}
					else if (r == "error:validation")
						$("#note_feedback").html("<b style=\"color:red\">'.addslashes($this->l('Error: your note is not valid')).'</b>").fadeIn(400);
					else if (r == "error:update")
						$("#note_feedback").html("<b style=\"color:red\">'.addslashes($this->l('Error: cannot save your note')).'</b>").fadeIn(400);
					$("#note_feedback").fadeOut(3000);
				});
			}
		</script>';
		
		
		echo '<h2>'.$this->l('Messages').' ('.sizeof($messages).')</h2>';
		if (sizeof($messages))
		{
			echo '
			<table cellspacing="0" cellpadding="0" class="table">
				<tr>
					<th class="center">'.$this->l('Status').'</th>
					<th class="center">'.$this->l('Message').'</th>
					<th class="center">'.$this->l('Sent on').'</th>
				</tr>';
			foreach ($messages AS $message)
				echo '<tr>
					<td>'.$message['status'].'</td>
					<td><a href="index.php?tab=AdminCustomerThreads&id_customer_thread='.(int)($message['id_customer_thread']).'&viewcustomer_thread&token='.Tools::getAdminTokenLite('AdminCustomerThreads').'">'.substr(strip_tags(html_entity_decode($message['message'], ENT_NOQUOTES, 'UTF-8')), 0, 75).'...</a></td>
					<td>'.Tools::displayDate($message['date_add'], (int)($cookie->id_lang), true).'</td>
				</tr>';
			echo '</table>
			<div class="clear">&nbsp;</div>';
		}
		else
			echo $customer->firstname.' '.$customer->lastname.' '.$this->l('has never contacted you.');

		// display hook specified to this page : AdminCustomers
		if (($hook = Module::hookExec('adminCustomers', array('id_customer' => $customer->id))) !== false)
			echo '<div>'.$hook.'</div>';
		echo '<div class="clear">&nbsp;</div>';

		echo '<h2>'.$this->l('Groups').' ('.sizeof($groups).')</h2>';
		if ($groups AND sizeof($groups))
		{
			echo '
			<table cellspacing="0" cellpadding="0" class="table">
				<tr>
					<th class="center">'.$this->l('ID').'</th>
					<th class="center">'.$this->l('Name').'</th>
					<th class="center">'.$this->l('Actions').'</th>
				</tr>';
			$tokenGroups = Tools::getAdminToken('AdminGroups'.(int)(Tab::getIdFromClassName('AdminGroups')).(int)($cookie->id_employee));
			foreach ($groups AS $group)
			{
				$objGroup = new Group($group);
				echo '
				<tr '.($irow++ % 2 ? 'class="alt_row"' : '').' style="cursor: pointer" onclick="document.location = \'?tab=AdminGroups&id_group='.$objGroup->id.'&viewgroup&token='.$tokenGroups.'\'">
					<td class="center">'.$objGroup->id.'</td>
					<td>'.$objGroup->name[$defaultLanguage].'</td>
					<td align="center"><a href="?tab=AdminGroups&id_group='.$objGroup->id.'&viewgroup&token='.$tokenGroups.'"><img src="../img/admin/details.gif" /></a></td>
				</tr>';
			}
			echo '
			</table>';
		}
		echo '<div class="clear">&nbsp;</div>';
		echo '<h2>'.$this->l('Orders').' ('.sizeof($orders).')</h2>';
		if ($orders AND sizeof($orders))
		{
			$totalOK = 0;
			$ordersOK = array();
			$ordersKO = array();
			$tokenOrders = Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee));
			foreach ($orders as $order)
			if ($order['valid'])
			{
				$ordersOK[] = $order;
				$totalOK += $order['total_paid_real'];
			}
			else
				$ordersKO[] = $order;
			$orderHead = '
			<table cellspacing="0" cellpadding="0" class="table float">
				<tr>
					<th class="center">'.$this->l('ID').'</th>
					<th class="center">'.$this->l('Date').'</th>
					<th class="center">'.$this->l('Products').'</th>
					<th class="center">'.$this->l('Total paid').'</th>
					<th class="center">'.$this->l('Payment').'</th>
					<th class="center">'.$this->l('State').'</th>
					<th class="center">'.$this->l('Actions').'</th>
				</tr>';
				$orderFoot = '</table>';
				if ($countOK = sizeof($ordersOK))
				{
					echo '<div style="float:left;margin-right:20px"><h3 style="color:green;font-weight:700">'.$this->l('Valid orders:').' '.$countOK.' '.$this->l('for').' '.Tools::displayPrice($totalOK, new Currency($defaultCurrency)).'</h3>'.$orderHead;
					foreach ($ordersOK AS $order)
						echo '<tr '.($irow++ % 2 ? 'class="alt_row"' : '').' style="cursor: pointer" onclick="document.location = \'?tab=AdminOrders&id_order='.$order['id_order'].'&vieworder&token='.$tokenOrders.'\'">
						<td class="center">'.$order['id_order'].'</td>
							<td>'.Tools::displayDate($order['date_add'], (int)($cookie->id_lang)).'</td>
							<td align="right">'.$order['nb_products'].'</td>
							<td align="right">'.Tools::displayPrice($order['total_paid_real'], new Currency((int)($order['id_currency']))).'</td>
							<td>'.$order['payment'].'</td>
							<td>'.$order['order_state'].'</td>
							<td align="center"><a href="?tab=AdminOrders&id_order='.$order['id_order'].'&vieworder&token='.$tokenOrders.'"><img src="../img/admin/details.gif" /></a></td>
						</tr>';
					echo $orderFoot.'</div>';
				}
				if ($countKO = sizeof($ordersKO))
				{
					echo '<div style="float:left;margin-right:20px"><h3 style="color:red;font-weight:700">'.$this->l('Invalid orders:').' '.$countKO.'</h3>'.$orderHead;
					foreach ($ordersKO AS $order)
						echo '
						<tr '.($irow++ % 2 ? 'class="alt_row"' : '').' style="cursor: pointer" onclick="document.location = \'?tab=AdminOrders&id_order='.$order['id_order'].'&vieworder&token='.$tokenOrders.'\'">
							<td class="center">'.$order['id_order'].'</td>
							<td>'.Tools::displayDate($order['date_add'], (int)($cookie->id_lang)).'</td>
							<td align="right">'.$order['nb_products'].'</td>
							<td align="right">'.Tools::displayPrice($order['total_paid_real'], new Currency((int)($order['id_currency']))).'</td>
							<td>'.$order['payment'].'</td>
							<td>'.$order['order_state'].'</td>
							<td align="center"><a href="?tab=AdminOrders&id_order='.$order['id_order'].'&vieworder&token='.$tokenOrders.'"><img src="../img/admin/details.gif" /></a></td>
						</tr>';
					echo $orderFoot.'</div><div class="clear">&nbsp;</div>';
				}
		}
		else
			echo $customer->firstname.' '.$customer->lastname.' '.$this->l('has not placed any orders yet');
			
		if ($products AND sizeof($products))
		{
			echo '<div class="clear">&nbsp;</div>
			<h2>'.$this->l('Products').' ('.sizeof($products).')</h2>
			<table cellspacing="0" cellpadding="0" class="table">
				<tr>
					<th class="center">'.$this->l('Date').'</th>
					<th class="center">'.$this->l('Name').'</th>
					<th class="center">'.$this->l('Quantity').'</th>
					<th class="center">'.$this->l('Actions').'</th>
				</tr>';
			$tokenOrders = Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee));
			foreach ($products AS $product)
				echo '
				<tr '.($irow++ % 2 ? 'class="alt_row"' : '').' style="cursor: pointer" onclick="document.location = \'?tab=AdminOrders&id_order='.$product['id_order'].'&vieworder&token='.$tokenOrders.'\'">
					<td>'.Tools::displayDate($product['date_add'], (int)($cookie->id_lang), true).'</td>
					<td>'.$product['product_name'].'</td>
					<td align="right">'.$product['product_quantity'].'</td>
					<td align="center"><a href="?tab=AdminOrders&id_order='.$product['id_order'].'&vieworder&token='.$tokenOrders.'"><img src="../img/admin/details.gif" /></a></td>
				</tr>';
			echo '
			</table>';
		}
		echo '<div class="clear">&nbsp;</div>
		<h2>'.$this->l('Addresses').' ('.sizeof($addresses).')</h2>';
		if (sizeof($addresses))
		{
			echo '
			<table cellspacing="0" cellpadding="0" class="table">
				<tr>
					<th>'.$this->l('Company').'</th>
					<th>'.$this->l('Name').'</th>
					<th>'.$this->l('Address').'</th>
					<th>'.$this->l('Country').'</th>
					<th>'.$this->l('Phone number(s)').'</th>
					<th>'.$this->l('Actions').'</th>
				</tr>';
			$tokenAddresses = Tools::getAdminToken('AdminAddresses'.(int)(Tab::getIdFromClassName('AdminAddresses')).(int)($cookie->id_employee));
			foreach ($addresses AS $address)
				echo '
				<tr '.($irow++ % 2 ? 'class="alt_row"' : '').'>
					<td>'.($address['company'] ? $address['company'] : '--').'</td>
					<td>'.$address['firstname'].' '.$address['lastname'].'</td>
					<td>'.$address['address1'].($address['address2'] ? ' '.$address['address2'] : '').' '.$address['postcode'].' '.$address['city'].'</td>
					<td>'.$address['country'].'</td>
					<td>'.($address['phone'] ? ($address['phone'].($address['phone_mobile'] ? '<br />'.$address['phone_mobile'] : '')) : ($address['phone_mobile'] ? '<br />'.$address['phone_mobile'] : '--')).'</td>
					<td align="center">
						<a href="?tab=AdminAddresses&id_address='.$address['id_address'].'&addaddress&token='.$tokenAddresses.'"><img src="../img/admin/edit.gif" /></a>
						<a href="?tab=AdminAddresses&id_address='.$address['id_address'].'&deleteaddress&token='.$tokenAddresses.'"><img src="../img/admin/delete.gif" /></a>
					</td>
				</tr>';
			echo '
			</table>';
		}
		else
			echo $customer->firstname.' '.$customer->lastname.' '.$this->l('has not registered any addresses yet').'.';
		echo '<div class="clear">&nbsp;</div>
		<h2>'.$this->l('Discounts').' ('.sizeof($discounts).')</h2>';
		if (sizeof($discounts))
		{
			echo '
			<table cellspacing="0" cellpadding="0" class="table">
				<tr>
					<th>'.$this->l('ID').'</th>
					<th>'.$this->l('Code').'</th>
					<th>'.$this->l('Type').'</th>
					<th>'.$this->l('Value').'</th>
					<th>'.$this->l('Qty available').'</th>
					<th>'.$this->l('Status').'</th>
					<th>'.$this->l('Actions').'</th>
				</tr>';
			$tokenDiscounts = Tools::getAdminToken('AdminDiscounts'.(int)(Tab::getIdFromClassName('AdminDiscounts')).(int)($cookie->id_employee));
			foreach ($discounts AS $discount)
			{
				echo '
				<tr '.($irow++ % 2 ? 'class="alt_row"' : '').'>
					<td align="center">'.$discount['id_discount'].'</td>
					<td>'.$discount['name'].'</td>
					<td>'.$discount['type'].'</td>
					<td align="right">'.$discount['value'].'</td>
					<td align="center">'.$discount['quantity_for_user'].'</td>
					<td align="center"><img src="../img/admin/'.($discount['active'] ? 'enabled.gif' : 'disabled.gif').'" alt="'.$this->l('Status').'" title="'.$this->l('Status').'" /></td>
					<td align="center">
						<a href="?tab=AdminDiscounts&id_discount='.$discount['id_discount'].'&adddiscount&token='.$tokenDiscounts.'"><img src="../img/admin/edit.gif" /></a>
						<a href="?tab=AdminDiscounts&id_discount='.$discount['id_discount'].'&deletediscount&token='.$tokenDiscounts.'"><img src="../img/admin/delete.gif" /></a>
					</td>
				</tr>';
			}
			echo '
			</table>';

		}
		else
			echo $customer->firstname.' '.$customer->lastname.' '.$this->l('has no discount vouchers').'.';
		echo '<div class="clear">&nbsp;</div>';
		 
		echo '<div style="float:left">
		<h2>'.$this->l('Carts').' ('.sizeof($carts).')</h2>';
		if ($carts AND sizeof($carts))
		{
			echo '
			<table cellspacing="0" cellpadding="0" class="table">
				<tr>
					<th class="center">'.$this->l('ID').'</th>
					<th class="center">'.$this->l('Date').'</th>
					<th class="center">'.$this->l('Total').'</th>
					<th class="center">'.$this->l('Carrier').'</th>
					<th class="center">'.$this->l('Actions').'</th>
				</tr>';
			$tokenCarts = Tools::getAdminToken('AdminCarts'.(int)(Tab::getIdFromClassName('AdminCarts')).(int)($cookie->id_employee));
			foreach ($carts AS $cart)
			{
				$cartI = new Cart((int)($cart['id_cart']));
				$summary = $cartI->getSummaryDetails();
				$currency = new Currency((int)($cart['id_currency']));
				$carrier = new Carrier((int)($cart['id_carrier']));
				echo '
				<tr '.($irow++ % 2 ? 'class="alt_row"' : '').' style="cursor: pointer" onclick="document.location = \'?tab=AdminCarts&id_cart='.$cart['id_cart'].'&viewcart&token='.$tokenCarts.'\'">
					<td class="center">'.sprintf('%06d', $cart['id_cart']).'</td>
					<td>'.Tools::displayDate($cart['date_add'], (int)($cookie->id_lang), true).'</td>
					<td align="right">'.Tools::displayPrice($summary['total_price'], $currency).'</td>
					<td>'.$carrier->name.'</td>
					<td align="center"><a href="index.php?tab=AdminCarts&id_cart='.$cart['id_cart'].'&viewcart&token='.$tokenCarts.'"><img src="../img/admin/details.gif" /></a></td>
				</tr>';
			}
			echo '
			</table>';
		}
		else
			echo $this->l('No cart available').'.';
		echo '</div>';
		
		$interested = Db::getInstance()->ExecuteS('SELECT DISTINCT id_product FROM '._DB_PREFIX_.'cart_product cp INNER JOIN '._DB_PREFIX_.'cart c on c.id_cart = cp.id_cart WHERE c.id_customer = '.(int)$customer->id.' AND cp.id_product NOT IN (
		SELECT product_id FROM '._DB_PREFIX_.'orders o inner join '._DB_PREFIX_.'order_detail od ON o.id_order = od.id_order WHERE o.valid = 1 AND o.id_customer = '.(int)$customer->id.')');
		if (count($interested))
		{
			echo '<div style="float:left;margin-left:20px">
			<h2>'.$this->l('Products').' ('.count($interested).')</h2>
			<table cellspacing="0" cellpadding="0" class="table">';
			foreach ($interested as $p)
			{
				$product = new Product((int)$p['id_product'], false, $cookie->id_lang);
				echo '
				<tr '.($irow++ % 2 ? 'class="alt_row"' : '').' style="cursor: pointer" onclick="document.location = \''.$link->getProductLink((int)$product->id, $product->link_rewrite, Category::getLinkRewrite($product->id_category_default, (int)($cookie->id_lang))).'\'">
					<td>'.(int)$product->id.'</td>
					<td>'.Tools::htmlentitiesUTF8($product->name).'</td>
					<td align="center"><a href="'.$link->getProductLink((int)$product->id, $product->link_rewrite, Category::getLinkRewrite($product->id_category_default, (int)($cookie->id_lang))).'"><img src="../img/admin/details.gif" /></a></td>
				</tr>';
			}
			echo '</table></div>';
		}
		
		echo '<div class="clear">&nbsp;</div>';

		/* Last connections */
        $connections = $customer->getLastConnections();
        if (sizeof($connections))    
        {
            echo '<h2>'.$this->l('Last connections').'</h2>
            <table cellspacing="0" cellpadding="0" class="table">
                <tr>
                    <th style="width: 200px">'.$this->l('Date').'</th>
                    <th style="width: 100px">'.$this->l('Pages viewed').'</th>
                    <th style="width: 100px">'.$this->l('Total time').'</th>
                    <th style="width: 100px">'.$this->l('Origin').'</th>
                    <th style="width: 100px">'.$this->l('IP Address').'</th>
                </tr>';
            foreach ($connections as $connection)
                echo '<tr>
                        <td>'.Tools::displayDate($connection['date_add'], (int)($cookie->id_lang), true).'</td>
                        <td>'.(int)($connection['pages']).'</td>
                        <td>'.$connection['time'].'</td>
                        <td>'.($connection['http_referer'] ? preg_replace('/^www./', '', parse_url($connection['http_referer'], PHP_URL_HOST)) : $this->l('Direct link')).'</td>
                        <td>'.$connection['ipaddress'].'</td>
                    </tr>';
            echo '</table><div class="clear">&nbsp;</div>';
        }
        if (sizeof($referrers))    
        {
            echo '<h2>'.$this->l('Referrers').'</h2>
            <table cellspacing="0" cellpadding="0" class="table">
                <tr>
                    <th style="width: 200px">'.$this->l('Date').'</th>
                    <th style="width: 200px">'.$this->l('Name').'</th>
                </tr>';
            foreach ($referrers as $referrer)
                echo '<tr>
                        <td>'.Tools::displayDate($referrer['date_add'], (int)($cookie->id_lang), true).'</td>
                        <td>'.$referrer['name'].'</td>
                    </tr>';
            echo '</table><div class="clear">&nbsp;</div>';
        }
        echo '<a href="'.$currentIndex.'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" /> '.$this->l('Back to customer list').'</a><br />';
    }

	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;
		
		$birthday = explode('-', $this->getFieldValue($obj, 'birthday'));
		$customer_groups = Tools::getValue('groupBox', $obj->getGroups());
		$groups = Group::getGroups($this->_defaultFormLanguage, true);
		
		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post" autocomplete="off">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/tab-customers.gif" />'.$this->l('Customer').'</legend>
				<label>'.$this->l('Gender:').' </label>
				<div class="margin-form">
					<input type="radio" size="33" name="id_gender" id="gender_1" value="1" '.($this->getFieldValue($obj, 'id_gender') == 1 ? 'checked="checked" ' : '').'/>
					<label class="t" for="gender_1"> '.$this->l('Male').'</label>
					<input type="radio" size="33" name="id_gender" id="gender_2" value="2" '.($this->getFieldValue($obj, 'id_gender') == 2 ? 'checked="checked" ' : '').'/>
					<label class="t" for="gender_2"> '.$this->l('Female').'</label>
					<input type="radio" size="33" name="id_gender" id="gender_3" value="9" '.(($this->getFieldValue($obj, 'id_gender') == 9 OR !$this->getFieldValue($obj, 'id_gender')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="gender_3"> '.$this->l('Unknown').'</label>
				</div>
				<label>'.$this->l('Last name:').' </label>
				<div class="margin-form">
					<input type="text" size="33" name="lastname" value="'.htmlentities($this->getFieldValue($obj, 'lastname'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<span class="hint" name="help_box">'.$this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:<span class="hint-pointer">&nbsp;</span></span>
				</div>
				<label>'.$this->l('First name:').' </label>
				<div class="margin-form">
					<input type="text" size="33" name="firstname" value="'.htmlentities($this->getFieldValue($obj, 'firstname'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<span class="hint" name="help_box">'.$this->l('Forbidden characters:').' 0-9!<>,;?=+()@#"�{}_$%:<span class="hint-pointer">&nbsp;</span></span>
				</div>
				<label>'.$this->l('Password:').' </label>
				<div class="margin-form">
					<input type="password" size="33" name="passwd" value="" /> '.(!$obj->id ? '<sup>*</sup>' : '').'
					<p>'.($obj->id ? $this->l('Leave blank if no change') : $this->l('5 characters min., only letters, numbers, or').' -_').'</p>
				</div>
				<label>'.$this->l('E-mail address:').' </label>
				<div class="margin-form">
					<input type="text" size="33" name="email" value="'.htmlentities($this->getFieldValue($obj, 'email'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
				</div>
				<label>'.$this->l('Birthday:').' </label>';
				$sl_year = ($this->getFieldValue($obj, 'birthday')) ? $birthday[0] : 0;
				$years = Tools::dateYears();
				$sl_month = ($this->getFieldValue($obj, 'birthday')) ? $birthday[1] : 0;
				$months = Tools::dateMonths();
				$sl_day = ($this->getFieldValue($obj, 'birthday')) ? $birthday[2] : 0;
				$days = Tools::dateDays();
				$tab_months = array(
					$this->l('January'),
					$this->l('February'),
					$this->l('March'),
					$this->l('April'),
					$this->l('May'),
					$this->l('June'),
					$this->l('July'),
					$this->l('August'),
					$this->l('September'),
					$this->l('October'),
					$this->l('November'),
					$this->l('December'));
				echo '
					<div class="margin-form">
					<select name="days">
						<option value="">-</option>';
						foreach ($days as $v)
							echo '<option value="'.$v.'" '.($sl_day == $v ? 'selected="selected"' : '').'>'.$v.'</option>';
					echo '
					</select>
					<select name="months">
						<option value="">-</option>';
						foreach ($months as $k => $v)
							echo '<option value="'.$k.'" '.($sl_month == $k ? 'selected="selected"' : '').'>'.$this->l($v).'</option>';
					echo '</select>
					<select name="years">
						<option value="">-</option>';
						foreach ($years as $v)
							echo '<option value="'.$v.'" '.($sl_year == $v ? 'selected="selected"' : '').'>'.$v.'</option>';
					echo '</select>
				</div>';
				echo '<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.($this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.(!$this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Allow or disallow this customer to log in').'</p>
				</div>
				<label>'.$this->l('Newsletter:').' </label>
				<div class="margin-form">
					<input type="radio" name="newsletter" id="newsletter_on" value="1" '.($this->getFieldValue($obj, 'newsletter') ? 'checked="checked" ' : '').'/>
					<label class="t" for="newsletter_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="newsletter" id="newsletter_off" value="0" '.(!$this->getFieldValue($obj, 'newsletter') ? 'checked="checked" ' : '').'/>
					<label class="t" for="newsletter_off"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Customer will receive your newsletter via e-mail').'</p>
				</div>
				<label>'.$this->l('Opt-in:').' </label>
				<div class="margin-form">
					<input type="radio" name="optin" id="optin_on" value="1" '.($this->getFieldValue($obj, 'optin') ? 'checked="checked" ' : '').'/>
					<label class="t" for="optin_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="optin" id="optin_off" value="0" '.(!$this->getFieldValue($obj, 'optin') ? 'checked="checked" ' : '').'/>
					<label class="t" for="optin_off"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Customer will receive your ads via e-mail').'</p>
				</div>
				<label>'.$this->l('Default group:').' </label>
				<div class="margin-form">
					<select name="id_default_group" onchange="checkDefaultGroup(this.value);">';
				foreach ($groups as $group)
					echo '<option value="'.(int)($group['id_group']).'"'.($group['id_group'] == $obj->id_default_group ? ' selected="selected"' : '').'>'.htmlentities($group['name'], ENT_NOQUOTES, 'utf-8').'</option>';
				echo '
					</select>
					<p>'.$this->l('Apply non-cumulative rules (e.g., price, display method, reduction)').'</p>
				</div>
				<label>'.$this->l('Groups:').' </label>
				<div class="margin-form">';
					if (sizeof($groups))
					{
						echo '
					<table cellspacing="0" cellpadding="0" class="table" style="width: 29.5em;">
						<tr>
							<th><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'groupBox[]\', this.checked)" /></th>
							<th>'.$this->l('ID').'</th>
							<th>'.$this->l('Group name').'</th>
						</tr>';
						$irow = 0;
						foreach ($groups as $group)
						{
							echo '
							<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
								<td>'.'<input type="checkbox" name="groupBox[]" class="groupBox" id="groupBox_'.$group['id_group'].'" value="'.$group['id_group'].'" '.(in_array($group['id_group'], $customer_groups) ? 'checked="checked" ' : '').'/></td>
								<td>'.$group['id_group'].'</td>
								<td><label for="groupBox_'.$group['id_group'].'" class="t">'.$group['name'].'</label></td>
							</tr>';
						}
						echo '
					</table>
					<p style="padding:0px; margin:10px 0px 10px 0px;">'.$this->l('Check all the box(es) of groups of which the customer is to be a member').'<sup> *</sup></p>
					';
					} else
						echo '<p>'.$this->l('No group created').'</p>';
				echo '
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}

	public function getList($id_lang, $orderBy = NULL, $orderWay = NULL, $start = 0, $limit = NULL)
	{
		global $cookie;
		return parent::getList((int)($cookie->id_lang), !Tools::getValue($this->table.'Orderby') ? 'date_add' : NULL, !Tools::getValue($this->table.'Orderway') ? 'DESC' : NULL);
	}
	
	public function beforeDelete($object)
	{
		return $object->isUsed();
	}
}


