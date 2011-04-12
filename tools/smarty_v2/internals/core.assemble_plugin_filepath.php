<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * assemble filepath of requested plugin
 *
 * @param string $type
 * @param string $name
 * @return string|false
 */
function smarty_core_assemble_plugin_filepath($params, &$smarty)
{
    static $_filepaths_cache = array();
	
	/* PrestaShop optimization */
	
	if (!sizeof($_filepaths_cache))
	{	
		// PHP functions or PrestaShop functions
		$_filepaths_cache['modifier.addslashes.php'] = '';
		$_filepaths_cache['modifier.htmlentities.php'] = '';
		$_filepaths_cache['modifier.stripslashes.php'] = '';
		$_filepaths_cache['modifier.intval.php'] = '';
		$_filepaths_cache['modifier.urlencode.php'] = '';
		$_filepaths_cache['modifier.ceil.php'] = '';
		$_filepaths_cache['modifier.urlencode.php'] = '';
		$_filepaths_cache['modifier.count.php'] = '';
		$_filepaths_cache['modifier.strpos.php'] = '';
		$_filepaths_cache['modifier.htmlspecialchars.php'] = '';
		$_filepaths_cache['modifier.floatval.php'] = '';
		$_filepaths_cache['modifier.html_entity_decode.php'] = '';
		$_filepaths_cache['compiler.l.php'] = '';
		$_filepaths_cache['block.l.php'] = '';
		$_filepaths_cache['compiler.math.php'] = '';
		$_filepaths_cache['block.math.php'] = '';
		$_filepaths_cache['compiler.convertPrice.php'] = '';
		$_filepaths_cache['block.convertPrice.php'] = '';
		$_filepaths_cache['compiler.m.php'] = '';
		$_filepaths_cache['block.m.php'] = '';
		$_filepaths_cache['compiler.t.php'] = '';
		$_filepaths_cache['block.t.php'] = '';
		$_filepaths_cache['block.displayWtPrice.php'] = '';
		$_filepaths_cache['compiler.displayWtPrice.php'] = '';
		$_filepaths_cache['compiler.counter.php'] = '';
		$_filepaths_cache['block.counter.php'] = '';
		$_filepaths_cache['modifier.sizeof.php'] = '';
		$_filepaths_cache['compiler.convertPriceWithCurrency.php'] = '';
		$_filepaths_cache['block.convertPriceWithCurrency.php'] = '';
		$_filepaths_cache['compiler.dateFormat.php'] = '';
		$_filepaths_cache['block.dateFormat.php'] = '';
		$_filepaths_cache['compiler.displayPrice.php'] = '';
		$_filepaths_cache['block.displayPrice.php'] = '';
		$_filepaths_cache['compiler.displayWtPriceWithCurrency.php'] = '';
		$_filepaths_cache['block.displayWtPriceWithCurrency.php'] = '';
		
		// Smarty plugins
		$_filepaths_cache['modifier.cat.php'] = SMARTY_DIR.$smarty->plugins_dir[0].DIRECTORY_SEPARATOR.'modifier.cat.php';
		$_filepaths_cache['modifier.escape.php'] = SMARTY_DIR.$smarty->plugins_dir[0].DIRECTORY_SEPARATOR.'modifier.escape.php';
		$_filepaths_cache['modifier.truncate.php'] = SMARTY_DIR.$smarty->plugins_dir[0].DIRECTORY_SEPARATOR.'modifier.truncate.php';
		$_filepaths_cache['modifier.strip_tags.php'] = SMARTY_DIR.$smarty->plugins_dir[0].DIRECTORY_SEPARATOR.'modifier.strip_tags.php';
		$_filepaths_cache['modifier.date_format.php'] = SMARTY_DIR.$smarty->plugins_dir[0].DIRECTORY_SEPARATOR.'modifier.date_format.php';
		$_filepaths_cache['shared.make_timestamp.php'] = SMARTY_DIR.$smarty->plugins_dir[0].DIRECTORY_SEPARATOR.'shared.make_timestamp.php';
		$_filepaths_cache['function.math.php'] = SMARTY_DIR.$smarty->plugins_dir[0].DIRECTORY_SEPARATOR.'function.math.php';
		$_filepaths_cache['function.counter.php'] = SMARTY_DIR.$smarty->plugins_dir[0].DIRECTORY_SEPARATOR.'function.counter.php';
		$_filepaths_cache['modifier.default.php'] = SMARTY_DIR.$smarty->plugins_dir[0].DIRECTORY_SEPARATOR.'modifier.default.php';
		$_filepaths_cache['compiler.assign.php'] = SMARTY_DIR.$smarty->plugins_dir[0].DIRECTORY_SEPARATOR.'compiler.assign.php';
		$_filepaths_cache['modifier.string_format.php'] = SMARTY_DIR.$smarty->plugins_dir[0].DIRECTORY_SEPARATOR.'modifier.string_format.php';
		$_filepaths_cache['modifier.nl2br.php'] = SMARTY_DIR.$smarty->plugins_dir[0].DIRECTORY_SEPARATOR.'modifier.nl2br.php';
	}
	
	/* End */

    $_plugin_filename = $params['type'] . '.' . $params['name'] . '.php';
    if (isset($_filepaths_cache[$_plugin_filename])) {
        return $_filepaths_cache[$_plugin_filename];
    }
    $_return = false;

    foreach ((array)$smarty->plugins_dir as $_plugin_dir) {

        $_plugin_filepath = $_plugin_dir . DIRECTORY_SEPARATOR . $_plugin_filename;

        // see if path is relative
        if (!preg_match("/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/", $_plugin_dir)) {
            $_relative_paths[] = $_plugin_dir;
            // relative path, see if it is in the SMARTY_DIR
            if (@is_readable(SMARTY_DIR . $_plugin_filepath)) {
                $_return = SMARTY_DIR . $_plugin_filepath;
                break;
            }
        }
        // try relative to cwd (or absolute)
        if (@is_readable($_plugin_filepath)) {
            $_return = $_plugin_filepath;
            break;
        }
    }

    if($_return === false) {
        // still not found, try PHP include_path
        if(isset($_relative_paths)) {
            foreach ((array)$_relative_paths as $_plugin_dir) {

                $_plugin_filepath = $_plugin_dir . DIRECTORY_SEPARATOR . $_plugin_filename;

                $_params = array('file_path' => $_plugin_filepath);
                require_once(SMARTY_CORE_DIR . 'core.get_include_path.php');
                if(smarty_core_get_include_path($_params, $smarty)) {
                    $_return = $_params['new_file_path'];
                    break;
                }
            }
        }
    }
	
    $_filepaths_cache[$_plugin_filename] = $_return;
    return $_return;
}

/* vim: set expandtab: */

?>
