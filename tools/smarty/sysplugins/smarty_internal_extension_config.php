<?php

/**
 * @package    Smarty
 * @subpackage PluginsInternal
 */
class Smarty_Internal_Extension_Config
{
    /**
     * @param        $obj
     * @param        $config_file
     * @param null   $sections
     * @param string $scope
     */
    static function configLoad($obj, $config_file, $sections = null, $scope = 'local')
    {
        $smarty = isset($obj->smarty) ? $obj->smarty : $obj;
        $confObj = new $smarty->template_class($config_file, $smarty, $obj);
        $confObj->caching = Smarty::CACHING_OFF;
        $confObj->source = Smarty_Template_Config::load($confObj);
        $confObj->source->config_sections = $sections;
        $confObj->source->scope = $scope;
        $confObj->compiled = Smarty_Template_Compiled::load($confObj);
        if ($confObj->smarty->debugging) {
            Smarty_Internal_Debug::start_render($confObj);
        }
        $confObj->compiled->render($confObj);
        if ($confObj->smarty->debugging) {
            Smarty_Internal_Debug::end_render($confObj);
        }
        if ($obj instanceof Smarty_Internal_Template) {
            $obj->properties['file_dependency'][$confObj->source->uid] = array($confObj->source->filepath, $confObj->source->timestamp, $confObj->source->type);
        }
    }

    /**
     * load config variables
     *
     * @param mixed  $sections array of section names, single section or null
     * @param string $scope    global,parent or local
     *
     * @throws Exception
     */
    static function loadConfigVars($_template, $_config_vars)
    {
        $scope = $_template->source->scope;
        // pointer to scope (local scope is parent of template object
        $scope_ptr = $_template->parent;
        if ($scope == 'parent') {
            if (isset($_template->parent->parent)) {
                $scope_ptr = $_template->parent->parent;
            }
        } elseif ($scope == 'root' || $scope == 'global') {
            while (isset($scope_ptr->parent)) {
                $scope_ptr = $scope_ptr->parent;
            }
        }
        // copy global config vars
        foreach ($_config_vars['vars'] as $variable => $value) {
            if ($_template->smarty->config_overwrite || !isset($scope_ptr->config_vars[$variable])) {
                $scope_ptr->config_vars[$variable] = $value;
            } else {
                $scope_ptr->config_vars[$variable] = array_merge((array) $scope_ptr->config_vars[$variable], (array) $value);
            }
        }
        // scan sections
        $sections = $_template->source->config_sections;
        if (!empty($sections)) {
            foreach ((array) $sections as $_template_section) {
                if (isset($_config_vars['sections'][$_template_section])) {
                    foreach ($_config_vars['sections'][$_template_section]['vars'] as $variable => $value) {
                        if ($_template->smarty->config_overwrite || !isset($scope_ptr->config_vars[$variable])) {
                            $scope_ptr->config_vars[$variable] = $value;
                        } else {
                            $scope_ptr->config_vars[$variable] = array_merge((array) $scope_ptr->config_vars[$variable], (array) $value);
                        }
                    }
                }
            }
        }
    }

    /**
     * Returns a single or all config variables
     *
     * @param  string $varname variable name or null
     * @param bool    $search_parents
     *
     * @return string variable value or or array of variables
     */
    static function getConfigVars($obj, $varname = null, $search_parents = true)
    {
        $_ptr = $obj;
        $var_array = array();
        while ($_ptr !== null) {
            if (isset($varname)) {
                if (isset($_ptr->config_vars[$varname])) {
                    return $_ptr->config_vars[$varname];
                }
            } else {
                $var_array = array_merge($_ptr->config_vars, $var_array);
            }
            // not found, try at parent
            if ($search_parents) {
                $_ptr = $_ptr->parent;
            } else {
                $_ptr = null;
            }
        }
        if (isset($varname)) {
            return '';
        } else {
            return $var_array;
        }
    }

    /**
     * gets  a config variable
     *
     * @param  string $variable the name of the config variable
     * @param bool    $error_enable
     *
     * @return mixed  the value of the config variable
     */
    static function getConfigVariable($obj, $variable, $error_enable = true)
    {
        $_ptr = $obj;
        while ($_ptr !== null) {
            if (isset($_ptr->config_vars[$variable])) {
                // found it, return it
                return $_ptr->config_vars[$variable];
            }
            // not found, try at parent
            $_ptr = $_ptr->parent;
        }
        if ($obj->error_unassigned && $error_enable) {
            // force a notice
            $x = $$variable;
        }

        return null;
    }

    /**
     * remove a single or all config variables
     *
     * @param  string $name variable name or null
     *
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for chaining
     */
    static function clearConfig($obj, $name = null)
    {
        if (isset($name)) {
            unset($obj->config_vars[$name]);
        } else {
            $obj->config_vars = array();
        }
        return $obj;
    }
}
