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
*  @version  Release: $Revision: 7300 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminContactsControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'contact';
	 	$this->className = 'Contact';
	 	$this->lang = true;
		$this->addRowAction('edit');
		$this->addRowAction('delete');
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fieldsDisplay = array(
			'id_contact' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'name' => array('title' => $this->l('Title'), 'width' => 130),
			'email' => array('title' => $this->l('E-mail address'), 'width' => 130),
			'description' => array('title' => $this->l('Description'), 'width' => 150),
		);

		$this->options = array(
			'general' => array(
				'title' =>	$this->l('Contact options'),
				'fields' =>	array(
					'PS_CUSTOMER_SERVICE_FILE_UPLOAD' => array('title' => $this->l('Allow file upload'), 'desc' => $this->l('Allow customers to upload file using contact page'), 'cast' => 'intval', 'type' => 'select', 'identifier' => 'value', 'list' => array(
						'0' => array('value' => 0, 'name' => $this->l('No')),
						'1' => array('value' => 1, 'name' => $this->l('Yes'))
					)),
					'PS_CUSTOMER_SERVICE_SIGNATURE' => array('title' => $this->l('Pre-defined message'), 'desc' => $this->l('Please fill the message that appears by default when you answer a thread on the customer service page'), 'cast' => 'pSQL', 'type' => 'textareaLang', 'identifier' => 'value', 'cols' => 40, 'rows' => 8),
				),
			),
		);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Contacts'),
				'image' => '../img/admin/contact.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Title:'),
					'name' => 'name',
					'size' => 33,
					'required' => true,
					'lang' => true,
					'attributeLang' => 'name',
					'p' => $this->l('Contact name, e.g., Technical Support'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('E-mail address'),
					'name' => 'email',
					'size' => 33,
					'required' => false,
					'p' => $this->l('E-mails will be sent to this address'),
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Save in Customer Service?'),
					'name' => 'customer_service',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'p' => $this->l('The messages will be saved in the Customer Service tab'),
					'values' => array(
						array(
							'id' => 'customer_service_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'customer_service_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Description'),
					'name' => 'description',
					'required' => false,
					'lang' => true,
					'attributeLang' => 'description',
					'cols' => 36,
					'rows' => 5,
					'p' => $this->l('Additional information about this contact'),
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		parent::__construct();
	}

	public function initContent()
	{
		if ($this->display != 'edit')
			$this->display = 'list';

		parent::initContent();

		if ($this->display == 'list')
		{
			$helper = new HelperOptions();
			$helper->id = $this->id;
			$helper->currentIndex = self::$currentIndex;
			$this->content .= $helper->generateOptions($this->options);
		}
	}
}


