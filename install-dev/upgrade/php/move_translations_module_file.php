<?php

/**
 * Move all translation modules files 1.4 for a good architecture in 1.5
 */
function move_translations_module_file()
{
	$res = true;
	// Get all languages
	$languages = Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'lang`
	');

	// Get the list of modules
	$modules = scandir(_PS_MODULE_DIR_);

	$error_list = array();
	// Scan all modules and check if translation file exists
	foreach ($modules as $module_name)
	{
		// Check if is a good module
		if (in_array($module_name, array('.', '..', '.svn', '.htaccess', 'index.php', 'autoupgrade')))
			continue;

		foreach ($languages as $lang)
		{
			// Name for the old file and the new file
			$old_file = _PS_MODULE_DIR_.$module_name.'/'.$lang['iso_code'].'.php';
			if (!file_exists($old_file))
				continue;

			$dir_translations = _PS_MODULE_DIR_.$module_name.'/translations/';
			$new_file = $dir_translations.$lang['iso_code'].'.php';

			// Create folder if no exist
			if (!is_dir($dir_translations))
				$res &= mkdir($dir_translations, 0777);

				if (!rename($old_file, $new_file))
				{
					$error_list[] = $module_name.' - '.$lang['iso_code']."<br/>\r\n";
					$res &= false;
				}
		}
	}

	if (!$res||(count($error_list)>0))
		return array('error' => 1, 'msg' => implode("\r\n<br/>", $error_list));
	else
		return true;
}
