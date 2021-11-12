<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * @property CustomerThread $object
 */
class AdminCustomerThreadsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'customer_thread';
        $this->className = 'CustomerThread';
        $this->lang = false;

        $contact_array = [];
        $contacts = Contact::getContacts($this->context->language->id);

        foreach ($contacts as $contact) {
            $contact_array[$contact['id_contact']] = $contact['name'];
        }

        $language_array = [];
        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            $language_array[$language['id_lang']] = $language['name'];
        }

        parent::__construct();

        $icon_array = [
            'open' => ['class' => 'icon-circle text-success', 'alt' => $this->trans('Open', [], 'Admin.Catalog.Feature')],
            'closed' => ['class' => 'icon-circle text-danger', 'alt' => $this->trans('Closed', [], 'Admin.Catalog.Feature')],
            'pending1' => ['class' => 'icon-circle text-warning', 'alt' => $this->trans('Pending 1', [], 'Admin.Catalog.Feature')],
            'pending2' => ['class' => 'icon-circle text-warning', 'alt' => $this->trans('Pending 2', [], 'Admin.Catalog.Feature')],
        ];

        $status_array = [];
        foreach ($icon_array as $k => $v) {
            $status_array[$k] = $v['alt'];
        }

        $this->fields_list = [
            'id_customer_thread' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'customer' => [
                'title' => $this->trans('Customer', [], 'Admin.Global'),
                'filter_key' => 'customer',
                'tmpTableFilter' => true,
            ],
            'email' => [
                'title' => $this->trans('Email', [], 'Admin.Global'),
                'filter_key' => 'a!email',
            ],
            'contact' => [
                'title' => $this->trans('Type', [], 'Admin.Catalog.Feature'),
                'type' => 'select',
                'list' => $contact_array,
                'filter_key' => 'cl!id_contact',
                'filter_type' => 'int',
            ],
            'language' => [
                'title' => $this->trans('Language', [], 'Admin.Global'),
                'type' => 'select',
                'list' => $language_array,
                'filter_key' => 'l!id_lang',
                'filter_type' => 'int',
            ],
            'status' => [
                'title' => $this->trans('Status', [], 'Admin.Global'),
                'type' => 'select',
                'list' => $status_array,
                'icon' => $icon_array,
                'align' => 'center',
                'filter_key' => 'a!status',
                'filter_type' => 'string',
            ],
            'employee' => [
                'title' => $this->trans('Employee', [], 'Admin.Global'),
                'filter_key' => 'employee',
                'tmpTableFilter' => true,
            ],
            'messages' => [
                'title' => $this->trans('Messages', [], 'Admin.Catalog.Feature'),
                'filter_key' => 'messages',
                'tmpTableFilter' => true,
                'maxlength' => 40,
            ],
            'private' => [
                'title' => $this->trans('Private', [], 'Admin.Catalog.Feature'),
                'type' => 'select',
                'filter_key' => 'private',
                'align' => 'center',
                'cast' => 'intval',
                'callback' => 'printOptinIcon',
                'list' => [
                    '0' => $this->trans('No', [], 'Admin.Global'),
                    '1' => $this->trans('Yes', [], 'Admin.Global'),
                ],
            ],
            'date_upd' => [
                'title' => $this->trans('Last message', [], 'Admin.Catalog.Feature'),
                'havingFilter' => true,
                'type' => 'datetime',
            ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->shopLinkType = 'shop';

        $this->fields_options = [
            'contact' => [
                'title' => $this->trans('Contact options', [], 'Admin.Catalog.Feature'),
                'fields' => [
                    'PS_CUSTOMER_SERVICE_FILE_UPLOAD' => [
                        'title' => $this->trans('Allow file uploading', [], 'Admin.Catalog.Feature'),
                        'hint' => $this->trans('Allow customers to upload files using the contact page.', [], 'Admin.Catalog.Help'),
                        'type' => 'bool',
                    ],
                    'PS_CUSTOMER_SERVICE_SIGNATURE' => [
                        'title' => $this->trans('Default message', [], 'Admin.Catalog.Feature'),
                        'hint' => $this->trans('Please fill out the message fields that appear by default when you answer a thread on the customer service page.', [], 'Admin.Catalog.Help'),
                        'type' => 'textareaLang',
                        'lang' => true,
                    ],
                ],
                'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
            ],
            'general' => [
                'title' => $this->trans('Customer service options', [], 'Admin.Catalog.Feature'),
                'fields' => [
                    'PS_SAV_IMAP_URL' => [
                        'title' => $this->trans('IMAP URL', [], 'Admin.Catalog.Feature'),
                        'hint' => $this->trans('URL for your IMAP server (ie.: mail.server.com).', [], 'Admin.Catalog.Help'),
                        'type' => 'text',
                        'validation' => 'isValidImapUrl',
                    ],
                    'PS_SAV_IMAP_PORT' => [
                        'title' => $this->trans('IMAP port', [], 'Admin.Catalog.Feature'),
                        'hint' => $this->trans('Port to use to connect to your IMAP server.', [], 'Admin.Catalog.Help'),
                        'type' => 'text',
                        'defaultValue' => 143,
                    ],
                    'PS_SAV_IMAP_USER' => [
                        'title' => $this->trans('IMAP user', [], 'Admin.Catalog.Feature'),
                        'hint' => $this->trans('User to use to connect to your IMAP server.', [], 'Admin.Catalog.Help'),
                        'type' => 'text',
                    ],
                    'PS_SAV_IMAP_PWD' => [
                        'title' => $this->trans('IMAP password', [], 'Admin.Catalog.Feature'),
                        'hint' => $this->trans('Password to use to connect your IMAP server.', [], 'Admin.Catalog.Help'),
                        'type' => 'password',
                    ],
                    'PS_SAV_IMAP_DELETE_MSG' => [
                        'title' => $this->trans('Delete messages', [], 'Admin.Catalog.Feature'),
                        'hint' => $this->trans('Delete messages after synchronization. If you do not enable this option, the synchronization will take more time.', [], 'Admin.Catalog.Help'),
                        'type' => 'bool',
                    ],
                    'PS_SAV_IMAP_CREATE_THREADS' => [
                        'title' => $this->trans('Create new threads', [], 'Admin.Catalog.Feature'),
                        'hint' => $this->trans('Create new threads for unrecognized emails.', [], 'Admin.Catalog.Help'),
                        'type' => 'bool',
                    ],
                    'PS_SAV_IMAP_OPT_POP3' => [
                        'title' => $this->trans('IMAP options', [], 'Admin.Catalog.Feature') . ' (/pop3)',
                        'hint' => $this->trans('Use POP3 instead of IMAP.', [], 'Admin.Catalog.Help'),
                        'type' => 'bool',
                    ],
                    'PS_SAV_IMAP_OPT_NORSH' => [
                        'title' => $this->trans('IMAP options', [], 'Admin.Catalog.Feature') . ' (/norsh)',
                        'type' => 'bool',
                        'hint' => $this->trans('Do not use RSH or SSH to establish a preauthenticated IMAP sessions.', [], 'Admin.Catalog.Help'),
                    ],
                    'PS_SAV_IMAP_OPT_SSL' => [
                        'title' => $this->trans('IMAP options', [], 'Admin.Catalog.Feature') . ' (/ssl)',
                        'type' => 'bool',
                        'hint' => $this->trans('Use the Secure Socket Layer (TLS/SSL) to encrypt the session.', [], 'Admin.Catalog.Help'),
                    ],
                    'PS_SAV_IMAP_OPT_VALIDATE-CERT' => [
                        'title' => $this->trans('IMAP options', [], 'Admin.Catalog.Feature') . ' (/validate-cert)',
                        'type' => 'bool',
                        'hint' => $this->trans('Validate certificates from the TLS/SSL server.', [], 'Admin.Catalog.Help'),
                    ],
                    'PS_SAV_IMAP_OPT_NOVALIDATE-CERT' => [
                        'title' => $this->trans('IMAP options', [], 'Admin.Catalog.Feature') . ' (/novalidate-cert)',
                        'type' => 'bool',
                        'hint' => $this->trans('Do not validate certificates from the TLS/SSL server. This is only needed if a server uses self-signed certificates.', [], 'Admin.Catalog.Help'),
                    ],
                    'PS_SAV_IMAP_OPT_TLS' => [
                        'title' => $this->trans('IMAP options', [], 'Admin.Catalog.Feature') . ' (/tls)',
                        'type' => 'bool',
                        'hint' => $this->trans('Force use of start-TLS to encrypt the session, and reject connection to servers that do not support it.', [], 'Admin.Catalog.Help'),
                    ],
                    'PS_SAV_IMAP_OPT_NOTLS' => [
                        'title' => $this->trans('IMAP options', [], 'Admin.Catalog.Feature') . ' (/notls)',
                        'type' => 'bool',
                        'hint' => $this->trans('Do not use start-TLS to encrypt the session, even with servers that support it.', [], 'Admin.Catalog.Help'),
                    ],
                ],
                'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
            ],
        ];
    }

    public function renderList()
    {
        // Check the new IMAP messages before rendering the list
        $this->renderProcessSyncImap();

        $this->addRowAction('view');
        $this->addRowAction('delete');

        $this->_select = '
			CONCAT(c.`firstname`," ",c.`lastname`) as customer, cl.`name` as contact, l.`name` as language, group_concat(cm.`message`) as messages, cm.private,
			(
				SELECT IFNULL(CONCAT(LEFT(e.`firstname`, 1),". ",e.`lastname`), "--")
				FROM `' . _DB_PREFIX_ . 'customer_message` cm2
				INNER JOIN ' . _DB_PREFIX_ . 'employee e
					ON e.`id_employee` = cm2.`id_employee`
				WHERE cm2.id_employee > 0
					AND cm2.`id_customer_thread` = a.`id_customer_thread`
				ORDER BY cm2.`date_add` DESC LIMIT 1
			) as employee';

        $this->_join = '
			LEFT JOIN `' . _DB_PREFIX_ . 'customer` c
				ON c.`id_customer` = a.`id_customer`
			LEFT JOIN `' . _DB_PREFIX_ . 'customer_message` cm
				ON cm.`id_customer_thread` = a.`id_customer_thread`
			LEFT JOIN `' . _DB_PREFIX_ . 'lang` l
				ON l.`id_lang` = a.`id_lang`
			LEFT JOIN `' . _DB_PREFIX_ . 'contact_lang` cl
				ON (cl.`id_contact` = a.`id_contact` AND cl.`id_lang` = ' . (int) $this->context->language->id . ')';

        if ($id_order = Tools::getValue('id_order')) {
            $this->_where .= ' AND id_order = ' . (int) $id_order;
        }

        $this->_group = 'GROUP BY cm.id_customer_thread';
        $this->_orderBy = 'id_customer_thread';
        $this->_orderWay = 'DESC';

        $contacts = CustomerThread::getContacts();

        $categories = Contact::getCategoriesContacts();

        $params = [
            $this->trans('Total threads', [], 'Admin.Catalog.Feature') => $all = CustomerThread::getTotalCustomerThreads(),
            $this->trans('Threads pending', [], 'Admin.Catalog.Feature') => $pending = CustomerThread::getTotalCustomerThreads('status LIKE "%pending%"'),
            $this->trans('Total number of customer messages', [], 'Admin.Catalog.Feature') => CustomerMessage::getTotalCustomerMessages('id_employee = 0'),
            $this->trans('Total number of employee messages', [], 'Admin.Catalog.Feature') => CustomerMessage::getTotalCustomerMessages('id_employee != 0'),
            $this->trans('Unread threads', [], 'Admin.Catalog.Feature') => $unread = CustomerThread::getTotalCustomerThreads('status = "open"'),
            $this->trans('Closed threads', [], 'Admin.Catalog.Feature') => $all - ($unread + $pending),
        ];

        $this->tpl_list_vars = [
            'contacts' => $contacts,
            'categories' => $categories,
            'params' => $params,
        ];

        return parent::renderList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function printOptinIcon($value, $customer)
    {
        return $value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>';
    }

    public function postProcess()
    {
        if ($id_customer_thread = (int) Tools::getValue('id_customer_thread')) {
            if (($id_contact = (int) Tools::getValue('id_contact'))) {
                $result = Db::getInstance()->execute(
                    '
					UPDATE ' . _DB_PREFIX_ . 'customer_thread
					SET id_contact = ' . $id_contact . '
					WHERE id_customer_thread = ' . $id_customer_thread
                );
                if ($result) {
                    $this->object->id_contact = $id_contact;
                }
            }
            if ($id_status = (int) Tools::getValue('setstatus')) {
                $status_array = [1 => 'open', 2 => 'closed', 3 => 'pending1', 4 => 'pending2'];
                $result = Db::getInstance()->execute('
					UPDATE ' . _DB_PREFIX_ . 'customer_thread
					SET status = "' . $status_array[$id_status] . '"
					WHERE id_customer_thread = ' . $id_customer_thread . ' LIMIT 1
				');
                if ($result) {
                    $this->object->status = $status_array[$id_status];
                }
            }
            if (isset($_POST['id_employee_forward'])) {
                $messages = Db::getInstance()->getRow('
					SELECT ct.*, cm.*, cl.name subject, CONCAT(e.firstname, \' \', e.lastname) employee_name,
						CONCAT(c.firstname, \' \', c.lastname) customer_name, c.firstname
					FROM ' . _DB_PREFIX_ . 'customer_thread ct
					LEFT JOIN ' . _DB_PREFIX_ . 'customer_message cm
						ON (ct.id_customer_thread = cm.id_customer_thread)
					LEFT JOIN ' . _DB_PREFIX_ . 'contact_lang cl
						ON (cl.id_contact = ct.id_contact AND cl.id_lang = ' . (int) $this->context->language->id . ')
					LEFT OUTER JOIN ' . _DB_PREFIX_ . 'employee e
						ON e.id_employee = cm.id_employee
					LEFT OUTER JOIN ' . _DB_PREFIX_ . 'customer c
						ON (c.email = ct.email)
					WHERE ct.id_customer_thread = ' . (int) Tools::getValue('id_customer_thread') . '
					ORDER BY cm.date_add DESC
				');
                $output = $this->displayMessage($messages, true, (int) Tools::getValue('id_employee_forward'));
                $cm = new CustomerMessage();
                $cm->id_employee = (int) $this->context->employee->id;
                $cm->id_customer_thread = (int) Tools::getValue('id_customer_thread');
                $cm->ip_address = (int) ip2long(Tools::getRemoteAddr());
                $current_employee = $this->context->employee;
                $id_employee = (int) Tools::getValue('id_employee_forward');
                $employee = new Employee($id_employee);
                $email = Tools::getValue('email');
                $message = Tools::getValue('message_forward');
                if (($error = $cm->validateField('message', $message, null, [], true)) !== true) {
                    $this->errors[] = $error;
                } elseif ($id_employee && $employee && Validate::isLoadedObject($employee)) {
                    $params = [
                        '{messages}' => Tools::stripslashes($output),
                        '{employee}' => $current_employee->firstname . ' ' . $current_employee->lastname,
                        '{comment}' => Tools::stripslashes(Tools::nl2br($_POST['message_forward'])),
                        '{firstname}' => $employee->firstname,
                        '{lastname}' => $employee->lastname,
                    ];

                    if (Mail::Send(
                        $this->context->language->id,
                        'forward_msg',
                        $this->trans(
                            'Fwd: Customer message',
                            [],
                            'Emails.Subject',
                            $this->context->language->locale
                        ),
                        $params,
                        $employee->email,
                        $employee->firstname . ' ' . $employee->lastname,
                        $current_employee->email,
                        $current_employee->firstname . ' ' . $current_employee->lastname,
                        null,
                        null,
                        _PS_MAIL_DIR_,
                        true
                    )) {
                        $cm->private = 1;
                        $cm->message = $this->trans('Message forwarded to', [], 'Admin.Catalog.Feature') . ' ' . $employee->firstname . ' ' . $employee->lastname . "\n" . $this->trans('Comment:') . ' ' . $message;
                        $cm->add();
                    }
                } elseif ($email && Validate::isEmail($email)) {
                    $params = [
                        '{messages}' => Tools::nl2br(Tools::stripslashes($output)),
                        '{employee}' => $current_employee->firstname . ' ' . $current_employee->lastname,
                        '{comment}' => Tools::stripslashes($_POST['message_forward']),
                        '{firstname}' => '',
                        '{lastname}' => '',
                    ];

                    if (Mail::Send(
                        $this->context->language->id,
                        'forward_msg',
                        $this->trans(
                            'Fwd: Customer message',
                            [],
                            'Emails.Subject',
                            $this->context->language->locale
                        ),
                        $params,
                        $email,
                        null,
                        $current_employee->email,
                        $current_employee->firstname . ' ' . $current_employee->lastname,
                        null,
                        null,
                        _PS_MAIL_DIR_,
                        true
                    )) {
                        $cm->message = $this->trans('Message forwarded to', [], 'Admin.Catalog.Feature') . ' ' . $email . "\n" . $this->trans('Comment:') . ' ' . $message;
                        $cm->add();
                    }
                } else {
                    $this->errors[] = '<div class="alert error">' . $this->trans('The email address is invalid.', [], 'Admin.Notifications.Error') . '</div>';
                }
            }
            if (Tools::isSubmit('submitReply')) {
                $ct = new CustomerThread($id_customer_thread);

                ShopUrl::cacheMainDomainForShop((int) $ct->id_shop);

                $cm = new CustomerMessage();
                $cm->id_employee = (int) $this->context->employee->id;
                $cm->id_customer_thread = $ct->id;
                $cm->ip_address = (int) ip2long(Tools::getRemoteAddr());
                $cm->message = Tools::getValue('reply_message');
                if (($error = $cm->validateField('message', $cm->message, null, [], true)) !== true) {
                    $this->errors[] = $error;
                } elseif (isset($_FILES) && !empty($_FILES['joinFile']['name']) && $_FILES['joinFile']['error'] != 0) {
                    $this->errors[] = $this->trans('An error occurred during the file upload process.', [], 'Admin.Notifications.Error');
                } elseif ($cm->add()) {
                    $file_attachment = null;
                    if (!empty($_FILES['joinFile']['name'])) {
                        $file_attachment['content'] = file_get_contents($_FILES['joinFile']['tmp_name']);
                        $file_attachment['name'] = $_FILES['joinFile']['name'];
                        $file_attachment['mime'] = $_FILES['joinFile']['type'];
                    }
                    $customer = new Customer($ct->id_customer);

                    $params = [
                        '{reply}' => Tools::nl2br(Tools::htmlentitiesUTF8(Tools::getValue('reply_message'))),
                        '{link}' => Tools::url(
                            $this->context->link->getPageLink('contact', true, null, null, false, $ct->id_shop),
                            'id_customer_thread=' . (int) $ct->id . '&token=' . $ct->token
                        ),
                        '{firstname}' => $customer->firstname,
                        '{lastname}' => $customer->lastname,
                    ];
                    //#ct == id_customer_thread    #tc == token of thread   <== used in the synchronization imap
                    $contact = new Contact((int) $ct->id_contact, (int) $ct->id_lang);

                    if (Validate::isLoadedObject($contact)) {
                        $from_name = $contact->name;
                        $from_email = $contact->email;
                    } else {
                        $from_name = null;
                        $from_email = null;
                    }

                    $language = new Language((int) $ct->id_lang);

                    if (Mail::Send(
                        (int) $ct->id_lang,
                        'reply_msg',
                        $this->trans(
                            'An answer to your message is available #ct%thread_id% #tc%thread_token%',
                            [
                                '%thread_id%' => $ct->id,
                                '%thread_token%' => $ct->token,
                            ],
                            'Emails.Subject',
                            $language->locale
                        ),
                        $params,
                        Tools::getValue('msg_email'),
                        null,
                        $from_email,
                        $from_name,
                        $file_attachment,
                        null,
                        _PS_MAIL_DIR_,
                        true,
                        $ct->id_shop
                    )) {
                        $ct->status = 'closed';
                        $ct->update();
                    }
                    Tools::redirectAdmin(
                        self::$currentIndex . '&id_customer_thread=' . (int) $id_customer_thread . '&viewcustomer_thread&token=' . Tools::getValue('token')
                    );
                } else {
                    $this->errors[] = $this->trans('An error occurred. Your message was not sent. Please contact your system administrator.', [], 'Admin.Orderscustomers.Notification');
                }
            }
        }

        return parent::postProcess();
    }

    public function initContent()
    {
        if (isset($_GET['filename']) && file_exists(_PS_UPLOAD_DIR_ . $_GET['filename']) && Validate::isFileName($_GET['filename'])) {
            AdminCustomerThreadsController::openUploadedFile();
        }

        return parent::initContent();
    }

    protected function openUploadedFile()
    {
        $filename = $_GET['filename'];

        $extensions = [
            '.txt' => 'text/plain',
            '.rtf' => 'application/rtf',
            '.doc' => 'application/msword',
            '.docx' => 'application/msword',
            '.pdf' => 'application/pdf',
            '.zip' => 'multipart/x-zip',
            '.png' => 'image/png',
            '.jpeg' => 'image/jpeg',
            '.gif' => 'image/gif',
            '.jpg' => 'image/jpeg',
        ];

        $extension = false;
        foreach ($extensions as $key => $val) {
            if (substr(Tools::strtolower($filename), -4) == $key || substr(Tools::strtolower($filename), -5) == $key) {
                $extension = $val;

                break;
            }
        }

        if (!$extension || !Validate::isFileName($filename)) {
            die(Tools::displayError());
        }

        if (ob_get_level() && ob_get_length() > 0) {
            ob_end_clean();
        }
        header('Content-Type: ' . $extension);
        header('Content-Disposition:attachment;filename="' . $filename . '"');
        readfile(_PS_UPLOAD_DIR_ . $filename);
        die;
    }

    public function renderKpis()
    {
        $time = time();
        $kpis = [];

        /* The data generation is located in AdminStatsControllerCore */

        $helper = new HelperKpi();
        $helper->id = 'box-pending-messages';
        $helper->icon = 'icon-envelope';
        $helper->color = 'color1';
        $helper->href = $this->context->link->getAdminLink('AdminCustomerThreads');
        $helper->title = $this->trans('Pending Discussion Threads', [], 'Admin.Catalog.Feature');
        if (ConfigurationKPI::get('PENDING_MESSAGES') !== false) {
            $helper->value = ConfigurationKPI::get('PENDING_MESSAGES');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=pending_messages';
        $helper->refresh = (bool) (ConfigurationKPI::get('PENDING_MESSAGES_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-age';
        $helper->icon = 'icon-time';
        $helper->color = 'color2';
        $helper->title = $this->trans('Average Response Time', [], 'Admin.Catalog.Feature');
        $helper->subtitle = $this->trans('30 days', [], 'Admin.Global');
        if (ConfigurationKPI::get('AVG_MSG_RESPONSE_TIME') !== false) {
            $helper->value = ConfigurationKPI::get('AVG_MSG_RESPONSE_TIME');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=avg_msg_response_time';
        $helper->refresh = (bool) (ConfigurationKPI::get('AVG_MSG_RESPONSE_TIME_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-messages-per-thread';
        $helper->icon = 'icon-copy';
        $helper->color = 'color3';
        $helper->title = $this->trans('Messages per Thread', [], 'Admin.Catalog.Feature');
        $helper->subtitle = $this->trans('30 day', [], 'Admin.Global');
        if (ConfigurationKPI::get('MESSAGES_PER_THREAD') !== false) {
            $helper->value = ConfigurationKPI::get('MESSAGES_PER_THREAD');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=messages_per_thread';
        $helper->refresh = (bool) (ConfigurationKPI::get('MESSAGES_PER_THREAD_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;

        return $helper->generate();
    }

    public function renderView()
    {
        if (!$id_customer_thread = (int) Tools::getValue('id_customer_thread')) {
            return;
        }

        $this->context = Context::getContext();
        if (!($thread = $this->loadObject())) {
            return;
        }
        $this->context->cookie->{'customer_threadFilter_cl!id_contact'} = $thread->id_contact;

        $employees = Employee::getEmployees();

        $messages = CustomerThread::getMessageCustomerThreads($id_customer_thread);

        foreach ($messages as $key => $mess) {
            if ($mess['id_employee']) {
                $employee = new Employee($mess['id_employee']);
                $messages[$key]['employee_image'] = $employee->getImage();
            }
            if (isset($mess['file_name']) && $mess['file_name'] != '') {
                $messages[$key]['file_name'] = _THEME_PROD_PIC_DIR_ . $mess['file_name'];
            } else {
                unset($messages[$key]['file_name']);
            }

            if ($mess['id_product']) {
                $product = new Product((int) $mess['id_product'], false, $this->context->language->id);
                if (Validate::isLoadedObject($product)) {
                    $messages[$key]['product_name'] = $product->name;
                    $messages[$key]['product_link'] = $this->context->link->getAdminLink('AdminProducts', true, ['id_product' => (int) $product->id, 'updateproduct' => '1']);
                }
            }
        }

        $next_thread = CustomerThread::getNextThread((int) $thread->id);

        $contacts = Contact::getContacts($this->context->language->id);

        $actions = [];

        if ($next_thread) {
            $next_thread = [
                'href' => self::$currentIndex . '&id_customer_thread=' . (int) $next_thread . '&viewcustomer_thread&token=' . $this->token,
                'name' => $this->trans('Reply to the next unanswered message in this thread', [], 'Admin.Catalog.Feature'),
            ];
        }

        if ($thread->status != 'closed') {
            $actions['closed'] = [
                'href' => self::$currentIndex . '&viewcustomer_thread&setstatus=2&id_customer_thread=' . (int) Tools::getValue('id_customer_thread') . '&viewmsg&token=' . $this->token,
                'label' => $this->trans('Mark as "handled"', [], 'Admin.Catalog.Feature'),
                'name' => 'setstatus',
                'value' => 2,
            ];
        } else {
            $actions['open'] = [
                'href' => self::$currentIndex . '&viewcustomer_thread&setstatus=1&id_customer_thread=' . (int) Tools::getValue('id_customer_thread') . '&viewmsg&token=' . $this->token,
                'label' => $this->trans('Re-open', [], 'Admin.Catalog.Feature'),
                'name' => 'setstatus',
                'value' => 1,
            ];
        }

        if ($thread->status != 'pending1') {
            $actions['pending1'] = [
                'href' => self::$currentIndex . '&viewcustomer_thread&setstatus=3&id_customer_thread=' . (int) Tools::getValue('id_customer_thread') . '&viewmsg&token=' . $this->token,
                'label' => $this->trans('Mark as "pending 1" (will be answered later)', [], 'Admin.Catalog.Feature'),
                'name' => 'setstatus',
                'value' => 3,
            ];
        } else {
            $actions['pending1'] = [
                'href' => self::$currentIndex . '&viewcustomer_thread&setstatus=1&id_customer_thread=' . (int) Tools::getValue('id_customer_thread') . '&viewmsg&token=' . $this->token,
                'label' => $this->trans('Disable pending status', [], 'Admin.Catalog.Feature'),
                'name' => 'setstatus',
                'value' => 1,
            ];
        }

        if ($thread->status != 'pending2') {
            $actions['pending2'] = [
                'href' => self::$currentIndex . '&viewcustomer_thread&setstatus=4&id_customer_thread=' . (int) Tools::getValue('id_customer_thread') . '&viewmsg&token=' . $this->token,
                'label' => $this->trans('Mark as "pending 2" (will be answered later)', [], 'Admin.Catalog.Feature'),
                'name' => 'setstatus',
                'value' => 4,
            ];
        } else {
            $actions['pending2'] = [
                'href' => self::$currentIndex . '&viewcustomer_thread&setstatus=1&id_customer_thread=' . (int) Tools::getValue('id_customer_thread') . '&viewmsg&token=' . $this->token,
                'label' => $this->trans('Disable pending status', [], 'Admin.Catalog.Feature'),
                'name' => 'setstatus',
                'value' => 1,
            ];
        }

        if ($thread->id_customer) {
            $customer = new Customer($thread->id_customer);
            $orders = Order::getCustomerOrders($customer->id);
            if ($orders && count($orders)) {
                $total_ok = 0;
                $orders_ok = [];
                foreach ($orders as $key => $order) {
                    if ($order['valid']) {
                        $orders_ok[] = $order;
                        $total_ok += $order['total_paid_real'] / $order['conversion_rate'];
                    }
                    $orders[$key]['date_add'] = Tools::displayDate($order['date_add']);
                    $orders[$key]['total_paid_real'] = $this->context->getCurrentLocale()->formatPrice($order['total_paid_real'], Currency::getIsoCodeById((int) $order['id_currency']));
                }
            }

            $products = $customer->getBoughtProducts();
            if ($products && count($products)) {
                foreach ($products as $key => $product) {
                    $products[$key]['date_add'] = Tools::displayDate($product['date_add'], null, true);
                }
            }
        }
        $timeline_items = $this->getTimeline($messages, $thread->id_order);
        $first_message = $messages[0];

        if (!$messages[0]['id_employee']) {
            unset($messages[0]);
        }

        $contact = '';
        foreach ($contacts as $c) {
            if ($c['id_contact'] == $thread->id_contact) {
                $contact = $c['name'];
            }
        }

        $this->tpl_view_vars = [
            'id_customer_thread' => $id_customer_thread,
            'thread' => $thread,
            'actions' => $actions,
            'employees' => $employees,
            'current_employee' => $this->context->employee,
            'messages' => $messages,
            'first_message' => $first_message,
            'contact' => $contact,
            'next_thread' => $next_thread,
            'orders' => isset($orders) ? $orders : false,
            'customer' => isset($customer) ? $customer : false,
            'products' => isset($products) ? $products : false,
            'total_ok' => isset($total_ok) ? $this->context->getCurrentLocale()->formatPrice($total_ok, $this->context->currency->iso_code) : false,
            'orders_ok' => isset($orders_ok) ? $orders_ok : false,
            'count_ok' => isset($orders_ok) ? count($orders_ok) : false,
            'PS_CUSTOMER_SERVICE_SIGNATURE' => str_replace('\r\n', "\n", Configuration::get('PS_CUSTOMER_SERVICE_SIGNATURE', (int) $thread->id_lang)),
            'timeline_items' => $timeline_items,
        ];

        if ($next_thread) {
            $this->tpl_view_vars['next_thread'] = $next_thread;
        }

        return parent::renderView();
    }

    public function getTimeline($messages, $id_order)
    {
        $timeline = [];
        foreach ($messages as $message) {
            $product = new Product((int) $message['id_product'], false, $this->context->language->id);

            $content = '';
            if (!$message['private']) {
                $content .= $this->trans('Message to:', [], 'Admin.Catalog.Feature') . ' <span class="badge">' . (!$message['id_employee'] ? $message['subject'] : $message['customer_name']) . '</span><br/>';
            }
            if (Validate::isLoadedObject($product)) {
                $content .= '<br/>' . $this->trans('Product:', [], 'Admin.Catalog.Feature') . '<span class="label label-info">' . $product->name . '</span><br/><br/>';
            }
            $content .= Tools::safeOutput($message['message']);

            $timeline[$message['date_add']][] = [
                'arrow' => 'left',
                'background_color' => '',
                'icon' => 'icon-envelope',
                'content' => $content,
                'date' => $message['date_add'],
            ];
        }

        $order = new Order((int) $id_order);
        if (Validate::isLoadedObject($order)) {
            $order_history = $order->getHistory($this->context->language->id);
            foreach ($order_history as $history) {
                $parameters = ['vieworder' => 1, 'id_order' => (int) $order->id];
                $link_order = $this->context->link->getAdminLink('AdminOrders', true, [], $parameters);

                $content = '<a class="badge" target="_blank" href="' . Tools::safeOutput($link_order) . '">' . $this->trans('Order', [], 'Admin.Global') . ' #' . (int) $order->id . '</a><br/><br/>';

                $content .= '<span>' . $this->trans('Status:', [], 'Admin.Catalog.Feature') . ' ' . $history['ostate_name'] . '</span>';

                $timeline[$history['date_add']][] = [
                    'arrow' => 'right',
                    'alt' => true,
                    'background_color' => $history['color'],
                    'icon' => 'icon-credit-card',
                    'content' => $content,
                    'date' => $history['date_add'],
                    'see_more_link' => $link_order,
                ];
            }
        }
        krsort($timeline);

        return $timeline;
    }

    protected function displayMessage($message, $email = false, $id_employee = null)
    {
        $tpl = $this->createTemplate('message.tpl');

        $contacts = Contact::getContacts($this->context->language->id);
        foreach ($contacts as $contact) {
            $contact_array[$contact['id_contact']] = ['id_contact' => $contact['id_contact'], 'name' => $contact['name']];
        }
        $contacts = $contact_array;

        if (!$email) {
            if (!empty($message['id_product']) && empty($message['employee_name'])) {
                $id_order_product = Order::getIdOrderProduct((int) $message['id_customer'], (int) $message['id_product']);
            }
        }
        $message['date_add'] = Tools::displayDate($message['date_add'], null, true);
        $message['user_agent'] = strip_tags($message['user_agent']);
        $message['message'] = preg_replace(
            '/(https?:\/\/[a-z0-9#%&_=\(\)\.\? \+\-@\/]{6,1000})([\s\n<])/Uui',
            '<a href="\1">\1</a>\2',
            html_entity_decode(
                $message['message'],
            ENT_QUOTES,
                'UTF-8'
            )
        );

        $is_valid_order_id = true;
        $order = new Order((int) $message['id_order']);

        if (!Validate::isLoadedObject($order)) {
            $is_valid_order_id = false;
        }

        $tpl->assign([
            'thread_url' => $this->context->link->getAdminLink('AdminCustomerThreads') . '&amp;id_customer_thread='
                . (int) $message['id_customer_thread'] . '&amp;viewcustomer_thread=1',
            'link' => Context::getContext()->link,
            'current' => self::$currentIndex,
            'token' => $this->token,
            'message' => $message,
            'id_order_product' => isset($id_order_product) ? $id_order_product : null,
            'email' => $email,
            'id_employee' => $id_employee,
            'PS_SHOP_NAME' => Configuration::get('PS_SHOP_NAME'),
            'file_name' => file_exists(_PS_UPLOAD_DIR_ . $message['file_name']),
            'contacts' => $contacts,
            'is_valid_order_id' => $is_valid_order_id,
        ]);

        return $tpl->fetch();
    }

    protected function displayButton($content)
    {
        return '<div><p>' . $content . '</p></div>';
    }

    public function renderOptions()
    {
        if (Configuration::get('PS_SAV_IMAP_URL')
        && Configuration::get('PS_SAV_IMAP_PORT')
        && Configuration::get('PS_SAV_IMAP_USER')
        && Configuration::get('PS_SAV_IMAP_PWD')) {
            $this->tpl_option_vars['use_sync'] = true;
        } else {
            $this->tpl_option_vars['use_sync'] = false;
        }

        return parent::renderOptions();
    }

    public function updateOptionPsSavImapOpt($value)
    {
        if ($this->access('edit') != '1') {
            throw new PrestaShopException($this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error'));
        }

        if (!$this->errors && $value) {
            Configuration::updateValue('PS_SAV_IMAP_OPT', implode('', $value));
        }
    }

    public function ajaxProcessMarkAsRead()
    {
        if ($this->access('edit') != '1') {
            throw new PrestaShopException($this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error'));
        }

        $id_thread = Tools::getValue('id_thread');
        $messages = CustomerThread::getMessageCustomerThreads($id_thread);
        if (count($messages)) {
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer_message` set `read` = 1 WHERE `id_employee` = ' . (int) $this->context->employee->id . ' AND `id_customer_thread` = ' . (int) $id_thread);
        }
    }

    /**
     * Call the IMAP synchronization during an AJAX process.
     *
     * @throws PrestaShopException
     */
    public function ajaxProcessSyncImap()
    {
        if ($this->access('edit') != '1') {
            throw new PrestaShopException($this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error'));
        }

        if (Tools::isSubmit('syncImapMail')) {
            die(json_encode($this->syncImap()));
        }
    }

    /**
     * Call the IMAP synchronization during the render process.
     */
    public function renderProcessSyncImap()
    {
        // To avoid an error if the IMAP isn't configured, we check the configuration here, like during
        // the synchronization. All parameters will exists.
        if (!(Configuration::get('PS_SAV_IMAP_URL')
            || Configuration::get('PS_SAV_IMAP_PORT')
            || Configuration::get('PS_SAV_IMAP_USER')
            || Configuration::get('PS_SAV_IMAP_PWD'))) {
            return;
        }

        // Executes the IMAP synchronization.
        $sync_errors = $this->syncImap();

        // Show the errors.
        if (isset($sync_errors['hasError']) && $sync_errors['hasError']) {
            if (isset($sync_errors['errors'])) {
                foreach ($sync_errors['errors'] as $error) {
                    $this->displayWarning($error);
                }
            }
        }
    }

    /**
     * Imap synchronization method.
     *
     * @return array errors list
     */
    public function syncImap()
    {
        if (!($url = Configuration::get('PS_SAV_IMAP_URL'))
            || !($port = Configuration::get('PS_SAV_IMAP_PORT'))
            || !($user = Configuration::get('PS_SAV_IMAP_USER'))
            || !($password = Configuration::get('PS_SAV_IMAP_PWD'))) {
            return ['hasError' => true, 'errors' => ['IMAP configuration is not correct']];
        }

        $conf = Configuration::getMultiple([
            'PS_SAV_IMAP_OPT_POP3', 'PS_SAV_IMAP_OPT_NORSH', 'PS_SAV_IMAP_OPT_SSL',
            'PS_SAV_IMAP_OPT_VALIDATE-CERT', 'PS_SAV_IMAP_OPT_NOVALIDATE-CERT',
            'PS_SAV_IMAP_OPT_TLS', 'PS_SAV_IMAP_OPT_NOTLS', ]);

        $conf_str = '';
        if ($conf['PS_SAV_IMAP_OPT_POP3']) {
            $conf_str .= '/pop3';
        }
        if ($conf['PS_SAV_IMAP_OPT_NORSH']) {
            $conf_str .= '/norsh';
        }
        if ($conf['PS_SAV_IMAP_OPT_SSL']) {
            $conf_str .= '/ssl';
        }
        if ($conf['PS_SAV_IMAP_OPT_VALIDATE-CERT']) {
            $conf_str .= '/validate-cert';
        }
        if ($conf['PS_SAV_IMAP_OPT_NOVALIDATE-CERT']) {
            $conf_str .= '/novalidate-cert';
        }
        if ($conf['PS_SAV_IMAP_OPT_TLS']) {
            $conf_str .= '/tls';
        }
        if ($conf['PS_SAV_IMAP_OPT_NOTLS']) {
            $conf_str .= '/notls';
        }

        if (!function_exists('imap_open')) {
            return ['hasError' => true, 'errors' => ['imap is not installed on this server']];
        }

        $mbox = @imap_open('{' . $url . ':' . $port . $conf_str . '}', $user, $password);

        //checks if there is no error when connecting imap server
        $errors = imap_errors();
        if (is_array($errors)) {
            $errors = array_unique($errors);
        }
        $str_errors = '';
        $str_error_delete = '';

        if (is_array($errors) && count($errors)) {
            $str_errors = '';
            foreach ($errors as $error) {
                $str_errors .= $error . ', ';
            }
            $str_errors = rtrim(trim($str_errors), ',');
        }
        //checks if imap connexion is active
        if (!$mbox) {
            return ['hasError' => true, 'errors' => ['Cannot connect to the mailbox :<br />' . ($str_errors)]];
        }

        //Returns information about the current mailbox. Returns FALSE on failure.
        $check = imap_check($mbox);
        if (!$check) {
            return ['hasError' => true, 'errors' => ['Fail to get information about the current mailbox']];
        }

        if ($check->Nmsgs == 0) {
            return ['hasError' => true, 'errors' => ['NO message to sync']];
        }

        $result = imap_fetch_overview($mbox, "1:{$check->Nmsgs}", 0);
        $message_errors = [];
        foreach ($result as $overview) {
            //check if message exist in database
            if (isset($overview->subject)) {
                $subject = $overview->subject;
            } else {
                $subject = '';
            }
            //Creating an md5 to check if message has been allready processed
            $md5 = md5($overview->date . $overview->from . $subject . $overview->msgno);
            $exist = Db::getInstance()->getValue(
                'SELECT `md5_header`
						 FROM `' . _DB_PREFIX_ . 'customer_message_sync_imap`
						 WHERE `md5_header` = \'' . pSQL($md5) . '\''
            );
            if ($exist) {
                if (Configuration::get('PS_SAV_IMAP_DELETE_MSG')) {
                    if (!imap_delete($mbox, $overview->msgno)) {
                        $str_error_delete = ', Fail to delete message';
                    }
                }
            } else {
                //check if subject has id_order
                preg_match('/\#ct([0-9]*)/', $subject, $matches1);
                preg_match('/\#tc([0-9-a-z-A-Z]*)/', $subject, $matches2);
                $match_found = false;
                if (isset($matches1[1], $matches2[1])) {
                    $match_found = true;
                }

                $new_ct = (Configuration::get('PS_SAV_IMAP_CREATE_THREADS') && !$match_found && (strpos($subject, '[no_sync]') == false));

                $fetch_succeed = true;
                if ($match_found || $new_ct) {
                    if ($new_ct) {
                        // parse from attribute and fix it if needed
                        $from_parsed = [];
                        if (!isset($overview->from)
                            || (!preg_match('/<(' . Tools::cleanNonUnicodeSupport('[a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z0-9]+') . ')>/', $overview->from, $from_parsed)
                            && !Validate::isEmail($overview->from))) {
                            $message_errors[] = $this->trans('Cannot create message in a new thread.', [], 'Admin.Orderscustomers.Notification');

                            continue;
                        }

                        // fix email format: from "Mr Sanders <sanders@blueforest.com>" to "sanders@blueforest.com"
                        $from = $overview->from;
                        if (isset($from_parsed[1])) {
                            $from = $from_parsed[1];
                        }

                        // we want to assign unrecognized mails to the right contact category
                        $contacts = Contact::getContacts($this->context->language->id);
                        if (!$contacts) {
                            continue;
                        }

                        foreach ($contacts as $contact) {
                            if (isset($overview->to) && strpos($overview->to, $contact['email']) !== false) {
                                $id_contact = $contact['id_contact'];
                            }
                        }

                        if (!isset($id_contact)) { // if not use the default contact category
                            $id_contact = $contacts[0]['id_contact'];
                        }

                        $customer = new Customer();
                        $client = $customer->getByEmail($from); //check if we already have a customer with this email
                        $ct = new CustomerThread();
                        if (isset($client->id)) { //if mail is owned by a customer assign to him
                            $ct->id_customer = $client->id;
                        }
                        $ct->email = $from;
                        $ct->id_contact = $id_contact;
                        $ct->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
                        $ct->id_shop = $this->context->shop->id; //new customer threads for unrecognized mails are not shown without shop id
                        $ct->status = 'open';
                        $ct->token = Tools::passwdGen(12);
                        $ct->add();
                    } else {
                        $ct = new CustomerThread((int) $matches1[1]);
                    } //check if order exist in database

                    if (Validate::isLoadedObject($ct) && ((isset($matches2[1]) && $ct->token == $matches2[1]) || $new_ct)) {
                        $structure = imap_bodystruct($mbox, $overview->msgno, '1');
                        if ($structure->type == 0) {
                            $message = imap_fetchbody($mbox, $overview->msgno, '1');
                        } elseif ($structure->type == 1) {
                            $structure = imap_bodystruct($mbox, $overview->msgno, '1.1');
                            $message = imap_fetchbody($mbox, $overview->msgno, '1.1');
                        } else {
                            continue;
                        }

                        switch ($structure->encoding) {
                            case 3:
                                $message = imap_base64($message);

                                break;
                            case 4:
                                $message = imap_qprint($message);

                                break;
                        }
                        $message = iconv($this->getEncoding($structure), 'utf-8', $message);
                        $message = nl2br($message);
                        if (!$message || strlen($message) == 0) {
                            $message_errors[] = $this->trans('The message body is empty, cannot import it.', [], 'Admin.Orderscustomers.Notification');
                            $fetch_succeed = false;

                            continue;
                        }
                        $cm = new CustomerMessage();
                        $cm->id_customer_thread = $ct->id;
                        if (empty($message) || !Validate::isCleanHtml($message)) {
                            $str_errors .= $this->trans('Invalid message content for subject: %s', [$subject], 'Admin.Orderscustomers.Notification');
                        } else {
                            try {
                                $cm->message = $message;
                                $cm->add();
                            } catch (PrestaShopException $pse) {
                                $message_errors[] = $this->trans('The message content is not valid, cannot import it.', [], 'Admin.Orderscustomers.Notification');
                                $fetch_succeed = false;

                                continue;
                            }
                        }
                    }
                }
                if ($fetch_succeed) {
                    Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'customer_message_sync_imap` (`md5_header`) VALUES (\'' . pSQL($md5) . '\')');
                }
            }
        }
        imap_expunge($mbox);
        imap_close($mbox);
        if (count($message_errors) > 0) {
            if (($more_error = $str_errors . $str_error_delete) && strlen($more_error) > 0) {
                $message_errors = array_merge([$more_error], $message_errors);
            }

            return ['hasError' => true, 'errors' => $message_errors];
        }
        if ($str_errors . $str_error_delete) {
            return ['hasError' => true, 'errors' => [$str_errors . $str_error_delete]];
        } else {
            return ['hasError' => false, 'errors' => ''];
        }
    }

    protected function getEncoding($structure)
    {
        foreach ($structure->parameters as $parameter) {
            if ($parameter->attribute == 'CHARSET') {
                return $parameter->value;
            }
        }

        return 'utf-8';
    }
}
