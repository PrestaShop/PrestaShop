<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminAdminPreferencesControllerCore extends AdminController
{

	public function __construct()
	{
		$this->bootstrap = true;
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
				'icon' =>	'icon-cogs',
				'fields' =>	array(
					'PRESTASTORE_LIVE' => array(
						'title' => $this->l('Automatically check for module updates'),
						'hint' => $this->l('New modules and updates are displayed on the modules page.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_COOKIE_CHECKIP' => array(
						'title' => $this->l('Check the IP address on the cookie'),
						'hint' => $this->l('Check the IP address of the cookie in order to prevent your cookie from being stolen.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool',
						'default' => '0',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_COOKIE_LIFETIME_FO' => array(
						'title' => $this->l('Lifetime of Front Office cookies'),
						'hint' => $this->l('Indicate the number of hours.'),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'text',
						'default' => '480',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_COOKIE_LIFETIME_BO' => array(
						'title' => $this->l('Lifetime of Back Office cookies'),
						'hint' => $this->l('Indicate the number of hours.'),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'text',
						'default' => '480',
						'visibility' => Shop::CONTEXT_ALL
					),
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'upload' => array(
				'title' =>	$this->l('Upload quota'),
				'icon' =>	'icon-cloud-upload',
				'fields' => array(
					'PS_ATTACHMENT_MAXIMUM_SIZE' => array(
						'title' => $this->l('Maximum size for attachment'),
						'hint' => $this->l('Set the maximum size allowed for attachment files (in megabytes).'),
						/***** TO DO - ajouter cette ligne dans le hint ? 
						.' '.$this->l('Maximum:').' '.
							((int)str_replace('M', '', ini_get('post_max_size')) > (int)str_replace('M', '', ini_get('upload_max_filesize')) ? ini_get('upload_max_filesize') : ini_get('post_max_size')),
							*****/
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'text',
						'suffix' => $this->l('megabytes'),
						'default' => '2'
					),
					'PS_LIMIT_UPLOAD_FILE_VALUE' => array(
						'title' => $this->l('Maximum size for a downloadable product'),
						'hint' => $this->l('Define the upload limit for a downloadable product (in megabytes). This value has to be lower or equal to the maximum file upload allotted by your server. ').sprintf('(%s MB).', $upload_mb),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'text',
						'suffix' => $this->l('megabytes'),
						'default' => '1'
					),
					'PS_LIMIT_UPLOAD_IMAGE_VALUE' => array(
						'title' => $this->l('Maximum size for a product\'s image'),
						'hint' => $this->l('Define the upload limit for an image (in megabytes). This value has to be lower or equal to the maximum file upload allotted by your server. ').sprintf('(%s MB).', $upload_mb),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'text',
						'suffix' => $this->l('megabytes'),
						'default' => '1'
					),
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'help' => array(
				'title' =>	$this->l('Help'),
				'icon' =>	'icon-question-sign',
				'fields' =>	array(
					'PS_HELPBOX' => array(
						'title' => $this->l('Back Office help boxes'),
						'hint' => $this->l('Allow yellow help boxes to be displayed under the form fields in the Back Office.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool',
						'visibility' => Shop::CONTEXT_ALL
					),
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'notifications' => array(
				'title' =>	$this->l('Notifications'),
				'icon' =>	'icon-list-alt',
				'fields' =>	array(
					'PS_SHOW_NEW_ORDERS' => array(
						'title' => $this->l('Show notifications for new orders'),
						'hint' => $this->l('This will display notifications when new orders are made in your shop.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_SHOW_NEW_CUSTOMERS' => array(
						'title' => $this->l('Show notifications for new customers'),
						'hint' => $this->l('This will display notifications every time a new customer registers in your shop.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_SHOW_NEW_MESSAGES' => array(
						'title' => $this->l('Show notifications for new messages'),
						'hint' => $this->l('This will display notifications when new messages are posted in your shop.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
				),
				'submit' => array('title' => $this->l('Save'))
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
