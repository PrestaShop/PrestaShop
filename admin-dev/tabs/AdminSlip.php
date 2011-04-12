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

class AdminSlip extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'order_slip';
	 	$this->className = 'OrderSlip';
		$this->edit = true;
	 	$this->delete = true;
		$this->noAdd = true;
		
 		$this->fieldsDisplay = array(
		'id_order_slip' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'id_order' => array('title' => $this->l('ID Order'), 'width' => 75, 'align' => 'center'),
		'date_add' => array('title' => $this->l('Date issued'), 'width' => 60, 'type' => 'date', 'align' => 'right'));
		
		$this->optionTitle = $this->l('Slip');
		
		parent::__construct();
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
				$orderSlips = OrderSlip::getSlipsIdByDate(Tools::getValue('date_from'), Tools::getValue('date_to'));
				if (count($orderSlips))
					Tools::redirectAdmin('pdf.php?slips&date_from='.urlencode(Tools::getValue('date_from')).'&date_to='.urlencode(Tools::getValue('date_to')).'&token='.$this->token);
				$this->_errors[] = $this->l('No order slips found for this period');
			}
		}
		return parent::postProcess();
	}

	public function display()
	{
		global $cookie, $currentIndex;		

		echo '<div style="float:left;width:600px">';
		$this->getList((int)($cookie->id_lang), !Tools::getValue($this->table.'Orderby') ? 'date_add' : NULL, !Tools::getValue($this->table.'Orderway') ? 'DESC' : NULL);
		$this->displayList();
		echo '</div>';
		
		echo '
		<fieldset style="float:left;width:280px"><legend><img src="../img/admin/pdf.gif" alt="" /> '.$this->l('Print PDF').'</legend>
			<form action="'.$currentIndex.'&token='.$this->token.'" method="post">
				<label style="width:90px">'.$this->l('From:').' </label>
				<div class="margin-form" style="padding-left:100px">
					<input type="text" size="4" maxlength="10" name="date_from" value="'.date('Y-m-01').'" style="width: 120px;" />
					<p class="clear">'.$this->l('Format: 2007-12-31 (inclusive)').'</p>
				</div>
				<label style="width:90px">'.$this->l('To:').' </label>
				<div class="margin-form" style="padding-left:100px">
					<input type="text" size="4" maxlength="10" name="date_to" value="'.date('Y-m-t').'" style="width: 120px;" />
					<p class="clear">'.$this->l('Format: 2008-12-31 (inclusive)').'</p>
				</div>
				<div class="margin-form" style="padding-left:100px">
					<input type="submit" value="'.$this->l('Generate PDF file').'" name="submitPrint" class="button" />
				</div>
			</form>
		</fieldset><div class="clear">&nbsp;</div>';
	}
	
	public function displayListContent($token = NULL)
	{
		global $currentIndex, $cookie;
		$irow = 0;
		if ($this->_list)
			foreach ($this->_list AS $tr)
			{
				$tr['id_order'] = $this->l('#').sprintf('%06d', $tr['id_order']);
				echo '<tr'.($irow++ % 2 ? ' class="alt_row"' : '').'>';
				echo '<td class="center"><input type="checkbox" name="'.$this->table.'Box[]" value="'.$tr['id_order_slip'].'" class="noborder" /></td>';
				foreach ($this->fieldsDisplay AS $key => $params)
					echo '<td class="pointer" onclick="document.location = \'pdf.php?id_order_slip='.$tr['id_order_slip'].'\'">'.$tr[$key].'</td>';
				echo '<td class="center">';
				echo '
				<a href="pdf.php?id_order_slip='.$tr['id_order_slip'].'">
				<img src="../img/admin/details.gif" border="0" alt="'.$this->l('View').'" title="'.$this->l('View').'" /></a>';
				echo '
				<a href="'.$currentIndex.'&id_'.$this->table.'='.$tr['id_order_slip'].'&delete'.$this->table.'&token='.($token!=NULL ? $token : $this->token).'" onclick="return confirm(\''.$this->l('Delete item #', __CLASS__, true, false).$tr['id_order_slip'].$this->l('?', __CLASS__, true, false).'\');">
				<img src="../img/admin/delete.gif" border="0" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a>';
				echo '</td>';
				echo '</tr>';
			}
	}
}

