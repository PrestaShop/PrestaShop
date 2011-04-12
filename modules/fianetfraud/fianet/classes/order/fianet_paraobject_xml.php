<?php

class	fianet_paraobject_xml
{
	var $name;
	var $value;
	
	function fianet_paraobject_xml($name, $value)
	{
		$this->name = $name;
		$this->value = $value;
	}
	
	function get_xml()
	{
		$xml = "\t". '<obj>' . "\n";
		$xml .= "\t\t". '<name>'.$this->name.'</name>' . "\n";
		$xml .= "\t\t". '<value>'.$this->value.'</value>' . "\n";
		$xml .= "\t". '</obj>' . "\n";
		return ($xml);
	}
}

