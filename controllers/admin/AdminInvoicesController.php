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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminInvoicesControllerCore extends AdminController
{
	public function __construct()
	{
		$this->table = 'invoice';

		$this->options = array(
			'general' => array(
				'title' =>	$this->l('Invoice options'),
				'fields' =>	array(
					'PS_INVOICE' => array('title' => $this->l('Enable invoices:'), 'desc' => $this->l('Select whether or not to activate invoices for your shop'), 'cast' => 'intval', 'type' => 'bool'),
					'PS_INVOICE_PREFIX' => array('title' => $this->l('Invoice prefix:'), 'desc' => $this->l('Prefix used for invoices'), 'size' => 6, 'type' => 'textLang'),
					'PS_INVOICE_START_NUMBER' => array('title' => $this->l('Invoice number:'), 'desc' => $this->l('The next invoice will begin with this number, and then increase with each additional invoice. Set to 0 if you want to keep the current number (#').(Order::getLastInvoiceNumber() + 1).').', 'size' => 6, 'type' => 'text', 'cast' => 'intval'),
					'PS_INVOICE_FREE_TEXT' => array('title' => $this->l('Free Text:'), 'desc' => $this->l('This text will appear at the bottom of the invoice'), 'size' => 6, 'type' => 'textareaLang',
						'cols' => 40, 'rows' => 8)
				),
				'submit' => array()
			),
		);

		parent::__construct();
	}

	public function initContent()
	{
		$statuses = OrderState::getOrderStates($this->context->language->id);
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT COUNT(*) as nbOrders, (
			SELECT oh.id_order_state
			FROM '._DB_PREFIX_.'order_history oh
			WHERE oh.id_order = o.id_order
			ORDER BY oh.date_add DESC, oh.id_order_history DESC
			LIMIT 1
		) id_order_state
		FROM '._DB_PREFIX_.'orders o
		WHERE o.id_shop IN('.implode(', ', $this->context->shop->getListOfID()).')
		GROUP BY id_order_state');
		$statusStats = array();
		foreach ($result as $row)
			$statusStats[$row['id_order_state']] = $row['nbOrders'];

		$this->context->smarty->assign(array(
			'date' => date('Y-m-d'),
			'statuses' => $statuses,
			'statusStats' => $statusStats
		));
		parent::initContent();
	}

	public function postProcess()
	{
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
		elseif (Tools::isSubmit('submitPrint2'))
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
		else
			parent::postProcess();
	}

	public function beforeUpdateOptions()
	{
		if ((int)Tools::getValue('PS_INVOICE_START_NUMBER') != 0 AND (int)Tools::getValue('PS_INVOICE_START_NUMBER') <= Order::getLastInvoiceNumber())
				$this->_errors[] = $this->l('Invalid invoice number (must be > ').Order::getLastInvoiceNumber() .')';
	}
}
