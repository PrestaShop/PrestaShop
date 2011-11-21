<?php
/*
* Copyright (c) 2011 Mailjet SAS
* 
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
* 
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
*  @author Dream me up
*  @copyright  2011 Mailjet SAS
*  @version  Release: $Revision: 1.4 $
*  @license    hhttp://opensource.org/licenses/mit-license  MIT License
*  International Registred Trademark & Property of Mailjet SAS
*/


// Security
if (!defined('_PS_VERSION_'))
	exit;

class Mailjet extends Module
{       

	/*
	** Construct Method
	**
	*/

	public function __construct()
	{
		$this->name = 'mailjet';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->displayName = 'Mailjet';

		parent::__construct();

		$this->description = $this->l('This modules sends through Mailjet all email coming from your Prestashop installation');

		if (Configuration::get('MAILJET_ACTIVATE') == 1 && (strlen(Configuration::get('MAILJET_API_KEY')) < 3 || strlen(Configuration::get('MAILJET_SECRET_KEY')) < 3))
			$this->warning = $this->l('The module is activated but api key or secret key are not correctly set.');

		// Defines ajax lang variables in way to translate them
		$this->l('Mailjet Test E-mail');
		$this->l('Hello');
		$this->l('This E-mail confirms you that Mailjet has successfully been installed on your shop.');
		$this->l('The E-mail was not successfully sent');
	}


	/*
	** Install / Uninstall Methods
	**
	*/

	public function install()
	{
		// Can't do anything else for retrocompatibility
		if (md5_file(dirname(__FILE__).'/override/Message.php') != md5_file(dirname(__FILE__).'/../../tools/swift/Swift/Message.php'))
			return false;
		if (!@copy(dirname(__FILE__).'/override/Message-mailjet.php', dirname(__FILE__).'/../../tools/swift/Swift/Message.php'))
			return false;

		// Create Token
		Configuration::updateValue('MAILJET_TOKEN', md5(rand()));

		// Install module
		if (!parent::install())
			return false;

		return true;
	}

	public function uninstall()
	{
		// Can't do anything else for retrocompatibility
		if (md5_file(dirname(__FILE__).'/override/Message-mailjet.php') != md5_file(dirname(__FILE__).'/../../tools/swift/Swift/Message.php'))
			return false;
		if (!@copy(dirname(__FILE__).'/override/Message.php', dirname(__FILE__).'/../../tools/swift/Swift/Message.php'))
			return false;

		// Uninstall module
		Configuration::updateValue('PS_MAIL_METHOD', 1);
		Configuration::updateValue('PS_MAIL_SERVER', "");
		Configuration::updateValue('PS_MAIL_USER', "");
		Configuration::updateValue('PS_MAIL_PASSWD', "");
		Configuration::updateValue('PS_MAIL_SMTP_ENCRYPTION', "");
		Configuration::updateValue('PS_MAIL_SMTP_PORT', 25);
		if (!Configuration::deleteByName('MAILJET_TOKEN') OR !Configuration::deleteByName('MAILJET_SECRET_KEY') OR !Configuration::deleteByName('MAILJET_API_KEY') OR !parent::uninstall())
			return false;

		return true;
	}


	/*
	** Form Config Methods
	**
	*/

