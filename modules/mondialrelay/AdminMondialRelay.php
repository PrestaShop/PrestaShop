<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once(dirname(__FILE__).'/classes/MondialRelayClass.php');
require_once(dirname(__FILE__).'/mondialrelay.php');

class AdminMondialRelay extends AdminTab
{
	private $mondialrelay = NULL;

	public function __construct()
	{
		MondialRelay::initModuleAccess();
		
		$this->table = 'mr_selected';
		$this->className = 'MondialRelayClass';
		
		parent::__construct();
	}

	private function displayOrdersTable()
	{
		global $cookie;

		$mondialrelay = new MondialRelay();
		$order_state = new OrderState((int)(Configuration::get('MONDIAL_RELAY_ORDER_STATE')), $cookie->id_lang);
		$mr_weight_coef = (int)(Configuration::get('MR_WEIGHT_COEF'));
		
		$html = '';
		
		$html .= $this->l('To generate labels, you must register a correct address for your store on').
			' <a href="index.php?tab=AdminContact&token='.Tools::getAdminToken('AdminContact'.
			(int)(Tab::getIdFromClassName('AdminContact')).(int)($cookie->id_employee)).'" class="green">'.
			$this->l('The contact page').'</a>';
		$html .= '<p>'.$this->l('All orders which have the state').' "<b>'.$order_state->name.'</b>"';
		$html .= '.&nbsp;<a href="index.php?tab=AdminModules&configure=mondialrelay&token='.
			Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).
			(int)($cookie->id_employee)).'" class="green">' . $this->l('Change configuration') . '</a></p>
			<div class="PS_MRErrorList error" id="otherErrors">
							<img src="'._PS_IMG_.'admin/error2.png" alt="" />
							<span></span>
					</div>';

		$orders = MondialRelay::getOrders();
		if (empty($orders))
			$html.= '<h3 style="color:red;">' . $this->l('No orders with this state.') . '</h3>';
		else
		{
			$html.= '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
			$html.= "\n<table class=\"table\" id='orders'>";
			$html.= '<tr>';
			$html.= '<th>'.$this->l('Order ID').'</th>';
			$html.= '<th>'.$this->l('Customer').'</th>';
			$html.= '<th>'.$this->l('Total price').'</th>';
			$html.= '<th>'.$this->l('Total shipping').'</th>';
			$html.= '<th>'.$this->l('Date').'</th>';
			$html.= '<th>'.$this->l('Put a Weight (grams)').'</th>';
			$html.= '<th class="fixed"><a href="javascript:void(0);" id="toggleStatusOrderList">'.$this->l('Toggle selection').'</a><br /></th>';
			$html.= '<th>'.$this->l('MR Number').'</th>';
			$html.= '<th>'.$this->l('MR Country').'</th>';
			$html.= '<th>'.$this->l('Exp Number').'</th>';
			$html.= '<th>'.$this->l('Detail').'</th>';
			$html.= '</tr>';
			
			foreach ($orders as $order)
			{
				if ($order['weight'] == 0) 
				{
					$result_weight = Db::getInstance()->getRow('
					SELECT SUM(product_weight * product_quantity) as weight
					FROM '._DB_PREFIX_.'order_detail
					WHERE id_order = '.(int)($order['id_order']));
					$order['weight'] = round($mr_weight_coef * $result_weight['weight']);
				}

				$html .= '<tr id="PS_MRLineOrderInformation-'.$order['id_order'].'">';
				$html .= '<td>'.$order['id_order'].'</td>';
				$html .= '<td>'.$order['customer'].'</td>';
				$html .= '<td>'.Tools::displayPrice($order['total'], new Currency($order['id_currency'])) . '</td>';
				$html .= '<td>'.Tools::displayPrice($order['shipping'], new Currency($order['id_currency'])) . '</td>';
				$html .= '<td>'.Tools::displayDate($order['date'], $order['id_lang']).'</td>';
				$html .= '<td><input type="text" name="weight_'.$order['id_order'].'" id="weight_' . $order['id_order'] . '" size="7" value="'.$order['weight'].'" /></td>';
				$html .= '<td><input type="checkbox" class="order_id_list" name="order_id_list[]" id="order_id_list" value="'.$order['id_order'].'" /></td>';
				$html .= '<td>'.$order['MR_Selected_Num'].'</td>';
				$html .= '<td>'.$order['MR_Selected_Pays'].'</td>';
				$html .= '<td>'.$order['exp_number'].'</td>';
				$html .= '
					<td class="center">
						<a href="index.php?tab=AdminOrders&id_order='.$order['id_order'].'&vieworder&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)).'">
						<img border="0" title="'.$this->l('View').'" alt="'.$this->l('View').'" src="'._PS_IMG_.'admin/details.gif"/></a>
					</td>
					</tr>
					<tr class="PS_MRErrorList error" id="errorCreatingTicket_'.$order['id_order'].'" style="display:none;">
						<td colspan="11" style="background:url('._PS_IMG_.'admin/error2.png) 10px 10px no-repeat;">
							<span></span>
						</td>
					</tr>
					<tr class="PS_MRSuccessList" id="successCreatingTicket_'.$order['id_order'].'" style="display:none;">
						<td>'.$order['id_order'].'</td>
						<td colspan="10" style="background:url('._PS_IMG_.'admin/ok2.png) 10px 5px no-repeat #DFFAD3;">
						'.$this->l('Operation successful').'
						<span></span>
						</td>
					</tr>';
			}
			$html .= '
					</table>';
			$html .= '
				<div class="submit_button">
					<div class="PS_MRSubmitButton" id="PS_MRSubmitButtonGenerateTicket">
						<input type="button" name="generate" id="generate" value="' . $this->l('Generate') . '" class="button" />
					</div>
					<div class="PS_MRLoader" id="PS_MRSubmitGenerateLoader"><img src="'.MondialRelay::$moduleURL.'images/getTickets.gif"</div>
				</div>';
			$html .= '</form>';
		}
		unset($mondialrelay);
		unset($order_state);
		return $html;
	}

	public function displayhistoriqueForm()
	{
		$mondialrelay = new MondialRelay();
		$_html = '';
	  $query = "SELECT * FROM `" . _DB_PREFIX_ ."mr_historique` ORDER BY `id` DESC ;";
		$query = Db::getInstance()->ExecuteS($query);
		
		$_html.= '
			<fieldset>
				<legend>' . $this->l('History of labels creation') . '</legend>
				<div style="overflow-x: auto;overflow-y: scroller; height: 300px; padding-top: 0.6em;" >
					<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
						<table class="table" id="PS_MRHistoriqueTableList">
							<tbody>
								<tr>
									<th><a href="javascript:void(0);" id="toggleStatusHistoryList">' . $this->l('Toggle selection') . '</a></th>
			 						<th>' . $this->l('Order ID') . '</th>
			 						<th>' . $this->l('Exp num') . '</th>
			 						<th>' . $this->l('Print stick A4') . '</th>
			 						<th>' . $this->l('Print stick A5') . '</th>
			 					</tr>';
		foreach ($query AS $k => $row) 
	  {
			$_html.= '
				<tr id="detailHistory_'.$row['order'].'">
					<td>
						<input type="checkbox" id="PS_MRHistoryId_'.$row['id'].'" class="history_id_list" name="history_id_list[]" value="' . $row['id'] . '" />
					</td>
					<td>'.$row['order'].'</td>
					<td id="expeditionNumber_'.$row['order'].'">'.$row['exp'].'</td>
					<td id="URLA4_'.$row['order'].'">
						<a href="'.$row['url_a4'].'" target="a4"><img width="20" src="'.MondialRelay::$moduleURL.'images/pdf_icon.jpg" /></a>
					</td>
					<td id="URLA5_'.$row['order'].'">
						<a href="'.$row['url_a5'].'" target="a5"><img width="20" src="'.MondialRelay::$moduleURL.'images/pdf_icon.jpg" /></a>
					</td>
				</tr>';
	  }
		$_html .= '
					</tbody>
				</table>
				<div class="PS_MRSubmitButton">
					<input type="button" id="PS_MRSubmitButtonDeleteHistories" name="deleteSelectedHistories" value="' . $this->l('Delete selected history') . '" class="button" />
					<div class="PS_MRLoader" id="PS_MRSubmitDeleteHistoriesLoader">
						<img src="'.MondialRelay::$moduleURL.'images/getTickets.gif"
					</div>
				</div>
			</form></div></fieldset>';

		return $_html;
	}

	public function display()
	{	
		$html = '';
		
		// Allow to override the older jquery to use a new one :)
		// Added for the 1.3 compatibility to keep using the recent code
		if (_PS_VERSION_ < '1.4')
			$html .= MondialRelay::getjQueryCompatibility();

			$html .= '
				<script type="text/javascript" language="javascript">
				var _PS_MR_MODULE_DIR_ = "'.MondialRelay::$moduleURL.'";
				</script>';
			
			$html .= $this->displayOrdersTable();
			$html .= '<br/><br/>';
			$html .= $this->displayhistoriqueForm();
		echo $html;
	}
}

?>
