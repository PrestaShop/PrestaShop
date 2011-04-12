<?php

	class fianet_product_list_xml
	{
		var $products_list = array();
	
		function fianet_product_list_xml()
		{
		}
		
		function add_product($product)
		{
			if (var_is_object_of_class($product, 'fianet_product_xml'))
			{
				$this->products_list[] = $product;
			}
			else
			{
				fianet_insert_log("fianet_product_list_xml.php - add_product() <br />Data are not a valid fianet_product_xml type\n");
			}
		}
		
		function get_xml()
		{
			$xml = '';
			if (count($this->products_list) > 0)
			{
				
				$xml .= "\t\t". '<list nbproduit="'.$this->count_nbproduct().'">' . "\n";
				foreach ($this->products_list as $product)
				{
					$xml .= $product->get_xml();
				}
				$xml .= "\t\t". '</list>' . "\n";
			}
			return ($xml);
		}
		
		function count_nbproduct()
		{
			$n = 0;
			foreach ($this->products_list as $product)
			{
				$n += $product->nb;
			}
			return ($n);
		}
	}

