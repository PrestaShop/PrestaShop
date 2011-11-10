<?php

function update_for_13version()
{
	global $oldversion;
	
	if (version_compare($oldversion, '1.4.0.1') >= 0)
		return; // if the old version is a 1.4 version
	
	// Disable the Smarty 3
	Configuration::updateValue('PS_FORCE_SMARTY_2', 1);
	// Disable the URL rewritting
	Configuration::updateValue('PS_REWRITING_SETTINGS', 0);
	// Disable Canonical redirection
	Configuration::updateValue('PS_CANONICAL_REDIRECT', 0);
}