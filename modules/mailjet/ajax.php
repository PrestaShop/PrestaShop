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

require('../../config/config.inc.php');
require('mailjet.php');

if (Tools::getValue('token') == '' || Tools::getValue('token') != Configuration::get('MAILJET_TOKEN'))
	die('Invalid Token');

$obj_mailjet = new Mailjet();
$email_from = urldecode($_GET['email_from']);
try {
	$sujet = $obj_mailjet->l('Mailjet Test E-mail');
	$message = $obj_mailjet->l('Hello').",\r\n\r\n".$obj_mailjet->l('This E-mail confirms you that Mailjet has successfully been installed on your shop.');
	$result = Mail::sendMailTest(true, "in.mailjet.com", $message, $sujet, "text/plain", $email_from, $email_from, $_GET['mailjet_api_key'], $_GET['mailjet_secret_key'], $smtpPort = 465, "tls");

	if ($result === true)
		echo "true";
	else
	{
		if ($result == 999)
			$result = $obj_mailjet->l('The E-mail was not successfully sent');	
		echo "false|".$result;
		reset_config_mailjet();
	}
} catch(Exception $e) {
	echo "false|".$e->getMessage();
	reset_config_mailjet();
}

function reset_config_mailjet()
{
	Configuration::updateValue('MAILJET_ACTIVATE', 0);
	Configuration::updateValue('PS_MAIL_METHOD', 1);
	Configuration::updateValue('PS_MAIL_SERVER', "");
	Configuration::updateValue('PS_MAIL_USER', "");
	Configuration::updateValue('PS_MAIL_PASSWD', "");
	Configuration::updateValue('PS_MAIL_SMTP_ENCRYPTION', "");
	Configuration::updateValue('PS_MAIL_SMTP_PORT', 25);	
}

