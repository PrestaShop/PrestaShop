<?php

	class fianet_user_siteconso_xml
	{
		var $nb = 0;
		var $ca = 0;
		var $datepremcmd = null;
		var $datederncmd = null;
		
		function fianet_user_siteconso_xml()
		{
		}
		
		function get_xml()
		{
			$xml = '';
			if ($this->nb > 0)
			{
				$xml .= "\t\t" . '<siteconso>' . "\n";
				$xml .= "\t\t\t" . '<nb>'.$this->nb.'</nb>' . "\n";
				$xml .= "\t\t\t" . '<ca>'.number_format($this->ca, 2, '.', '').'</ca>' . "\n";
				$xml .= "\t\t\t" . '<datepremcmd>'.$this->datepremcmd.'</datepremcmd>' . "\n";
				$xml .= "\t\t\t" . '<datederncmd>'.$this->datederncmd.'</datederncmd>' . "\n";
				$xml .= "\t\t" . '</siteconso>' . "\n";
			}
			return ($xml);
		}
	}

