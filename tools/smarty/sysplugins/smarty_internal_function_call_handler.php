<?php
/**
 * Smarty Internal Plugin Function Call Handler
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */

/**
 * This class does handles template functions defined with the {function} tag missing in cache file.
 * It can happen when the template function was called with the nocache option or within a nocache section.
 * The template function will be loaded from it's compiled template file, executed and added to the cache file
 * for later use.
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 */
class Smarty_Internal_Function_Call_Handler
{
    /**
     * This function handles calls to template functions defined by {function}
     * It does create a PHP function at the first call
     *
     * @param string                   $_name     template function name
     * @param Smarty_Internal_Template $_smarty_tpl
     * @param string                   $_function PHP function name
     * @param array                    $_params   Smarty variables passed as call parameter
     * @param bool                     $_nocache  nocache flag
     *
     * @return bool
     */
    public static function call($_name, Smarty_Internal_Template $_smarty_tpl, $_function, $_params, $_nocache)
    {
        $funcParam = $_smarty_tpl->properties['tpl_function'][$_name];
        if (is_file($funcParam['compiled_filepath'])) {
            // read compiled file
            $code = file_get_contents($funcParam['compiled_filepath']);
            // grab template function
            if (preg_match("/\/\* {$_function} \*\/([\S\s]*?)\/\*\/ {$_function} \*\//", $code, $match)) {
                // grab source info from file dependency
                preg_match("/\s*'{$funcParam['uid']}'([\S\s]*?)\),/", $code, $match1);
                unset($code);
                $output = '';
                // make PHP function known
                eval($match[0]);
                if (function_exists($_function)) {
                    // search cache file template
                    $tplPtr = $_smarty_tpl;
                    while (!isset($tplPtr->cached) && isset($tplPtr->parent)) {
                        $tplPtr = $tplPtr->parent;
                    }
                    // add template function code to cache file
                    if (isset($tplPtr->cached)) {
                        $cache = $tplPtr->cached;
                        $content = $cache->read($tplPtr);
                        if ($content) {
                            // check if we must update file dependency
                            if (!preg_match("/'{$funcParam['uid']}'([\S\s]*?)'nocache_hash'/", $content, $match2)) {
                                $content = preg_replace("/('file_dependency'([\S\s]*?)\()/", "\\1{$match1[0]}", $content);
                            }
                            $cache->write($tplPtr, $content . "<?php " . $match[0] . "?>\n");
                        }
                    }
                    return true;
                }
            }
        }
        return false;
    }
}
