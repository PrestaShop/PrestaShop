<?php

	class fianet_xml_paracallback_builder
	{
		var $param_list = array();
		
		function fianet_xml_paracallback_builder()
		{

		}
		
		function add_param($param)
		{
			if (var_is_object_of_class($param, 'fianet_paraobject_xml'))
			{
				$this->param_list[] = $param;
			}
			else
			{
				fianet_insert_log("Erreur : le paramètre n'est pas un objet fianet_paraobject_xml mais un objet : ".get_class($param)."<br>");
			}
		}
		
		function get_xml()
		{
			$xml = '';
			
			if (count($this->param_list) > 0)
			{
				$xml .= '<?xml version="1.0" encoding="'.FIANET_ENCODING.'" ?>
				<ParamCBack>' . "\n";
				
				foreach ($this->param_list as $param)
				{
					$xml .= $param->get_xml();
				}
				
				$xml .= '</ParamCBack>' . "\n";
			}
			
			return($xml);
		}
	}

