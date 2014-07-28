<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty cat modifier plugin
 * Type:     modifier<br>
 * Name:     cat<br>
 * Date:     Feb 24, 2003<br>
 * Purpose:  catenate a value to a variable<br>
 * Input:    string to catenate<br>
 * Example:  {$var|cat:"foo"}
 *
 * @link     http://smarty.php.net/manual/en/language.modifier.cat.php cat
 *           (Smarty online manual)
 * @author   Uwe Tews
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_cat($params)
{
    return '(' . implode(').(', $params) . ')';
}
