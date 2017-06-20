<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property Mail $object
 */
class AdminEmailsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;

        $this->table = 'mail';
        $this->className = 'Mail';

        parent::__construct();

        if (Configuration::get('PS_LOG_EMAILS')) {
            $this->lang = false;
            $this->noLink = true;
            $this->list_no_link = true;
            $this->explicitSelect = true;
            $this->addRowAction('delete');

            $this->bulk_actions = array(
                'delete' => array(
                    'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                    'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                    'icon' => 'icon-trash'
                )
            );

            foreach (Language::getLanguages() as $language) {
                $languages[$language['id_lang']] = $language['name'];
            }

            $this->fields_list = array(
                'id_mail' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'),
                'recipient' => array('title' => $this->trans('Recipient', array(), 'Admin.Advparameters.Feature')),
                'template' => array('title' => $this->trans('Template', array(), 'Admin.Advparameters.Feature')),
                'language' => array(
                    'title' => $this->trans('Language', array(), 'Admin.Global'),
                    'type' => 'select',
                    'color' => 'color',
                    'list' => $languages,
                    'filter_key' => 'a!id_lang',
                    'filter_type' => 'int',
                    'order_key' => 'language'
                ),
                'subject' => array('title' => $this->trans('Subject', array(), 'Admin.Advparameters.Feature')),
                'date_add' => array(
                    'title' => $this->trans('Sent', array(), 'Admin.Advparameters.Feature'),
                    'type' => 'datetime',
                )
            );
            $this->_select .= 'l.name as language';
            $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'lang l ON (a.id_lang = l.id_lang)';
            $this->_use_found_rows = false;
        }

        $arr = array();

        foreach (Contact::getContacts($this->context->language->id) as $contact) {
            $arr[] = array('email_message' => $contact['id_contact'], 'name' => $contact['name']);
        }

        $this->fields_options = array(
            'email' => array(
                'title' => $this->trans('Email', array(), 'Admin.Global'),
                'icon' => 'icon-envelope',
                'fields' =>    array(
                    'PS_MAIL_EMAIL_MESSAGE' => array(
                        'title' => $this->trans('Send emails to', array(), 'Admin.Advparameters.Feature'),
                        'desc' => $this->trans('Where customers send messages from the order page.', array(), 'Admin.Advparameters.Help'),
                        'validation' => 'isUnsignedId',
                        'type' => 'select',
                        'cast' => 'intval',
                        'identifier' => 'email_message',
                        'list' => $arr
                    ),
                    'PS_MAIL_METHOD' => array(
                        'title' => '',
                        'validation' => 'isGenericName',
                        'type' => 'radio',
                        'required' => true,
                        'choices' => array(
                            3 => $this->trans('Never send emails (may be useful for testing purposes)', array(), 'Admin.Advparameters.Feature'),
                            2 => $this->trans('Set my own SMTP parameters (for advanced users ONLY)', array(), 'Admin.Advparameters.Feature')
                        )
                    ),
                    'PS_MAIL_TYPE' => array(
                        'title' => '',
                        'validation' => 'isGenericName',
                        'type' => 'radio',
                        'required' => true,
                        'choices' => array(
                            Mail::TYPE_HTML => $this->trans('Send email in HTML format', array(), 'Admin.Advparameters.Feature'),
                            Mail::TYPE_TEXT => $this->trans('Send email in text format', array(), 'Admin.Advparameters.Feature'),
                            Mail::TYPE_BOTH => $this->trans('Both', array(), 'Admin.Advparameters.Feature')
                        )
                    ),
                    'PS_LOG_EMAILS' => array(
                        'title' => $this->trans('Log Emails', array(), 'Admin.Advparameters.Feature'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
            'smtp' => array(
                'title' => $this->trans('Email', array(), 'Admin.Global'),
                'fields' =>    array(
                    'PS_MAIL_DOMAIN' => array(
                        'title' => $this->trans('Mail domain name', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('Fully qualified domain name (keep this field empty if you don\'t know).', array(), 'Admin.Advparameters.Help'),
                        'empty' => true, 'validation' =>
                        'isUrl',
                        'type' => 'text',
                    ),
                    'PS_MAIL_SERVER' => array(
                        'title' => $this->trans('SMTP server', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('IP address or server name (e.g. smtp.mydomain.com).', array(), 'Admin.Advparameters.Help'),
                        'validation' => 'isGenericName',
                        'type' => 'text',
                    ),
                    'PS_MAIL_USER' => array(
                        'title' => $this->trans('SMTP username', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('Leave blank if not applicable.', array(), 'Admin.Advparameters.Help'),
                        'validation' => 'isGenericName',
                        'type' => 'text',
                    ),
                    'PS_MAIL_PASSWD' => array(
                        'title' => $this->trans('SMTP password', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('Leave blank if not applicable.', array(), 'Admin.Advparameters.Help'),
                        'validation' => 'isAnything',
                        'type' => 'password',
                        'autocomplete' => false
                    ),
                    'PS_MAIL_SMTP_ENCRYPTION' => array(
                        'title' => $this->trans('Encryption', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('Use an encrypt protocol', array(), 'Admin.Advparameters.Help'),
                        'desc' => extension_loaded('openssl') ? '' : '/!\\ '.$this->trans('SSL does not seem to be available on your server.', array(), 'Admin.Advparameters.Notification'),
                        'type' => 'select',
                        'cast' => 'strval',
                        'identifier' => 'mode',
                        'list' => array(
                            array(
                                'mode' => 'off',
                                'name' => $this->trans('None', array(), 'Admin.Advparameters.Feature')
                            ),
                            array(
                                'mode' => 'tls',
                                'name' => $this->trans('TLS', array(), 'Admin.Advparameters.Feature')
                            ),
                            array(
                                'mode' => 'ssl',
                                'name' => $this->trans('SSL', array(), 'Admin.Advparameters.Feature')
                            )
                        ),
                    ),
                    'PS_MAIL_SMTP_PORT' => array(
                        'title' => $this->trans('Port', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('Port number to use.', array(), 'Admin.Advparameters.Feature'),
                        'validation' => 'isInt',
                        'type' => 'text',
                        'cast' => 'intval',
                        'class' => 'fixed-width-sm'
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
            'test' => array(
                'title' =>    $this->trans('Test your email configuration', array(), 'Admin.Advparameters.Feature'),
                'hide_multishop_checkbox' => true,
                'fields' =>    array(
                    'PS_SHOP_EMAIL' => array(
                        'title' => $this->trans('Send a test email to', array(), 'Admin.Advparameters.Feature'),
                        'type' => 'text',
                        'id' => 'testEmail',
                        'no_multishop_checkbox' => true
                    ),
                ),
                'bottom' => '<div class="row"><div class="col-lg-9 col-lg-offset-3">
					<div class="alert" id="mailResultCheck" style="display:none;"></div>
				</div></div>',
                'buttons' => array(
                    array('title' => $this->trans('Send a test email', array(), 'Admin.Advparameters.Feature'),
                        'icon' => 'process-icon-envelope',
                        'name' => 'btEmailTest',
                        'js' => 'verifyMail()',
                        'class' => 'btn btn-default pull-right'
                    )
                )
            )
        );

        if (!defined('_PS_HOST_MODE_')) {
            $this->fields_options['email']['fields']['PS_MAIL_METHOD']['choices'][1] =
                $this->trans('Use PHP\'s mail() function (recommended; works in most cases)', array(), 'Admin.Advparameters.Feature');
        }

        ksort($this->fields_options['email']['fields']['PS_MAIL_METHOD']['choices']);
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addJs(_PS_JS_DIR_.'/admin/email.js');

        Media::addJsDefL('textMsg', $this->trans('This is a test message. Your server is now configured to send email.', array(), 'Admin.Advparameters.Feature'));
        Media::addJsDefL('textSubject', $this->trans('Test message -- Prestashop', array(), 'Admin.Advparameters.Feature'));
        Media::addJsDefL('textSendOk', $this->trans('A test email has been sent to the email address you provided.', array(), 'Admin.Advparameters.Feature'));
        Media::addJsDefL('textSendError', $this->trans('Error: Please check your configuration', array(), 'Admin.Advparameters.Feature'));
        Media::addJsDefL('token_mail', $this->token);
        Media::addJsDefL('errorMail', $this->trans('This email address is not valid', array(), 'Admin.Advparameters.Feature'));
    }

    public function processDelete()
    {
        if ((int)$id_mail = Tools::getValue('id_mail', 0)) {
            $return = Mail::eraseLog((int)$id_mail);
        } else {
            $return = Mail::eraseAllLogs();
        }

        return $return;
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->toolbar_btn['delete'] = array(
            'short' => 'Erase',
            'desc' => $this->trans('Erase all', array(), 'Admin.Advparameters.Feature'),
            'js' => 'if (confirm(\''.$this->trans('Are you sure?', array(), 'Admin.Notifications.Warning').'\')) document.location = \''.Tools::safeOutput($this->context->link->getAdminLink('AdminEmails')).'&amp;token='.$this->token.'&amp;deletemail=1\';'
        );
        unset($this->toolbar_btn['new']);
    }

    public function updateOptionPsMailPasswd($value)
    {
        if (Tools::getValue('PS_MAIL_PASSWD') == '' && Configuration::get('PS_MAIL_PASSWD')) {
            return true;
        } else {
            Configuration::updateValue('PS_MAIL_PASSWD', Tools::getValue('PS_MAIL_PASSWD'));
        }
    }

    /**
     * AdminController::initContent() override
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        $this->addToolBarModulesListButton();

        unset($this->toolbar_btn['save']);
        $back = $this->context->link->getAdminLink('AdminDashboard');

        $this->toolbar_btn['back'] = array(
            'href' => $back,
            'desc' => $this->trans('Back to the dashboard', array(), 'Admin.Advparameters.Feature')
        );

        // $this->content .= $this->renderOptions();

        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));

        return parent::initContent();
    }

    public function beforeUpdateOptions()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', array(), 'Admin.Notifications.Error');
            return;
        }
        /* PrestaShop demo mode*/

        // We don't want to update the shop e-mail when sending test e-mails
        if (isset($_POST['PS_SHOP_EMAIL'])) {
            $_POST['PS_SHOP_EMAIL'] = Configuration::get('PS_SHOP_EMAIL');
        }

        if (isset($_POST['PS_MAIL_METHOD']) && $_POST['PS_MAIL_METHOD'] == 2
            && (empty($_POST['PS_MAIL_SERVER']) || empty($_POST['PS_MAIL_SMTP_PORT']))) {
            $this->errors[] = $this->trans('You must define an SMTP server and an SMTP port. If you do not know it, use the PHP mail() function instead.', array(), 'Admin.Shopparameters.Notification');
        }
    }

    public function ajaxProcessSendMailTest()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            die($this->trans('This functionality has been disabled.', array(), 'Admin.Notifications.Error'));
        }
        /* PrestaShop demo mode */
        if ($this->access('view')) {
            $smtpChecked = (trim(Tools::getValue('mailMethod')) == 'smtp');
            $smtpServer = Tools::getValue('smtpSrv');
            $content = urldecode(Tools::getValue('testMsg'));
            $content = html_entity_decode($content);
            $subject = urldecode(Tools::getValue('testSubject'));
            $type = 'text/html';
            $to = Tools::getValue('testEmail');
            $from = Configuration::get('PS_SHOP_EMAIL');
            $smtpLogin = Tools::getValue('smtpLogin');
            $smtpPassword = Tools::getValue('smtpPassword');
            $smtpPassword = (!empty($smtpPassword)) ? urldecode($smtpPassword) : Configuration::get('PS_MAIL_PASSWD');
            $smtpPassword = str_replace(
                array('&lt;', '&gt;', '&quot;', '&amp;'),
                array('<', '>', '"', '&'),
                Tools::htmlentitiesUTF8($smtpPassword)
            );

            $smtpPort = Tools::getValue('smtpPort');
            $smtpEncryption = Tools::getValue('smtpEnc');

            $result = Mail::sendMailTest(Tools::htmlentitiesUTF8($smtpChecked), Tools::htmlentitiesUTF8($smtpServer), Tools::htmlentitiesUTF8($content), Tools::htmlentitiesUTF8($subject), Tools::htmlentitiesUTF8($type), Tools::htmlentitiesUTF8($to), Tools::htmlentitiesUTF8($from), Tools::htmlentitiesUTF8($smtpLogin), $smtpPassword, Tools::htmlentitiesUTF8($smtpPort), Tools::htmlentitiesUTF8($smtpEncryption));
            die($result === true ? 'ok' : $result);
        }
    }
}
