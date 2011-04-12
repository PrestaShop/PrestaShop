<?php
//Cette fonction est appellée par l'API en cas d'erreur, vous pouvez l'implémenter pour créer un journal d'erreur
function fianet_insert_log($message)
{
	echo $message . '<br />';
}

//Cette fonction est appelée dès qu'une flux xml est généré. Le premier paramètre est le flux lui-même, le second paramètre est la référence de la commande
function save_flux_xml($xml, $ref_id)
{
}

