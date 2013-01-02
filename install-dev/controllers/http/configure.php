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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Step 4 : configure the shop and admin access
 */
class InstallControllerHttpConfigure extends InstallControllerHttp
{
	
	public $list_countries = array();
	
	/**
	 * @see InstallAbstractModel::processNextStep()
	 */
	public function processNextStep()
	{
		// Save shop configuration
		$this->session->shop_name = trim(Tools::getValue('shop_name'));
		$this->session->shop_activity = Tools::getValue('shop_activity');
		$this->session->install_type = Tools::getValue('db_mode');
		$this->session->shop_country = Tools::getValue('shop_country');
		$this->session->shop_timezone = Tools::getValue('shop_timezone');

		// Save admin configuration
		$this->session->admin_firstname = trim(Tools::getValue('admin_firstname'));
		$this->session->admin_lastname = trim(Tools::getValue('admin_lastname'));
		$this->session->admin_email = trim(Tools::getValue('admin_email'));
		$this->session->send_informations = Tools::getValue('send_informations');

		// If password fields are empty, but are already stored in session, do not fill them again
		if (!$this->session->admin_password || trim(Tools::getValue('admin_password')))
			$this->session->admin_password = trim(Tools::getValue('admin_password'));

		if (!$this->session->admin_password_confirm || trim(Tools::getValue('admin_password_confirm')))
			$this->session->admin_password_confirm = trim(Tools::getValue('admin_password_confirm'));
	}

	/**
	 * @see InstallAbstractModel::validate()
	 */
	public function validate()
	{
		// List of required fields
		$required_fields = array('shop_name', 'shop_country', 'shop_timezone', 'admin_firstname', 'admin_lastname', 'admin_email', 'admin_password');
		foreach ($required_fields as $field)
			if (!$this->session->$field)
				$this->errors[$field] = $this->l('Field required');

		// Check shop name
		if ($this->session->shop_name && !Validate::isGenericName($this->session->shop_name))
			$this->errors['shop_name'] = $this->l('Invalid shop name');
		else if (strlen($this->session->shop_name) > 64)
			$this->errors['shop_name'] = $this->l('The field %s is limited to %d characters', $this->l('shop name'), 64);
			
		// Check admin name
		if ($this->session->admin_firstname && !Validate::isName($this->session->admin_firstname))
			$this->errors['admin_firstname'] = $this->l('Your firstname contains some invalid characters');
		else if (strlen($this->session->admin_firstname) > 32)
			$this->errors['admin_firstname'] = $this->l('The field %s is limited to %d characters', $this->l('firstname'), 32);
		
		if ($this->session->admin_lastname && !Validate::isName($this->session->admin_lastname))
			$this->errors['admin_lastname'] = $this->l('Your lastname contains some invalid characters');
		else if (strlen($this->session->admin_lastname) > 32)
			$this->errors['admin_lastname'] = $this->l('The field %s is limited to %d characters', $this->l('lastname'), 32);
		
		// Check passwords
		if ($this->session->admin_password)
		{
			if (!Validate::isPasswdAdmin($this->session->admin_password))
				$this->errors['admin_password'] = $this->l('The password is incorrect (alphanumeric string with at least 8 characters)');
			else if ($this->session->admin_password != $this->session->admin_password_confirm)
				$this->errors['admin_password'] = $this->l('Password and its confirmation are different');
		}

		// Check email
		if ($this->session->admin_email && !Validate::isEmail($this->session->admin_email))
			$this->errors['admin_email'] = $this->l('This e-mail address is invalid');

		return count($this->errors) ? false : true;
	}

	public function process()
	{
		if (Tools::getValue('uploadLogo'))
			$this->processUploadLogo();
		else if (Tools::getValue('timezoneByIso'))
			$this->processTimezoneByIso();
	}

	/**
	 * Process the upload of new logo
	 */
	public function processUploadLogo()
	{
		$error = '';
		if (isset($_FILES['fileToUpload']['tmp_name']) && $_FILES['fileToUpload']['tmp_name'])
		{
			$file = $_FILES['fileToUpload'];
			$error = ImageManager::validateUpload($file, 300000);
			if (!strlen($error))
			{
				$tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
				if (!$tmp_name || !move_uploaded_file($file['tmp_name'], $tmp_name))
					return false;
				
				list($width, $height, $type) = getimagesize($tmp_name);
				
				$newheight = ($height > 500) ? 500 : $height;
				$percent = $newheight / $height;
				$newwidth = $width * $percent;
				$newheight = $height * $percent;
				
				if (!is_writable(_PS_ROOT_DIR_.'/img/'))
					$error = $this->l('Image folder %s is not writable', _PS_ROOT_DIR_.'/img/');
				if (!$error)
				{
					list($src_width, $src_height, $type) = getimagesize($tmp_name);
					$src_image = ImageManager::create($type, $tmp_name);
					$dest_image = imagecreatetruecolor($src_width, $src_height);
					$white = imagecolorallocate($dest_image, 255, 255, 255);
					imagefilledrectangle($dest_image, 0, 0, $src_width, $src_height, $white);
					imagecopyresampled($dest_image, $src_image, 0, 0, 0, 0, $src_width, $src_height, $src_width, $src_height);
					if (!imagejpeg($dest_image, _PS_ROOT_DIR_.'/img/logo.jpg', 95))
						$error = $this->l('An error occurred during logo copy.');
					else
					{
						imagedestroy($dest_image);
						@chmod($filename, 0664);
					}
				}
			}
			else
				$error = $this->l('An error occurred during logo upload.');
		}

		$this->ajaxJsonAnswer(($error) ? false : true, $error);
	}

