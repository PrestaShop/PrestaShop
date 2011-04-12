<?php

	class fianet_adress_xml
	{
		var $type;
		var $format = 1;
		var $rue1;
		var $rue2;
		var $cpostal;
		var $ville;
		var $pays;
		var $appartement = null;
		
		function get_xml()
		{
			$xml = '';
			if ($this->type != null)
			{
				$xml .= "\t" . '<adresse type="'.$this->type.'" format="'.$this->format.'">' . "\n";
				if ($this->rue1 != '')
				{
					$xml .= "\t\t" . '<rue1>'.clean_invalid_char($this->rue1).'</rue1>' . "\n";
				}
				else
				{
					fianet_insert_log("fianet_adress_xml.php - get_xml() <br />\n rue1 is undefined");
					return;
				}
				if ($this->rue2 != "")
				{
					$xml .= "\t\t" . '<rue2>'.clean_invalid_char($this->rue2).'</rue2>' . "\n";
				}
				if ($this->cpostal != "")
				{
					$xml .= "\t\t" . '<cpostal>'.clean_invalid_char($this->cpostal).'</cpostal>' . "\n";
				}
				else
				{
					fianet_insert_log("fianet_adress_xml.php - get_xml() <br />\n cpostal is undefined");
					return;
				}
				if ($this->ville != "")
				{
					$xml .= "\t\t" . '<ville>'.clean_invalid_char($this->ville).'</ville>' . "\n";
				}
				else
				{
					fianet_insert_log("fianet_adress_xml.php - get_xml() <br />\n ville is undefined");
					return;
				}
				if ($this->pays != "")
				{
					$xml .= "\t\t" . '<pays>'.clean_invalid_char($this->pays).'</pays>' . "\n";
				}
				else
				{
					fianet_insert_log("fianet_adress_xml.php - get_xml() <br />\n pays is undefined");
					return;
				}
				if ($this->appartement != null)
				{
					if (var_is_object_of_class($this->appartement, 'fianet_appartment_xml'))
					{
						$xml .= $this->appartement->get_xml();
					}
					else
					{
						fianet_insert_log("fianet_adress_xml.php - get_xml() <br />\nAppartement is not an object of type fianet_appartment_xml");
					}
				}
				$xml .= "\t" . '</adresse>' . "\n";
			}
			return ($xml);
		}
	}

