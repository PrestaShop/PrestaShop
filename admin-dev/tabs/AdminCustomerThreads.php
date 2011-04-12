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

class AdminCustomerThreads extends AdminTab
{
	public function __construct()
	{
	 	global $cookie;
	 	
	 	$this->table = 'customer_thread';
	 	$this->lang = false;
	 	$this->className = 'CustomerThread';
	 	$this->edit = false; 
	 	$this->view = true; 
	 	$this->delete = true;
		
 		$this->_select = 'CONCAT(c.firstname," ",c.lastname) as customer, cl.name as contact, l.name as language, group_concat(message) as messages, (
			SELECT IFNULL(CONCAT(LEFT(e.firstname, 1),". ",e.lastname), "--")
			FROM '._DB_PREFIX_.'customer_message cm2 INNER JOIN '._DB_PREFIX_.'employee e ON e.id_employee = cm2.id_employee
			WHERE cm2.id_employee > 0 AND cm2.`id_customer_thread` = a.`id_customer_thread`
			ORDER BY cm2.date_add DESC LIMIT 1) as employee';
		$this->_group = 'GROUP BY cm.id_customer_thread';
		$this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON c.`id_customer` = a.`id_customer`
		LEFT JOIN `'._DB_PREFIX_.'customer_message` cm ON cm.`id_customer_thread` = a.`id_customer_thread`
		LEFT JOIN `'._DB_PREFIX_.'lang` l ON l.`id_lang` = a.`id_lang`
		LEFT JOIN `'._DB_PREFIX_.'contact_lang` cl ON (cl.`id_contact` = a.`id_contact` AND cl.`id_lang` = '.(int)$cookie->id_lang.')';
		
		$contactArray = array();
		$contacts = Contact::getContacts($cookie->id_lang);
		foreach ($contacts AS $contact)
			$contactArray[$contact['id_contact']] = $contact['name'];
			
		$languageArray = array();
		$languages = Language::getLanguages();
		foreach ($languages AS $language)
			$languageArray[$language['id_lang']] = $language['name'];
			
		$statusArray = array(
			'open' => $this->l('Open'),
			'closed' => $this->l('Closed'),
			'pending1' => $this->l('Pending 1'),
			'pending2' => $this->l('Pending 2')
		);
		
		$imagesArray = array(
			'open' => 'status_green.gif',
			'closed' => 'status_red.gif',
			'pending1' => 'status_orange.gif',
			'pending2' => 'status_orange.gif'
		);
		
		$this->fieldsDisplay = array(
			'id_customer_thread' => array('title' => $this->l('ID'), 'width' => 25),
			'customer' => array('title' => $this->l('Customer'), 'width' => 100, 'filter_key' => 'customer', 'tmpTableFilter' => true),
			'email' => array('title' => $this->l('E-mail'), 'width' => 100, 'filter_key' => 'a!email'),
			'contact' => array('title' => $this->l('Type'), 'width' => 75, 'type' => 'select', 'select' => $contactArray, 'filter_key' => 'cl!id_contact', 'filter_type' => 'int'),
			'language' => array('title' => $this->l('Language'), 'width' => 60, 'type' => 'select', 'select' => $languageArray, 'filter_key' => 'l!id_lang', 'filter_type' => 'int'),
			'status' => array('title' => $this->l('Status'), 'width' => 50, 'type' => 'select', 'select' => $statusArray, 'icon' => $imagesArray, 'align' => 'center', 'filter_key' => 'a!status', 'filter_type' => 'string'),
			'employee' => array('title' => $this->l('Employee'), 'width' => 100, 'filter_key' => 'employee', 'tmpTableFilter' => true),
			'messages' => array('title' => $this->l('Messages'), 'width' => 50, 'filter_key' => 'messages', 'tmpTableFilter' => true, 'maxlength' => 0),
			'date_upd' => array('title' => $this->l('Last message'), 'width' => 90)
		);

