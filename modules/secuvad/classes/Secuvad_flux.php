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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Secuvad_flux 
{
	public $encoding = 'utf-8';	
	public $flux_xml;
	public $id_order;	
	public $imp_time;
	public $idsecuvad;
	
	function __construct($idsecuvad, $encoding)
	{				
		$this->idsecuvad = $idsecuvad;
		$this->encoding	= $encoding;
	}
	
	public function get_flux_xml_fraud($id_order)
	{
		$this->id_order = (int)($id_order);
		$this->imp_time = date("Y-m-d H:i:s");
		$this->flux_xml = '<?xml version="1.0" encoding="'.$this->encoding.'" ?>' . "\n";
		$this->flux_xml .= '<impaye><idsecuvad>'.$this->idsecuvad.'</idsecuvad><idtransaction>'.(int)($this->id_order).'</idtransaction><imptimestamp>'.$this->imp_time.'</imptimestamp></impaye>';
		
		return ($this->flux_xml);
	}
	
	function get_flux_xml($id_order)
	{
		$this->id_order = (int)($id_order);
		$this->flux_xml = '';
		$this->flux_xml .= '<?xml version="1.0" encoding="'.$this->encoding.'" ?>' . "\n";
		$this->flux_xml .= '<bulk_transactions>' . "\n";
		$this->flux_xml .= $this->get_flux_xml_order();
		$this->flux_xml .= '</bulk_transactions>' . "\n";
		return $this->flux_xml;
	}
	
	private function get_flux_xml_order()
	{	
		global $cookie;
		
		$order = new Order((int)($this->id_order));			
		$address_delivery = new Address((int)($order->id_address_delivery));
		$address_invoice = new Address((int)($order->id_address_invoice));
		$customer = new Customer((int)($order->id_customer));
		$currency = new Currency((int)($order->id_currency));
		$carrier = new Carrier((int)($order->id_carrier));
		
		$ip = Db::getInstance()->getValue('
		SELECT `ip` 
		FROM `'._DB_PREFIX_.'secuvad_order` 
		WHERE `id_secuvad_order` = '.(int)($this->id_order));
		if (!$ip)
			return false;
		$payment_cc = Db::getInstance()->getRow('
		SELECT *
		FROM `'._DB_PREFIX_.'payment_cc`
		WHERE `id_order` = '.(int)($this->id_order));
		if ($payment_cc)
		{
			$card_number = $payment_cc['card_number'];
			$card_expiration = $payment_cc['card_expiration'];
		}
		
		$carrier = Db::getInstance()->getRow('
		SELECT at.`transport_id`, td.`transport_delay_name` 
		FROM `'._DB_PREFIX_.'secuvad_assoc_transport` at 
		JOIN `'._DB_PREFIX_.'secuvad_transport_delay` td ON (at.`transport_delay_id` = td.`transport_delay_id`)  
		JOIN `'._DB_PREFIX_.'lang` l ON (l.`id_lang` = td.`id_lang`)
		WHERE l.`id_lang` = '.((isset($cookie->id_lang) AND (int)($cookie->id_lang)) ? (int)($cookie->id_lang) : (int)(Configuration::get('PS_LANG_DEFAULT'))).'
		AND at.`id_carrier` = '.(int)($order->id_carrier));
		$transptype = $carrier['transport_id'];
		$rapidite = $carrier['transport_delay_name'];
		
		$code_payment = Db::getInstance()->getValue('
		SELECT sap.`code` 
		FROM `'._DB_PREFIX_.'module` m 
		JOIN  `'._DB_PREFIX_.'secuvad_assoc_payment` sap ON (m.`id_module` = sap.`id_module`)
		WHERE m.`name` = \''.pSQL($order->module).'\'');
		
		$flux_xml = "<transaction>\n";
		switch ($customer->id_gender)
		{
			case 1:
				$gender = 'M';
				break;
			case 2:
				$gender = 'Mme';
				break;
			case 3:
				$gender = 'Mlle';
				break;
			default:
				$gender = 'M';
				break;
		}
		
		if($address_invoice->company == '')
			$flux_xml .= '<client mode="facturation" qualite="particulier">'."\n";
		else
			$flux_xml .= '<client mode="facturation" qualite="entreprise">'."\n";
		$flux_xml .= '<nom titre="'.$gender .'">'.$address_invoice->lastname.'</nom>'."\n";
		$flux_xml .= '<prenom>'.$address_invoice->firstname.'</prenom>'."\n";
		if($address_invoice->company != '')	
			$flux_xml .= '<societe>'.$address_invoice->company.'</societe>'."\n";
		$flux_xml .= '<telephoneperso>'.$address_invoice->phone.'</telephoneperso>'."\n";
		$flux_xml .= '<portable>'.$address_invoice->phone_mobile.'</portable>'."\n";
		$flux_xml .= '<email>'.$customer->email.'</email>'."\n";
		$flux_xml .= '</client>';
				
		$flux_xml .= '<adresse mode="facturation">'."\n";
		$flux_xml .= '<rue1>'.$address_invoice->address1.'</rue1>'."\n";
		$flux_xml .= '<rue2>'.$address_invoice->address2.'</rue2>'."\n";
		$flux_xml .= '<codepostal>'.$address_invoice->postcode.'</codepostal>'."\n";
		$flux_xml .= '<ville>'.$address_invoice->city.'</ville>'."\n";
		$flux_xml .= '<pays>'.$address_invoice->country.'</pays>'."\n";
		$flux_xml .= '</adresse>'."\n";
		
		$flux_xml .= '<adresse mode="livraison">'."\n";
		$flux_xml .= '<rue1>'.$address_delivery->address1.'</rue1>'."\n";
		$flux_xml .= '<rue2>'.$address_delivery->address2.'</rue2>'."\n";
		$flux_xml .= '<codepostal>'.$address_delivery->postcode.'</codepostal>'."\n";
		$flux_xml .= '<ville>'.$address_delivery->city.'</ville>'."\n";
		$flux_xml .= '<pays>'.$address_delivery->country.'</pays>'."\n";
		$flux_xml .= '</adresse>'."\n";
		
		$flux_xml .= '<commande>'."\n";
		$flux_xml .= '<idsecuvad>'.$this->idsecuvad.'</idsecuvad>'."\n";
		$flux_xml .= '<idtransaction>'.(int)($this->id_order).'</idtransaction>'."\n";	
		$flux_xml .= '<trstimestamp>'.$order->date_add.'</trstimestamp>'."\n";
		$flux_xml .= '<montantttc devise="'.$currency->iso_code.'">'.$order->total_paid_real.'</montantttc>'."\n";
		$flux_xml .= '<montantlivraison>'.$order->total_shipping.'</montantlivraison>'."\n";
		$flux_xml .= '<ip>'.$ip.'</ip>'."\n";
		$flux_xml .= '<transport transptype="'.$transptype.'" rapidite="'.$rapidite.'"></transport>'."\n";
		$flux_xml .= $this->get_flux_xml_products();
		$flux_xml .= '</commande>'."\n";

		$flux_xml .= '<paiement>'."\n";
		$flux_xml .= '<paiementtype>'."\n";
		if($code_payment == 'cheque')
			$flux_xml .= '<cheque></cheque>'."\n";
		elseif($code_payment == 'virement')
			$flux_xml .= '<virement></virement>'."\n";
		elseif($code_payment == 'paypal')
			$flux_xml .= '<paypal></paypal>'."\n";
		elseif($code_payment == 'cb en n fois')
			$flux_xml .= '<CBX></CBX>'."\n";
		elseif($code_payment == 'contre-remboursement')
			$flux_xml .= '<CR></CR>'."\n";
		elseif($code_payment == "carte")
		{
			if (!empty($card_number))
			{
				$cc_array = preg_split('/([X0-9]{4})/Ui', strtoupper($card_number), -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
				
				if (sizeof($cc_array))
				{
					$bin_array = array();
					foreach ($cc_array as $element)
						$bin_array[] = str_replace('X', '', $element);
					
					$card_number 	= str_replace('X', '', $card_number); // 16 char
					$bin	= $bin_array[0].'-'.$bin_array[1]; // 6 char
					$bin4	= $bin_array[0]; // 4 char
					$bin42	= $bin_array[0].'-'.$bin_array[3]; // 6 char
					
					if (strlen($bin42) != 7 AND strlen($bin4) != 4 AND strlen($bin) != 7 AND strlen($card_number) != 16)
						return false;
					
					$flux_xml .= '<CB datevalidite="'.$card_expiration.'" '.(strlen($card_number) == 16 ? 'numcb="'.$card_number.'"' : '').' '.(strlen($bin) == 7 ? 'bin="'.$bin.'"' : '').' '.(strlen($bin4) == 4 ? 'bin4="'.$bin4.'"' : '').' '.(strlen($bin42) == 7 ? 'bin42="'.$bin42.'"' : '').'></CB>'."\n";
				}
				else
					return false;
			}
			else
				return false;
		}
		$flux_xml .= '</paiementtype>'."\n";						
		$flux_xml .= '</paiement>'."\n";
		$flux_xml .= '</transaction>'."\n";

		return $flux_xml;
	}
	
	private function get_flux_xml_products()
	{
		global $cookie;
		
		$flux_xml 	= '';
		$order = new Order((int)($this->id_order));
		$products = $order->getProducts();
		foreach($products as $product)
		{
			$data = Db::getInstance()->getRow('
			SELECT sac.`category_id`, pl.`name` 
			FROM `'._DB_PREFIX_.'secuvad_assoc_category` sac 
			JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = sac.`id_category`)
			JOIN `'._DB_PREFIX_.'category` c ON (c.`id_category` = cp.`id_category`)
			JOIN `'._DB_PREFIX_.'product_lang` pl ON (cp.`id_product` = pl.`id_product`) 
			JOIN `'._DB_PREFIX_.'lang` l ON (l.`id_lang` = pl.`id_lang` AND l.`id_lang` = '.((isset($cookie->id_lang) AND (int)($cookie->id_lang)) ? (int)($cookie->id_lang) : (int)(Configuration::get('PS_LANG_DEFAULT'))).')
			WHERE pl.`id_product` = '.(int)($product['product_id']).'
			ORDER BY c.`level_depth` DESC
			',true);
			$flux_xml .= '<produit categorie="'.(int)($data['category_id']).'" reference="'.(int)($product['product_id']).'-'.(int)($product['product_attribute_id']).'" modele="'.addslashes($data['name']).'" prix="'.(float)($product['product_price_wt']).'" quantite="'.(int)($product['product_quantity']).'"></produit>'."\n";
		}
		$flux_xml = '<caddie nbproduit="'.sizeof($products).'">'."\n".$flux_xml.'</caddie>'."\n";
		return $flux_xml;
	}
	
}


