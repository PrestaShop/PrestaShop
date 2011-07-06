<?php

	class fianet_appartment_xml
	{
		var $digicode1;
		var $digicode2;
		var $escalier;
		var $etage;
		var $nporte;
		var $batiment;

		function get_xml()
		{
			$xml = '';
			$xml .= "\t\t" . '<appartement>' . "\n";
			if ($this->digicode1 != '')
			{
				$xml .= "\t\t\t" . '<digicode1>'.clean_invalid_char($this->digicode1).'</digicode1>' . "\n";
			}
			if ($this->digicode2 != '')
			{
				$xml .= "\t\t\t" . '<digicode2>'.clean_invalid_char($this->digicode2).'</digicode2>' . "\n";
			}
			if ($this->escalier != '')
			{
				$xml .= "\t\t\t" . '<escalier>'.clean_invalid_char($this->escalier).'</escalier>' . "\n";
			}
			if ($this->etage != '')
			{
				$xml .= "\t\t\t" . '<etage>'.clean_invalid_char($this->etage).'</etage>' . "\n";
			}
			if ($this->nporte != '')
			{
				$xml .= "\t\t\t" . '<nporte>'.clean_invalid_char($this->nporte).'</nporte>' . "\n";
			}
			if ($this->batiment != '')
			{
				$xml .= "\t\t\t" . '<batiment>'.clean_invalid_char($this->batiment).'</batiment>' . "\n";
			}
			$xml .= "\t\t" . '</appartement>' . "\n";
			return ($xml);
		}
	}

