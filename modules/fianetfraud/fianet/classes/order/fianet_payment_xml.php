<?php

	class fianet_payment_xml
	{
		var $type;
		var $numcb;
		var $dateval;
		var $bin;
		var $bin4;
		var $bin42;
	
		function fianet_payment_xml($order = null)
		{

		}
		
		function set_cb_number($cb, $dateval)
		{
			if (!eregi("^[0-9]{16}$", $cb))
			{
				fianet_insert_log("fianet_payment_xml.php - set_cb_number() <br />Cb number format is invalid, must be 00001111222233334444\n");
			}
			else if (!eregi("^[0-9]{2}/[0-9]{4}$", $dateval))
			{
				fianet_insert_log("fianet_payment_xml.php - set_cb_number() <br />Validity date format is invalid, must be MM/YYYY\n");
			}
			else
			{
				$crypt = new HashMD5();
				$this->numcb = $crypt->hash($cb);
				$this->dateval = $crypt->hash($dateval);
			}
		}
		
		function get_xml()
		{
			$xml = '';
			if ($this->type != null)
			{
				$xml .= "\t".'<paiement>'."\n";
				
				$xml .= "\t\t".'<type>'.$this->type.'</type>'."\n";
				if ($this->type == 'carte' || $this->type == 'paypal')
				{
					if ($this->numcb != null)
					{
						$xml .= "\t\t".'<numcb>'.$this->numcb.'</numcb>'."\n";
					}
					if ($this->dateval != null)
					{
						$xml .= "\t\t".'<dateval>'.$this->dateval.'</dateval>'."\n";
					}
					if ($this->bin != null)
					{
						$xml .= "\t\t".'<bin>'.$this->bin.'</bin>'."\n";
					}
					if ($this->bin4 != null)
					{
						$xml .= "\t\t".'<bin4>'.$this->bin4.'</bin4>'."\n";
					}
					if ($this->bin42 != null)
					{
						$xml .= "\t\t".'<bin42>'.$this->bin42.'</bin42>'."\n";
					}
				}
				$xml .= "\t".'</paiement>'."\n";
			}
			else
			{
				fianet_insert_log("fianet_payment_xml.php - get_xml() <br />Type is undefined\n");
				return;
			}
			return ($xml);
		}
	}

