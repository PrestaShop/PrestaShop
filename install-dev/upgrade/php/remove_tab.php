<?php


function remove_tab($tabname)
{
	Db::getInstance()->execute('
	DELETE t, l
	FROM `'._DB_PREFIX_.'tab` t LEFT JOIN `'._DB_PREFIX_.'tab_lang` l ON (t.id_tab = l.id_tab)
	WHERE t.`class_name` = '.pSQL($tabname));
}

