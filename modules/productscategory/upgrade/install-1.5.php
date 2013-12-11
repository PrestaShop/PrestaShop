<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_5($object)
{
	if (!$object->isRegisteredInHook('header'))
		return $object->registerHook('header');
	return true;
}
