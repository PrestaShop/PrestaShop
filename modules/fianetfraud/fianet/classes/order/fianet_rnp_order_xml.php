<?php

	class fianet_rnp_order_xml
	{
		var $billing_user = null;
		var $billing_adress = null;
		var $info_commande = null;
		var $wallet = null;
		
		var $delivery_user = null;
		var $delivery_adress = null;
				
		function fianet_rnp_order_xml()
		{
			$this->billing_user = new fianet_billing_user_xml();
			$this->billing_adress = new fianet_billing_adress_xml();
			$this->info_commande = new fianet_rnp_info_order_xml();
			$this->wallet = new fianet_rnp_wallet_xml();
		}
		
		function reset()
		{
			$this->billing_user = new fianet_billing_user_xml();
			$this->delivery_user = null;
			$this->billing_adress = new fianet_billing_adress_xml();
			$this->delivery_adress = null;
			$this->info_commande = new fianet_rnp_info_order_xml();
			$this->wallet = new fianet_rnp_wallet_xml();
		}
		
		function get_xml()
		{
			$xml = '';
			$xml .= '<?xml version="1.0" encoding="'. FIANET_ENCODING . '" ?>' . "\n";
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
					fianet_insert_log("fianet_rnp_order_xml.php - get_xml() <br />\nDelivery user is not an object of type fianet_delivery_user_xml");
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
					fianet_insert_log("fianet_rnp_order_xml.php - get_xml() <br />\nDelivery adress is not an object of type fianet_delivery_adress_xml");
				}
			}
			$xml .= $this->info_commande->get_xml();
			
			$this->wallet->generate_wallet_crypt_data($this->info_commande->refid, $this->billing_user->nom, $this->billing_user->email, $this->info_commande->montant);
			$xml .= $this->wallet->get_xml();
			
			$xml .= '</control>';
			
			save_flux_xml($xml, $this->info_commande->refid);
			return ($xml);
		}
		
		/*
		Cette fonction génère le formulaire de redirection vers ReceiveAndPay
		Elle prends des paramètres optionnels
		url_call : url de retour sur le site marchand
		url_sys : url de réponse des tags asynchrone pour le serveur ReceiveAndPay
		ParamCallBack : tableau associatif des données que vous souhaitez voir retourner par le serveur ReceiveAndPay sur url_sys et url_call
		typeIHM : 1 pour carte bancaire seulement, 2 pour à crédit uniquement, 3 pour les deux en même temps
		enProd : mettre à true pour rediriger vers ReceiveAndPay de production
		auto_send : si true : génère un javascript qui soumettra immédiatement le formulaire
		*/
		function get_formular($url_call = null, $url_sys = null, $ParamCallBack = array(), $typeIHM = 3, $enProd = false, $auto_send = true)
		{
			
			$flux = $this->get_xml();
			$flux = clean_xml($flux);
			$flux = str_replace('"', "'", $flux);
			$flux = str_replace('&amp;', '&amp;amp;', $flux);
			$flux = str_replace('&lt;', '&amp;lt;', $flux);
			$flux = str_replace('&gt;', '&amp;gt;', $flux);
			$my_hashmd5 = new HashMD5();
			//$toto = html_entity_decode($flux);
			//debug($toto);
			$checksum = $my_hashmd5->hash(html_entity_decode($flux));
			
			if (is_array($ParamCallBack) && count($ParamCallBack) > 0)
			{
				$XMLParam = new fianet_xml_paracallback_builder();
				foreach ($ParamCallBack as $index => $value)
				{
					$XMLParam->add_param(new fianet_paraobject_xml($index, urlencode(htmlentities($value))));
				}
			}
		
			if ($enProd)
			{
				$url = URL_RNP_PROD;
			}
			else
			{
				$url = URL_RNP_TEST;
			}
			$url .= URL_RNP_FRONTLINE; 
			
			$form = '';
			$form .= '<form name="RnPform" action="'.$url.'" method="post">';
			$form .= '<input type="hidden" name="MerchID" value="'. $this->info_commande->siteid .'">' . "\n";
			$form .= '<input type="hidden" name="XMLInfo" value="'. $flux .'">' . "\n";
			if ($url_call != null && $url_call != '')
			{
				$form .= '<input type="hidden" name="URLCall" value="'. $url_call .'">' . "\n";
			}
			if ($url_sys != null && $url_sys != '')
			{
				$form .= '<input type="hidden" name="URLSys" value="'. $url_sys .'">' . "\n";
			}
			if (isset($XMLParam))
			{
				$form .= '<input type="hidden" name="XMLParam" value="'.clean_xml(str_replace('"', "'", $XMLParam->get_xml())).'">' . "\n";
			}
			$form .= '<input type="hidden" name="CheckSum" value="'. $checksum .'">' . "\n";
			$form .= '<input type="hidden" name="TypeIHM" value="'. $typeIHM .'">' . "\n";
			$form .= '</form>';
			if ($auto_send)
			{
				$form .= '<script>document.RnPform.submit();</script>';
			}

			return ($form);
		}
		
	}

