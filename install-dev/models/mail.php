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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class InstallModelMail extends InstallAbstractModel
{
	/**
	 * Send a test email
	 */
	public function sendTestMail($smtp_checked, $server, $login, $password, $port, $encryption, $email)
	{
		require_once(_PS_INSTALL_PATH_.'../tools/swift/Swift.php');
		require_once(_PS_INSTALL_PATH_.'../tools/swift/Swift/Connection/SMTP.php');
		require_once(_PS_INSTALL_PATH_.'../tools/swift/Swift/Connection/NativeMail.php');

		try
		{
			// Test with custom SMTP connection
			if ($smtp_checked)
			{

				$smtp = new Swift_Connection_SMTP($server, $port, ($encryption == "off") ? Swift_Connection_SMTP::ENC_OFF : (($encryption == "tls") ? Swift_Connection_SMTP::ENC_TLS : Swift_Connection_SMTP::ENC_SSL));
				$smtp->setUsername($login);
				$smtp->setpassword($password);
				$smtp->setTimeout(5);
				$swift = new Swift($smtp);
			}
			else
				// Test with normal PHP mail() call
				$swift = new Swift(new Swift_Connection_NativeMail());

			$subject = $this->language->l('Test message from PrestaShop');
			$content = $this->language->l('This is a test message, your server is now available to send email');
			$message = new Swift_Message($subject, $content, 'text/html');

			if (@$swift->send($message, $email, 'no-reply@'.Tools::getHttpHost()))
				$result = true;
			else
				$result = 999;

			$swift->disconnect();
		}
		catch (Swift_Exception $e)
		{
			$result = $e->getCode();
		}

		return $result;
	}
}
