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
*  @version  Release: $Revision: 7471 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ContactControllerCore extends FrontController
{
	public $php_self = 'contact';
	public $ssl = true;

	/**
	 * Start forms process
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		if (Tools::isSubmit('submitMessage'))
		{
			$fileAttachment = null;
			if (isset($_FILES['fileUpload']['name']) && !empty($_FILES['fileUpload']['name']) && !empty($_FILES['fileUpload']['tmp_name']))
			{
				$extension = array('.txt', '.rtf', '.doc', '.docx', '.pdf', '.zip', '.png', '.jpeg', '.gif', '.jpg');
				$filename = uniqid().substr($_FILES['fileUpload']['name'], -5);
				$fileAttachment['content'] = file_get_contents($_FILES['fileUpload']['tmp_name']);
				$fileAttachment['name'] = $_FILES['fileUpload']['name'];
				$fileAttachment['mime'] = $_FILES['fileUpload']['type'];
			}
			$message = Tools::getValue('message'); // Html entities is not usefull, iscleanHtml check there is no bad html tags.
			if (!($from = trim(Tools::getValue('from'))) || !Validate::isEmail($from))
				$this->errors[] = Tools::displayError('Invalid e-mail address');
			else if (!$message)
				$this->errors[] = Tools::displayError('Message cannot be blank');
			else if (!Validate::isCleanHtml($message))
				$this->errors[] = Tools::displayError('Invalid message');
			else if (!($id_contact = (int)(Tools::getValue('id_contact'))) || !(Validate::isLoadedObject($contact = new Contact($id_contact, $this->context->language->id))))
				$this->errors[] = Tools::displayError('Please select a subject from the list.');
			else if (!empty($_FILES['fileUpload']['name']) && $_FILES['fileUpload']['error'] != 0)
				$this->errors[] = Tools::displayError('An error occurred during the file upload');
			else if (!empty($_FILES['fileUpload']['name']) && !in_array(substr($_FILES['fileUpload']['name'], -4), $extension) && !in_array(substr($_FILES['fileUpload']['name'], -5), $extension))
				$this->errors[] = Tools::displayError('Bad file extension');
			else
			{
				$customer = $this->context->customer;
				if (!$customer->id)
					$customer->getByEmail($from);

				$contact = new Contact($id_contact, $this->context->language->id);

				if (!((
						($id_customer_thread = (int)Tools::getValue('id_customer_thread'))
						&& (int)Db::getInstance()->getValue('
						SELECT cm.id_customer_thread FROM '._DB_PREFIX_.'customer_thread cm
						WHERE cm.id_customer_thread = '.(int)$id_customer_thread.' AND cm.id_shop = '.(int)$this->context->shop->id.' AND token = \''.pSQL(Tools::getValue('token')).'\'')
					) || (
						$id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($from, (int)Tools::getValue('id_order'))
					)))
				{
					$fields = Db::getInstance()->executeS('
					SELECT cm.id_customer_thread, cm.id_contact, cm.id_customer, cm.id_order, cm.id_product, cm.email
					FROM '._DB_PREFIX_.'customer_thread cm
					WHERE email = \''.pSQL($from).'\' AND cm.id_shop = '.(int)$this->context->shop->id.' AND ('.
						($customer->id ? 'id_customer = '.(int)($customer->id).' OR ' : '').'
						id_order = '.(int)(Tools::getValue('id_order')).')');
					$score = 0;
					foreach ($fields as $key => $row)
					{
						$tmp = 0;
						if ((int)$row['id_customer'] && $row['id_customer'] != $customer->id && $row['email'] != $from)
							continue;
						if ($row['id_order'] != 0 && Tools::getValue('id_order') != $row['id_order'])
							continue;
						if ($row['email'] == $from)
							$tmp += 4;
						if ($row['id_contact'] == $id_contact)
							$tmp++;
						if (Tools::getValue('id_product') != 0 && $row['id_product'] == Tools::getValue('id_product'))
							$tmp += 2;
						if ($tmp >= 5 && $tmp >= $score)
						{
							$score = $tmp;
							$id_customer_thread = $row['id_customer_thread'];
						}
					}
				}
				$old_message = Db::getInstance()->getValue('
					SELECT cm.message FROM '._DB_PREFIX_.'customer_message cm
					LEFT JOIN '._DB_PREFIX_.'customer_thread cc on (cm.id_customer_thread = cc.id_customer_thread)
					WHERE cc.id_customer_thread = '.(int)($id_customer_thread).' AND cc.id_shop = '.(int)$this->context->shop->id.'
					ORDER BY cm.date_add DESC');
				if ($old_message == $message)
				{
					$this->context->smarty->assign('alreadySent', 1);
					$contact->email = '';
					$contact->customer_service = 0;
				}
				if (!empty($contact->email))
				{
					$id_order = (int)Tools::getValue('id_order', 0);
					$order = new Order($id_order);

					$mail_var_list = array(
						'{email}' => $from,
						'{message}' => Tools::nl2br(stripslashes($message)),
						'{id_order}' => $id_order,
						'{order_name}' => $order->getUniqReference(),
						'{attached_file}' => isset($_FILES['fileUpload'], $_FILES['fileUpload']['name']) ? $_FILES['fileUpload']['name'] : ''
					);

					if (Mail::Send($this->context->language->id, 'contact', Mail::l('Message from contact form'),
						$mail_var_list, $contact->email, $contact->name, $from, ($customer->id ? $customer->firstname.' '.$customer->lastname : ''),
								$fileAttachment) &&
							Mail::Send($this->context->language->id, 'contact_form', Mail::l('Your message has been correctly sent'), $mail_var_list, $from))
								$this->context->smarty->assign('confirmation', 1);
					else
						$this->errors[] = Tools::displayError('An error occurred while sending message.');
				}

				if ($contact->customer_service)
				{
					if ((int)$id_customer_thread)
					{
						$ct = new CustomerThread($id_customer_thread);
						$ct->status = 'open';
						$ct->id_lang = (int)$this->context->language->id;
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
						$ct->id_shop = (int)$this->context->shop->id;
						if ($id_order = (int)Tools::getValue('id_order'))
							$ct->id_order = $id_order;
						if ($id_product = (int)Tools::getValue('id_product'))
							$ct->id_product = $id_product;
						$ct->id_contact = (int)($id_contact);
						$ct->id_lang = (int)$this->context->language->id;
						$ct->email = $from;
						$ct->status = 'open';
						$ct->token = Tools::passwdGen(12);
						$ct->add();
					}

					if ($ct->id)
					{
						$cm = new CustomerMessage();
						$cm->id_customer_thread = $ct->id;
						$cm->message = Tools::htmlentitiesUTF8($message);
						if (isset($filename) && rename($_FILES['fileUpload']['tmp_name'], _PS_MODULE_DIR_.'../upload/'.$filename))
							$cm->file_name = $filename;
						$cm->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
						$cm->user_agent = $_SERVER['HTTP_USER_AGENT'];
						if ($cm->add())
						{
							if (empty($contact->email))
								Mail::Send($this->context->language->id, 'contact_form', Mail::l('Your message has been correctly sent'), array('{message}' => stripslashes($message)), $from);
							$this->context->smarty->assign('confirmation', 1);
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
		$this->addCSS(_THEME_CSS_DIR_.'contact-form.css');
		$this->addJS(_THEME_JS_DIR_.'contact-form.js');
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$this->assignOrderList();

		$email = Tools::safeOutput(Tools::getValue('from',
		((isset($this->context->cookie) && isset($this->context->cookie->email) && Validate::isEmail($this->context->cookie->email)) ? $this->context->cookie->email : '')));
		$this->context->smarty->assign(array(
			'errors' => $this->errors,
			'email' => $email,
			'fileupload' => Configuration::get('PS_CUSTOMER_SERVICE_FILE_UPLOAD')
		));


		if ($id_customer_thread = (int)Tools::getValue('id_customer_thread') && $token = Tools::getValue('token'))
		{
			$customerThread = Db::getInstance()->getRow('
				SELECT cm.* 
				FROM '._DB_PREFIX_.'customer_thread cm
				WHERE cm.id_customer_thread = '.(int)$id_customer_thread.' 
				AND cm.id_shop = '.(int)$this->context->shop->id.' 
				AND token = \''.pSQL($token).'\'
			');
			$this->context->smarty->assign('customerThread', $customerThread);
		}
		
		$this->context->smarty->assign(array(
			'contacts' => Contact::getContacts($this->context->language->id),
			'message' => html_entity_decode(Tools::getValue('message'))
		));

		$this->setTemplate(_PS_THEME_DIR_.'contact-form.tpl');
	}

	/**
	 * Assign template vars related to order list and product list ordered by the customer
	 */
	protected function assignOrderList()
	{
		if ($this->context->customer->isLogged())
		{
			$this->context->smarty->assign('isLogged', 1);

			$products = array();
			$orders = array();
			$getOrders = Db::getInstance()->executeS('
				SELECT id_order
				FROM '._DB_PREFIX_.'orders
				WHERE id_customer = '.(int)$this->context->customer->id.' ORDER BY date_add');
			foreach ($getOrders as $row)
			{
				$order = new Order($row['id_order']);
				$date = explode(' ', $order->date_add);
				$orders[$row['id_order']] = Tools::displayDate($date[0], $this->context->language->id);
				$tmp = $order->getProducts();
				foreach ($tmp as $key => $val)
					$products[$row['id_order']][$val['product_id']] = array('value' => $val['product_id'], 'label' => $val['product_name']);
			}

			$order_tab = array();
			foreach ($orders as $key => $val)
				$order_tab[] = array('value' => $key, 'label' => $key.' -- '.$val, 'selected' => (int)(Tools::getValue('id_order')) == $key);
							
			$this->context->smarty->assign('orderList', $order_tab);
			$this->context->smarty->assign('orderedProductList', $products);
		}
	}
}

