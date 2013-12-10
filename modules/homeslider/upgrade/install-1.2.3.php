<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_2_3($object)
{
	return ($object->unregisterHook('displayHome') && $object->registerHook('displayHeader') && $object->registerHook('displayTopColumn'));
}