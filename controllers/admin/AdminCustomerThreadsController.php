<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminCustomerThreadsControllerCore extends AdminController
{
	public function __construct()
	{
		$this->context = Context::getContext();
	 	$this->table = 'customer_thread';
		$this->className = 'CustomerThread';
	 	$this->lang = false;

		$contact_array = array();
		$contacts = Contact::getContacts($this->context->language->id);

		foreach ($contacts as $contact)
			$contact_array[$contact['id_contact']] = $contact['name'];

		$language_array = array();
		$languages = Language::getLanguages();
		foreach ($languages as $language)
			$language_array[$language['id_lang']] = $language['name'];

		$images_array = array(
			'open' => array('src' => 'status_green.png', 'alt' => $this->l('Open')),
			'closed' => array('src' => 'status_red.png', 'alt' => $this->l('Closed')),
			'pending1' => array('src' => 'status_orange.png', 'alt' => $this->l('Pending 1')),
			'pending2' => array('src' => 'status_orange.png', 'alt' => $this->l('Pending 2')),
		);

		$status_array = array();
		foreach ($images_array as $k => $v)
			$status_array[$k] = $v['alt'];

		$this->fields_list = array(
			'id_customer_thread' => array(
				'title' => $this->l('ID'),
				'width' => 25,
			),
			'customer' => array(
				'title' => $this->l('Customer'),
				'width' => 100,
				'filter_key' => 'customer',
				'tmpTableFilter' => true,
			),
			'email' => array(
				'title' => $this->l('Email'),
				'width' => 100,
				'filter_key' => 'a!email',
			),
			'contact' => array(
				'title' => $this->l('Type'),
				'width' => 75,
				'type' => 'select',
				'list' => $contact_array,
				'filter_key' => 'cl!id_contact',
				'filter_type' => 'int',
			),
			'language' => array(
				'title' => $this->l('Language'),
				'width' => 60,
				'type' => 'select',
				'list' => $language_array,
				'filter_key' => 'l!id_lang',
				'filter_type' => 'int',
			),
			'status' => array(
				'title' => $this->l('Status'),
				'width' => 50,
				'type' => 'select',
				'list' => $status_array,
				'icon' => $images_array,
				'align' => 'center',
				'filter_key' => 'a!status',
				'filter_type' => 'string',
			),
			'employee' => array(
				'title' => $this->l('Employee'),
				'width' => 100,
				'filter_key' => 'employee',
				'tmpTableFilter' => true,
			),
			'messages' => array(
				'title' => $this->l('Messages'),
				'width' => 50,
				'filter_key' => 'messages',
				'tmpTableFilter' => true,
				'maxlength' => 40,
			),
			'date_upd' => array(
				'title' => $this->l('Last message'),
				'width' => 90,
				'havingFilter' => true,
			),
		);

	 	$this->bulk_actions = array(
	 		'delete' => array(
	 			'text' => $this->l('Delete selected'),
	 			'confirm' => $this->l('Delete selected items?'),
	 		),
	 	);

		$this->shopLinkType = 'shop';

		$this->fields_options = array(
			'contact' => array(
				'title' =>	$this->l('Contact options'),
				'fields' =>	array(
					'PS_CUSTOMER_SERVICE_FILE_UPLOAD' => array(
							'title' => $this->l('Allow file uploading'),
							'desc' => $this->l('Allow customers to upload files using the contact page.'),
							'type' => 'bool'
						),
					'PS_CUSTOMER_SERVICE_SIGNATURE' => array(
							'title' => $this->l('Default message'),
							'desc' => $this->l('Please fill out the message fields that appear by default when you answer a thread on the customer service page.'),
							'type' => 'textareaLang',
							'lang' => true,
							'rows' => 10,
							'cols' => 40
						)
					),
			),
			'general' => array(
				'title' =>	$this->l('Customer service options'),
				'fields' =>	array(
					'PS_SAV_IMAP_URL' => array(
						'title' => $this->l('IMAP URL'),
						'desc' => $this->l('URL for IMAP server (mail.server.com)'),
						'type' => 'text',
						'size' => 40,
					),
					'PS_SAV_IMAP_PORT' => array(
						'title' => $this->l('IMAP port'),
						'desc' => $this->l('Port to use to connect to IMAP server'),
						'type' => 'text',
						'defaultValue' => 143,
					),
					'PS_SAV_IMAP_USER' => array(
						'title' => $this->l('IMAP user'),
						'desc' => $this->l('User to use to connect to IMAP server'),
						'type' => 'text',
						'size' => 40,
					),
					'PS_SAV_IMAP_PWD' => array(
						'title' => $this->l('IMAP password'),
						'desc' => $this->l('Password used to connect IMAP server'),
						'type' => 'text',
						'size' => 40,
					),
					'PS_SAV_IMAP_DELETE_MSG' => array(
						'title' => $this->l('Delete messages'),
						'desc' => $this->l('Delete messages after sync. If you do not active this option, the sync will take more time.'),
						'type' => 'bool',
					),
					'PS_SAV_IMAP_CREATE_THREADS' => array(
						'title' => $this->l('Create new threads'),
						'desc' => $this->l('Create new threads for unrecognized emails'),
						'type' => 'bool',
					),
					'PS_SAV_IMAP_OPT_NORSH' => array(
						'title' => $this->l('IMAP options').' (/norsh)',
						'type' => 'bool',
						'desc' => $this->l('Do not use RSH or SSH to establish a preauthenticated IMAP sessions.'),
					),
					'PS_SAV_IMAP_OPT_SSL' => array(
						'title' => $this->l('IMAP options').' (/ssl)',
						'type' => 'bool',
						'desc' => $this->l('Use the Secure Socket Layer to encrypt the session.'),
					),
					'PS_SAV_IMAP_OPT_VALIDATE-CERT' => array(
						'title' => $this->l('IMAP options').' (/validate-cert)',
						'type' => 'bool',
						'desc' => $this->l('Validate certificates from the TLS/SSL server'),
					),
					'PS_SAV_IMAP_OPT_NOVALIDATE-CERT' => array(
						'title' => $this->l('IMAP options').' (/novalidate-cert)',
						'type' => 'bool',
						'desc' => $this->l('Do not validate certificates from the TLS/SSL server. This is only needed if a server uses self-signed certificates'),
					),
					'PS_SAV_IMAP_OPT_TLS' => array(
						'title' => $this->l('IMAP options').' (/tls)',
						'type' => 'bool',
						'desc' => $this->l('Force use of start-TLS to encrypt the session, and reject connection to servers that do not support it.'),
					),
					'PS_SAV_IMAP_OPT_NOTLS' => array(
						'title' => $this->l('IMAP options').' (/notls)',
						'type' => 'bool',
						'desc' => $this->l('Do not use start-TLS to encrypt the session, even with servers that support it.'),
					),
				),
				'submit' => array('title' => $this->l('Save'), 'class' => 'button'),
			),
		);

		parent::__construct();
	}

	public function renderList()
	{
	 	$this->addRowAction('view');
	 	$this->addRowAction('delete');

 		$this->_select = '
 			CONCAT(c.`firstname`," ",c.`lastname`) as customer, cl.`name` as contact, l.`name` as language, group_concat(message) as messages,
 			(
				SELECT IFNULL(CONCAT(LEFT(e.`firstname`, 1),". ",e.`lastname`), "--")
				FROM `'._DB_PREFIX_.'customer_message` cm2
				INNER JOIN '._DB_PREFIX_.'employee e
					ON e.`id_employee` = cm2.`id_employee`
				WHERE cm2.id_employee > 0
					AND cm2.`id_customer_thread` = a.`id_customer_thread`
				ORDER BY cm2.`date_add` DESC LIMIT 1
			) as employee';

		$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'customer` c
				ON c.`id_customer` = a.`id_customer`
			LEFT JOIN `'._DB_PREFIX_.'customer_message` cm
				ON cm.`id_customer_thread` = a.`id_customer_thread`
			LEFT JOIN `'._DB_PREFIX_.'lang` l
				ON l.`id_lang` = a.`id_lang`
			LEFT JOIN `'._DB_PREFIX_.'contact_lang` cl
				ON (cl.`id_contact` = a.`id_contact` AND cl.`id_lang` = '.(int)$this->context->language->id.')';

		$this->_group = 'GROUP BY cm.id_customer_thread';

		$contacts = CustomerThread::getContacts();

		$categories = Contact::getCategoriesContacts();

		$params = array(
			$this->l('Total threads') => $all = CustomerThread::getTotalCustomerThreads(),
			$this->l('Threads pending') => $pending = CustomerThread::getTotalCustomerThreads('status LIKE "%pending%"'),
			$this->l('Total number of customer messages') => CustomerMessage::getTotalCustomerMessages('id_employee = 0'),
			$this->l('Total number of employee messages') => CustomerMessage::getTotalCustomerMessages('id_employee != 0'),
			$this->l('Unread threads') => $unread = CustomerThread::getTotalCustomerThreads('status = "open"'),
			$this->l('Closed threads') => $all - ($unread + $pending)
		);

		$this->tpl_list_vars = array(
			'contacts' => $contacts,
			'categories' => $categories,
			'params' => $params
		);

		return parent::renderList();
	}

	public function initToolbar()
	{
		parent::initToolbar();
		unset($this->toolbar_btn['new']);
	}

	public function postProcess()
	{
		if ($id_customer_thread = (int)Tools::getValue('id_customer_thread'))
		{
			if (($id_contact = (int)Tools::getValue('id_contact')))
				Db::getInstance()->execute('
					UPDATE '._DB_PREFIX_.'customer_thread
					SET id_contact = '.(int)$id_contact.'
					WHERE id_customer_thread = '.(int)$id_customer_thread
				);
			if ($id_status = (int)Tools::getValue('setstatus'))
			{
				$status_array = array(1 => 'open', 2 => 'closed', 3 => 'pending1', 4 => 'pending2');
				Db::getInstance()->execute('
					UPDATE '._DB_PREFIX_.'customer_thread
					SET status = "'.$status_array[$id_status].'"
					WHERE id_customer_thread = '.(int)$id_customer_thread.' LIMIT 1
				');
			}
			if (isset($_POST['id_employee_forward']))
			{
				$messages = Db::getInstance()->executeS('
					SELECT ct.*, cm.*, cl.name subject, CONCAT(e.firstname, \' \', e.lastname) employee_name,
						CONCAT(c.firstname, \' \', c.lastname) customer_name, c.firstname
					FROM '._DB_PREFIX_.'customer_thread ct
					LEFT JOIN '._DB_PREFIX_.'customer_message cm
						ON (ct.id_customer_thread = cm.id_customer_thread)
					LEFT JOIN '._DB_PREFIX_.'contact_lang cl
						ON (cl.id_contact = ct.id_contact AND cl.id_lang = '.(int)$this->context->language->id.')
					LEFT OUTER JOIN '._DB_PREFIX_.'employee e
						ON e.id_employee = cm.id_employee
					LEFT OUTER JOIN '._DB_PREFIX_.'customer c
						ON (c.email = ct.email)
					WHERE ct.id_customer_thread = '.(int)Tools::getValue('id_customer_thread').'
					ORDER BY cm.date_add DESC
				');
				$output = '';
				foreach ($messages as $message)
					$output .= $this->displayMessage($message, true, (int)Tools::getValue('id_employee_forward'));

				$cm = new CustomerMessage();
				$cm->id_employee = (int)$this->context->employee->id;
				$cm->id_customer_thread = (int)Tools::getValue('id_customer_thread');
				$cm->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
				$current_employee = $this->context->employee;
				$id_employee = (int)Tools::getValue('id_employee_forward');
				$employee = new Employee($id_employee);
				$email = Tools::getValue('email');
				if ($id_employee && $employee && Validate::isLoadedObject($employee))
				{
					$params = array(
					'{messages}' => Tools::nl2br(stripslashes($output)),
					'{employee}' => $current_employee->firstname.' '.$current_employee->lastname,
					'{comment}' => stripslashes($_POST['message_forward']));

					if (Mail::Send(
						$this->context->language->id,
						'forward_msg',
						Mail::l('Fwd: Customer message', $this->context->language->id),
						$params,
						$employee->email,
						$employee->firstname.' '.$employee->lastname,
						$current_employee->email,
						$current_employee->firstname.' '.$current_employee->lastname,
						null, null, _PS_MAIL_DIR_, true))
					{
						$cm->private = 1;
						$cm->message = $this->l('Message forwarded to').' '.$employee->firstname.' '.$employee->lastname."\n".$this->l('Comment:').' '.$_POST['message_forward'];
						$cm->add();
					}
				}
				elseif ($email && Validate::isEmail($email))
				{
					$params = array(
					'{messages}' => Tools::nl2br(stripslashes($output)),
					'{employee}' => $current_employee->firstname.' '.$current_employee->lastname,
					'{comment}' => stripslashes($_POST['message_forward']));

					if (Mail::Send(
						$this->context->language->id,
						'forward_msg',
						Mail::l('Fwd: Customer message', $this->context->language->id),
						$params, $email, null,
						$current_employee->email, $current_employee->firstname.' '.$current_employee->lastname,
						null, null, _PS_MAIL_DIR_, true))
					{
						$cm->message = $this->l('Message forwarded to').' '.$email."\n".$this->l('Comment:').' '.$_POST['message_forward'];
						$cm->add();
					}
				}
				else
					$this->errors[] = '<div class="alert error">'.Tools::displayError('The email address is invalid.').'</div>';
			}
			if (Tools::isSubmit('submitReply'))
			{
				$ct = new CustomerThread($id_customer_thread);
				$cm = new CustomerMessage();
				$cm->id_employee = (int)$this->context->employee->id;
				$cm->id_customer_thread = $ct->id;
				
				$cm->message = Tools::htmlentitiesutf8(Tools::getValue('reply_message'));
				$cm->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
				if (isset($_FILES) && !empty($_FILES['joinFile']['name']) && $_FILES['joinFile']['error'] != 0)
					$this->errors[] = Tools::displayError('An error occurred during the file upload process.');
				elseif ($cm->add())
				{
					$file_attachment = null;
					if (!empty($_FILES['joinFile']['name']))
					{
						$file_attachment['content'] = file_get_contents($_FILES['joinFile']['tmp_name']);
						$file_attachment['name'] = $_FILES['joinFile']['name'];
						$file_attachment['mime'] = $_FILES['joinFile']['type'];
					}
					$params = array(
						'{reply}' => Tools::nl2br(Tools::getValue('reply_message')),
						'{link}' => Tools::url(
							$this->context->link->getPageLink('contact', true),
							'id_customer_thread='.(int)$ct->id.'&token='.$ct->token
						),
					);
					//#ct == id_customer_thread    #tc == token of thread   <== used in the synchronization imap
					$contact = new Contact((int)$ct->id_contact);
					if (Validate::isLoadedObject($contact))
					{
						$from_name = $contact->name;
						$from_email = $contact->email;
					}
					else
					{
						$from_name = null;
						$from_email = null;
					}
					if (Mail::Send(
						(int)$ct->id_lang,
						'reply_msg',
						sprintf(Mail::l('An answer to your message is available #ct%1$s #tc%2$s', $ct->id_lang), $ct->id, $ct->token),
						$params, Tools::getValue('msg_email'), null, $from_email, $from_name, $file_attachment, null,
						_PS_MAIL_DIR_, true))
					{
						$ct->status = 'closed';
						$ct->update();
					}
					Tools::redirectAdmin(
						self::$currentIndex.'&id_customer_thread='.(int)$id_customer_thread.'&viewcustomer_thread&token='.Tools::getValue('token')
					);
				}
				else
					$this->errors[] = Tools::displayError('An error occurred. Your message was not sent. Please contact your system administrator.');
			}
		}

		return parent::postProcess();
	}

	public function initContent()
	{
		if (isset($_GET['filename']) && file_exists(_PS_UPLOAD_DIR_.$_GET['filename']) && Validate::isFileName($_GET['filename']))
			AdminCustomerThreadsController::openUploadedFile();

		return parent::initContent();
	}

	protected function openUploadedFile()
	{
		$filename = $_GET['filename'];

		$extensions = array(
			'.txt' => 'text/plain',
			'.rtf' => 'application/rtf',
			'.doc' => 'application/msword',
			'.docx'=> 'application/msword',
			'.pdf' => 'application/pdf',
			'.zip' => 'multipart/x-zip',
			'.png' => 'image/png',
			'.jpeg' => 'image/jpeg',
			'.gif' => 'image/gif',
			'.jpg' => 'image/jpeg',
		);

		$extension = false;
		foreach ($extensions as $key => $val)
			if (substr(Tools::strtolower($filename), -4) == $key || substr(Tools::strtolower($filename), -5) == $key)
			{
				$extension = $val;
				break;
			}

		if (!$extension || !Validate::isFileName($filename))
			die(Tools::displayError());
			
		ob_end_clean();
		header('Content-Type: '.$extension);
		header('Content-Disposition:attachment;filename="'.$filename.'"');
		readfile(_PS_UPLOAD_DIR_.$filename);
		die;
	}

	public function renderView()
	{
		if (!$id_customer_thread = (int)Tools::getValue('id_customer_thread'))
			return;

		$this->context = Context::getContext();
		if (!($thread = $this->loadObject()))
			return;
		$this->context->cookie->{'customer_threadFilter_cl!id_contact'} = $thread->id_contact;

		$employees = Employee::getEmployees();

		$messages = CustomerThread::getMessageCustomerThreads($id_customer_thread);

		$next_thread = CustomerThread::getNextThread((int)$thread->id);

		$actions = array();

		if ($next_thread)
			$actions['next_thread'] = array(
				'href' => self::$currentIndex.'&id_customer_thread='.(int)$next_thread.'&viewcustomer_thread&token='.$this->token,
				'name' => $this->l('Reply to the next unanswered message in this category.')
			);
		else
			$actions['next_thread'] = array(
				'href' => false,
				'name' => $this->l('All other messages in this category have been answered.')
			);

		if ($thread->status != 'closed')
			$actions['closed'] = array(
				'href' => self::$currentIndex.'&viewcustomer_thread&setstatus=2&id_customer_thread='.(int)Tools::getValue('id_customer_thread').'&viewmsg&token='.$this->token,
				'name' => $this->l('Mark this message as handled.')
			);

		if ($thread->status != 'pending1')
			$actions['pending1'] = array(
				'href' => self::$currentIndex.'&viewcustomer_thread&setstatus=3&id_customer_thread='.(int)Tools::getValue('id_customer_thread').'&viewmsg&token='.$this->token,
				'name' => $this->l('Mark this message as "pending 1" (will be answered later).')
			);
		else
			$actions['pending1'] = array(
				'href' => self::$currentIndex.'&viewcustomer_thread&setstatus=1&id_customer_thread='.(int)Tools::getValue('id_customer_thread').'&viewmsg&token='.$this->token,
				'name' => $this->l('Disable pending status.')
			);

		if ($thread->status != 'pending2')
			$actions['pending2'] = array(
				'href' => self::$currentIndex.'&viewcustomer_thread&setstatus=4&id_customer_thread='.(int)Tools::getValue('id_customer_thread').'&viewmsg&token='.$this->token,
				'name' => $this->l('Mark this message as "pending 2" (will be answered later).')
			);
		else
			$actions['pending2'] = array(
				'href' => self::$currentIndex.'&viewcustomer_thread&setstatus=1&id_customer_thread='.(int)Tools::getValue('id_customer_thread').'&viewmsg&token='.$this->token,
				'name' => $this->l('Disable pending status.')
			);

		if ($thread->id_customer)
		{
			$customer = new Customer($thread->id_customer);
			$orders = Order::getCustomerOrders($customer->id);
			if ($orders && count($orders))
			{
				$total_ok = 0;
				$orders_ok = array();
				foreach ($orders as $key => $order)
				{
					if ($order['valid'])
					{
						$orders_ok[] = $order;
						$total_ok += $order['total_paid_real'];
					}
					$orders[$key]['date_add'] = Tools::displayDate($order['date_add'], $this->context->language->id);
					$orders[$key]['total_paid_real'] = Tools::displayPrice($order['total_paid_real'], new Currency((int)$order['id_currency']));
				}
			}

			$products = $customer->getBoughtProducts();
			if ($products && count($products))
				foreach ($products as $key => $product)
					$products[$key]['date_add'] = Tools::displayDate($product['date_add'], $this->context->language->id, true);
		}

		foreach ($messages as $key => $message)
			$messages[$key] = $this->displayMessage($message);

		$this->tpl_view_vars = array(
			'id_customer_thread' => $id_customer_thread,
			'thread' => $thread,
			'actions' => $actions,
			'employees' => $employees,
			'messages' => $messages,
			'next_thread' => $next_thread,
			'orders' => isset($orders) ? $orders : false,
			'customer' => isset($customer) ? $customer : false,
			'products' => isset($products) ? $products : false,
			'total_ok' => isset($total_ok) ?  Tools::displayPrice($total_ok, $this->context->currency) : false,
			'orders_ok' => isset($orders_ok) ? $orders_ok : false,
			'count_ok' => isset($orders_ok) ? count($orders_ok) : false
		);

		return parent::renderView();
	}

	protected function displayMessage($message, $email = false, $id_employee = null)
	{
		$tpl = $this->createTemplate('message.tpl');

		$contacts = Contact::getContacts($this->context->language->id);
		foreach ($contacts as $contact)
			$contact_array[$contact['id_contact']] = array('id_contact' => $contact['id_contact'], 'name' => $contact['name']);
		$contacts = $contact_array;
		
		if (!$email)
		{
			if (!empty($message['id_product']) && empty($message['employee_name']))
				$id_order_product = Order::getIdOrderProduct((int)$message['id_customer'], (int)$message['id_product']);
		}
		$message['date_add'] = Tools::displayDate($message['date_add'], $this->context->language->id, true);
		$message['user_agent'] = strip_tags($message['user_agent']);
		$message['message'] = preg_replace(
			'/(https?:\/\/[a-z0-9#%&_=\(\)\.\? \+\-@\/]{6,1000})([\s\n<])/Uui',
			'<a href="\1">\1</a>\2',
			html_entity_decode($message['message'],
			ENT_NOQUOTES, 'UTF-8')
		);

		$tpl->assign(array(
			'current' => self::$currentIndex,
			'token' => $this->token,
			'message' => $message,
			'id_order_product' => isset($id_order_product) ? $id_order_product : null,
			'email' => $email,
			'id_employee' => $id_employee,
			'PS_SHOP_NAME' => Configuration::get('PS_SHOP_NAME'),
			'file_name' => file_exists(_PS_UPLOAD_DIR_.$message['file_name']),
			'contacts' => $contacts,
			'PS_CUSTOMER_SERVICE_SIGNATURE' => str_replace('\r\n', "\n", Configuration::get('PS_CUSTOMER_SERVICE_SIGNATURE', $message['id_lang']))
		));

		return $tpl->fetch();
	}

	protected function displayButton($content)
	{
		return '
		<div style="margin-bottom:10px;border:1px solid #005500;width:200px;height:130px;padding:10px;background:#EFE">
			<p style="text-align:center;font-size:15px;font-weight:bold">
				'.$content.'
			</p>
		</div>';
	}

	public function renderOptions()
	{
		if (Configuration::get('PS_SAV_IMAP_URL')
		&& Configuration::get('PS_SAV_IMAP_PORT')
		&& Configuration::get('PS_SAV_IMAP_USER')
		&& Configuration::get('PS_SAV_IMAP_PWD'))
			$this->tpl_option_vars['use_sync'] = true;
		else
			$this->tpl_option_vars['use_sync'] = false;

		return parent::renderOptions();
	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		$nb_items = count($this->_list);
		for ($i = 0; $i < $nb_items; ++$i)
		{
			if (isset($this->_list[$i]['messages']))
				$this->_list[$i]['messages'] = Tools::htmlentitiesDecodeUTF8($this->_list[$i]['messages']);
		}
	}

	public function updateOptionPsSavImapOpt($value)
	{
		if ($this->tabAccess['edit'] != '1')
			throw new PrestaShopException(Tools::displayError('You do not have permission to edit this.'));

		if (!$this->errors && $value)
			Configuration::updateValue('PS_SAV_IMAP_OPT', implode('', $value));
	}
	
	public function ajaxProcessMarkAsRead()
	{
		if ($this->tabAccess['edit'] != '1')
			throw new PrestaShopException(Tools::displayError('You do not have permission to edit this.'));

		$id_thread = Tools::getValue('id_thread');
		$messages = CustomerThread::getMessageCustomerThreads($id_thread);		
		if (count($messages))
			Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'customer_message set `read` = 1');
	}
	
	public function ajaxProcessSyncImap()
	{
		if ($this->tabAccess['edit'] != '1')
			throw new PrestaShopException(Tools::displayError('You do not have permission to edit this.'));

		if (Tools::isSubmit('syncImapMail'))
		{
			if (!($url = Configuration::get('PS_SAV_IMAP_URL'))
			|| !($port = Configuration::get('PS_SAV_IMAP_PORT'))
			|| !($user = Configuration::get('PS_SAV_IMAP_USER'))
			|| !($password = Configuration::get('PS_SAV_IMAP_PWD')))
			die('{"hasError" : true, "errors" : ["Configuration is not correct"]}');

			$conf = Configuration::getMultiple(array(
				'PS_SAV_IMAP_OPT_NORSH', 'PS_SAV_IMAP_OPT_SSL',
				'PS_SAV_IMAP_OPT_VALIDATE-CERT', 'PS_SAV_IMAP_OPT_NOVALIDATE-CERT',
				'PS_SAV_IMAP_OPT_TLS', 'PS_SAV_IMAP_OPT_NOTLS'));
	
			$conf_str = '';
			if ($conf['PS_SAV_IMAP_OPT_NORSH'])
				$conf_str .= '/norsh';
			if ($conf['PS_SAV_IMAP_OPT_SSL'])
				$conf_str .= '/ssl';
			if ($conf['PS_SAV_IMAP_OPT_VALIDATE-CERT'])
				$conf_str .= '/validate-cert';
			if ($conf['PS_SAV_IMAP_OPT_NOVALIDATE-CERT'])
				$conf_str .= '/novalidate-cert';
			if ($conf['PS_SAV_IMAP_OPT_TLS'])
				$conf_str .= '/tls';
			if ($conf['PS_SAV_IMAP_OPT_NOTLS'])
				$conf_str .= '/notls';

			if (!function_exists('imap_open'))
				die('{"hasError" : true, "errors" : ["imap is not installed on this server"]}');

			$mbox = @imap_open('{'.$url.':'.$port.$conf_str.'}', $user, $password);

			//checks if there is no error when connecting imap server
			$errors = imap_errors();
			$str_errors = '';
			$str_error_delete = '';
			if (sizeof($errors) && is_array($errors))
			{
				$str_errors = '';
				foreach($errors as $error)
					$str_errors .= '"'.$error.'",';
				$str_errors = rtrim($str_errors, ',').'';
			}
			//checks if imap connexion is active
			if (!$mbox)
				die('{"hasError" : true, "errors" : ["Cannot connect to the mailbox:.<br />'.addslashes($str_errors).'"]}');

			//Returns information about the current mailbox. Returns FALSE on failure.
			$check = imap_check($mbox);
			if (!$check)
				die('{"hasError" : true, "errors" : ["Fail to get information about the current mailbox"]}');

			if ($check->Nmsgs == 0)
				die('{"hasError" : true, "errors" : ["NO message to sync"]}');

			$result = imap_fetch_overview($mbox,"1:{$check->Nmsgs}",0);
			foreach ($result as $overview)
			{
				 //check if message exist in database
				 if (isset($overview->subject))
						$subject = $overview->subject;
					else
						$subject = '';
				//Creating an md5 to check if message has been allready processed
				 $md5 = md5($overview->date.$overview->from.$subject.$overview->msgno);
				 $exist = Db::getInstance()->getValue(
						 'SELECT `md5_header`
						 FROM `'._DB_PREFIX_.'customer_message_sync_imap`
						 WHERE `md5_header` = \''.pSQL($md5).'\'');
				 if ($exist)
				 {
					if (Configuration::get('PS_SAV_IMAP_DELETE_MSG'))
						if (!imap_delete($mbox, $overview->msgno))
							$str_error_delete = ', "Fail to delete message"';
				 }
				 else
				 {
				 	//check if subject has id_order
				 	preg_match('/\#ct([0-9]*)/', $subject, $matches1);
				 	preg_match('/\#tc([0-9-a-z-A-Z]*)/', $subject, $matches2);
					$new_ct = (Configuration::get('PS_SAV_IMAP_CREATE_THREADS') && !isset($matches1[1]) && !isset($matches2[1]) && !preg_match('/[no_sync]/', $subject));
					if (isset($matches1[1]) && isset($matches2[1]) || $new_ct)
					{
						if ($new_ct)
						{
							if (!preg_match('/<('.Tools::cleanNonUnicodeSupport('[a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z0-9]+').')>/', $overview->from, $result)
								|| !Validate::isEmail($from = $result[1]))
									continue;
							
							$contacts = Contact::getCategoriesContacts();
							if (!$contacts)
								continue;
							$id_contact = $contacts[0]['id_contact'];
							
							$ct = new CustomerThread();
							$ct->email = $from;
							$ct->id_contact = $id_contact;
							$ct->id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
							$ct->status = 'open';
							$ct->token = Tools::passwdGen(12);
							$ct->add();	
						}
						else
							$ct = new CustomerThread((int)$matches1[1]); //check if order exist in database

						if (Validate::isLoadedObject($ct) && ((isset($matches2[1]) && $ct->token == $matches2[1]) || $new_ct))
						{
							$cm = new CustomerMessage();
							$cm->id_customer_thread = $ct->id;
							$cm->message = imap_fetchbody($mbox, $overview->msgno, 1);
							$cm->add();
						}
					}
					Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'customer_message_sync_imap` (`md5_header`) VALUES (\''.pSQL($md5).'\')');
				}
			}
			imap_expunge($mbox);
			imap_close($mbox);
			die('{"hasError" : false, "errors" : ["'.$str_errors.$str_error_delete.'"]}');
		}
	}
}