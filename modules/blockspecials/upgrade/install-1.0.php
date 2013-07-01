<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_0($object)
{
	Configuration::updateValue('BLOCKSPECIALS_NB_CACHES', 20);
	return ($object->registerHook('addproduct') && $object->registerHook('updateproduct') && $object->registerHook('deleteproduct'));
}
