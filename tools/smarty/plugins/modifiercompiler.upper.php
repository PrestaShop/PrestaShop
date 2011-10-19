<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty upper modifier plugin
 * 
 * Type:     modifier<br>
 * Name:     lower<br>
 * Purpose:  convert string to uppercase
 * 
 * @link http://smarty.php.net/manual/en/language.modifier.upper.php lower (Smarty online manual)
 * @author Uwe Tews 
 * @param array $params parameters
 * @return string with compiled code
 */
function smarty_modifiercompiler_upper($params, $compiler)
{
    if (SMARTY_MBSTRING /* ^phpunit */&&empty($_SERVER['SMARTY_PHPUNIT_DISABLE_MBSTRING'])/* phpunit$ */) {
        return 'mb_strtoupper(' . $params[0] . ',SMARTY_RESOURCE_CHAR_SET)' ;
    }
    // no MBString fallback
    return 'strtoupper(' . $params[0] . ')';
} 

?>