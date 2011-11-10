<?php


function remove_tab($tabname)
{
	Db::getInstance()->execute('
	DELETE t, l
	FROM `ps_tab` t LEFT JOIN `PREFIX_tab_lang` l ON (t.id_tab = l.id_tab)
	WHERE t.`class_name` = '.pSQL($tabname));
}