	/**
	 * Obtain the timezone associated to an iso
	 */
	public function processTimezoneByIso()
	{
		$timezone = $this->getTimezoneByIso(Tools::getValue('iso'));
		$this->ajaxJsonAnswer(($timezone) ? true : false, $timezone);
	}

	/**
	 * Get list of timezones
	 *
	 * @return array
	 */
	public function getTimezones()
	{
		if (!is_null($this->cache_timezones))
			return;

		if (!file_exists(_PS_INSTALL_DATA_PATH_.'xml/timezone.xml'))
			return array();

		$xml = simplexml_load_file(_PS_INSTALL_DATA_PATH_.'xml/timezone.xml');
		$timezones = array();
		foreach ($xml->entities->timezone as $timezone)
			$timezones[] = (string)$timezone['name'];
		return $timezones;
	}

	/**
	 * Get a timezone associated to an iso
	 *
	 * @param string $iso
	 * @return string
	 */
	public function getTimezoneByIso($iso)
	{
		if (!file_exists(_PS_INSTALL_DATA_PATH_.'iso_to_timezone.xml'))
			return '';

		$xml = simplexml_load_file(_PS_INSTALL_DATA_PATH_.'iso_to_timezone.xml');
		$timezones = array();
		foreach ($xml->relation as $relation)
			$timezones[(string)$relation['iso']] = (string)$relation['zone'];
		return isset($timezones[$iso]) ? $timezones[$iso] : '';
	}

	/**
	 * @see InstallAbstractModel::display()
	 */
	public function display()
	{
		// List of activities
		$list_activities = array(
			$this->l('Lingerie and Adult'),
			$this->l('Animals and Pets'),
			$this->l('Art and Culture'),
			$this->l('Babies'),
			$this->l('Beauty and Personal Care'),
			$this->l('Cars'),
			$this->l('Computer Hardware and Software'),
			$this->l('Download'),
			$this->l('Fashion and accessories'),
			$this->l('Flowers, Gifts and Crafts'),
			$this->l('Food and beverage'),
			$this->l('HiFi, Photo and Video'),
			$this->l('Home and Garden'),
			$this->l('Home Appliances'),
			$this->l('Jewelry'),
			$this->l('Mobile and Telecom'),
			$this->l('Services'),
			$this->l('Shoes and accessories'),
			$this->l('Sports and Entertainment'),
			$this->l('Travel'),
		);
		sort($list_activities);
		$this->list_activities = $list_activities;

		// Countries list
		$this->list_countries = array();
		$countries = $this->language->getCountries();
		$top_countries = array(
			'fr', 'es', 'us',
			'gb', 'it', 'de',
			'nl', 'pl', 'id',
			'be', 'br', 'se',
			'ca', 'ru', 'cn',
		);

		foreach ($top_countries as $iso)
			$this->list_countries[] = array('iso' => $iso, 'name' => $countries[$iso]);
		$this->list_countries[] = array('name' => '-----------------');

		foreach ($countries as $iso => $lang)
			if (!in_array($iso, $top_countries))
				$this->list_countries[] = array('iso' => $iso, 'name' => $lang);

		// Try to detect default country
		if (!$this->session->shop_country)
		{
			$detect_language = $this->language->detectLanguage();
			if (isset($detect_language['primarytag']))
			{
				$this->session->shop_country = strtolower(isset($detect_language['subtag']) ? $detect_language['subtag'] : $detect_language['primarytag']);
				$this->session->shop_timezone = $this->getTimezoneByIso($this->session->shop_country);
			}
		}

		// Install type
		$this->install_type = ($this->session->install_type) ? $this->session->install_type : 'full';

		$this->displayTemplate('configure');
	}

	/**
	 * Helper to display error for a field
	 *
	 * @param unknown_type $field
	 */
	public function displayError($field)
	{
		if (!isset($this->errors[$field]))
			return;

		return '<span class="result aligned errorTxt">'.$this->errors[$field].'</span>';
	}
}
