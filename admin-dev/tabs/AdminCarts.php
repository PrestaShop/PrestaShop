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

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');

class AdminCarts extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'cart';
	 	$this->className = 'Cart';
		$this->lang = false;
	 	$this->edit = false;
	 	$this->view = true;
	 	$this->delete = false;


		$this->_select = 'CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`, a.id_cart as total, ca.name as carrier';
		$this->_join = 'LEFT JOIN '._DB_PREFIX_.'customer c on (c.id_customer = a.id_customer)
		LEFT JOIN '._DB_PREFIX_.'currency cu on (cu.id_currency = a.id_currency)
		LEFT JOIN '._DB_PREFIX_.'carrier ca on (ca.id_carrier = a.id_carrier)
		';

 		$this->fieldsDisplay = array(
		'id_cart' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'customer' => array('title' => $this->l('Customer'), 'width' => 80, 'filter_key' => 'c!lastname'),
		'total' => array('title' => $this->l('Total'), 'callback' => 'getOrderTotalUsingTaxCalculationMethod', 'orderby' => false, 'search' => false, 'width' => 50, 'align' => 'right', 'prefix' => '<b>', 'suffix' => '</b>', 'currency' => true),
		'carrier' => array('title' => $this->l('Carrier'), 'width' => 25, 'align' => 'center', 'callback' => 'replaceZeroByShopName'),
		'date_add' => array('title' => $this->l('Date'), 'width' => 90, 'align' => 'right', 'type' => 'datetime', 'filter_key' => 'a!date_add'));
		parent::__construct();
	}

	public function viewDetails()
	{
		global $currentIndex, $cookie;

		if (!($cart = $this->loadObject(true)))
			return;
		$customer = new Customer($cart->id_customer);
		$customerStats = $customer->getStats();
		$products = $cart->getProducts();
		$customizedDatas = Product::getAllCustomizedDatas((int)($cart->id));
		Product::addCustomizationPrice($products, $customizedDatas);
		$summary = $cart->getSummaryDetails();
		$discounts = $cart->getDiscounts();

		$currency = new Currency($cart->id_currency);
		$currentLanguage = new Language((int)($cookie->id_lang));

		// display cart header
		echo '<h2>'.(($customer->id) ? $customer->firstname.' '.$customer->lastname : $this->l('Guest')).' - '.$this->l('Cart #').sprintf('%06d', $cart->id).' '.$this->l('from').' '.$cart->date_upd.'</h2>';

		/* Display customer information */
		echo '
		<br />
		<div style="float: left;">
		<fieldset style="width: 400px">
			<legend><img src="../img/admin/tab-customers.gif" /> '.$this->l('Customer information').'</legend>
			<span style="font-weight: bold; font-size: 14px;">';
			if ($customer->id)
				echo '
			<a href="?tab=AdminCustomers&id_customer='.$customer->id.'&viewcustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee)).'"> '.$customer->firstname.' '.$customer->lastname.'</a></span> ('.$this->l('#').$customer->id.')<br />
			(<a href="mailto:'.$customer->email.'">'.$customer->email.'</a>)<br /><br />
			'.$this->l('Account registered:').' '.Tools::displayDate($customer->date_add, (int)($cookie->id_lang), true).'<br />
			'.$this->l('Valid orders placed:').' <b>'.$customerStats['nb_orders'].'</b><br />
			'.$this->l('Total paid since registration:').' <b>'.Tools::displayPrice($customerStats['total_orders'], $currency, false, false).'</b><br />';
			else
				echo $this->l('Guest not registered').'</span>';
		echo '</fieldset>';
		echo '
		</div>
		<div style="float: left; margin-left: 40px">';

		/* Display order information */
		$id_order = (int)(Order::getOrderByCartId($cart->id));
		$order = new Order($id_order);

		if ($order->getTaxCalculationMethod() == PS_TAX_EXC)
		{
		    $total_products = $summary['total_products'];
		    $total_discount = $summary['total_discounts_tax_exc'];
		    $total_wrapping = $summary['total_wrapping_tax_exc'];
		    $total_price = $summary['total_price_without_tax'];
   		    $total_shipping = $summary['total_shipping_tax_exc'];
		} else {
		    $total_products = $summary['total_products_wt'];
		    $total_discount = $summary['total_discounts'];
		    $total_wrapping = $summary['total_wrapping'];
		    $total_price = $summary['total_price'];
  		    $total_shipping = $summary['total_shipping'];
		}

		echo '
		<fieldset style="width: 400px">
			<legend><img src="../img/admin/cart.gif" /> '.$this->l('Order information').'</legend>
			<span style="font-weight: bold; font-size: 14px;">';
			if ($order->id)
				echo '
			<a href="?tab=AdminOrders&id_order='.(int)($order->id).'&vieworder&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)).'"> '.$this->l('Order #').sprintf('%06d', $order->id).'</a></span>
			<br /><br />
			'.$this->l('Made on:').' '.Tools::displayDate($order->date_add, (int)$cookie->id_lang, true).'<br /><br /><br /><br />';
			else
				echo $this->l('No order created from this cart').'</span>';
		echo '</fieldset>';
		echo '
		</div>';


		// List of products
		echo '
		<br style="clear:both;" />
			<fieldset style="margin-top:25px; width: 715px; ">
				<legend><img src="../img/admin/cart.gif" alt="'.$this->l('Products').'" />'.$this->l('Cart summary').'</legend>
				<div style="float:left;">
					<table style="width: 700px;" cellspacing="0" cellpadding="0" class="table" id="orderProducts">
						<tr>
							<th align="center" style="width: 60px">&nbsp;</th>
							<th>'.$this->l('Product').'</th>
							<th style="width: 80px; text-align: center">'.$this->l('UP').'</th>
							<th style="width: 20px; text-align: center">'.$this->l('Qty').'</th>
							<th style="width: 30px; text-align: center">'.$this->l('Stock').'</th>
							<th style="width: 90px; text-align: right; font-weight:bold;">'.$this->l('Total').'</th>
						</tr>';
						$tokenCatalog = Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee));
						foreach ($products as $k => $product)
						{
                   			if ($order->getTaxCalculationMethod() == PS_TAX_EXC)
                			{
                			    $product_price = $product['price'];
                			    $product_total = $product['total'];
                			} else {
	            			    $product_price = $product['price_wt'];
                			    $product_total = $product['total_wt'];
                			}


							$image = array();
							if (isset($product['id_product_attribute']) AND (int)($product['id_product_attribute']))
								$image = Db::getInstance()->getRow('
								SELECT id_image
								FROM '._DB_PREFIX_.'product_attribute_image
								WHERE id_product_attribute = '.(int)($product['id_product_attribute']));
						 	if (!isset($image['id_image']))
								$image = Db::getInstance()->getRow('
								SELECT id_image
								FROM '._DB_PREFIX_.'image
								WHERE id_product = '.(int)($product['id_product']).' AND cover = 1');
						 	$stock = Db::getInstance()->getRow('
							SELECT '.($product['id_product_attribute'] ? 'pa' : 'p').'.quantity
							FROM '._DB_PREFIX_.'product p
							'.($product['id_product_attribute'] ? 'LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON p.id_product = pa.id_product' : '').'
							WHERE p.id_product = '.(int)($product['id_product']).'
							'.($product['id_product_attribute'] ? 'AND pa.id_product_attribute = '.(int)($product['id_product_attribute']) : ''));
							/* Customization display */
							$this->displayCustomizedDatas($customizedDatas, $product, $currency, $image, $tokenCatalog, $stock);
							if ($product['cart_quantity'] > $product['customizationQuantityTotal'])
								echo '
								<tr>
									<td align="center">'.(isset($image['id_image']) ? cacheImage(_PS_IMG_DIR_.'p/'.(int)($product['id_product']).'-'.(int)($image['id_image']).'.jpg',
									'product_mini_'.(int)($product['id_product']).(isset($product['id_product_attribute']) ? '_'.(int)($product['id_product_attribute']) : '').'.jpg', 45, 'jpg') : '--').'</td>
									<td><a href="index.php?tab=AdminCatalog&id_product='.$product['id_product'].'&updateproduct&token='.$tokenCatalog.'">
										<span class="productName">'.$product['name'].'</span><br />
										'.($product['reference'] ? $this->l('Ref:').' '.$product['reference'] : '')
										.(($product['reference'] AND $product['supplier_reference']) ? ' / '.$product['supplier_reference'] : '')
										.'</a></td>
									<td align="center">'.Tools::displayPrice($product_price, $currency, false, false).'</td>
									<td align="center" class="productQuantity">'.((int)($product['cart_quantity']) - $product['customizationQuantityTotal']).'</td>
									<td align="center" class="productQuantity">'.(int)($stock['quantity']).'</td>
									<td align="right">'.Tools::displayPrice($product_total, $currency, false, false).'</td>
								</tr>';
						}
					echo '
					<tr class="cart_total_product">
				<td colspan="5">'.$this->l('Total products:').'</td>
				<td class="price bold right">'.Tools::displayPrice($total_products, $currency, false).'</td>
			</tr>';

			if ($summary['total_discounts'] != 0)
			echo '
			<tr class="cart_total_voucher">
				<td colspan="5">'.$this->l('Total vouchers:').'</td>
				<td class="price-discount bold right">'.Tools::displayPrice($total_discount, $currency, false).'</td>
			</tr>';
			if ($summary['total_wrapping'] > 0)
			 echo '
			 <tr class="cart_total_voucher">
				<td colspan="5">'.$this->l('Total gift-wrapping:').'</td>
				<td class="price-discount bold right">'.Tools::displayPrice($total_wrapping, $currency, false).'</td>
			</tr>';
			if ($cart->getOrderTotal(true, Cart::ONLY_SHIPPING) > 0)
			echo '
			<tr class="cart_total_delivery">
				<td colspan="5">'.$this->l('Total shipping:').'</td>
				<td class="price bold right">'.Tools::displayPrice($total_shipping, $currency, false).'</td>
			</tr>';
			echo '
			<tr class="cart_total_price">
				<td colspan="5" class="bold">'.$this->l('Total:').'</td>
				<td class="price bold right">'.Tools::displayPrice($total_price, $currency, false).'</td>
			</tr>
			</table>';

			if (sizeof($discounts))
			{
				echo '
			<table cellspacing="0" cellpadding="0" class="table" style="width:280px; margin:15px 0px 0px 420px;">
				<tr>
					<th><img src="../img/admin/coupon.gif" alt="'.$this->l('Discounts').'" />'.$this->l('Discount name').'</th>
					<th align="center" style="width: 100px">'.$this->l('Value').'</th>
				</tr>';

				foreach ($discounts as $discount)
					echo '
				<tr>
					<td><a href="?tab=AdminDiscounts&id_discount='.$discount['id_discount'].'&updatediscount&token='.Tools::getAdminToken('AdminDiscounts'.(int)(Tab::getIdFromClassName('AdminDiscounts')).(int)($cookie->id_employee)).'">'.$discount['name'].'</a></td>
					<td align="center">- '.Tools::displayPrice($discount['value_real'], $currency, false).'</td>
				</tr>';
				echo '
			</table>';
			}
				echo '<div style="float:left; margin-top:15px;">'.
				$this->l('According to the group of this customer, prices are printed:').' '.($order->getTaxCalculationMethod() == PS_TAX_EXC ? $this->l('tax excluded.') : $this->l('tax included.')).'
				</div></div>';

				// Cancel product
				echo '
			</fieldset>
		<div class="clear" style="height:20px;">&nbsp;</div>';
	}

	private function displayCustomizedDatas(&$customizedDatas, &$product, &$currency, &$image, $tokenCatalog, &$stock)
	{
		if (!($order = $this->loadObject(true)))
			return;

		if (is_array($customizedDatas) AND isset($customizedDatas[(int)($product['id_product'])][(int)($product['id_product_attribute'])]))
		{
			echo '
			<tr>
				<td align="center">'.(isset($image['id_image']) ? cacheImage(_PS_IMG_DIR_.'p/'.(int)($product['id_product']).'-'.(int)($image['id_image']).'.jpg',
				'product_mini_'.(int)($product['id_product']).(isset($product['id_product_attribute']) ? '_'.(int)($product['id_product_attribute']) : '').'.jpg', 45, 'jpg') : '--').'</td>
				<td><a href="index.php?tab=AdminCatalog&id_product='.$product['id_product'].'&updateproduct&token='.$tokenCatalog.'">
					<span class="productName">'.$product['name'].'</span><br />
					'.($product['reference'] ? $this->l('Ref:').' '.$product['reference'] : '')
					.(($product['reference'] AND $product['supplier_reference']) ? ' / '.$product['supplier_reference'] : '')
					.'</a></td>
				<td align="center">'.Tools::displayPrice($product['price_wt'], $currency, false, false).'</td>
				<td align="center" class="productQuantity">'.$product['customizationQuantityTotal'].'</td>
				<td align="center" class="productQuantity">'.(int)($stock['quantity']).'</td>
				<td align="right">'.Tools::displayPrice($product['total_customization_wt'], $currency, false, false).'</td>
			</tr>';
			foreach ($customizedDatas[(int)($product['id_product'])][(int)($product['id_product_attribute'])] AS $customization)
			{
				echo '
				<tr>
					<td colspan="2">';
				foreach ($customization['datas'] AS $type => $datas)
					if ($type == _CUSTOMIZE_FILE_)
					{
						$i = 0;
						echo '<ul style="margin: 4px 0px 4px 0px; padding: 0px; list-style-type: none;">';
						foreach ($datas AS $data)
							echo '<li style="display: inline; margin: 2px;">
									<a href="displayImage.php?img='.$data['value'].'&name='.(int)($order->id).'-file'.++$i.'" target="_blank"><img src="'._THEME_PROD_PIC_DIR_.$data['value'].'_small" alt="" /></a>
								</li>';
						echo '</ul>';
					}
					elseif ($type == _CUSTOMIZE_TEXTFIELD_)
					{
						$i = 0;
						echo '<ul style="margin: 0px 0px 4px 0px; padding: 0px 0px 0px 6px; list-style-type: none;">';
						foreach ($datas AS $data)
							echo '<li>'.($data['name'] ? $data['name'] : $this->l('Text #').++$i).$this->l(':').' '.$data['value'].'</li>';
						echo '</ul>';
					}
				echo '</td>
					<td align="center"></td>
					<td align="center" class="productQuantity">'.$customization['quantity'].'</td>
					<td align="center" class="productQuantity"></td>
					<td align="center"></td>
				</tr>';
			}
		}
	}

	public function display()
	{
		global $cookie;

		if (isset($_GET['view'.$this->table]))
			$this->viewDetails();
		else
		{
			$this->getList((int)($cookie->id_lang), !Tools::getValue($this->table.'Orderby') ? 'date_add' : NULL, !Tools::getValue($this->table.'Orderway') ? 'DESC' : NULL);
			$this->displayList();
		}
	}
}

