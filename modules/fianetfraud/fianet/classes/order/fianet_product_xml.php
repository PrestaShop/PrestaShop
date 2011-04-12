<?php

	class fianet_product_xml
	{
		var $type;
		var $ref;
		var $nb = 0;
		var $prixunit;
		var $name;
	
		function fianet_product_xml()
		{

		}
		
		function get_xml()
		{
			$xml = '';
			if ($this->name != null)
			{
				$nb = '';
				$prixunit = '';
				if ($this->type != null)
				{
					$type = ' type="'.$this->type.'"';
				}
				else
				{
					fianet_insert_log("fianet_product_xml.php - get_xml() <br />\nproduct type is missing");
					return;
				}
				if ($this->ref != null)
				{
					$ref = ' ref="'.clean_invalid_char($this->ref).'"';
				}
				else
				{
					fianet_insert_log("fianet_product_xml.php - get_xml() <br />\nproduct ref is missing");
					return;
				}
				if ($this->nb != null)
				{
					$nb = ' nb="'.$this->nb.'"';
				}
				if ($this->prixunit != null)
				{
					$prixunit = ' prixunit="'.number_format($this->prixunit, 2, '.', '').'"';
				}
				$xml .= "\t\t\t<produit$type$ref$nb$prixunit>".clean_invalid_char($this->name)."</produit>\n";
			}
			else
			{
				fianet_insert_log("fianet_product_xml.php - get_xml() <br />\nproduct name is missing");
				return;
			}
			return ($xml);
		}
	}

