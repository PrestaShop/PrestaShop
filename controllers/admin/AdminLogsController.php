<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminLogsControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'log';
	 	$this->className = 'Logger';
	 	$this->lang = false;
		$this->noLink = true;

	 	$this->addRowAction('delete');
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fields_list = array(
			'id_log' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'severity' => array('title' => $this->l('Severity (1-4)'), 'align' => 'center', 'width' => 50),
			'message' => array('title' => $this->l('Message')),
			'object_type' => array('title' => $this->l('Object type'), 'width' => 75),
			'object_id' => array('title' => $this->l('Object ID'), 'width' => 50),
			'error_code' => array('title' => $this->l('Error code'), 'width' => 75, 'prefix' => '0x'),
			'date_add' => array('title' => $this->l('Date'), 'width' => 150, 'align' => 'right', 'type' => 'datetime')
		);

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Logs by email'),
				'fields' =>	array(
					'PS_LOGS_BY_EMAIL' => array(
						'title' => $this->l('Minimum severity level'),
						'desc' => $this->l('Enter "5" if you do not want to receive any emails.').'<br />'.$this->l('Emails will be sent to the shop owner.'),
						'cast' => 'intval',
						'type' => 'text',
						'size' => 5
					)
				),
				'submit' => array()
			)
		);
		$this->list_no_link = true;
		parent::__construct();
	}

	public function initToolbar()
	{
		parent::initToolbar();
		unset($this->toolbar_btn['new']);
	}

}

?>
