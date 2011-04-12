<?php

	class fianet_transport_xml
	{
		var $type;
		var $nom;
		var $rapidite;
	
		function fianet_transport_xml()
		{

		}
		
		function get_xml()
		{
			$xml = '';
			if ($this->type == null)
			{
				fianet_insert_log("fianet_transport_xml.php - get_xml() <br />\nTransport type undefined");
				return;
			}
			if ($this->nom == null)
			{
				fianet_insert_log("fianet_transport_xml.php - get_xml() <br />\nTransport name undefined");
				return;
			}
			if ($this->rapidite == null)
			{
				fianet_insert_log("fianet_transport_xml.php - get_xml() <br />\nTransport time undefined");
				return;
			}
			$xml .= "\t\t". '<transport>' . "\n";
			
			$xml .= "\t\t\t". '<type>'.$this->type.'</type>' . "\n";
			$xml .= "\t\t\t". '<nom>'.clean_invalid_char($this->nom).'</nom>' . "\n";
			$xml .= "\t\t\t". '<rapidite>'.$this->rapidite.'</rapidite>' . "\n";

			$xml .= "\t\t". '</transport>' . "\n";
			return ($xml);
		}
	}

