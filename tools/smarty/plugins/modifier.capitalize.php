<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty capitalize modifier plugin
 * Type:     modifier<br>
 * Name:     capitalize<br>
 * Purpose:  capitalize words in the string
 * {@internal {$string|capitalize:true:true} is the fastest option for MBString enabled systems }}
 *
 * @param string  $string    string to capitalize
 * @param boolean $uc_digits also capitalize "x123" to "X123"
 * @param boolean $lc_rest   capitalize first letters, lowercase all following letters "aAa" to "Aaa"
 *
 * @return string capitalized string
 * @author Monte Ohrt <monte at ohrt dot com>
 * @author Rodney Rehm
 */
function smarty_modifier_capitalize($string, $uc_digits = false, $lc_rest = false)
{
    if (Smarty::$_MBSTRING) {
        if ($lc_rest) {
            // uppercase (including hyphenated words)
            $upper_string = mb_convert_case($string, MB_CASE_TITLE, Smarty::$_CHARSET);
        } else {
            // uppercase word breaks
            $upper_string = preg_replace_callback("!(^|[^\p{L}'])([\p{Ll}])!S" . Smarty::$_UTF8_MODIFIER, 'smarty_mod_cap_mbconvert_cb', $string);
        }
        // check uc_digits case
        if (!$uc_digits) {
            if (preg_match_all("!\b([\p{L}]*[\p{N}]+[\p{L}]*)\b!" . Smarty::$_UTF8_MODIFIER, $string, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[1] as $match) {
                    $upper_string = substr_replace($upper_string, mb_strtolower($match[0], Smarty::$_CHARSET), $match[1], strlen($match[0]));
                }
            }
        }
        $upper_string = preg_replace_callback("!((^|\s)['\"])(\w)!" . Smarty::$_UTF8_MODIFIER, 'smarty_mod_cap_mbconvert2_cb', $upper_string);
        return $upper_string;
    }

    // lowercase first
    if ($lc_rest) {
        $string = strtolower($string);
    }
    // uppercase (including hyphenated words)
    $upper_string = preg_replace_callback("!(^|[^\p{L}'])([\p{Ll}])!S" . Smarty::$_UTF8_MODIFIER, 'smarty_mod_cap_ucfirst_cb', $string);
    // check uc_digits case
    if (!$uc_digits) {
        if (preg_match_all("!\b([\p{L}]*[\p{N}]+[\p{L}]*)\b!" . Smarty::$_UTF8_MODIFIER, $string, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[1] as $match) {
                $upper_string = substr_replace($upper_string, strtolower($match[0]), $match[1], strlen($match[0]));
            }
        }
    }
    $upper_string = preg_replace_callback("!((^|\s)['\"])(\w)!" . Smarty::$_UTF8_MODIFIER, 'smarty_mod_cap_ucfirst2_cb', $upper_string);
    return $upper_string;
}

/* 
 *
 * Bug: create_function() use exhausts memory when used in long loops
 * Fix: use declared functions for callbacks instead of using create_function()
 * Note: This can be fixed using anonymous functions instead, but that requires PHP >= 5.3
 *
 * @author Kyle Renfrow
 */
function smarty_mod_cap_mbconvert_cb($matches)
{
    return stripslashes($matches[1]) . mb_convert_case(stripslashes($matches[2]), MB_CASE_UPPER, Smarty::$_CHARSET);
}

function smarty_mod_cap_mbconvert2_cb($matches)
{
    return stripslashes($matches[1]) . mb_convert_case(stripslashes($matches[3]), MB_CASE_UPPER, Smarty::$_CHARSET);
}

function smarty_mod_cap_ucfirst_cb($matches)
{
    return stripslashes($matches[1]) . ucfirst(stripslashes($matches[2]));
}

function smarty_mod_cap_ucfirst2_cb($matches)
{
    return stripslashes($matches[1]) . ucfirst(stripslashes($matches[3]));
}