		parent::__construct();
	}
	
	public function postProcess()
	{
		global $currentIndex, $cookie;
		
		if ($id_customer_thread = (int)Tools::getValue('id_customer_thread'))
		{
			if (($id_contact = (int)Tools::getValue('id_contact')))
				Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'customer_thread SET id_contact = '.(int)$id_contact.' WHERE id_customer_thread = '.(int)$id_customer_thread);
			if ($id_status = (int)Tools::getValue('setstatus'))
			{
				$statusArray = array(1 => 'open', 2 => 'closed', 3 => 'pending1', 4 => 'pending2');
				Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'customer_thread SET status = "'.$statusArray[$id_status].'" WHERE id_customer_thread = '.(int)$id_customer_thread.' LIMIT 1');
			}
			if (isset($_POST['id_employee_forward']))
			{
				// Todo: need to avoid doubles 
				$messages = Db::getInstance()->ExecuteS('
				SELECT ct.*, cm.*, cl.name subject, CONCAT(e.firstname, \' \', e.lastname) employee_name, CONCAT(c.firstname, \' \', c.lastname) customer_name, c.firstname
				FROM '._DB_PREFIX_.'customer_thread ct
				LEFT JOIN '._DB_PREFIX_.'customer_message cm ON (ct.id_customer_thread = cm.id_customer_thread)
				LEFT JOIN '._DB_PREFIX_.'contact_lang cl ON (cl.id_contact = ct.id_contact AND cl.id_lang = '.(int)$cookie->id_lang.')
				LEFT OUTER JOIN '._DB_PREFIX_.'employee e ON e.id_employee = cm.id_employee
				LEFT OUTER JOIN '._DB_PREFIX_.'customer c ON (c.email = ct.email)
				WHERE ct.id_customer_thread = '.(int)Tools::getValue('id_customer_thread').'
				ORDER BY cm.date_add DESC');
				$output = '';
				foreach ($messages AS $message)
					$output .= $this->displayMsg($message, true, (int)Tools::getValue('id_employee_forward'));
				
				$cm = new CustomerMessage();
				$cm->id_employee = (int)$cookie->id_employee;
				$cm->id_customer_thread = (int)Tools::getValue('id_customer_thread');
				$cm->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
				$currentEmployee = new Employee($cookie->id_employee);
				if (($id_employee = (int)Tools::getValue('id_employee_forward')) AND ($employee = new Employee($id_employee)) AND Validate::isLoadedObject($employee))
				{
					$params = array(
					'{messages}' => $output,
					'{employee}' => $currentEmployee->firstname.' '.$currentEmployee->lastname,
					'{comment}' => stripslashes($_POST['message_forward']));
					Mail::Send((int)($cookie->id_lang), 'forward_msg', Mail::l('Fwd: Customer message'), $params,
						$employee->email, $employee->firstname.' '.$employee->lastname,
						$currentEmployee->email, $currentEmployee->firstname.' '.$currentEmployee->lastname);
					$cm->message = $this->l('Message forwarded to').' '.$employee->firstname.' '.$employee->lastname."\n".$this->l('Comment:').' '.$_POST['message_forward'];
					$cm->add();
				}
				elseif (($email = Tools::getValue('email')) AND Validate::isEmail($email))
				{
					$params = array(
					'{messages}' => $output,
					'{employee}' => $currentEmployee->firstname.' '.$currentEmployee->lastname,
					'{comment}' => stripslashes($_POST['message_forward']));
					Mail::Send((int)($cookie->id_lang), 'forward_msg', Mail::l('Fwd: Customer message'), $params,
						$email, NULL,
						$currentEmployee->email, $currentEmployee->firstname.' '.$currentEmployee->lastname);
					$cm->message = $this->l('Message forwarded to').' '.$email."\n".$this->l('Comment:').' '.$_POST['message_forward'];
					$cm->add();
				}
				else
					echo '<div class="alert error">'.Tools::displayError('Email invalid.').'</div>';
			}
			if (Tools::isSubmit('submitReply'))
			{
				$ct = new CustomerThread($id_customer_thread);
				$cm = new CustomerMessage();
				$cm->id_employee = (int)$cookie->id_employee;
				$cm->id_customer_thread = $ct->id;
				$cm->message = Tools::htmlentitiesutf8(nl2br2(Tools::getValue('reply_message')));
				$cm->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
				if (isset($_FILES) AND !empty($_FILES['joinFile']['name']) AND $_FILES['joinFile']['error'] != 0)
					$this->_errors[] = Tools::displayError('An error occurred with the file upload.');
				else if ($cm->add())
				{
					$fileAttachment = NULL;
					if (!empty($_FILES['joinFile']['name']))
					{
						$fileAttachment['content'] = file_get_contents($_FILES['joinFile']['tmp_name']);
						$fileAttachment['name'] = $_FILES['joinFile']['name'];
						$fileAttachment['mime'] = $_FILES['joinFile']['type'];
					}
					$params = array(
					'{reply}' => nl2br2(Tools::getValue('reply_message')),
					'{link}' => Tools::getHttpHost(true).__PS_BASE_URI__.'contact-form.php?id_customer_thread='.(int)($ct->id).'&token='.$ct->token);
					Mail::Send($ct->id_lang, 'reply_msg', Mail::l('An answer to your message is available'), $params, Tools::getValue('msg_email'), NULL, NULL, NULL, $fileAttachment);
					$ct->status = 'closed';
					$ct->update();
					Tools::redirectAdmin($currentIndex.'&id_customer_thread='.(int)$id_customer_thread.'&viewcustomer_thread&token='.Tools::getValue('token'));
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred, your message was not sent.  Please contact your system administrator.');
			}
		}

		return parent::postProcess();
	}
	
	public function display()
	{
		global $cookie;

		if (isset($_GET['filename']) AND file_exists(_PS_UPLOAD_DIR_.$_GET['filename']))
			self::openUploadedFile();
		else if (isset($_GET['view'.$this->table]))
			$this->viewcustomer_thread();
		else
		{
			$this->getList((int)$cookie->id_lang, !Tools::getValue($this->table.'Orderby') ? 'date_upd' : NULL, !Tools::getValue($this->table.'Orderway') ? 'DESC' : NULL);
			$this->displayList();
		}
	}
	
	public function displayListHeader($token = NULL)
	{
		global $currentIndex, $cookie;

		$contacts = Db::getInstance()->ExecuteS('
			SELECT cl.*, COUNT(*) as total, (
				SELECT id_customer_thread
				FROM '._DB_PREFIX_.'customer_thread ct2
				WHERE status = "open" AND ct.id_contact = ct2.id_contact
				ORDER BY date_upd ASC
				LIMIT 1			
			) as id_customer_thread
			FROM '._DB_PREFIX_.'customer_thread ct
			LEFT JOIN '._DB_PREFIX_.'contact_lang cl ON (cl.id_contact = ct.id_contact AND cl.id_lang = '.$cookie->id_lang.')
			WHERE ct.status = "open"
			GROUP BY ct.id_contact HAVING COUNT(*) > 0');
		$categories = Db::getInstance()->ExecuteS('
			SELECT cl.*
			FROM '._DB_PREFIX_.'contact ct
			LEFT JOIN '._DB_PREFIX_.'contact_lang cl ON (cl.id_contact = ct.id_contact AND cl.id_lang = '.$cookie->id_lang.')
			WHERE ct.customer_service = 1');
		$dim = count($categories);

		echo '<div style="float:left;border:0;width:640px;">';
		foreach ($categories as $key => $val)
		{
			$totalThread = 0;
			$id_customer_thread = 0;
			foreach ($contacts as $tmp => $tmp2)
				if ($val['id_contact'] == $tmp2['id_contact'])
				{
					$totalThread = $tmp2['total'];
					$id_customer_thread = $tmp2['id_customer_thread'];
					break; 
				}
			echo '<div style="background-color:#EFEFEF;float:left;margin:0 10px 10px 0;width:'.($dim > 6 ? '200' : '300').'px;border:1px solid #CFCFCF" >
					<h3 style="overflow:hidden;line-height:25px;color:#812143;height:25px;margin:0;">&nbsp;'.$val['name'].'</h3>'.
					($dim > 6 ? '' : '<p style="overflow:hidden;line-height:15px;height:45px;margin:0;padding:0 5px;">'.$val['description'].'</p>').
					($totalThread == 0 ? '<h3 style="padding:0 5px;margin:0;height:23px;line-height:23px;background-color:#DEDEDE">'.$this->l('No new message').'</h3>' 
					: '<a href="'.$currentIndex.'&token='.Tools::getValue('token').'&id_customer_thread='.$id_customer_thread.'&viewcustomer_thread" style="padding:0 5px;display:block;height:23px;line-height:23px;border:0;" class="button">'.$totalThread.' '.($totalThread > 1 ? $this->l('new messages'): $this->l('new message')).'</a>').'
				</div>';
		}
		echo '</div>';
		
		$params = array(
			$this->l('Total threads') => $all = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'customer_thread'),
			$this->l('Threads pending') => $pending = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'customer_thread WHERE status LIKE "%pending%"'),
			$this->l('Total customer messages') => Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'customer_message WHERE id_employee = 0'),
			$this->l('Total employee messages') => Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'customer_message WHERE id_employee != 0'),
			$this->l('Threads unread') => $unread = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'customer_thread WHERE status = "open"'),
			$this->l('Threads closed') => $all - ($unread + $pending));

		echo '<div style="float:right;padding 0px;border:1px solid #CFCFCF;width:280px;">
				<h3 class="button" style="margin:0;line-height:23px;height:23px;border:0;padding:0 5px;">'.$this->l('Customer service').' : '.$this->l('Statistics').'</h3>
				<table cellspacing="1" class="table" style="border-collapse:separate;width:280px;border:0">';
		$count = 0;
		foreach ($params as $key => $val)
			echo '<tr '.(++$count % 2 == 0 ? 'class="alt_row"' : '').'><td>'.$key.'</td><td>'.$val.'</td></tr>';
		echo '	</table>
			</div><p class="clear">&nbsp;</p>';
		parent::displayListHeader($token);
	}
	
	private function openUploadedFile()
	{
		$filename = $_GET['filename'];
		
		$extensions = array('.txt' => 'text/plain', '.rtf' => 'application/rtf', '.doc' => 'application/msword', '.docx'=> 'application/msword',
		'.pdf' => 'application/pdf', '.zip' => 'multipart/x-zip', '.png' => 'image/png', '.jpeg' => 'image/jpeg', '.gif' => 'image/gif', '.jpg' => 'image/jpeg');

		$extension = '';
		foreach ($extensions AS $key => $val)
			if (substr($filename, -4) == $key OR substr($filename, -5) == $key)
			{
				$extension = $val;
				break;
			}

		ob_end_clean();
		header('Content-Type: '.$extension);
		header('Content-Disposition:attachment;filename="'.$filename.'"');
		readfile(_PS_UPLOAD_DIR_.$filename);
		die;
	}
	private function displayMsg($message, $email = false, $id_employee = null)
	{
		global $cookie, $currentIndex;

		$customersToken = Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee));
		$contacts = Contact::getContacts($cookie->id_lang);
		
		if (!$email)
		{
			if (!empty($message['id_product']) AND empty($message['employee_name']))
				$id_order_product = Db::getInstance()->getValue('
				SELECT o.id_order
				FROM '._DB_PREFIX_.'orders o
				LEFT JOIN '._DB_PREFIX_.'order_detail od ON o.id_order = od.id_order
				WHERE o.id_customer = '.(int)$message['id_customer'].'
				AND od.product_id = '.(int)$message['id_product'].'
				ORDER BY o.date_add DESC');
			
			$output = '
			<fieldset style="'.(!empty($message['employee_name']) ? 'background: rgb(255,236,242);' : '').'width:600px;margin-top:10px">
				<legend '.(empty($message['employee_name']) ? '' : 'style="background:rgb(255,210,225)"').'>'.(
					!empty($message['employee_name'])
					? '<img src="../img/t/AdminCustomers.gif" alt="'.Configuration::get('PS_SHOP_NAME').'" /> '.Configuration::get('PS_SHOP_NAME').' - '.$message['employee_name']
					: '<img src="'.__PS_BASE_URI__.'img/admin/tab-customers.gif" alt="'.Configuration::get('PS_SHOP_NAME').'" /> '.(
						!empty($message['id_customer'])
						? '<a href="index.php?tab=AdminCustomers&id_customer='.(int)($message['id_customer']).'&viewcustomer&token='.$customersToken.'" title="'.$this->l('View customer').'">'.$message['customer_name'].'</a>'
						: $message['email']
					)
				).'</legend>
				<div style="font-size:11px">'.(
						(!empty($message['id_customer']) AND empty($message['employee_name']))
						? '<b>'.$this->l('Customer ID:').'</b> <a href="index.php?tab=AdminCustomers&id_customer='.(int)($message['id_customer']).'&viewcustomer&token='.$customersToken.'" title="'.$this->l('View customer').'">'.(int)($message['id_customer']).' <img src="../img/admin/search.gif" alt="'.$this->l('view').'" /></a><br />'
						: ''
					).'
					<b>'.$this->l('Sent on:').'</b> '.Tools::displayDate($message['date_add'], (int)($cookie->id_lang), true).'<br />'.(
						empty($message['employee_name'])
						? '<b>'.$this->l('Browser:').'</b> '.strip_tags($message['user_agent']).'<br />'
						: ''
					).(
						(!empty($message['file_name']) AND file_exists(_PS_UPLOAD_DIR_.$message['file_name']))
						? '<b>'.$this->l('File attachment').'</b> <a href="index.php?tab=AdminCustomerThreads&id_customer_thread='.$message['id_customer_thread'].'&viewcustomer_thread&token='.Tools::getAdminToken('AdminCustomerThreads'.(int)(Tab::getIdFromClassName('AdminCustomerThreads')).(int)($cookie->id_employee)).'&filename='.$message['file_name'].'" title="'.$this->l('View file').'"><img src="../img/admin/search.gif" alt="'.$this->l('view').'" /></a><br />'
						: ''
					).(
						(!empty($message['id_order']) AND empty($message['employee_name']))
						? '<b>'.$this->l('Order #').'</b> <a href="index.php?tab=AdminOrders&id_order='.(int)($message['id_order']).'&vieworder&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)).'" title="'.$this->l('View order').'">'.(int)($message['id_order']).' <img src="../img/admin/search.gif" alt="'.$this->l('view').'" /></a><br />'
						: ''
					).(
						(!empty($message['id_product']) AND empty($message['employee_name']))
						? '<b>'.$this->l('Product #').'</b> <a href="index.php?tab=AdminOrders&id_order='.(int)($id_order_product).'&vieworder&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)).'" title="'.$this->l('View order').'">'.(int)($message['id_product']).' <img src="../img/admin/search.gif" alt="'.$this->l('view').'" /></a><br />'
						: ''
					).'<br />
					<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post">
						<b>'.$this->l('Subject:').'</b>
						<input type="hidden" name="id_customer_message" value="'.$message['id_customer_message'].'" />
						<select name="id_contact" onchange="this.form.submit();">';
			foreach ($contacts as $contact)
				$output .= '<option value="'.(int)$contact['id_contact'].'" '.($contact['id_contact'] == $message['id_contact'] ? 'selected="selected"' : '').'>'.Tools::htmlentitiesutf8($contact['name']).'</option>';
			$output .= '</select>
					</form>';
		}
		else
		{
			$output = '<div style="font-size:11px">
			'.($id_employee ? '<a href="'.Tools::getHttpHost(true).$currentIndex.'&token='.Tools::getAdminToken('AdminCustomerThreads'.(int)(Tab::getIdFromClassName('AdminCustomerThreads')).(int)($id_employee)).'&id_customer_thread='.(int)$message['id_customer_thread'].'&viewcustomer_thread">'.$this->l('View this thread').'</a><br />' : '').'
			<b>'.$this->l('Sent by:').'</b> '.(!empty($message['customer_name']) ? $message['customer_name'].' ('.$message['email'].')' : $message['email'])
			.((!empty($message['id_customer']) AND empty($message['employee_name'])) ? '<br /><b>'.$this->l('Customer ID:').'</b> '.(int)($message['id_customer']).'<br />' : '')
			.((!empty($message['id_order']) AND empty($message['employee_name'])) ? '<br /><b>'.$this->l('Order #').':</b> '.(int)($message['id_order']).'<br />' : '')
			.((!empty($message['id_product']) AND empty($message['employee_name'])) ? '<br /><b>'.$this->l('Product #').':</b> '.(int)($message['id_product']).'<br />' : '')
			.'<br /><b>'.$this->l('Subject:').'</b> '.$message['subject'];
		}
		
		$message['message'] = preg_replace('/(https?:\/\/[a-z0-9#%&_=\(\)\.\? \+\-@\/]{6,1000})([\s\n<])/Uui', '<a href="\1">\1</a>\2', html_entity_decode($message['message'], ENT_NOQUOTES, 'UTF-8'));
		$output .= '<br /><br />
			<b>'.$this->l('Thread ID:').'</b> '.(int)$message['id_customer_thread'].'<br />
			<b>'.$this->l('Message ID:').'</b> '.(int)$message['id_customer_message'].'<br />
			<b>'.$this->l('Message:').'</b><br />
			'.$message['message'].'
		</div>';
		
		if (!$email)
		{
			if (empty($message['employee_name']))
				$output .= '
				<p style="text-align:right">
					<button style="font-family: Verdana; font-size: 11px; font-weight:bold; height: 65px; width: 120px;" onclick="$(\'#reply_to_'.(int)($message['id_customer_message']).'\').show(500); $(this).hide();">
						<img src="'.__PS_BASE_URI__.'img/admin/contact.gif" alt="" style="margin-bottom: 5px;" /><br />'.$this->l('Reply to this message').'
					</button>
				</p>
				<div id="reply_to_'.(int)($message['id_customer_message']).'" style="display: none; margin-top: 20px;"">
					<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post" enctype="multipart/form-data">
						<p>'.$this->l('Please type your reply below:').'</p>
						<textarea style="width: 450px; height: 175px;" name="reply_message">'.str_replace('\r\n', "\n", Configuration::get('PS_CUSTOMER_SERVICE_SIGNATURE', $message['id_lang'])).'</textarea>
						<div style="width: 450px; text-align: right; font-style: italic; font-size: 9px; margin-top: 2px;">
							'.$this->l('Your reply will be sent to:').' '.$message['email'].'
						</div>
						<div style="width: 450px; margin-top: 0px;">
							<input type="file" name="joinFile"/>
						<div>
						<div style="width: 450px; text-align: center;">
							<input type="submit" class="button" name="submitReply" value="'.$this->l('Send my reply').'" style="margin-top:20px;" />
							<input type="hidden" name="id_customer_thread" value="'.(int)($message['id_customer_thread']).'" />
							<input type="hidden" name="msg_email" value="'.$message['email'].'" />
						</div>					
					</form>
				</div>';
			$output .= '
			</fieldset>';
		}
		
		return $output;
	}
	
	public function viewcustomer_thread()
	{
		global $cookie, $currentIndex;	
		
		if (!($thread = $this->loadObject()))
			return;
		$cookie->{'customer_threadFilter_cl!id_contact'} = $thread->id_contact;
		
		$employees = Db::getInstance()->ExecuteS('
		SELECT e.id_employee, e.firstname, e.lastname FROM '._DB_PREFIX_.'employee e
		WHERE e.active = 1 ORDER BY e.lastname ASC');

		echo '
		<h2>'.$this->l('Messages').'</h2>
		<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post" enctype="multipart/form-data">
			<p>
				<img src="../img/admin/msg-forward.png" alt="" style="vertical-align: middle;" /> '.$this->l('Forward this discussion to an employee:').' 
				<select name="id_employee_forward" style="vertical-align: middle;" onchange="
					if ($(this).val() >= 0)
						$(\'#message_forward\').show(400);
					else
						$(\'#message_forward\').hide(200);
					if ($(this).val() == 0)
						$(\'#message_forward_email\').show(200);
					else
						$(\'#message_forward_email\').hide(200);
				">
					<option value="-1">'.$this->l('-- Choose --').'</option>
					<option value="0">'.$this->l('Someone else').'</option>';
		foreach ($employees AS $employee)
			echo '	<option value="'.(int)($employee['id_employee']).'">'.substr($employee['firstname'], 0, 1).'. '.$employee['lastname'].'</option>';
		echo '	</select>
				<div id="message_forward_email" style="display:none">
					<b>'.$this->l('E-mail').'</b> <input type="text" name="email" />
				</div>
				<div id="message_forward" style="display:none;margin-bottom:10px">
					<textarea name="message_forward" style="width: 500px; height: 80px; margin-top: 15px;" onclick="if ($(this).val() == \''.addslashes($this->l('You can add a comment here.')).'\') { $(this).val(\'\'); }">'.$this->l('You can add a comment here.').'</textarea><br />
					<input type="Submit" name="submitForward" class="button" value="'.$this->l('Forward this discussion').'" style="margin-top: 10px;" />
				</div>
			</p>
		</form>
		<div class="clear">&nbsp;</div>';
		
		$messages = Db::getInstance()->ExecuteS('
		SELECT ct.*, cm.*, cl.name subject, CONCAT(e.firstname, \' \', e.lastname) employee_name, CONCAT(c.firstname, \' \', c.lastname) customer_name, c.firstname
		FROM '._DB_PREFIX_.'customer_thread ct
		LEFT JOIN '._DB_PREFIX_.'customer_message cm ON (ct.id_customer_thread = cm.id_customer_thread)
		LEFT JOIN '._DB_PREFIX_.'contact_lang cl ON (cl.id_contact = ct.id_contact AND cl.id_lang = '.(int)$cookie->id_lang.')
		LEFT JOIN '._DB_PREFIX_.'employee e ON e.id_employee = cm.id_employee
		LEFT JOIN '._DB_PREFIX_.'customer c ON (IFNULL(ct.id_customer, ct.email) = IFNULL(c.id_customer, c.email))
		WHERE ct.id_customer_thread = '.(int)Tools::getValue('id_customer_thread').'
		ORDER BY cm.date_add DESC');

		echo '<div style="float:right">';

		$nextThread = Db::getInstance()->getValue('
		SELECT id_customer_thread FROM '._DB_PREFIX_.'customer_thread ct
		WHERE ct.status = "open" AND ct.date_upd > (
			SELECT date_add FROM '._DB_PREFIX_.'customer_message
			WHERE (id_employee IS NULL OR id_employee = 0) AND id_customer_thread = '.(int)$thread->id.'
			ORDER BY date_add DESC LIMIT 1
		)
		'.($cookie->{'customer_threadFilter_cl!id_contact'} ? 'AND ct.id_contact = '.(int)$cookie->{'customer_threadFilter_cl!id_contact'} : '').'
		'.($cookie->{'customer_threadFilter_l!id_lang'} ? 'AND ct.id_lang = '.(int)$cookie->{'customer_threadFilter_l!id_lang'} : '').
		' ORDER BY ct.date_upd ASC');
		if ($nextThread)
			echo $this->displayButton('
			<a href="'.$currentIndex.'&id_customer_thread='.(int)$nextThread.'&viewcustomer_thread&token='.$this->token.'">
				<img src="../img/admin/next-msg.png" title="'.$this->l('Go to the oldest next unanswered message').'" style="margin-bottom: 10px;" />
				<br />'.$this->l('Answer to the next unanswered message in this category').' &gt;
			</a>');
		else
			echo $this->displayButton('
			<img src="../img/admin/msg-ok.png" title="'.$this->l('Go to the oldest next unanswered message').'" style="margin-bottom: 10px;" />
			<br />'.$this->l('The other messages in this category have been answered'));

		if ($thread->status != "closed")
			echo $this->displayButton('
			<a href="'.$currentIndex.'&viewcustomer_thread&setstatus=2&id_customer_thread='.Tools::getValue('id_customer_thread').'&viewmsg&token='.$this->token.'">
				<img src="../img/admin/msg-ok.png" style="margin-bottom:10px" />
				<br />'.$this->l('Set this message as handled').'
			</a>');
			
		if ($thread->status != "pending1")
			echo $this->displayButton('
			<a href="'.$currentIndex.'&viewcustomer_thread&setstatus=3&id_customer_thread='.Tools::getValue('id_customer_thread').'&viewmsg&token='.$this->token.'">
				<img src="../img/admin/msg-pending.png" style="margin-bottom:10px" />
				<br />'.$this->l('Declare this message').'<br />'.$this->l('as "pending 1"').'<br />'.$this->l('(will be answered later)').'
			</a>');
		else
			echo $this->displayButton('
			<a href="'.$currentIndex.'&viewcustomer_thread&setstatus=1&id_customer_thread='.Tools::getValue('id_customer_thread').'&viewmsg&token='.$this->token.'">
				<img src="../img/admin/msg-is-pending.png" style="margin-bottom:10px" />
				<br />'.$this->l('Click here to disable pending status').'
			</a>');
			
		if ($thread->status != "pending2")
			echo $this->displayButton('
			<a href="'.$currentIndex.'&viewcustomer_thread&setstatus=4&id_customer_thread='.Tools::getValue('id_customer_thread').'&viewmsg&token='.$this->token.'">
				<img src="../img/admin/msg-pending.png" style="margin-bottom:10px" />
				<br />'.$this->l('Declare this message').'<br />'.$this->l('as "pending 2"').'<br />'.$this->l('(will be answered later)').'
			</a>');
		else
			echo $this->displayButton('
			<a href="'.$currentIndex.'&viewcustomer_thread&setstatus=1&id_customer_thread='.Tools::getValue('id_customer_thread').'&viewmsg&token='.$this->token.'">
				<img src="../img/admin/msg-is-pending.png" style="margin-bottom:10px" />
				<br />'.$this->l('Click here to disable pending status').'
			</a>');
			
		echo '</div>';
		
		if ($thread->id_customer)
		{
			$customer = new Customer($thread->id_customer);
			$products = $customer->getBoughtProducts();
			$orders = Order::getCustomerOrders($customer->id);
			
			echo '<div style="float:left;width:600px">';
			if ($orders AND sizeof($orders))
			{
				$totalOK = 0;
				$ordersOK = array();
				$tokenOrders = Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee));
				foreach ($orders as $order)
					if ($order['valid'])
					{
						$ordersOK[] = $order;
						$totalOK += $order['total_paid_real'];
					}
				if ($countOK = sizeof($ordersOK))
				{
					echo '<div style="float:left;margin-right:20px;">
					<h2>'.$this->l('Orders').'</h2>
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
						$irow = 0;
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
					echo '</table>
					<h3 style="color:green;font-weight:700;margin-top:10px">'.$this->l('Validated Orders:').' '.$countOK.' '.$this->l('for').' '.Tools::displayPrice($totalOK, new Currency(1)).'</h3>
					</div>';
				}
			}
			if ($products AND sizeof($products))
			{
				echo '<div style="float:left;margin-right:20px">
				<h2>'.$this->l('Products').'</h2>
				<table cellspacing="0" cellpadding="0" class="table">
					<tr>
						<th class="center">'.$this->l('Date').'</th>
						<th class="center">'.$this->l('ID').'</th>
						<th class="center">'.$this->l('Name').'</th>
						<th class="center">'.$this->l('Quantity').'</th>
						<th class="center">'.$this->l('Actions').'</th>
					</tr>';
				$irow = 0;
				$tokenOrders = Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee));
				foreach ($products AS $product)
					echo '
					<tr '.($irow++ % 2 ? 'class="alt_row"' : '').' style="cursor: pointer" onclick="document.location = \'?tab=AdminOrders&id_order='.$product['id_order'].'&vieworder&token='.$tokenOrders.'\'">
						<td>'.Tools::displayDate($product['date_add'], (int)($cookie->id_lang), true).'</td>
						<td>'.$product['product_id'].'</td>
						<td>'.$product['product_name'].'</td>
						<td align="right">'.$product['product_quantity'].'</td>
						<td align="center"><a href="?tab=AdminOrders&id_order='.$product['id_order'].'&vieworder&token='.$tokenOrders.'"><img src="../img/admin/details.gif" /></a></td>
					</tr>';
				echo '</table></div>';
			}
			echo '</div>';
		}
		
		echo '<div style="float:left;margin-top:10px">';
		foreach ($messages AS $message)
			echo $this->displayMsg($message);
		echo '</div><div class="clear">&nbsp;</div>';
	}
	
	private function displayButton($content)
	{
		return '
		<div style="margin-bottom:10px;border:1px solid #005500;width:200px;height:130px;padding:10px;background:#EFE">
			<p style="text-align:center;font-size:15px;font-weight:bold">
				'.$content.'
			</p>
		</div>';
	}
}

