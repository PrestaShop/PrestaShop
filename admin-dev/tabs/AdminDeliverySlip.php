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

class AdminDeliverySlip extends AdminTab
{
	public function __construct()
	{
		global $cookie;

		$this->table = 'delivery';
		
		$this->optionTitle = $this->l('Delivery slips options');
		$this->_fieldsOptions = array(
			'PS_DELIVERY_PREFIX' => array('title' => $this->l('Delivery prefix:'), 'desc' => $this->l('Prefix used for delivery slips'), 'size' => 6, 'type' => 'textLang'),
			'PS_DELIVERY_NUMBER' => array('title' => $this->l('Delivery number:'), 'desc' => $this->l('The next delivery slip will begin with this number, and then increase with each additional slip'), 'size' => 6, 'type' => 'text'),
		);

		parent::__construct();
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();
		
		$output = '
		<h2>'.$this->l('Print PDF delivery slips').'</h2>
		<fieldset>
			<form action="'.$currentIndex.'&submitPrint=1&token='.$this->token.'" method="post">
				<label>'.$this->l('From:').' </label>
				<div class="margin-form">
					<input type="text" size="4" maxlength="10" name="date_from" value="'.(date('Y-m-d')).'" style="width: 120px;" /> <sup>*</sup>
					<p class="clear">'.$this->l('Format: 2007-12-31 (inclusive)').'</p>
				</div>
				<label>'.$this->l('To:').' </label>
				<div class="margin-form">
					<input type="text" size="4" maxlength="10" name="date_to" value="'.(date('Y-m-d')).'" style="width: 120px;" /> <sup>*</sup>
					<p class="clear">'.$this->l('Format: 2008-12-31 (inclusive)').'</p>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('Generate PDF file').'" name="submitPrint" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required fields').'</div>
			</form>
		</fieldset>';
		
		echo $output;
	}
	
	public function display()
	{
		$this->displayForm();
		$this->displayOptionsList();
	}
	
	public function postProcess()
	{
		global $currentIndex;
		
		if (Tools::getValue('submitPrint'))
		{
			if (!Validate::isDate($_POST['date_from']))
				$this->_errors[] = $this->l('Invalid from date');
			if (!Validate::isDate($_POST['date_to']))
				$this->_errors[] = $this->l('Invalid end date');
			if (!sizeof($this->_errors))
			{
				$orders = Order::getOrdersIdByDate($_POST['date_from'], $_POST['date_to'], NULL, 'delivery');
				if (sizeof($orders))
					Tools::redirectAdmin('pdf.php?deliveryslips='.urlencode(serialize($orders)).'&token='.$this->token);
				else
					$this->_errors[] = $this->l('No delivery slip found for this period');
			}			
		}
		else
			parent::postProcess();
	}
}


