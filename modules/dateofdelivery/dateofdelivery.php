<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;
	
class DateOfDelivery extends Module
{
	private $_html = '';

	public function __construct()
	{
		$this->name = 'dateofdelivery';
		$this->tab = 'shipping_logistics';
		$this->version = '1.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		
		parent::__construct();
		
		$this->displayName = $this->l('Date of delivery');
		$this->description = $this->l('Displays an approximate date of delivery');
	}
	
	public function install()
	{	
		if (!parent::install()
			|| !$this->registerHook('beforeCarrier')
			|| !$this->registerHook('orderDetailDisplayed')
			||!$this->registerHook('displayPDFInvoice'))
				return false;
		
		if (!Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'dateofdelivery_carrier_rule` (
			`id_carrier_rule` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_carrier` INT NOT NULL,
			`minimal_time` INT NOT NULL,
			`maximal_time` INT NOT NULL,
			`delivery_saturday` TINYINT(1) NOT NULL,
			`delivery_sunday` TINYINT(1) NOT NULL
		) ENGINE ='._MYSQL_ENGINE_.';
		'))
			return false;
		
		Configuration::updateValue('DOD_EXTRA_TIME_PRODUCT_OOS', 0);
		Configuration::updateValue('DOD_EXTRA_TIME_PREPARATION', 1);
		Configuration::updateValue('DOD_PREPARATION_SATURDAY', 1);
		Configuration::updateValue('DOD_PREPARATION_SUNDAY', 1);
		Configuration::updateValue('DOD_DATE_FORMAT', 'l j F Y');
		
		return true;
	}
	
	public function uninstall()
	{
		Configuration::deleteByName('DOD_EXTRA_TIME_PRODUCT_OOS');
		Configuration::deleteByName('DOD_EXTRA_TIME_PREPARATION');
		Configuration::deleteByName('DOD_PREPARATION_SATURDAY');
		Configuration::deleteByName('DOD_PREPARATION_SUNDAY');
		Configuration::deleteByName('DOD_DATE_FORMAT');
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'dateofdelivery_carrier_rule`');
		
		return parent::uninstall();
	}

	public function getContent()
	{		
		$this->_html .= '<h2>'.$this->l('Date of delivery configuration').'</h2>';
		
		$this->_postProcess();
		if (Tools::isSubmit('addCarrierRule') OR (Tools::isSubmit('editCarrierRule') AND Tools::isSubmit('id_carrier_rule')))
			$this->_setCarrierRuleForm();
		else
			$this->_setConfigurationForm();
		
		return $this->_html;
	}

	public function hookBeforeCarrier($params)
	{
		if (!isset($params['delivery_option_list']) || !count($params['delivery_option_list']))
			return false;
		
		$package_list = $params['cart']->getPackageList();

		$datesDelivery = array();
		foreach ($params['delivery_option_list'] as $id_address => $by_address)
		{
			$datesDelivery[$id_address] = array();
			foreach ($by_address as $key => $delivery_option)
			{
				$date_from = null;
				$date_to = null;
				$datesDelivery[$id_address][$key] = array();
				
				foreach ($delivery_option['carrier_list'] as $id_carrier => $carrier)
				{
					foreach ($carrier['package_list'] as $id_package)
					{
						$package = $package_list[$id_address][$id_package];
						$oos = false; // For out of stock management
						foreach ($package['product_list'] as $product)
							if (StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']) <= 0)
							{
								$oos = true;
								break;
							}
						
						$date_range = $this->_getDatesOfDelivery($id_carrier, $oos);
						if (is_null($date_from) || $date_from < $date_range[0])
						{
							$date_from = $date_range[0][1];
							$datesDelivery[$id_address][$key][0] = $date_range[0];
						}
						if (is_null($date_to) || $date_to < $date_range[1])
						{
							$date_to = $date_range[1][1];
							$datesDelivery[$id_address][$key][1] = $date_range[1];
						}
					}
				}
			}
		}
		
		$this->smarty->assign(array(
			'nbPackages' => $params['cart']->getNbOfPackages(),
			'datesDelivery' => $datesDelivery,
			'delivery_option' => $params['delivery_option']
		));
		
		return $this->display(__FILE__, 'beforeCarrier.tpl');
	}
	
	public function hookOrderDetailDisplayed($params)
	{
		
		$oos = false; // For out of stock management
		foreach ($params['order']->getProducts() as $product)
			if ($product['product_quantity_in_stock'] < 1)
				$oos = true;
		
		$datesDelivery = array();		
		$datesDelivery = $this->_getDatesOfDelivery((int)($params['order']->id_carrier), $oos, $params['order']->date_add);
		
		if (!is_array($datesDelivery) OR !sizeof($datesDelivery))
			return ;
			
		$this->smarty->assign('datesDelivery', $datesDelivery);
		
		return $this->display(__FILE__, 'orderDetail.tpl');
	}

	/**
	 * Displays the delivery dates on the invoice
	 *
	 * @param $params contains an instance of OrderInvoice
	 * @return string
	 *
	 */
	public function hookDisplayPDFInvoice($params)
	{
		$order_invoice = $params['object'];
		if (!($order_invoice instanceof OrderInvoice))
			return;

		$order = new Order((int)$order_invoice->id_order);

		$oos = false; // For out of stock management
		foreach ($order->getProducts() as $product)
			if ($product['product_quantity_in_stock'] < 1)
				$oos = true;

		$id_carrier = (int)OrderInvoice::getCarrierId($order_invoice->id);
		$return = '';
		if ($datesDelivery = $this->_getDatesOfDelivery($id_carrier, $oos, $order_invoice->date_add))
			$return = sprintf($this->l('Approximate date of delivery is between %1$s and %2$s'), $datesDelivery[0], $datesDelivery[1]);

		return $return;
	}

	private function _postProcess()
	{
		$errors = array();
		if (Tools::isSubmit('submitMoreOptions'))
		{
			if (Tools::getValue('date_format') == '' OR !Validate::isCleanHtml(Tools::getValue('date_format')))
				$errors[] = $this->l('Date format is invalid');
			
			if (!sizeof($errors))
			{
				Configuration::updateValue('DOD_EXTRA_TIME_PRODUCT_OOS', (int)(Tools::getValue('extra_time_product_oos')));
				Configuration::updateValue('DOD_EXTRA_TIME_PREPARATION', (int)(Tools::getValue('extra_time_preparation')));
				Configuration::updateValue('DOD_PREPARATION_SATURDAY', (int)(Tools::isSubmit('preparation_saturday')));
				Configuration::updateValue('DOD_PREPARATION_SUNDAY', (int)(Tools::isSubmit('preparation_sunday')));
				Configuration::updateValue('DOD_DATE_FORMAT', Tools::getValue('date_format'));
				$this->_html .= $this->displayConfirmation($this->l('Settings are updated'));
			}
			else
				$this->_html .= $this->displayError(implode('<br />', $errors));
		}
		
		if (Tools::isSubmit('submitCarrierRule'))
		{	
			if (!Validate::isUnsignedInt(Tools::getValue('minimal_time')))
				$errors[] = $this->l('Minimum time is invalid');
			if (!Validate::isUnsignedInt(Tools::getValue('maximal_time')))
				$errors[] = $this->l('Maximum time is invalid');
			if (($carrier = new Carrier((int)(Tools::getValue('id_carrier')))) AND !Validate::isLoadedObject($carrier))
				$errors[] = $this->l('Carrier is invalid');
			if ($this->_isAlreadyDefinedForCarrier((int)($carrier->id), (int)(Tools::getValue('id_carrier_rule', 0))))
				$errors[] = $this->l('You cannot use this carrier, a rule has already been saved.');
			
			if (!sizeof($errors))
			{
				if (Tools::isSubmit('addCarrierRule'))
				{
					if (Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'dateofdelivery_carrier_rule`(`id_carrier`, `minimal_time`, `maximal_time`, `delivery_saturday`, `delivery_sunday`) 
					VALUES ('.(int)($carrier->id).', '.(int)(Tools::getValue('minimal_time')).', '.(int)(Tools::getValue('maximal_time')).', '.(int)(Tools::isSubmit('delivery_saturday')).', '.(int)(Tools::isSubmit('delivery_sunday')).')
					'))
						Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&confirmAddCarrierRule');
					else
						$this->_html .= $this->displayError($this->l('An error occurred on adding of carrier rule.'));
				}
				else
				{
					if (Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'dateofdelivery_carrier_rule`  
					SET `id_carrier` = '.(int)($carrier->id).', `minimal_time` = '.(int)(Tools::getValue('minimal_time')).', `maximal_time` = '.(int)(Tools::getValue('maximal_time')).', `delivery_saturday` = '.(int)(Tools::isSubmit('delivery_saturday')).', `delivery_sunday` = '.(int)(Tools::isSubmit('delivery_sunday')).'
					WHERE `id_carrier_rule` = '.(int)(Tools::getValue('id_carrier_rule'))
					))
						Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&confirmEditCarrierRule');
					else
						$this->_html .= $this->displayError($this->l('An error occurred on updating of carrier rule.'));
				}
				
			}
			else
				$this->_html .= $this->displayError(implode('<br />', $errors));
		}
		
		if (Tools::isSubmit('deleteCarrierRule') AND Tools::isSubmit('id_carrier_rule') AND (int)(Tools::getValue('id_carrier_rule')) AND $this->_isCarrierRuleExists((int)(Tools::getValue('id_carrier_rule'))))
		{
			$this->_deleteByIdCarrierRule((int)(Tools::getValue('id_carrier_rule')));
			$this->_html .= $this->displayConfirmation($this->l('Carrier rule deleted successfully'));
		}
		
		if (Tools::isSubmit('confirmAddCarrierRule'))
			$this->_html = $this->displayConfirmation($this->l('Carrier rule added successfully'));
		
		if (Tools::isSubmit('confirmEditCarrierRule'))
			$this->_html = $this->displayConfirmation($this->l('Carrier rule updated successfully'));
	}
	
	private function _setConfigurationForm()
	{
		$this->_html .= '
		<fieldset>
			<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/img/time.png" alt="" /> '.$this->l('Carrier configuration').'</legend>
			
			<p><a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&addCarrierRule"><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/img/time_add.png" alt="" /> '.$this->l('Add a new carrier rule').'</a></p>
			
			<h3>'.$this->l('List of carrier rules').'</h3>';
			
		$carrier_rules = $this->_getCarrierRulesWithCarrierName();
		if (sizeof($carrier_rules))
		{
			$this->_html .= '<table width="100%" class="table" cellspacing="0" cellpadding="0">
			<thead>
			<tr>
				<th width="30%"><b>'.$this->l('Name of carrier').'</b></th>
				<th width="40%" class="center"><b>'.$this->l('Delivery between').'</b></th>
				<th width="10%" class="center"><b>'.$this->l('Saturday delivery').'</b></th>
				<th width="10%" class="center"><b>'.$this->l('Sunday delivery').'</b></th>
				<th width="10%" class="center"><b>'.$this->l('Actions').'</b></th>
			</tr>
			</thead>
			<tbody>
			';
			
			foreach ($carrier_rules as $rule)
			{
				$this->_html .= '
				<tr>
					<td width="30%">'.(!preg_match('/^0$/Ui', $rule['name']) ? htmlentities($rule['name'], ENT_QUOTES, 'UTF-8') : Configuration::get('PS_SHOP_NAME')).'</td>
					<td width="40%" class="center"><b>'.'</b> '.sprintf($this->l('%1$d day(s) and %2$d day(s)'), $rule['minimal_time'], $rule['maximal_time']).'</td>
					<td width="10%" class="center">';
					
				if ($rule['delivery_saturday'])
					$this->_html .= '<img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/img/tick.png" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />';
				else
					$this->_html .= '<img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/img/cross.png" alt="'.$this->l('No').'" title="'.$this->l('No').'" />';
				$this->_html .='
					</td>
					<td width="10%" class="center">';
					
				if ($rule['delivery_sunday'])
					$this->_html .= '<img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/img/tick.png" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />';
				else
					$this->_html .= '<img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/img/cross.png" alt="'.$this->l('No').'" title="'.$this->l('No').'" />';
				$this->_html .= '
					</td>
					<td width="10%" class="center">
						<a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editCarrierRule&id_carrier_rule='.(int)($rule['id_carrier_rule']).'" title="'.$this->l('Edit').'"><img src="'._PS_ADMIN_IMG_.'edit.gif" alt="" /></a> 
						<a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteCarrierRule&id_carrier_rule='.(int)($rule['id_carrier_rule']).'" title="'.$this->l('Delete').'"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="" /></a>
					</td>
				</tr>';
			}
			
			$this->_html .= '
			</tbody>
			</table>';
		}
		else
			$this->_html .= '<p class="center">'.$this->l('No carrier rule').'</p>';
		
		$this->_html .= '
		</fieldset>
		<br />
		<form method="post" action="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'">
			<fieldset style="width:500px;">
				<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/img/time.png" alt="" /> '.$this->l('More options').'</legend>
				
				<label for="extra_time_product_oos">'.$this->l('Extra time when a product is out of stock').'</label>
				<div class="margin-form">
					<input type="text" name="extra_time_product_oos" id="extra_time_product_oos" value="'.(int)(Tools::getValue('extra_time_product_oos', Configuration::get('DOD_EXTRA_TIME_PRODUCT_OOS'))).'" size="2" /> '.$this->l('day(s)').'
				</div>
				<div class="clear"></div>
				<label for="extra_time_preparation">'.$this->l('Extra time for preparation of the order').'</label>
				<div class="margin-form">
					<input type="text" name="extra_time_preparation" id="extra_time_preparation" value="'.(int)(Tools::getValue('extra_time_preparation', Configuration::get('DOD_EXTRA_TIME_PREPARATION'))).'" size="2" /> '.$this->l('day(s)').'
				</div>
				<div class="clear"></div>
				<label>'.$this->l('Preparation option').'</label>
				<div class="margin-form">
					<ul style="list-style-type:none;margin:0;padding:0;">
						<li><input type="checkbox" name="preparation_saturday" id="preparation_saturday" '.(Configuration::get('DOD_PREPARATION_SATURDAY') ? 'checked="checked"' : '').' /> <label class="t" for="preparation_saturday">'.$this->l('Saturday preparation').'</label></li>
						<li><input type="checkbox" name="preparation_sunday" id="preparation_sunday" '.(Configuration::get('DOD_PREPARATION_SUNDAY') ? 'checked="checked"' : '').' /> <label class="t" for="preparation_sunday">'.$this->l('Sunday preparation').'</label></li>
					</ul>
				</div>
				<label for="date_format">'.$this->l('Date format:').'</label>
				<div class="margin-form">
					<input type="text" name="date_format" id="date_format" value="'.htmlentities(Tools::getValue('date_format', Configuration::get('DOD_DATE_FORMAT')), ENT_QUOTES, 'UTF-8').'" size="10" />
					<p>'.$this->l('You can see all parameters available at:').' <a href="http://www.php.net/manual/en/function.date.php" target="_blank">http://www.php.net/manual/en/function.date.php</a></p>
				</div>
				<p class="center"><input type="submit" class="button" name="submitMoreOptions" value="'.$this->l('Save').'" /></p>
			</fieldset>
		</form>
		';
	}
	
	private function _setCarrierRuleForm()
	{
		$carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
		if (Tools::isSubmit('editCarrierRule') AND $this->_isCarrierRuleExists(Tools::getValue('id_carrier_rule')))
			$carrier_rule = $this->_getCarrierRule(Tools::getValue('id_carrier_rule'));
		
		$this->_html .= '
		<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
		';
		
		if (isset($carrier_rule) AND $carrier_rule['id_carrier_rule'])
			$this->_html .= '<input type="hidden" name="id_carrier_rule" value="'.(int)($carrier_rule['id_carrier_rule']).'" />';
		$this->_html .= '
		<fieldset>
		';
		
		if (Tools::isSubmit('addCarrierRule'))
			$this->_html .= '<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/img/time_add.png" alt="" /> '.$this->l('New carrier rule').'</legend>';
		elseif (Tools::isSubmit('editCarrierRule'))
			$this->_html .= '<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/img/time_add.png" alt="" /> '.$this->l('Edit carrier rule').'</legend>';
		
		$this->_html .= '
			<label for="id_carrier">'.$this->l('Carrier:').'</label>
			<div class="margin-form">
				<select name="id_carrier" id="id_carrier">
					<option>'.$this->l('Choose').'</option>';
		foreach ($carriers as $carrier)
			$this->_html .= '<option value="'.$carrier['id_carrier'].'" '.((Tools::isSubmit('id_carrier') AND Tools::getValue('id_carrier') == $carrier['id_carrier']) ? 'selected="selected"' : ((isset($carrier_rule) AND $carrier_rule['id_carrier'] == $carrier['id_carrier']) ? 'selected="selected"' : '')).'>'.htmlentities($carrier['name'].' | '.$carrier['delay'], ENT_QUOTES, 'UTF-8').'</option>';
		
		$this->_html .= '
				</select>
			</div>
			
			<label>'.$this->l('Delivery between').'</label>
			<div class="margin-form">
				<input type="text" name="minimal_time" value="'.htmlentities(Tools::getValue('minimal_time', ((isset($carrier_rule) AND $carrier_rule['minimal_time']) ? $carrier_rule['minimal_time'] : 0)), ENT_QUOTES, 'UTF-8').'" size="2" /> '.$this->l('day(s) and').' 
				<input type="text" name="maximal_time" value="'.htmlentities(Tools::getValue('maximal_time', ((isset($carrier_rule) AND $carrier_rule['maximal_time']) ? $carrier_rule['maximal_time'] : 0)), ENT_QUOTES, 'UTF-8').'" size="2" /> '.$this->l('day(s)').'
			</div>
			
			<label>'.$this->l('Delivery options:').'</label>
			<div class="margin-form">
				<ul style="list-style-type:none;margin:0;padding:0;">
					<li><input type="checkbox" name="delivery_saturday" id="delivery_saturday" '.(Tools::isSubmit('delivery_saturday') ? 'checked="checked"' : ((isset($carrier_rule) AND $carrier_rule['delivery_saturday']) ? 'checked="checked"' : '')).' /> <label class="t" for="delivery_saturday">'.$this->l('Saturday delivery').'</label></li>
					<li><input type="checkbox" name="delivery_sunday" id="delivery_sunday" '.(Tools::isSubmit('delivery_sunday') ? 'checked="checked"' : ((isset($carrier_rule) AND $carrier_rule['delivery_sunday']) ? 'checked="checked"' : '')).' /> <label class="t" for="delivery_sunday">'.$this->l('Sunday delivery').'</label></li>
				</ul>
			</div>
			
			<p class="center"><input type="submit" class="button" name="submitCarrierRule" value="'.$this->l('Save').'" /></p>
			<p class="center"><a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'">'.$this->l('Cancel').'</a></p>
		';
		
		$this->_html .= '
		</fieldset>
		</form>
		';
	}
	
	private function _getCarrierRulesWithCarrierName()
	{
		return Db::getInstance()->executeS('
		SELECT * 
		FROM `'._DB_PREFIX_.'dateofdelivery_carrier_rule` dcr 
		LEFT JOIN `'._DB_PREFIX_.'carrier` c ON (c.`id_carrier` = dcr.`id_carrier`)
		');
	}
	
	private function _getCarrierRule($id_carrier_rule)
	{
		if (!(int)($id_carrier_rule))
			return false;
		return Db::getInstance()->getRow('
		SELECT * 
		FROM `'._DB_PREFIX_.'dateofdelivery_carrier_rule` 
		WHERE `id_carrier_rule` = '.(int)($id_carrier_rule)
		);
	}
	
	private function _getCarrierRuleWithIdCarrier($id_carrier)
	{
		if (!(int)($id_carrier))
			return false;
		return Db::getInstance()->getRow('
		SELECT * 
		FROM `'._DB_PREFIX_.'dateofdelivery_carrier_rule` 
		WHERE `id_carrier` = '.(int)($id_carrier)
		);
	}
	
	private function _isCarrierRuleExists($id_carrier_rule)
	{
		if (!(int)($id_carrier_rule))
			return false;
		return (bool)Db::getInstance()->getValue('
		SELECT COUNT(*) 
		FROM `'._DB_PREFIX_.'dateofdelivery_carrier_rule` 
		WHERE `id_carrier_rule` = '.(int)($id_carrier_rule)
		);
	}
	
	private function _deleteByIdCarrierRule($id_carrier_rule)
	{
		if (!(int)($id_carrier_rule))
			return false;
		return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'dateofdelivery_carrier_rule` 
		WHERE `id_carrier_rule` = '.(int)($id_carrier_rule)
		);
	}
	
	private function _isAlreadyDefinedForCarrier($id_carrier, $id_carrier_rule = 0)
	{
		if (!(int)($id_carrier))
			return false;
		return (bool)Db::getInstance()->getValue('
		SELECT COUNT(*) 
		FROM `'._DB_PREFIX_.'dateofdelivery_carrier_rule` 
		WHERE `id_carrier` = '.(int)($id_carrier).'
		'.((int)$id_carrier_rule != 0 ? 'AND `id_carrier_rule` != '.(int)($id_carrier_rule) : ''));
	}

	/**
	 * @param $id_carrier
	 * @param bool $product_oos
	 * @param null $date
	 *
	 * @return array|bool returns the min & max delivery date
	 */
	private function _getDatesOfDelivery($id_carrier, $product_oos = false, $date = null)
	{
		if (!(int)($id_carrier))
			return false;
		$carrier_rule = $this->_getCarrierRuleWithIdCarrier((int)($id_carrier));
		if (empty($carrier_rule))
			return false;

		if ($date != null AND Validate::isDate($date))
			$date_now = strtotime($date);
		else
			$date_now = time(); // Date on timestamp format
		if ($product_oos)
			$date_now += Configuration::get('DOD_EXTRA_TIME_PRODUCT_OOS') * 24 * 3600;
		if (!Configuration::get('DOD_PREPARATION_SATURDAY') AND date('l', $date_now) == 'Saturday')
			$date_now += 24 * 3600;
		if (!Configuration::get('DOD_PREPARATION_SUNDAY') AND date('l', $date_now) == 'Sunday')
			$date_now += 24 * 3600;

		$date_minimal_time = $date_now + ($carrier_rule['minimal_time'] * 24 * 3600) + (Configuration::get('DOD_EXTRA_TIME_PREPARATION') * 24 * 3600);
		$date_maximal_time = $date_now + ($carrier_rule['maximal_time'] * 24 * 3600) + (Configuration::get('DOD_EXTRA_TIME_PREPARATION') * 24 * 3600);
		
		if (!$carrier_rule['delivery_saturday'] AND date('l', $date_minimal_time) == 'Saturday')
		{
			$date_minimal_time += 24 * 3600;
			$date_maximal_time += 24 * 3600;
		}
		if (!$carrier_rule['delivery_saturday'] AND date('l', $date_maximal_time) == 'Saturday')
			$date_maximal_time += 24 * 3600;
		
		if (!$carrier_rule['delivery_sunday'] AND date('l', $date_minimal_time) == 'Sunday')
		{
			$date_minimal_time += 24 * 3600;
			$date_maximal_time += 24 * 3600;
		}
		if (!$carrier_rule['delivery_sunday'] AND date('l', $date_maximal_time) == 'Sunday')
			$date_maximal_time += 24 * 3600;

		/*
		
		// Do not remove this commentary, it's usefull to allow translations of months and days in the translator tool
		
		$this->l('Sunday');
		$this->l('Monday');
		$this->l('Tuesday');
		$this->l('Wednesday');
		$this->l('Thursday');
		$this->l('Friday');
		$this->l('Saturday');

		$this->l('January');
		$this->l('February');
		$this->l('March');
		$this->l('April');
		$this->l('May');
		$this->l('June');
		$this->l('July');
		$this->l('August');
		$this->l('September');
		$this->l('October');
		$this->l('November');
		$this->l('December');
		*/
		
		$date_minimal_string = '';
		$date_maximal_string = '';
		$date_format = preg_split('/([a-z])/Ui', Configuration::get('DOD_DATE_FORMAT'), NULL, PREG_SPLIT_DELIM_CAPTURE);
		foreach ($date_format as $elmt)
		{
			if ($elmt == 'l' OR $elmt == 'F')
			{
				$date_minimal_string .= $this->l(date($elmt, $date_minimal_time));
				$date_maximal_string .= $this->l(date($elmt, $date_maximal_time));
			}
			elseif (preg_match('/[a-z]/Ui', $elmt))
			{
				$date_minimal_string .= date($elmt, $date_minimal_time);
				$date_maximal_string .= date($elmt, $date_maximal_time);
			}
			else
			{
				$date_minimal_string .= $elmt;
				$date_maximal_string .= $elmt;
			}
		}
		return array(
			array(
				$date_minimal_string,
				$date_minimal_time
			),
			array(
			$date_maximal_string,
				$date_maximal_time
			)
		);
	}
}
