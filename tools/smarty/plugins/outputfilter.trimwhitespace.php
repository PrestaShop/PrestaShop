<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFilter
 */

/**
 * Smarty trimwhitespace outputfilter plugin
 * Trim unnecessary whitespace from HTML markup.
 *
 * @author   Rodney Rehm
 *
 * @param string $source input string
 *
 * @return string filtered output
 * @todo     substr_replace() is not overloaded by mbstring.func_overload - so this function might fail!
 */
function smarty_outputfilter_trimwhitespace($source)
{
    $store = array();
    $_store = 0;
    $_offset = 0;

    // Unify Line-Breaks to \n
    $source = preg_replace("/\015\012|\015|\012/", "\n", $source);

    // capture Internet Explorer Conditional Comments
    if (preg_match_all('#<!--\[[^\]]+\]>.*?<!\[[^\]]+\]-->#is', $source, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $store[] = $match[0][0];
            $_length = strlen($match[0][0]);
            $replace = '@!@SMARTY:' . $_store . ':SMARTY@!@';
            $source = substr_replace($source, $replace, $match[0][1] - $_offset, $_length);

            $_offset += $_length - strlen($replace);
            $_store ++;
        }
    }

    // Strip all HTML-Comments
    // yes, even the ones in <script> - see http://stackoverflow.com/a/808850/515124
    $source = preg_replace('#<!--.*?-->#ms', '', $source);

    // capture html elements not to be messed with
    $_offset = 0;
    if (preg_match_all('#<(script|pre|textarea)[^>]*>.*?</\\1>#is', $source, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $store[] = $match[0][0];
            $_length = strlen($match[0][0]);
            $replace = '@!@SMARTY:' . $_store . ':SMARTY@!@';
            $source = substr_replace($source, $replace, $match[0][1] - $_offset, $_length);

            $_offset += $_length - strlen($replace);
            $_store ++;
        }
    }

    $expressions = array(
        // replace multiple spaces between tags by a single space
        // can't remove them entirely, becaue that might break poorly implemented CSS display:inline-block elements
        '#(:SMARTY@!@|>)\s+(?=@!@SMARTY:|<)#s'                            => '\1 \2',
        // remove spaces between attributes (but not in attribute values!)
        '#(([a-z0-9]\s*=\s*(["\'])[^\3]*?\3)|<[a-z0-9_]+)\s+([a-z/>])#is' => '\1 \4',
        // note: for some very weird reason trim() seems to remove spaces inside attributes.
        // maybe a \0 byte or something is interfering?
        '#^\s+<#Ss'                                                       => '<',
        '#>\s+$#Ss'                                                       => '>',
    );

    $source = preg_replace(array_keys($expressions), array_values($expressions), $source);
    // note: for some very weird reason trim() seems to remove spaces inside attributes.
    // maybe a \0 byte or something is interfering?
    // $source = trim( $source );

    $_offset = 0;
    if (preg_match_all('#@!@SMARTY:([0-9]+):SMARTY@!@#is', $source, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $_length = strlen($match[0][0]);
            $replace = $store[$match[1][0]];
            $source = substr_replace($source, $replace, $match[0][1] + $_offset, $_length);

            $_offset += strlen($replace) - $_length;
            $_store ++;
        }
    }

    return $source;
}
