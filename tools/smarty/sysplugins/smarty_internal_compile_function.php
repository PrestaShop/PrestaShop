<?php
/**
 * Smarty Internal Plugin Compile Function
 * Compiles the {function} {/function} tags
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Function Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Function extends Smarty_Internal_CompileBase
{

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array('name');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array('name');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('_any');

    /**
     * Compiles code for the {function} tag
     *
     * @param  array  $args      array with attributes from parser
     * @param  object $compiler  compiler object
     * @param  array  $parameter array with compilation parameter
     *
     * @return boolean true
     */
    public function compile($args, $compiler, $parameter)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);

        if ($_attr['nocache'] === true) {
            $compiler->trigger_template_error('nocache option not allowed', $compiler->lex->taglineno);
        }
        unset($_attr['nocache']);
        $_name = trim($_attr['name'], "'\"");
        $compiler->parent_compiler->templateProperties['tpl_function'][$_name] = array();
        $save = array($_attr, $compiler->parser->current_buffer, $compiler->template->has_nocache_code, $compiler->template->required_plugins, $compiler->template->caching);
        $this->openTag($compiler, 'function', $save);
        // set flag that we are compiling a template function
        $compiler->compiles_template_function = true;
        // Init temporary context
        $compiler->template->required_plugins = array('compiled' => array(), 'nocache' => array());
        $compiler->parser->current_buffer = new Smarty_Internal_ParseTree_Template($compiler->parser);
        $compiler->template->has_nocache_code = false;
        $compiler->template->caching = true;
        return true;
    }
}

