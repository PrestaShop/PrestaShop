<?php

	class fianet_rnp_wallet_xml
	{
		var $version = '1.0';
		var $datelivr = null;
		var $datecom = null;
		var $crypt = null;
		var $crypt_version = '2.0';
		
		var $order_date_time_seconds = null;
	
		function fianet_rnp_wallet_xml()
		{
			//$this->datecom = "2008-05-28 14:25:31";
			$this->datecom = date("Y-m-d H:i:s");
			$this->order_date_time_seconds = substr($this->datecom, -2, 2);
		}
		
		function generate_wallet_crypt_data($order_id, $billing_lastname, $customer_email, $total)
		{
			$total = number_format($total, 2, '.', '');
			$this->generate_crypt_key($order_id, $billing_lastname, $customer_email, $total);
		}
		
		function get_xml()
		{
			$xml = '';
			
			$xml .= "\t" . '<wallet version="'.$this->version.'">' . "\n";
			$xml .= "\t\t" . '<datelivr>'.$this->datelivr.'</datelivr>' . "\n";
			$xml .= "\t\t" . '<datecom>'.$this->datecom.'</datecom>' . "\n";
			$xml .= "\t\t" . '<crypt version="'.$this->crypt_version.'">'.$this->crypt.'</crypt>' . "\n";
			$xml .= "\t" . '</wallet>' . "\n";
			
			return ($xml);
		}
		
		function generate_crypt_key($order_id, $billing_lastname, $customer_email, $total)
		{
			if (FIANET_ENCODING == 'UTF-8')
			{
				$billing_lastname = utf8_decode($billing_lastname);
			}
			else
			{
				$billing_lastname = $billing_lastname;
			}
			$key = FIANET_RNP_KEY;

			$encodingKey = new EncodingKey();
			$this->crypt = $encodingKey->giveHashCode2(	$key,
														$this->order_date_time_seconds,
														$customer_email,
														$order_id,
														$total,
														$billing_lastname);
		}
	}