	public function getContent()
	{
		global $cookie;

		$lang = new Language((int)($cookie->id_lang));
		if (!in_array($lang->iso_code, array('fr', 'en', 'es')))
			$lang->iso_code = 'en';

		$output = '<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/'.$this->name.'/ajax.js"></script>
		<p style="margin-bottom: 5px;"><img src="'.__PS_BASE_URI__.'modules/'.$this->name.'/logo-mailjet.jpg" alt="" /></p>';

		if (Tools::isSubmit('submitMailjet'))
		{
			Configuration::updateValue('MAILJET_API_KEY', pSQL(Tools::getValue('mailjet_api_key')));
			Configuration::updateValue('MAILJET_SECRET_KEY', pSQL(Tools::getValue('mailjet_secret_key')));
			Configuration::updateValue('MAILJET_ACTIVATE', (int)(Tools::getValue('mailjet_activation')));
                        
			// If mailjet activation, let's configure
                        if ((int)Tools::getValue('mailjet_activation') == 1)
                        {
				Configuration::updateValue('PS_MAIL_METHOD', 2);
				Configuration::updateValue('PS_MAIL_SERVER', "in.mailjet.com");
				Configuration::updateValue('PS_MAIL_USER', pSQL(Configuration::get('MAILJET_API_KEY')));
				Configuration::updateValue('PS_MAIL_PASSWD', pSQL(Configuration::get('MAILJET_SECRET_KEY')));
				Configuration::updateValue('PS_MAIL_SMTP_ENCRYPTION', "tls");
				Configuration::updateValue('PS_MAIL_SMTP_PORT', 465);
			}
			else
			{
				Configuration::updateValue('PS_MAIL_METHOD', 1);
				Configuration::updateValue('PS_MAIL_SERVER', "");
				Configuration::updateValue('PS_MAIL_USER', "");
				Configuration::updateValue('PS_MAIL_PASSWD', "");
				Configuration::updateValue('PS_MAIL_SMTP_ENCRYPTION', "");
				Configuration::updateValue('PS_MAIL_SMTP_PORT', 25);
                        }

                        $output .= '
                        <div class="conf confirm">
                                <img src="../img/admin/ok.gif" alt="" title="" />
                                '.$this->l('Settings updated').'
                        </div>';
                }

		$chk_yes = "";
		$chk_no = " checked=\"checked\"";

		if ((int)(Tools::getValue('mailjet_activation', Configuration::get('MAILJET_ACTIVATE'))) == 1)
		{
			$chk_yes = ' checked="checked"';
			$chk_no = '';
                }

                $output .= '
		<div>
			<p style="margin-bottom:10px;">
				<b>'.$this->l('This module sends through Mailjet all email coming from your Prestashop installation (and most third party modules)').'.</b>
			</p>
			<form action="'.htmlentities($_SERVER['REQUEST_URI']).'" method="post">
				<fieldset>
					<legend><img src="../img/admin/cog.gif" alt="" class="middle" />'.$this->l('Settings').'</legend>
					<label>'.$this->l('Mailjet API Key:').'</label>
					<div class="margin-form">
						<input type="text" name="mailjet_api_key" id="mailjet_api_key" size="30" value="'.htmlentities(Tools::getValue('mailjet_api_key', Configuration::get('MAILJET_API_KEY'))).'" />
					</div>
					<hr size="1" style="margin-bottom: 20px;" noshade />
					<label>'.$this->l('Mailjet Secret Key').'</label>
					<div class="margin-form">
						<input type="text" name="mailjet_secret_key" id="mailjet_secret_key" size="30" value="'.htmlentities(Tools::getValue('mailjet_secret_key', Configuration::get('MAILJET_SECRET_KEY'))).'" />
					</div>
					<hr size="1" style="margin-bottom: 20px;" noshade />
					<label style="vertical-align: middle;">'.$this->l('Send Email through Mailjet:').'</label>
					<div class="margin-form" style="margin-top: 5px;">
						<input type="radio" name="mailjet_activation" value="1" style="vertical-align: middle;" '.$chk_yes.' /> '.$this->l('Yes').'&nbsp;
						<input type="radio" name="mailjet_activation" id="mailjet_activation_no" value="0" style="vertical-align: middle;" '.$chk_no.' /> '.$this->l('No').'
					</div>
					<hr size="1" style="margin-bottom: 20px;" noshade />
					<div class="conf confirm" id="mailjet_test_ok" style="display:none">
						<img src="../img/admin/ok.gif" alt="" title="" />
						'.$this->l('Authentication successful ! Your configuration is correct.').'
					</div>
					<div class="conf error" id="mailjet_test_ko" style="display:none">
						<img src="../img/admin/forbbiden.gif" alt="" title="" />
						'.$this->l('An Error has occured : ').'<span id="mailjet_error_message"></span>
						<p>'.$this->l('If you don\'t understand this error please contact').' <a href="http://fr.mailjet.com/support" target="_blank">Mailjet Support</a></p>
					</div>
					<div id="div_email_test" style="display:none">
						<p style="text-align:center">'.$this->l('E-mail From / to :').'&nbsp;<input type="text" id="email_from" value="'.htmlentities(Configuration::get('PS_SHOP_EMAIL')).'" size="40" />&nbsp;<input type="button" name="sendTestMailjet" value="'.$this->l('Send').'" class="button" rel="'.htmlentities(Configuration::get('MAILJET_TOKEN')).'" id="button_send_mailjet" /></p>
						<hr size="1" style="margin-bottom: 20px;" noshade />
					</div>
					<center><input type="button" name="testMailjet" value="'.$this->l('Test Configuration').'" class="button" id="button_test_mailjet" /><img src="'.__PS_BASE_URI__.'modules/'.$this->name.'/ajax-mailjet.gif" id="image_ajax_mailjet" style="display:none" />&nbsp;<input type="submit" name="submitMailjet" value="'.$this->l('Save settings').'" class="button" /></center>
				</fieldset>
			</form>
		</div>';

                return $output;
        }
}

