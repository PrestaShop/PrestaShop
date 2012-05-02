<?php

/**
 * Move all translation modules files 1.4 for a good architecture in 1.5
 */
function moveTranslationsModuleFile()
{
	// Get all languages
	$languages = Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'lang`
	');

	// Get the list of modules
	$modules = scandir(_PS_MODULE_DIR_);

	// Scan all modules and check if translation file exists
	foreach ($modules as $module_name)
		foreach ($languages as $lang)
		{
			// Check if is a good module
			if (!in_array($module_name, array('.', '..', '.svn', '.htaccess', 'index.php')))
			{
				// Name for the old file and the new file
				$old_file = _PS_MODULE_DIR_.$module_name.'/'.$lang['iso_code'].'.php';
				$dir_translations = _PS_MODULE_DIR_.$module_name.'/translations/';
				$new_file = $dir_translations.$lang['iso_code'].'.php';

				// Create folder if no exist
				if (!is_dir($dir_translations))
					mkdir($dir_translations, 0777);

				if (file_exists($old_file))
					if (copy($old_file, $new_file))
						unlink($old_file);
			}
		}
}