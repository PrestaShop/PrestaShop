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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminAdminPreferencesControllerCore extends AdminController
{

	public function __construct()
	{
		$this->className = 'Configuration';
		$this->table = 'configuration';

		parent::__construct();

		// Upload quota
		$max_upload = (int)ini_get('upload_max_filesize');
		$max_post = (int)ini_get('post_max_size');
		$upload_mb = min($max_upload, $max_post);

		// Options list
		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('General'),
				'icon' =>	'tab-preferences',
				'fields' =>	array(
					'PRESTASTORE_LIVE' => array(
						'title' => $this->l('Automatically check for module updates'),
						'desc' => $this->l('New modules and updates are displayed on the modules page'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_SHOW_ADDONS_THEMES' => array(
						'title' => $this->l('Show PrestaShop Addons themes'),
						'desc' => $this->l('Addons themes are displayed on the themes settings page.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_COOKIE_CHECKIP' => array(
						'title' => $this->l('Check IP on the cookie'),
						'desc' => $this->l('Check the IP address of the cookie in order to avoid your cookie being stolen'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool',
						'default' => '0',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_COOKIE_LIFETIME_FO' => array(
						'title' => $this->l('Lifetime of the Front Office cookie'),
						'desc' => $this->l('Indicate the number of hours'),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'text',
						'default' => '480',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_COOKIE_LIFETIME_BO' => array(
						'title' => $this->l('Lifetime of the Back Office cookie'),
						'desc' => $this->l('Indicate the number of hours'),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'text',
						'default' => '480',
						'visibility' => Shop::CONTEXT_ALL
					),
				),
				'submit' => array()
			),
			'upload' => array(
				'title' =>	$this->l('Upload quota'),
				'icon' =>	'tab-preferences',
				'fields' => array(
					'PS_ATTACHMENT_MAXIMUM_SIZE' => array(
						'title' => $this->l('Attachment maximum size'),
						'desc' => $this->l('Set the maximum size of attachment files (in MegaBytes).').' '.$this->l('Maximum:').' '.
							((int)str_replace('M', '', ini_get('post_max_size')) > (int)str_replace('M', '', ini_get('upload_max_filesize')) ? ini_get('upload_max_filesize') : ini_get('post_max_size')),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'text',
						'suffix' => $this->l('Megabytes'),
						'default' => '2'
					),
					'PS_LIMIT_UPLOAD_FILE_VALUE' => array(
						'title' => $this->l('File value upload limit'),
						'desc' => $this->l('Define the limit upload for a downloadable product, this value have to be inferior or equal to your server\'s maximum upload file ').sprintf('(%s MB).', $upload_mb),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'text',
						'suffix' => $this->l('Megabytes'),
						'default' => '1'
					),
					'PS_LIMIT_UPLOAD_IMAGE_VALUE' => array(
						'title' => $this->l('Image value upload limit'),
						'desc' => $this->l('Define the limit upload for an image, this value have to be inferior or equal to your server\'s maximum upload file ').sprintf('(%s MB).', $upload_mb),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'text',
						'suffix' => $this->l('Megabytes'),
						'default' => '1'
					),
				),
			),
			'help' => array(
				'title' =>	$this->l('Help'),
				'icon' =>	'tab-preferences',
				'fields' =>	array(
					'PS_HELPBOX' => array(
						'title' => $this->l('Back Office help boxes'),
						'desc' => $this->l('Enable yellow help boxes which are displayed under form fields in the Back Office'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_HIDE_OPTIMIZATION_TIPS' => array(
						'title' => $this->l('Hide optimization tips'),
						'desc' => $this->l('Hide optimization tips on the Back Office homepage'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
				),
			),
			'notifications' => array(
				'title' =>	$this->l('Notifications'),
				'icon' =>	'tab-preferences',
				'fields' =>	array(
					'PS_SHOW_NEW_ORDERS' => array(
						'title' => $this->l('Show notifications for new orders'),
						'desc' => $this->l('This will display notifications when new orders are made on your shop'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_SHOW_NEW_CUSTOMERS' => array(
						'title' => $this->l('Show notifications for new customers'),
						'desc' => $this->l('This will display notifications when new customers register on your shop'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_SHOW_NEW_MESSAGES' => array(
						'title' => $this->l('Show notifications for new messages'),
						'desc' => $this->l('This will display notifications when new messages are posted on your shop'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
				),
			),
		);
	}

	public function postProcess()
	{
		$upload_max_size = (int)str_replace('M', '', ini_get('upload_max_filesize'));
		$post_max_size = (int)str_replace('M', '', ini_get('post_max_size'));
		$max_size = $upload_max_size < $post_max_size ? $upload_max_size : $post_max_size;

		if (Tools::getValue('PS_LIMIT_UPLOAD_FILE_VALUE') > $max_size || Tools::getValue('PS_LIMIT_UPLOAD_IMAGE_VALUE') > $max_size)
		{
			$this->errors[] = Tools::displayError('The limit chosen is larger than the server\'s maximum upload limit. Please increase the limits of your server.');
			return;
		}

		if (Tools::getIsset('PS_LIMIT_UPLOAD_FILE_VALUE') && !Tools::getValue('PS_LIMIT_UPLOAD_FILE_VALUE'))
			$_POST['PS_LIMIT_UPLOAD_FILE_VALUE'] = 1;

		if (Tools::getIsset('PS_LIMIT_UPLOAD_IMAGE_VALUE') && !Tools::getValue('PS_LIMIT_UPLOAD_IMAGE_VALUE'))
			$_POST['PS_LIMIT_UPLOAD_IMAGE_VALUE'] = 1;

		parent::postProcess();
	}

	/**
	 * Update PS_ATTACHMENT_MAXIMUM_SIZE
	 */
	public function updateOptionPsAttachementMaximumSize($value)
	{
		if (!$value)
			return;

		$upload_max_size = (int)str_replace('M', '', ini_get('upload_max_filesize'));
		$post_max_size = (int)str_replace('M', '', ini_get('post_max_size'));
		$max_size = $upload_max_size < $post_max_size ? $upload_max_size : $post_max_size;
		$value = ($max_size < Tools::getValue('PS_ATTACHMENT_MAXIMUM_SIZE')) ? $max_size : Tools::getValue('PS_ATTACHMENT_MAXIMUM_SIZE');
		Configuration::updateValue('PS_ATTACHMENT_MAXIMUM_SIZE', $value);
	}
}
