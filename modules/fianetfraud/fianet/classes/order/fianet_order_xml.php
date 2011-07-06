<?php

	class fianet_order_xml
	{
		var $billing_user = null;
		var $billing_adress = null;
		var $info_commande = null;
		var $payment = null;
		
		var $delivery_user = null;
		var $delivery_adress = null;
				
		function fianet_order_xml()
		{
			$this->billing_user = new fianet_billing_user_xml();
			$this->billing_adress = new fianet_billing_adress_xml();
			$this->info_commande = new fianet_info_order_xml();
			$this->payment = new fianet_payment_xml();
		}
		
		function reset()
		{
			$this->billing_user = new fianet_billing_user_xml();
			$this->delivery_user = null;
			$this->billing_adress = new fianet_billing_adress_xml();
			$this->delivery_adress = null;
			$this->info_commande = new fianet_info_order_xml();
			$this->payment = new fianet_payment_xml();
		}
		
		function get_xml()
		{
			$xml = '';
			$xml .= '<control fianetmodule="'.FIANET_MODULE.'" version="'.FIANET_VERSION.'">' . "\n";
			$xml .= $this->billing_user->get_xml();
			$xml .= $this->billing_adress->get_xml();
			if ($this->delivery_user != null)
			{
				if (var_is_object_of_class($this->delivery_user, 'fianet_delivery_user_xml'))
				{
					$xml .= $this->delivery_user->get_xml();
				}
				else
				{
					fianet_insert_log("fianet_order_xml.php - get_xml() <br />\nDelivery user is not an object of type fianet_delivery_user_xml");
				}
			}
			if ($this->delivery_adress != null && ($this->info_commande->transport->type == 4 || $this->info_commande->transport->type == 5))
			{
				if (var_is_object_of_class($this->delivery_adress, 'fianet_delivery_adress_xml'))
				{
					$xml .= $this->delivery_adress->get_xml();
				}
				else
				{
					fianet_insert_log("fianet_order_xml.php - get_xml() <br />\nDelivery adress is not an object of type fianet_delivery_adress_xml");
				}
			}
			$xml .= $this->info_commande->get_xml();
			$xml .= $this->payment->get_xml();
			$xml .= '</control>';
			
			save_flux_xml($xml, $this->info_commande->refid);
			return ($xml);
		}
		
	}

