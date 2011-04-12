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

class AdminInvoices extends AdminTab
{
	public function __construct()
	{
		global $cookie;

		$this->table = 'invoice';

		$this->optionTitle = $this->l('Invoice options');
		$this->_fieldsOptions = array(
			'PS_INVOICE' => array('title' => $this->l('Enable invoices:'), 'desc' => $this->l('Select whether or not to activate invoices for your shop'), 'cast' => 'intval', 'type' => 'bool'),
			'PS_INVOICE_PREFIX' => array('title' => $this->l('Invoice prefix:'), 'desc' => $this->l('Prefix used for invoices'), 'size' => 6, 'type' => 'textLang'),
			'PS_INVOICE_START_NUMBER' => array('title' => $this->l('Invoice number:'), 'desc' => $this->l('The next invoice will begin with this number, and then increase with each additional invoice. Set to 0 if you wan\'t to keep the current number (#').(Order::getLastInvoiceNumber() + 1).').', 'size' => 6, 'type' => 'text', 'cast' => 'intval')
		);

		parent::__construct();
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;

		$statuses = OrderState::getOrderStates($cookie->id_lang);
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT COUNT(*) as nbOrders, (
			SELECT oh.id_order_state
			FROM '._DB_PREFIX_.'order_history oh
			WHERE oh.id_order = o.id_order
			ORDER BY oh.date_add DESC, oh.id_order_history DESC
			LIMIT 1
		) id_order_state
		FROM '._DB_PREFIX_.'orders o
		GROUP BY id_order_state');
		$statusStats = array();
		foreach ($result as $row)
			$statusStats[$row['id_order_state']] = $row['nbOrders'];

		echo '
		<h2>'.$this->l('Print PDF invoices').'</h2>
		<fieldset style="float:left;width:300px"><legend><img src="../img/admin/pdf.gif" alt="" /> '.$this->l('By date').'</legend>
			<form action="'.$currentIndex.'&token='.$this->token.'" method="post">
				<label style="width:90px">'.$this->l('From:').' </label>
				<div class="margin-form" style="padding-left:100px">
					<input type="text" size="4" maxlength="10" name="date_from" value="'.(date('Y-m-d')).'" style="width: 120px;" /> <sup>*</sup>
					<p class="clear">'.$this->l('Format: 2007-12-31 (inclusive)').'</p>
				</div>
				<label style="width:90px">'.$this->l('To:').' </label>
				<div class="margin-form" style="padding-left:100px">
					<input type="text" size="4" maxlength="10" name="date_to" value="'.(date('Y-m-d')).'" style="width: 120px;" /> <sup>*</sup>
					<p class="clear">'.$this->l('Format: 2008-12-31 (inclusive)').'</p>
				</div>
				<div class="margin-form" style="padding-left:100px">
					<input type="submit" value="'.$this->l('Generate PDF file').'" name="submitPrint" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required fields').'</div>
			</form>
		</fieldset>
		<fieldset style="float:left;width: 500px;margin-left:10px"><legend><img src="../img/admin/pdf.gif" alt="" /> '.$this->l('By statuses').'</legend>
			<form action="'.$currentIndex.'&token='.$this->token.'" method="post">
				<label style="width:90px">'.$this->l('Statuses').' :</label>
				<div class="margin-form" style="padding-left:100px">
					<ul>';
		foreach ($statuses as $status)
			echo '		<li style="list-style: none;">
							<input type="checkbox" name="id_order_state[]" value="'.(int)$status['id_order_state'].'" id="id_order_state_'.(int)$status['id_order_state'].'">
							<label for="id_order_state_'.(int)$status['id_order_state'].'" style="float:none;'.((isset($statusStats[$status['id_order_state']]) AND $statusStats[$status['id_order_state']]) ? '' : 'font-weight:normal;').'padding:0;text-align:left;width:100%;color:#000">
								<img src="../img/admin/charged_'.($status['invoice'] ? 'ok' : 'ko').'.gif" alt="" />
								'.$status['name'].' ('.((isset($statusStats[$status['id_order_state']]) AND $statusStats[$status['id_order_state']]) ? $statusStats[$status['id_order_state']] : '0').')
							</label>
						</li>';
		echo '		</ul>
					<p class="clear">'.$this->l('You can also export orders which have not been charged yet.').'(<img src="../img/admin/charged_ko.gif" alt="" />)</p>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('Generate PDF file').'" name="submitPrint2" class="button" />
				</div>
			</form>
		</fieldset>
		<div class="clear">&nbsp;</div>';

		return parent::displayForm();
	}

	public function display()
	{
		$this->displayForm();
		$this->displayOptionsList();
	}

	public function postProcess()
	{
		global $currentIndex;

		if (Tools::isSubmit('submitPrint'))
		{
			if (!Validate::isDate(Tools::getValue('date_from')))
				$this->_errors[] = $this->l('Invalid from date');
			if (!Validate::isDate(Tools::getValue('date_to')))
				$this->_errors[] = $this->l('Invalid end date');
			if (!sizeof($this->_errors))
			{
				$orders = Order::getOrdersIdInvoiceByDate(Tools::getValue('date_from'), Tools::getValue('date_to'), NULL, 'invoice');
				if (sizeof($orders))
					Tools::redirectAdmin('pdf.php?invoices&date_from='.urlencode(Tools::getValue('date_from')).'&date_to='.urlencode(Tools::getValue('date_to')).'&token='.$this->token);
				$this->_errors[] = $this->l('No invoice found for this period');
			}
		}
		elseif(Tools::isSubmit('submitPrint2'))
		{
			if (!is_array($statusArray = Tools::getValue('id_order_state')) OR !count($statusArray))
				$this->_errors[] = $this->l('Invalid order statuses');
			else
			{
				foreach ($statusArray as $id_order_state)
					if (count($orders = Order::getOrderIdsByStatus((int)$id_order_state)))
						Tools::redirectAdmin('pdf.php?invoices2&id_order_state='.implode('-',$statusArray).'&token='.$this->token);
				$this->_errors[] = $this->l('No invoice found for this status');
			}
		}
		elseif (Tools::isSubmit('submitOptionsinvoice'))
		{
			if ((int)(Tools::getValue('PS_INVOICE_START_NUMBER')) != 0 AND (int)(Tools::getValue('PS_INVOICE_START_NUMBER')) <= Order::getLastInvoiceNumber())
				$this->_errors[] = $this->l('Invalid invoice number (must be > ').Order::getLastInvoiceNumber() .')';
			else
				parent::postProcess();
		}
		else
			parent::postProcess();
	}
}

