<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_1($object)
{
	return ($object->unregisterHook('displayRightColumn') && $object->registerHook('displayNav'));
}
