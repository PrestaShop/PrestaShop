<?php
/**
 * Smarty Internal Plugin Compile Include
 * Compiles the {include} tag
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Include Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Include extends Smarty_Internal_CompileBase
{
    /**
     * caching mode to create nocache code but no cache file
     */
    const CACHING_NOCACHE_CODE = 9999;
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array('file');
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array('file');
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $option_flags = array('nocache', 'inline', 'caching');
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('_any');

    /**
     * Compiles code for the {include} tag
     *
     * @param  array                                  $args      array with attributes from parser
     * @param  Smarty_Internal_SmartyTemplateCompiler $compiler  compiler object
     * @param  array                                  $parameter array with compilation parameter
     *
     * @throws SmartyCompilerException
     * @return string compiled code
     */
    public function compile($args, Smarty_Internal_SmartyTemplateCompiler $compiler, $parameter)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);

        // save possible attributes
        $include_file = $_attr['file'];

        if (isset($_attr['assign'])) {
            // output will be stored in a smarty variable instead of being displayed
            $_assign = $_attr['assign'];
        }

        $_parent_scope = Smarty::SCOPE_LOCAL;
        if (isset($_attr['scope'])) {
            $_attr['scope'] = trim($_attr['scope'], "'\"");
            if ($_attr['scope'] == 'parent') {
                $_parent_scope = Smarty::SCOPE_PARENT;
            } elseif ($_attr['scope'] == 'root') {
                $_parent_scope = Smarty::SCOPE_ROOT;
            } elseif ($_attr['scope'] == 'global') {
                $_parent_scope = Smarty::SCOPE_GLOBAL;
            }
        }

        // assume caching is off
        $_caching = Smarty::CACHING_OFF;

        if ($_attr['nocache'] === true) {
            $compiler->tag_nocache = true;
        }

        $call_nocache = $compiler->tag_nocache || $compiler->nocache;

        // caching was on and {include} is not in nocache mode
        if ($compiler->template->caching && !$compiler->nocache && !$compiler->tag_nocache) {
            $_caching = self::CACHING_NOCACHE_CODE;
        }

        // flag if included template code should be merged into caller
        $merge_compiled_includes = ($compiler->smarty->merge_compiled_includes || ($compiler->inheritance && $compiler->smarty->inheritance_merge_compiled_includes) || $_attr['inline'] === true) && !$compiler->template->source->recompiled;

        if ($merge_compiled_includes && $_attr['inline'] !== true) {
            // variable template name ?
            if ($compiler->has_variable_string || !((substr_count($include_file, '"') == 2 || substr_count($include_file, "'") == 2)) || substr_count($include_file, '(') != 0 || substr_count($include_file, '$_smarty_tpl->') != 0) {
                $merge_compiled_includes = false;
                if ($compiler->template->caching) {
                    // must use individual cache file
                    //$_attr['caching'] = 1;
                }
                if ($compiler->inheritance && $compiler->smarty->inheritance_merge_compiled_includes && $_attr['inline'] !== true) {
                    $compiler->trigger_template_error(' variable template file names not allow within {block} tags');
                }
            }
            // variable compile_id?
            if (isset($_attr['compile_id'])) {
                if (!((substr_count($_attr['compile_id'], '"') == 2 || substr_count($_attr['compile_id'], "'") == 2 || is_numeric($_attr['compile_id']))) || substr_count($_attr['compile_id'], '(') != 0 || substr_count($_attr['compile_id'], '$_smarty_tpl->') != 0) {
                    $merge_compiled_includes = false;
                    if ($compiler->template->caching) {
                        // must use individual cache file
                        //$_attr['caching'] = 1;
                    }
                    if ($compiler->inheritance && $compiler->smarty->inheritance_merge_compiled_includes && $_attr['inline'] !== true) {
                        $compiler->trigger_template_error(' variable compile_id not allow within {block} tags');
                    }
                }
            }
        }

        /*
        * if the {include} tag provides individual parameter for caching or compile_id
        * the subtemplate must not be included into the common cache file and is treated like
        * a call in nocache mode.
        *
        */
        if ($_attr['nocache'] !== true && $_attr['caching']) {
            $_caching = $_new_caching = (int) $_attr['caching'];
            $call_nocache = true;
        } else {
            $_new_caching = Smarty::CACHING_LIFETIME_CURRENT;
        }
        if (isset($_attr['cache_lifetime'])) {
            $_cache_lifetime = $_attr['cache_lifetime'];
            $call_nocache = true;
            $_caching = $_new_caching;
        } else {
            $_cache_lifetime = '$_smarty_tpl->cache_lifetime';
        }
        if (isset($_attr['cache_id'])) {
            $_cache_id = $_attr['cache_id'];
            $call_nocache = true;
            $_caching = $_new_caching;
        } else {
            $_cache_id = '$_smarty_tpl->cache_id';
        }
        if (isset($_attr['compile_id'])) {
            $_compile_id = $_attr['compile_id'];
        } else {
            $_compile_id = '$_smarty_tpl->compile_id';
        }

        // if subtemplate will be called in nocache mode do not merge
        if ($compiler->template->caching && $call_nocache) {
            $merge_compiled_includes = false;
        }

        $has_compiled_template = false;
        if ($merge_compiled_includes) {
            if ($compiler->template->caching && ($compiler->tag_nocache || $compiler->nocache) && $_caching != self::CACHING_NOCACHE_CODE) {
                //                $merge_compiled_includes = false;
                if ($compiler->inheritance && $compiler->smarty->inheritance_merge_compiled_includes) {
                    $compiler->trigger_template_error(' invalid caching mode of subtemplate within {block} tags');
                }
            }
            $c_id = isset($_attr['compile_id']) ? $_attr['compile_id'] : $compiler->template->compile_id;
            // we must observe different compile_id and caching
            $uid = sha1($c_id . ($_caching ? '--caching' : '--nocaching'));
            $tpl_name = null;

            /** @var Smarty_Internal_Template $_smarty_tpl
             * used in evaluated code
             */
            $_smarty_tpl = $compiler->template;
            eval("\$tpl_name = $include_file;");
            if (!isset($compiler->parent_compiler->mergedSubTemplatesData[$tpl_name][$uid])) {
                $compiler->smarty->allow_ambiguous_resources = true;
                $tpl = new $compiler->smarty->template_class ($tpl_name, $compiler->smarty, $compiler->template, $compiler->template->cache_id, $c_id, $_caching);
                // save unique function name
                $compiler->parent_compiler->mergedSubTemplatesData[$tpl_name][$uid]['func'] = $tpl->properties['unifunc'] = 'content_' . str_replace(array('.', ','), '_', uniqid('', true));
                if ($compiler->inheritance) {
                    $tpl->compiler->inheritance = true;
                }
                // make sure whole chain gets compiled
                $tpl->mustCompile = true;
                if (!($tpl->source->uncompiled) && $tpl->source->exists) {
                    $tpl->compiler->suppressTemplatePropertyHeader = true;
                    $compiler->parent_compiler->mergedSubTemplatesData[$tpl_name][$uid]['nocache_hash'] = $tpl->properties['nocache_hash'] = str_replace(array('.', ','), '_', uniqid(rand(), true));
                    // get compiled code
                    $compiled_code = Smarty_Internal_Extension_CodeFrame::createFunctionFrame($tpl, $tpl->compiler->compileTemplate($tpl, null, $compiler->parent_compiler));
                    unset($tpl->compiler);

                    // remove header code
                    $compiled_code = preg_replace("/(<\?php \/\*%%SmartyHeaderCode:{$tpl->properties['nocache_hash']}%%\*\/(.+?)\/\*\/%%SmartyHeaderCode%%\*\/\?>\n)/s", '', $compiled_code);
                    if ($tpl->has_nocache_code) {
                        // replace nocache_hash
                        $compiled_code = str_replace("{$tpl->properties['nocache_hash']}", $compiler->template->properties['nocache_hash'], $compiled_code);
                        $compiler->template->has_nocache_code = true;
                    }
                    $compiler->parent_compiler->mergedSubTemplatesCode[$tpl->properties['unifunc']] = $compiled_code;
                    $has_compiled_template = true;
                    if (!empty($tpl->required_plugins['compiled'])) {
                        foreach ($tpl->required_plugins['compiled'] as $name => $callBack) {
                            if (!isset($compiler->template->required_plugins['compiled'][$name])) {
                                $compiler->template->required_plugins['compiled'][$name] = $callBack;
                            }
                        }
                    }
                    if (!empty($tpl->required_plugins['nocache'])) {
                        foreach ($tpl->required_plugins['nocache'] as $name => $callBack) {
                            if (!isset($compiler->template->required_plugins['nocache'][$name])) {
                                $compiler->template->required_plugins['nocache'][$name] = $callBack;
                            }
                        }
                    }
                    unset ($tpl);
                }
            } else {
                $has_compiled_template = true;
            }
        }
        // delete {include} standard attributes
        unset($_attr['file'], $_attr['assign'], $_attr['cache_id'], $_attr['compile_id'], $_attr['cache_lifetime'], $_attr['nocache'], $_attr['caching'], $_attr['scope'], $_attr['inline']);
        // remaining attributes must be assigned as smarty variable
        $_vars_nc = '';
        if (!empty($_attr)) {
            if ($_parent_scope == Smarty::SCOPE_LOCAL) {
                $_pairs = array();
                // create variables
                foreach ($_attr as $key => $value) {
                    $_pairs[] = "'$key'=>$value";
                    $_vars_nc .= "\$_smarty_tpl->tpl_vars['$key'] =  new Smarty_Variable($value);\n";
                }
                $_vars = 'array(' . join(',', $_pairs) . ')';
            } else {
                $compiler->trigger_template_error('variable passing not allowed in parent/global scope', $compiler->lex->taglineno);
            }
        } else {
            $_vars = 'array()';
        }
        $update_compile_id = $compiler->template->caching && !$compiler->tag_nocache && !$compiler->nocache && $_compile_id != '$_smarty_tpl->compile_id';
        if ($has_compiled_template && !$call_nocache) {
            //           if ($has_compiled_template && !$compiler->tag_nocache && !$compiler->nocache) {
            // never call inline templates in nocache mode
            //$compiler->suppressNocacheProcessing = true;
            $_hash = $compiler->parent_compiler->mergedSubTemplatesData[$tpl_name][$uid]['nocache_hash'];
            $_output = "<?php /*  Call merged included template \"" . $tpl_name . "\" */\n";
            if ($update_compile_id) {
                $_output .= $compiler->makeNocacheCode("\$_compile_id_save[] = \$_smarty_tpl->compile_id;\n\$_smarty_tpl->compile_id = {$_compile_id};\n");
            }
            if (!empty($_vars_nc) && $_caching == 9999 && $_smarty_tpl->caching) {
                //$compiler->suppressNocacheProcessing = false;
                $_output .= substr($compiler->processNocacheCode('<?php ' . $_vars_nc . "?>\n", true), 6, - 3);
                //$compiler->suppressNocacheProcessing = true;
            }
            if (isset($_assign)) {
                $_output .= " \$_smarty_tpl->tpl_vars[$_assign] = new Smarty_Variable(\$_smarty_tpl->getInlineSubTemplate({$include_file}, {$_cache_id}, {$_compile_id}, {$_caching}, {$_cache_lifetime}, {$_vars}, {$_parent_scope}, '{$_hash}', '{$compiler->parent_compiler->mergedSubTemplatesData[$tpl_name][$uid]['func']}'));\n";
            } else {
                $_output .= "echo \$_smarty_tpl->getInlineSubTemplate({$include_file}, {$_cache_id}, {$_compile_id}, {$_caching}, {$_cache_lifetime}, {$_vars}, {$_parent_scope}, '{$_hash}', '{$compiler->parent_compiler->mergedSubTemplatesData[$tpl_name][$uid]['func']}');\n";
            }
            if ($update_compile_id) {
                $_output .= $compiler->makeNocacheCode("\$_smarty_tpl->compile_id = array_pop(\$_compile_id_save);\n");
            }
            $_output .= "/*  End of included template \"" . $tpl_name . "\" */?>\n";

            return $_output;
        }

        if ($call_nocache) {
            $compiler->tag_nocache = true;
        }
        $_output = "<?php ";
        if ($update_compile_id) {
            $_output .= "\$_compile_id_save[] = \$_smarty_tpl->compile_id;\n\$_smarty_tpl->compile_id = {$_compile_id};\n";
        }
        // was there an assign attribute
        if (isset($_assign)) {
            $_output .= "\$_smarty_tpl->tpl_vars[$_assign] = new Smarty_Variable(\$_smarty_tpl->getSubTemplate ($include_file, $_cache_id, $_compile_id, $_caching, $_cache_lifetime, $_vars, $_parent_scope));\n";
        } else {
            $_output .= "echo \$_smarty_tpl->getSubTemplate ($include_file, $_cache_id, $_compile_id, $_caching, $_cache_lifetime, $_vars, $_parent_scope);\n";
        }
        if ($update_compile_id) {
            $_output .= "\$_smarty_tpl->compile_id = array_pop(\$_compile_id_save);\n";
        }
        $_output .= "?>\n";
        return $_output;
    }
}
