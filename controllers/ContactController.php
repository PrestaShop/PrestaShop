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

class ContactControllerCore extends FrontController
{
	public function __construct()
	{
		$this->php_self = 'contact-form.php';
		$this->ssl = true;

		parent::__construct();
	}

	public function preProcess()
	{
		parent::preProcess();

		if (self::$cookie->isLogged())
		{
			self::$smarty->assign('isLogged', 1);
			$customer = new Customer((int)(self::$cookie->id_customer));
			if (!Validate::isLoadedObject($customer))
				die(Tools::displayError('Customer not found'));
			$products = array();
			$orders = array();
			$getOrders = Db::getInstance()->ExecuteS('
				SELECT id_order
				FROM '._DB_PREFIX_.'orders
				WHERE id_customer = '.(int)$customer->id.' ORDER BY date_add');
			foreach ($getOrders as $row)
			{
				$order = new Order($row['id_order']);
				$date = explode(' ', $order->date_add);
				$orders[$row['id_order']] = Tools::displayDate($date[0], self::$cookie->id_lang);
				$tmp = $order->getProducts();
				foreach ($tmp as $key => $val)
					$products[$val['product_id']] = $val['product_name'];
			}

			$orderList = '';
			foreach ($orders as $key => $val)
				$orderList .= '<option value="'.$key.'" '.((int)(Tools::getValue('id_order')) == $key ? 'selected' : '').' >'.$key.' -- '.$val.'</option>';
			$orderedProductList = '';

			foreach ($products as $key => $val)
				$orderedProductList .= '<option value="'.$key.'" '.((int)(Tools::getValue('id_product')) == $key ? 'selected' : '').' >'.$val.'</option>';
			self::$smarty->assign('orderList', $orderList);
			self::$smarty->assign('orderedProductList', $orderedProductList);
		}

		if (Tools::isSubmit('submitMessage'))
		{
			$fileAttachment = NULL;
			if (isset($_FILES['fileUpload']['name']) AND !empty($_FILES['fileUpload']['name']) AND !empty($_FILES['fileUpload']['tmp_name']))
			{
				$extension = array('.txt', '.rtf', '.doc', '.docx', '.pdf', '.zip', '.png', '.jpeg', '.gif', '.jpg');
				$filename = uniqid().substr($_FILES['fileUpload']['name'], -5);
				$fileAttachment['content'] = file_get_contents($_FILES['fileUpload']['tmp_name']);
				$fileAttachment['name'] = $_FILES['fileUpload']['name'];
				$fileAttachment['mime'] = $_FILES['fileUpload']['type'];
			}
			$message = Tools::htmlentitiesUTF8(Tools::getValue('message'));
			if (!($from = trim(Tools::getValue('from'))) OR !Validate::isEmail($from))
				$this->errors[] = Tools::displayError('Invalid e-mail address');
			elseif (!($message = nl2br2($message)))
				$this->errors[] = Tools::displayError('Message cannot be blank');
			elseif (!Validate::isMessage($message))
				$this->errors[] = Tools::displayError('Invalid message');
			elseif (!($id_contact = (int)(Tools::getValue('id_contact'))) OR !(Validate::isLoadedObject($contact = new Contact((int)($id_contact), (int)(self::$cookie->id_lang)))))
				$this->errors[] = Tools::displayError('Please select a subject on the list.');
			elseif (!empty($_FILES['fileUpload']['name']) AND $_FILES['fileUpload']['error'] != 0)
				$this->errors[] = Tools::displayError('An error occurred during the file upload');
			elseif (!empty($_FILES['fileUpload']['name']) AND !in_array(substr($_FILES['fileUpload']['name'], -4), $extension) AND !in_array(substr($_FILES['fileUpload']['name'], -5), $extension))
				$this->errors[] = Tools::displayError('Bad file extension');
			else
			{
				if ((int)(self::$cookie->id_customer))
					$customer = new Customer((int)(self::$cookie->id_customer));
				else
				{
					$customer = new Customer();
					$customer->getByEmail($from);
				}

				$contact = new Contact($id_contact, self::$cookie->id_lang);

				if (!((
						$id_customer_thread = (int)Tools::getValue('id_customer_thread')
						AND (int)Db::getInstance()->getValue('
						SELECT cm.id_customer_thread FROM '._DB_PREFIX_.'customer_thread cm
						WHERE cm.id_customer_thread = '.(int)$id_customer_thread.' AND token = \''.pSQL(Tools::getValue('token')).'\'')
					) OR (
						$id_customer_thread = (int)Db::getInstance()->getValue('
						SELECT cm.id_customer_thread FROM '._DB_PREFIX_.'customer_thread cm
						WHERE cm.email = \''.pSQL($from).'\' AND cm.id_order = '.(int)(Tools::getValue('id_order')).'')
					)))
				{
					$fields = Db::getInstance()->ExecuteS('
					SELECT cm.id_customer_thread, cm.id_contact, cm.id_customer, cm.id_order, cm.id_product, cm.email
					FROM '._DB_PREFIX_.'customer_thread cm
					WHERE email = \''.pSQL($from).'\' AND ('.
						($customer->id ? 'id_customer = '.(int)($customer->id).' OR ' : '').'
						id_order = '.(int)(Tools::getValue('id_order')).')');
					$score = 0;
					foreach ($fields as $key => $row)
					{
						$tmp = 0;
						if ((int)$row['id_customer'] AND $row['id_customer'] != $customer->id AND $row['email'] != $from)
							continue;
						if ($row['id_order'] != 0 AND Tools::getValue('id_order') != $row['id_order'])
							continue;
						if ($row['email'] == $from)
							$tmp += 4;
						if ($row['id_contact'] == $id_contact)
							$tmp++;
						if (Tools::getValue('id_product') != 0 AND $row['id_product'] ==  Tools::getValue('id_product'))
							$tmp += 2;
						if ($tmp >= 5 AND $tmp >= $score)
						{
							$score = $tmp;
							$id_customer_thread = $row['id_customer_thread'];
						}
					}
				}
				$old_message = Db::getInstance()->getValue('
					SELECT cm.message FROM '._DB_PREFIX_.'customer_message cm
					WHERE cm.id_customer_thread = '.(int)($id_customer_thread).'
					ORDER BY date_add DESC');
				if ($old_message == htmlentities($message, ENT_COMPAT, 'UTF-8'))
				{
					self::$smarty->assign('alreadySent', 1);
					$contact->email = '';
					$contact->customer_service = 0;
				}
				if (!empty($contact->email))
				{
					if (Mail::Send((int)(self::$cookie->id_lang), 'contact', Mail::l('Message from contact form'), array('{email}' => $from, '{message}' => stripslashes($message)), $contact->email, $contact->name, $from, ((int)(self::$cookie->id_customer) ? $customer->firstname.' '.$customer->lastname : ''), $fileAttachment)
						AND Mail::Send((int)(self::$cookie->id_lang), 'contact_form', Mail::l('Your message has been correctly sent'), array('{message}' => stripslashes($message)), $from))
						self::$smarty->assign('confirmation', 1);
					else
						$this->errors[] = Tools::displayError('An error occurred while sending message.');
				}

				if ($contact->customer_service)
				{
					if ((int)$id_customer_thread)
					{
						$ct = new CustomerThread($id_customer_thread);
						$ct->status = 'open';
						$ct->id_lang = (int)self::$cookie->id_lang;
						$ct->id_contact = (int)($id_contact);
						if ($id_order = (int)Tools::getValue('id_order'))
							$ct->id_order = $id_order;
						if ($id_product = (int)Tools::getValue('id_product'))
							$ct->id_product = $id_product;
						$ct->update();
					}
					else
					{
						$ct = new CustomerThread();
						if (isset($customer->id))
							$ct->id_customer = (int)($customer->id);
						if ($id_order = (int)Tools::getValue('id_order'))
							$ct->id_order = $id_order;
						if ($id_product = (int)Tools::getValue('id_product'))
							$ct->id_product = $id_product;
						$ct->id_contact = (int)($id_contact);
						$ct->id_lang = (int)self::$cookie->id_lang;
						$ct->email = $from;
						$ct->status = 'open';
						$ct->token = Tools::passwdGen(12);
						$ct->add();
					}

					if ($ct->id)
					{
						$cm = new CustomerMessage();
						$cm->id_customer_thread = $ct->id;
						$cm->message = htmlentities($message, ENT_COMPAT, 'UTF-8');
						if (isset($filename) AND rename($_FILES['fileUpload']['tmp_name'], _PS_MODULE_DIR_.'../upload/'.$filename))
							$cm->file_name = $filename;
						$cm->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
						$cm->user_agent = $_SERVER['HTTP_USER_AGENT'];
						if ($cm->add())
						{
							if (empty($contact->email))
								Mail::Send((int)(self::$cookie->id_lang), 'contact_form', Mail::l('Your message has been correctly sent'), array('{message}' => stripslashes($message)), $from);
							self::$smarty->assign('confirmation', 1);
						}
						else
							$this->errors[] = Tools::displayError('An error occurred while sending message.');
					}
					else
						$this->errors[] = Tools::displayError('An error occurred while sending message.');
				}
				if (count($this->errors) > 1)
					array_unique($this->errors);
			}
		}
	}

	public function setMedia()
	{
		parent::setMedia();
		Tools::addCSS(_THEME_CSS_DIR_.'contact-form.css');
	}

	public function process()
	{
		parent::process();

		$email = Tools::safeOutput(Tools::getValue('from', ((isset(self::$cookie) AND isset(self::$cookie->email) AND Validate::isEmail(self::$cookie->email)) ? self::$cookie->email : '')));
		self::$smarty->assign(array(
			'errors' => $this->errors,
			'email' => $email,
			'fileupload' => Configuration::get('PS_CUSTOMER_SERVICE_FILE_UPLOAD')
		));


		if ($id_customer_thread = (int)Tools::getValue('id_customer_thread') AND $token = Tools::getValue('token'))
		{
			$customerThread = Db::getInstance()->getRow('
			SELECT cm.* FROM '._DB_PREFIX_.'customer_thread cm
			WHERE cm.id_customer_thread = '.(int)$id_customer_thread.' AND token = \''.pSQL($token).'\'');
			self::$smarty->assign('customerThread', $customerThread);
		}

		self::$smarty->assign(array('contacts' => Contact::getContacts((int)(self::$cookie->id_lang)),
		'message' => html_entity_decode(Tools::getValue('message'))
		));
	}

	public function displayContent()
	{
		$_POST = array_merge($_POST, $_GET);
		parent::displayContent();
		self::$smarty->display(_PS_THEME_DIR_.'contact-form.tpl');
	}
}

