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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(_PS_SWIFT_DIR_.'Swift.php');
include_once(_PS_SWIFT_DIR_.'Swift/Connection/SMTP.php');
include_once(_PS_SWIFT_DIR_.'Swift/Connection/NativeMail.php');
include_once(_PS_SWIFT_DIR_.'Swift/Plugin/Decorator.php');

class MailCore
{
	const TYPE_HTML = 1;
	const TYPE_TEXT = 2;
	const TYPE_BOTH = 3;

	public static function Send($id_lang, $template, $subject, $templateVars, $to,
		$toName = null, $from = null, $fromName = null, $fileAttachment = null, $modeSMTP = null, $templatePath = _PS_MAIL_DIR_, $die = false)
	{
		$configuration = Configuration::getMultiple(array(
			'PS_SHOP_EMAIL',
			'PS_MAIL_METHOD',
			'PS_MAIL_SERVER',
			'PS_MAIL_USER',
			'PS_MAIL_PASSWD',
			'PS_SHOP_NAME',
			'PS_MAIL_SMTP_ENCRYPTION',
			'PS_MAIL_SMTP_PORT',
			'PS_MAIL_METHOD',
			'PS_MAIL_TYPE'
		));

		if (!isset($configuration['PS_MAIL_SMTP_ENCRYPTION'])) $configuration['PS_MAIL_SMTP_ENCRYPTION'] = 'off';
		if (!isset($configuration['PS_MAIL_SMTP_PORT'])) $configuration['PS_MAIL_SMTP_PORT'] = 'default';

		if (!isset($from)) $from = $configuration['PS_SHOP_EMAIL'];
		if (!isset($fromName)) $fromName = $configuration['PS_SHOP_NAME'];

		if (!empty($from) && !Validate::isEmail($from))
		{
 			Tools::dieOrLog(Tools::displayError('Error: parameter \\"from\\" is corrupted'), $die);
 			return false;
		}
		if (!empty($fromName) && !Validate::isMailName($fromName))
		{
	 		Tools::dieOrLog(Tools::displayError('Error: parameter \\"fromName\\" is corrupted'), $die);
	 		return false;
		}
		if (!is_array($to) && !Validate::isEmail($to))
		{
	 		Tools::dieOrLog(Tools::displayError('Error: parameter \\"to\\" is corrupted'), $die);
	 		return false;
		}
			
		if (!is_array($templateVars))
		{
	 		Tools::dieOrLog(Tools::displayError('Error: parameter \\"templateVars\\" is not an array'), $die);
	 		return false;
		}
		
		// Do not crash for this error, that may be a complicated customer name
		if (is_string($toName))
		{
			if (!empty($toName) && !Validate::isMailName($toName))
	 			$toName = null;
		}
			
		if (!Validate::isTplName($template))
		{
	 		Tools::dieOrLog(Tools::displayError('Error: invalid e-mail template'), $die);
	 		return false;
		}
			
		if (!Validate::isMailSubject($subject))
		{
	 		Tools::dieOrLog(Tools::displayError('Error: invalid e-mail subject'), $die);
	 		return false;
		}

		/* Construct multiple recipients list if needed */
		if (is_array($to) && isset($to))
		{
			$to_list = new Swift_RecipientList();
			foreach ($to as $key => $addr)
			{
				$to_name = null;
				$addr = trim($addr);
				if (!Validate::isEmail($addr))
				{
					Tools::dieOrLog(Tools::displayError('Error: invalid e-mail address'), $die);
					return false;
				}
				if (is_array($toName))
				{
					if ($toName && is_array($toName) && Validate::isGenericName($toName[$key]))
						$to_name = $toName[$key];
				}
                /* Encode accentuated chars */
				$to_list->addTo($addr, '=?UTF-8?B?'.base64_encode($to_name).'?=');
			}
			$to_plugin = $to[0];
			$to = $to_list;
		} else {
			/* Simple recipient, one address */
			$to_plugin = $to;
			$to = new Swift_Address($to, '=?UTF-8?B?'.base64_encode($toName).'?=');
		}
		try {
			/* Connect with the appropriate configuration */
			if ($configuration['PS_MAIL_METHOD'] == 2)
			{
				if (empty($configuration['PS_MAIL_SERVER']) || empty($configuration['PS_MAIL_SMTP_PORT']))
				{
					Tools::dieOrLog(Tools::displayError('Error: invalid SMTP server or SMTP port'), $die);
					return false;
				}
				$connection = new Swift_Connection_SMTP($configuration['PS_MAIL_SERVER'], $configuration['PS_MAIL_SMTP_PORT'],
					($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'ssl') ? Swift_Connection_SMTP::ENC_SSL :
					(($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'tls') ? Swift_Connection_SMTP::ENC_TLS : Swift_Connection_SMTP::ENC_OFF));
				$connection->setTimeout(4);
				if (!$connection)
					return false;
				if (!empty($configuration['PS_MAIL_USER']))
					$connection->setUsername($configuration['PS_MAIL_USER']);
				if (!empty($configuration['PS_MAIL_PASSWD']))
					$connection->setPassword($configuration['PS_MAIL_PASSWD']);
			}
			else
				$connection = new Swift_Connection_NativeMail();

			if (!$connection)
				return false;
			$swift = new Swift($connection, Configuration::get('PS_MAIL_DOMAIN'));
			/* Get templates content */
			$iso = Language::getIsoById((int)($id_lang));
			if (!$iso)
			{
				Tools::dieOrLog(Tools::displayError('Error - No ISO code for email'), $die);
				return false;
			}
			$template = $iso.'/'.$template;

			$moduleName = false;
			$overrideMail = false;

			// get templatePath
			if (preg_match('#'.__PS_BASE_URI__.'modules/#', $templatePath) && preg_match('#modules/([a-z0-9_-]+)/#ui', $templatePath, $res))
				$moduleName = $res[1];

			if ($moduleName !== false && (file_exists(_PS_THEME_DIR_.'modules/'.$moduleName.'/mails/'.$template.'.txt') ||
				file_exists(_PS_THEME_DIR_.'modules/'.$moduleName.'/mails/'.$template.'.html')))
				$templatePath = _PS_THEME_DIR_.'modules/'.$moduleName.'/mails/';
			else if (file_exists(_PS_THEME_DIR_.'mails/'.$template.'.txt') || file_exists(_PS_THEME_DIR_.'mails/'.$template.'.html'))
			{
				$templatePath = _PS_THEME_DIR_.'mails/';
				$overrideMail  = true;
			}
			else if (!file_exists($templatePath.$template.'.txt') || !file_exists($templatePath.$template.'.html'))
			{
				Tools::dieOrLog(Tools::displayError('Error - The following e-mail template is missing:').' '.$templatePath.$template.'.txt', $die);
				return false;
			}
			$templateHtml = file_get_contents($templatePath.$template.'.html');
			$templateTxt = strip_tags(html_entity_decode(file_get_contents($templatePath.$template.'.txt'), null, 'utf-8'));

			if ($overrideMail && file_exists($templatePath.$iso.'/lang.php'))
					include_once($templatePath.$iso.'/lang.php');
			else if ($moduleName && file_exists($templatePath.$iso.'/lang.php'))
				include_once(_PS_THEME_DIR_.'mails/'.$iso.'/lang.php');
			else
				include_once(dirname(__FILE__).'/../mails/'.$iso.'/lang.php');

			/* Create mail and attach differents parts */
			$message = new Swift_Message('['.Configuration::get('PS_SHOP_NAME').'] '.$subject);
			$templateVars['{shop_logo}'] = (Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL'))) ? $message->attach(new Swift_Message_Image(new Swift_File(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL')))) : 
					((file_exists(_PS_IMG_DIR_.'logo.jpg')) ? $message->attach(new Swift_Message_Image(new Swift_File(_PS_IMG_DIR_.Configuration::get('PS_LOGO')))) : '');
			$templateVars['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME'));
			$templateVars['{shop_url}'] = Tools::getShopDomain(true, true).__PS_BASE_URI__;
			$swift->attachPlugin(new Swift_Plugin_Decorator(array($to_plugin => $templateVars)), 'decorator');
			if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT)
				$message->attach(new Swift_Message_Part($templateTxt, 'text/plain', '8bit', 'utf-8'));
			if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML)
				$message->attach(new Swift_Message_Part($templateHtml, 'text/html', '8bit', 'utf-8'));
			if ($fileAttachment && isset($fileAttachment['content']) && isset($fileAttachment['name']) && isset($fileAttachment['mime']))
				$message->attach(new Swift_Message_Attachment($fileAttachment['content'], $fileAttachment['name'], $fileAttachment['mime']));
			/* Send mail */
			$send = $swift->send($message, $to, new Swift_Address($from, $fromName));
			$swift->disconnect();
			return $send;
		}

		catch (Swift_ConnectionException $e)
		{
			return false;
		}
	}

	public static function sendMailTest($smtpChecked, $smtpServer, $content, $subject, $type, $to, $from, $smtpLogin, $smtpPassword, $smtpPort = 25, $smtpEncryption)
	{
		$swift = null;
		$result = false;
		try
		{
			if ($smtpChecked)
			{
				$smtp = new Swift_Connection_SMTP($smtpServer, $smtpPort, ($smtpEncryption == 'off') ? 
					Swift_Connection_SMTP::ENC_OFF : (($smtpEncryption == 'tls') ? Swift_Connection_SMTP::ENC_TLS : Swift_Connection_SMTP::ENC_SSL));
				$smtp->setUsername($smtpLogin);
				$smtp->setpassword($smtpPassword);
				$smtp->setTimeout(5);
				$swift = new Swift($smtp, Configuration::get('PS_MAIL_DOMAIN'));
			}
			else
				$swift = new Swift(new Swift_Connection_NativeMail(), Configuration::get('PS_MAIL_DOMAIN'));

			$message = new Swift_Message($subject, $content, $type);

			if ($swift->send($message, $to, $from))
				$result = true;

			$swift->disconnect();
		}
		catch (Swift_ConnectionException $e)
		{
			$result = $e->getMessage();
		}
		catch (Swift_Message_MimeException $e)
		{
			$result = $e->getMessage();
		}

		return $result;
	}

	/**
	 * This method is used to get the translation for email Object.
	 * For an object is forbidden to use htmlentities,
	 * we have to return a sentence with accents.
	 * 
	 * @param string $string raw sentence (write directly in file)
	 */
	public static function l($string, $id_lang = null, Context $context = null)
	{
		global $_LANGMAIL;
		if (!$context)
			$context = Context::getContext();

		$key = str_replace('\'', '\\\'', $string);
		if ($id_lang == null)
			$id_lang = (!isset($context->language) || !is_object($context->language)) ? (int)Configuration::get('PS_LANG_DEFAULT') : (int)$context->language->id;

		$file_core = _PS_ROOT_DIR_.'/mails/'.$context->language->iso_code.'/lang.php';
		if (Tools::file_exists_cache($file_core) && empty($_LANGMAIL))
			include_once($file_core);

		$file_theme = _PS_THEME_DIR_.'mails/'.$context->language->iso_code.'/lang.php';
		if (Tools::file_exists_cache($file_theme))
			include_once($file_theme);

		if (!is_array($_LANGMAIL))
			return (str_replace('"', '&quot;', $string));
		if (key_exists($key, $_LANGMAIL))
			$str = $_LANGMAIL[$key];
		else
			$str = $string;

		return str_replace('"', '&quot;', addslashes($str));
	}
}
