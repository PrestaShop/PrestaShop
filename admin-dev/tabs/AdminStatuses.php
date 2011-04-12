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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include(PS_ADMIN_DIR.'/tabs/AdminOrdersStates.php');
include(PS_ADMIN_DIR.'/tabs/AdminReturnStates.php');

class AdminStatuses extends AdminTab
{
	private $adminOrdersStates;
	private $adminReturnStates;

	public function __construct()
	{
		$this->table = array('order_state', 'order_return_state');
		$this->adminOrdersStates = new adminOrdersStates();
		$this->adminReturnStates = new adminReturnStates();

		parent::__construct();
	}

	public function viewAccess($disable = false)
	{
		$result = parent::viewAccess($disable);
		$this->adminOrdersStates->tabAccess = $this->tabAccess;
		$this->adminReturnStates->tabAccess = $this->tabAccess;
		return $result;
	}

	public function postProcess()
	{
		$this->adminOrdersStates->token = $this->token;
		$this->adminReturnStates->token = $this->token;

		$this->adminOrdersStates->postProcess($this->token);
		$this->adminReturnStates->postProcess($this->token);
	}

	public function displayErrors()
	{
		$this->adminOrdersStates->displayErrors($this->token);
		$this->adminReturnStates->displayErrors($this->token);
	}

	public function display()
	{
		global $currentIndex;

		if (!Tools::isSubmit('updateorder_return_state') AND !Tools::isSubmit('submitAddorder_return_state'))
		{
			echo '<h2>'.$this->l('Order statuses').'</h2>';
			$this->adminOrdersStates->display($this->token);
		}
		if (!Tools::isSubmit('updateorder_state') AND !Tools::isSubmit('submitAddupdateorder_state') AND !Tools::isSubmit('addorder_state'))
		{
			if (!Tools::isSubmit('updateorder_return_state') AND !Tools::isSubmit('submitAddorder_return_state'))
				echo '<div style="margin:10px">&nbsp;</div>';
			echo '<h2>'.$this->l('Order return statuses').'</h2>';
			$this->adminReturnStates->display($this->token);
		}
	}
}

