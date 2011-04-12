<?php
	class fianet_rnp_info_order_xml
	{
		var $siteid = 0;
		var $refid = 0;
		var $montant = 0;
		var $devise = 'EUR';
		var $transport;
		var $list;
	
		function fianet_rnp_info_order_xml()
		{
			$this->list = new fianet_product_list_xml();
			$this->transport = new fianet_transport_xml();
			$this->siteid = FIANET_RNP_SITE_ID;
		}
		
		function get_xml()
		{
			$xml = '';

			if ($this->siteid == '' || $this->refid == '' || $this->montant <= 0 || $this->devise == '')
			{
				fianet_insert_log("fianet_rnp_info_order_xml.php - get_xml() <br />Somes values are undefined\n");
			}
			$xml .= "\t" . '<infocommande>' . "\n";
			$xml .= "\t\t" . '<siteid>'.$this->siteid.'</siteid>' . "\n";
			$xml .= "\t\t" . '<refid>'.$this->refid.'</refid>' . "\n";
			$xml .= "\t\t" . '<montant devise="'.$this->devise.'">'.number_format($this->montant, 2, '.', '').'</montant>' . "\n";
			$xml .= $this->transport->get_xml();
			$xml .= $this->list->get_xml();
			$xml .= "\t" . '</infocommande>' . "\n";
			return ($xml);
		}
	}

