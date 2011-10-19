<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsShared
 */

/**
 * evaluate compiler parameter
 *
 * @param array   $params  paramter array as given to the compiler function
 * @param integer $index   array index of the paramter to convert
 * @param mixed   $default value to be returned if the paramter is not present
 * @return mixed evaluated value of paramter or $default
 * @throws SmartyException if paramter is not a literal (but an expression, variable, …)
 * @author Rodney Rehm
 */
function smarty_literal_compiler_param($params, $index, $default=null)
{
    // not set, go default
    if (!isset($params[$index])) {
        return $default;
    }
    // test if param is a literal
    if (!preg_match('/^([\'"]?)[a-zA-Z0-9]+(\\1)$/', $params[$index])) {
        throw new SmartyException('$param[' . $index . '] is not a literal and is thus not evaluatable at compile time');
    }

    $t = null;
    eval("\$t = " . $params[$index] . ";");
    return $t;
}
