<?php

class OrderInfoTnt
{
	private $_idOrder;
	
	public	function __construct($id_order)
	{
		$this->_idOrder = $id_order;
	}
	
	public function getInfo()
	{
		$info = Db::getInstance()->ExecuteS('SELECT o.shipping_number, a.lastname, a.firstname, a.address1, a.address2, a.postcode, a.city, a.phone, c.email, c.id_customer, a.company
														FROM `'._DB_PREFIX_.'orders` as o, `'._DB_PREFIX_.'address` as a, `'._DB_PREFIX_.'customer` as c
														WHERE o.id_order = "'.$this->_idOrder.'" AND a.id_address = o.id_address_delivery AND c.id_customer = o.id_customer');
		if (!$info)
			return false;
		$weight = Db::getInstance()->ExecuteS('SELECT p.weight, o.product_quantity
												FROM `'._DB_PREFIX_.'order_detail` as o, `'._DB_PREFIX_.'product` as p
												WHERE o.id_order = "'.$this->_idOrder.'" AND p.id_product = o.product_id');
		$option = Db::getInstance()->ExecuteS('SELECT t.option 
												FROM `'._DB_PREFIX_.'tnt_carrier_option` as t , `'._DB_PREFIX_.'orders` as o
												WHERE t.id_carrier = o.id_carrier AND o.id_order = "'.$this->_idOrder.'"');
		if ($option != null && strpos($option[0]['option'], "D") !== false)
			$dropOff = Db::getInstance()->ExecuteS('SELECT d.code, d.name, d.address, d.zipcode, d.city
												FROM `'._DB_PREFIX_.'tnt_carrier_drop_off` as d , `'._DB_PREFIX_.'orders` as o
												WHERE d.id_cart = o.id_cart AND o.id_order = "'.$this->_idOrder.'"');
		$w = 0;
		$tooBig = false;
		foreach ($weight as $key => $val)
		{
			while ($val['product_quantity'] > 0)
			{
				if ((int)($val['weight']) > 20)
					return "Un ou plusieurs articles sont sup&eacute;rieurs &agrave; 20 Kg<br/>Vous devez contacter votre commercial TNT";
				if ($w + $val['weight'] > 20)
				{
					$info[1]['weight'][] = (string)($w);
					$w = $val['weight'];
				}
				else
					$w += $val['weight'];
				$val['product_quantity']--;
			}
		}
		$info[1]['weight'][] = (string)($w);
		
		if (date("N") == 5)
			$next_day = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+3, date("Y")));
        elseif (date("N") == 6)
            $next_day = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+2, date("Y")));
		else
			$next_day  = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
		$newDate = Tools::getValue('dateErrorOrder');
		$info[2] = array('delivery_date' => ($newDate != '' ? $newDate : $next_day));
		if ($option)
			$info[3] = array('option' => $option[0]['option']);
		if (isset($dropOff))
			$info[4] = $dropOff[0];
		else
			$info[4] = null;
		return $info;
	}
	
}

?>
