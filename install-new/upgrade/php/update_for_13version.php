<?php

function update_for_13version()
{
	global $oldversion;
	
	if (version_compare($oldversion, '1.4.0.1') >= 0)
		return; // if the old version is a 1.4 version
	
	// Disable the Smarty 3
	// Disable the URL rewritting
	// Disable Canonical redirection
	$res = Db::getInstance()->getValue('REPLACE INTO `'._DB_PREFIX_.'configuration`
		(name, value) VALUES 
		("PS_FORCE_SMARTY_2", "1"),
		("PS_REWRITING_SETTINGS", "0")
		("PS_CANONICAL_REDIRECT", "0")
		');
}
