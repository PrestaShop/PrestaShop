<?php



function generic_add_missing_column($table, $column_to_add)
{
	$column = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.$table.'`');
	$column_formated = array();

	foreach($column_exist as $c)
		$column_formated[] = $c['Field'] ;

	foreach($column_to_add as $name => $details)
		if (!in_array($name, $column_formated))
			$res &= Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.$table'` ADD COLUMN `'.$name.'` '.$details);
	
	return $res;
}