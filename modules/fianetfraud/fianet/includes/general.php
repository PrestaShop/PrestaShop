<?php

function clean_xml($xml)
{
	$xml = str_replace("\\'", "'", $xml);
	$xml = str_replace("\\\"", "\"", $xml);
	$xml = str_replace("\\\\", "\\", $xml);
	$xml = str_replace("\t", "", $xml);
	$xml = str_replace("\n", "", $xml);
	$xml = str_replace("\r", "", $xml);
	$xml = trim($xml);
	return ($xml);
}

function clean_invalid_char($var)
{
	//supprimes les balises html
	$var = strip_tags($var);
	$var = str_replace("&", "&amp;", $var);
	$var = str_replace("<", "&lt;", $var);
	$var = str_replace(">", "&gt;", $var);
	$var = trim($var);
	return ($var);
}

function var_is_object_of_class($var, $class_name)
{
	$res = false;
	if (is_object($var))
	{
		$name = get_class($var);
		if ($name == $class_name)
		{
			$res = true;
		}
	}
	return ($res);
}

//Calcule la date de livraison en jour ouvré à partir de la date courante
function get_delivery_date($delivery_times)
{
	define('H', date("H"));
	define('i', date("i"));
	define('s', date("s"));
	define('m', date("m"));
	define('d', date("d"));
	define('Y', date("Y"));
	define('SUNDAY', 0);
	define('SATURDAY', 6);
	
	$nb_days = 0;
	$j = 0;
	while ($nb_days < $delivery_times)
	{
		$j++;
		$date = mktime(H, i, s, m, d + $j, Y);
		$day = date("w", $date);
		if ($day != SUNDAY && $day != SATURDAY)
		{
			$nb_days++;
		}
	}
	if ($j > FIANET_MAX_DELIVERY_TIME)
	{//si on dépasse le délais de livraison max à causes des samedi et dimanche on remet le délais de livraison à son maximum
		$j = FIANET_MAX_DELIVERY_TIME;
	}
	$date = mktime(H, i, s, m, d + $j, Y);
	return (date("Y-m-d", $date));
}