/**
 * Smarty Internal Plugin Compile Functionclose Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Functionclose extends Smarty_Internal_CompileBase
{

    /**
     * Compiler object
     *
     * @var object
     */
    private $compiler = null;

    /**
     * Compiles code for the {/function} tag
     *
     * @param  array                                       $args      array with attributes from parser
     * @param object|\Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                       $parameter array with compilation parameter
     *
     * @return bool true
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        $this->compiler = $compiler;
        $saved_data = $this->closeTag($compiler, array('function'));
        $_attr = $saved_data[0];
        $_name = trim($_attr['name'], "'\"");
        // reset flag that we are compiling a template function
        $compiler->compiles_template_function = false;
        $compiler->parent_compiler->templateProperties['tpl_function'][$_name]['called_functions'] = $compiler->called_functions;
        $compiler->parent_compiler->templateProperties['tpl_function'][$_name]['compiled_filepath'] = $compiler->parent_compiler->template->compiled->filepath;
        $compiler->parent_compiler->templateProperties['tpl_function'][$_name]['uid'] = $compiler->template->source->uid;
        $compiler->called_functions = array();
        $_parameter = $_attr;
        unset($_parameter['name']);
        // default parameter
        $_paramsArray = array();
        foreach ($_parameter as $_key => $_value) {
            if (is_int($_key)) {
                $_paramsArray[] = "$_key=>$_value";
            } else {
                $_paramsArray[] = "'$_key'=>$_value";
            }
        }
        if (!empty($_paramsArray)) {
            $_params = 'array(' . implode(",", $_paramsArray) . ')';
            $_paramsCode = "\$params = array_merge($_params, \$params);\n";
        } else {
            $_paramsCode = '';
        }
        $_functionCode = $compiler->parser->current_buffer;
        // setup buffer for template function code
        $compiler->parser->current_buffer = new Smarty_Internal_ParseTree_Template($compiler->parser);

        $_funcName = "smarty_template_function_{$_name}_{$compiler->template->properties['nocache_hash']}";
        $_funcNameCaching = $_funcName . '_nocache';
        if ($compiler->template->has_nocache_code) {
            $compiler->parent_compiler->templateProperties['tpl_function'][$_name]['call_name_caching'] = $_funcNameCaching;
            $output = "<?php\n";
            $output .= "/* {$_funcNameCaching} */\n";
            $output .= "if (!function_exists('{$_funcNameCaching}')) {\n";
            $output .= "function {$_funcNameCaching} (\$_smarty_tpl,\$params) {\n";
            // build plugin include code
            if (!empty($compiler->template->required_plugins['compiled'])) {
                foreach ($compiler->template->required_plugins['compiled'] as $tmp) {
                    foreach ($tmp as $data) {
                        $output .= "if (!is_callable('{$data['function']}')) require_once '{$data['file']}';\n";
                    }
                }
            }
            if (!empty($compiler->template->required_plugins['nocache'])) {
                $output .= "echo '/*%%SmartyNocache:{$compiler->template->properties['nocache_hash']}%%*/<?php ";
                foreach ($compiler->template->required_plugins['nocache'] as $tmp) {
                    foreach ($tmp as $data) {
                        $output .= "if (!is_callable(\'{$data['function']}\')) require_once \'{$data['file']}\';\n";
                    }
                }
                $output .= "?>/*/%%SmartyNocache:{$compiler->template->properties['nocache_hash']}%%*/';\n";
            }
            $output .= "ob_start();\n";
            $output .= $_paramsCode;
            $output .= "\$_smarty_tpl->properties['saved_tpl_vars'][] = \$_smarty_tpl->tpl_vars;\n";
            $output .= "foreach (\$params as \$key => \$value) {\n\$_smarty_tpl->tpl_vars[\$key] = new Smarty_Variable(\$value);\n}";
            $output .= "\$params = var_export(\$params, true);\n";
            $output .= "echo \"/*%%SmartyNocache:{$compiler->template->properties['nocache_hash']}%%*/<?php ";
            $output .= "\\\$saved_tpl_vars = \\\$_smarty_tpl->tpl_vars;\nforeach (\$params as \\\$key => \\\$value) {\n\\\$_smarty_tpl->tpl_vars[\\\$key] = new Smarty_Variable(\\\$value);\n}\n?>";
            $output .= "/*/%%SmartyNocache:{$compiler->template->properties['nocache_hash']}%%*/\n\";?>";
            $compiler->parser->current_buffer->append_subtree(new Smarty_Internal_ParseTree_Tag($compiler->parser, $output));
            $compiler->parser->current_buffer->append_subtree($_functionCode);
            $output = "<?php echo \"/*%%SmartyNocache:{$compiler->template->properties['nocache_hash']}%%*/<?php ";
            $output .= "foreach (Smarty::\\\$global_tpl_vars as \\\$key => \\\$value){\n";
            $output .= "if (\\\$_smarty_tpl->tpl_vars[\\\$key] === \\\$value) \\\$saved_tpl_vars[\\\$key] = \\\$value;\n}\n";
            $output .= "\\\$_smarty_tpl->tpl_vars = \\\$saved_tpl_vars;?>\n";
            $output .= "/*/%%SmartyNocache:{$compiler->template->properties['nocache_hash']}%%*/\";\n?>";
            $output .= "<?php echo str_replace('{$compiler->template->properties['nocache_hash']}', \$_smarty_tpl->properties['nocache_hash'], ob_get_clean());\n";
            $output .= "\$_smarty_tpl->tpl_vars = array_pop(\$_smarty_tpl->properties['saved_tpl_vars']);\n}\n}\n";
            $output .= "/*/ {$_funcName}_nocache */\n\n";
            $output .= "?>\n";
            $compiler->parser->current_buffer->append_subtree(new Smarty_Internal_ParseTree_Tag($compiler->parser, $output));
            $_functionCode = new Smarty_Internal_ParseTree_Tag($compiler->parser, preg_replace_callback("/((<\?php )?echo '\/\*%%SmartyNocache:{$compiler->template->properties['nocache_hash']}%%\*\/([\S\s]*?)\/\*\/%%SmartyNocache:{$compiler->template->properties['nocache_hash']}%%\*\/';(\?>\n)?)/", array($this, 'removeNocache'), $_functionCode->to_smarty_php()));
        }
        $compiler->parent_compiler->templateProperties['tpl_function'][$_name]['call_name'] = $_funcName;
        $output = "<?php\n";
        $output .= "/* {$_funcName} */\n";
        $output .= "if (!function_exists('{$_funcName}')) {\n";
        $output .= "function {$_funcName}(\$_smarty_tpl,\$params) {\n";
        // build plugin include code
        if (!empty($compiler->template->required_plugins['nocache'])) {
            $compiler->template->required_plugins['compiled'] = array_merge($compiler->template->required_plugins['compiled'], $compiler->template->required_plugins['nocache']);
        }
        if (!empty($compiler->template->required_plugins['compiled'])) {
            foreach ($compiler->template->required_plugins['compiled'] as $tmp) {
                foreach ($tmp as $data) {
                    $output .= "if (!is_callable('{$data['function']}')) require_once '{$data['file']}';\n";
                }
            }
        }
        $output .= "\$saved_tpl_vars = \$_smarty_tpl->tpl_vars;\n";
        $output .= $_paramsCode;
        $output .= "foreach (\$params as \$key => \$value) {\n\$_smarty_tpl->tpl_vars[\$key] = new Smarty_Variable(\$value);\n}?>";
        $compiler->parser->current_buffer->append_subtree(new Smarty_Internal_ParseTree_Tag($compiler->parser, $output));
        $compiler->parser->current_buffer->append_subtree($_functionCode);
        $output = "<?php foreach (Smarty::\$global_tpl_vars as \$key => \$value){\n";
        $output .= "if (\$_smarty_tpl->tpl_vars[\$key] === \$value) \$saved_tpl_vars[\$key] = \$value;\n}\n";
        $output .= "\$_smarty_tpl->tpl_vars = \$saved_tpl_vars;\n}\n}\n";
        $output .= "/*/ {$_funcName} */\n\n";
        $output .= "?>\n";
        $compiler->parser->current_buffer->append_subtree(new Smarty_Internal_ParseTree_Tag($compiler->parser, $output));
        $compiler->parent_compiler->templateFunctionCode .= $compiler->parser->current_buffer->to_smarty_php();
        // restore old buffer
        $compiler->parser->current_buffer = $saved_data[1];
        // restore old status
        $compiler->template->has_nocache_code = $saved_data[2];
        $compiler->template->required_plugins = $saved_data[3];
        $compiler->template->caching = $saved_data[4];
        return true;
    }

    /**
     * @param $match
     *
     * @return mixed
     */
    function removeNocache($match)
    {
        $code = preg_replace("/((<\?php )?echo '\/\*%%SmartyNocache:{$this->compiler->template->properties['nocache_hash']}%%\*\/)|(\/\*\/%%SmartyNocache:{$this->compiler->template->properties['nocache_hash']}%%\*\/';(\?>\n)?)/", '', $match[0]);
        $code = str_replace(array('\\\'', '\\\\\''), array('\'', '\\\''), $code);
        return $code;
    }
}
