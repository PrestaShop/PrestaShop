<?php
/**
 * Smarty Resource Extension
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Uwe Tews
 */

/**
 * Smarty Resource Extension
 * Default template and config file handling
 *
 * @package    Smarty
 * @subpackage TemplateResources
 */
class Smarty_Internal_Extension_DefaultTemplateHandler
{

    /**
     * get default content from template of config resource handler
     *
     * @param Smarty_Internal_Template        $_template
     * @param Smarty_Internal_Template_Source $source
     * @param  Smarty_Resource                $resObj
     */
    static function _getDefault(Smarty_Internal_Template $_template, &$source, &$resObj)
    {
        if ($source->isConfig) {
            $default_handler = $_template->smarty->default_config_handler_func;
        } else {
            $default_handler = $_template->smarty->default_template_handler_func;
        }
        $_content = $_timestamp = null;
        $_return = call_user_func_array($default_handler,
                                        array($source->type, $source->name, &$_content, &$_timestamp, $source->smarty));
        if (is_string($_return)) {
            $source->exists = is_file($_return);
            if ($source->exists) {
                $source->timestamp = filemtime($_return);
            }
            $source->filepath = $_return;
        } elseif ($_return === true) {
            $source->content = $_content;
            $source->timestamp = $_timestamp;
            $source->exists = true;
            $source->recompiled = true;
            $source->filepath = false;
        }
    }

    /**
     * register template default handler
     *
     * @param Smarty $smarty
     * @param mixed  $callback
     *
     * @throws SmartyException
     */
    static function registerDefaultTemplateHandler(Smarty $smarty, $callback)
    {
        if (is_callable($callback)) {
            $smarty->default_template_handler_func = $callback;
        } else {
            throw new SmartyException("Default template handler not callable");
        }
    }

    /**
     * register config default handler
     *
     * @param Smarty $smarty
     * @param mixed  $callback
     *
     * @throws SmartyException
     */
    static function registerDefaultConfigHandler(Smarty $smarty, $callback)
    {
        if (is_callable($callback)) {
            $smarty->default_config_handler_func = $callback;
        } else {
            throw new SmartyException("Default config handler not callable");
        }
    }
}