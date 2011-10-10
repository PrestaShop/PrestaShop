<?php

function move_crossselling()
{

if (Db::getInstance()->executeS('SELECT FROM `'._DB_PREFIX_.'module` WHERE `name` = \'crossselling\''))
{
Db::getInstance()->execute('
INSERT INTO `'._DB_PREFIX_.'hook_module` (`id_module`, `id_hook`, `position`)
VALUES ((SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = \'crossselling\'), 9, (SELECT max_position FROM (SELECT MAX(position)+1 as max_position FROM `'._DB_PREFIX_.'hook_module` WHERE `id_hook` = 9) tmp))');
}

}

