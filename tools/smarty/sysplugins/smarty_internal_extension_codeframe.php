<?php
/**
 * Smarty Internal Extension
 * This file contains the Smarty template extension to create a code frame
 *
 * @package    Smarty
 * @subpackage Template
 * @author     Uwe Tews
 */

/**
 * Class Smarty_Internal_Extension_CodeFrame
 * Create code frame for compiled and cached templates
 */
class Smarty_Internal_Extension_CodeFrame
{
    /**
     * Create code frame for compiled and cached templates
     *
     * @param Smarty_Internal_Template $_template
     * @param  string                  $content optional template content
     * @param  bool                    $cache   flag for cache file
     *
     * @return string
     */
    public static function create(Smarty_Internal_Template $_template, $content = '', $cache = false)
    {
        // build property code
        $_template->properties['has_nocache_code'] = $_template->has_nocache_code || !empty($_template->required_plugins['nocache']);
        $_template->properties['version'] = Smarty::SMARTY_VERSION;
        if (!isset($_template->properties['unifunc'])) {
            $_template->properties['unifunc'] = 'content_' . str_replace(array('.', ','), '_', uniqid('', true));
        }
        $properties = $_template->properties;
        if (!$cache) {
            unset($properties['tpl_function']);
            if (!empty($_template->compiler->templateProperties)) {
                $properties['tpl_function'] = $_template->compiler->templateProperties['tpl_function'];
            }
        }
        $output = "<?php\n";
        $output .= "/*%%SmartyHeaderCode:{$_template->properties['nocache_hash']}%%*/\n";
        if ($_template->smarty->direct_access_security) {
            $output .= "if(!defined('SMARTY_DIR')) exit('no direct access allowed');\n";
        }
        $output .= "\$_valid = \$_smarty_tpl->decodeProperties(" . var_export($properties, true) . ',' . ($cache ? 'true' : 'false') . ");\n";
        $output .= "/*/%%SmartyHeaderCode%%*/\n";
        $output .= "if (\$_valid && !is_callable('{$_template->properties['unifunc']}')) {\n";
        $output .= "function {$_template->properties['unifunc']} (\$_smarty_tpl) {\n";
        // include code for plugins
        if (!$cache) {
            if (!empty($_template->required_plugins['compiled'])) {
                foreach ($_template->required_plugins['compiled'] as $tmp) {
                    foreach ($tmp as $data) {
                        $file = addslashes($data['file']);
                        if (is_Array($data['function'])) {
                            $output .= "if (!is_callable(array('{$data['function'][0]}','{$data['function'][1]}'))) require_once '{$file}';\n";
                        } else {
                            $output .= "if (!is_callable('{$data['function']}')) require_once '{$file}';\n";
                        }
                    }
                }
            }
            if (!empty($_template->required_plugins['nocache'])) {
                $_template->has_nocache_code = true;
                $output .= "echo '/*%%SmartyNocache:{$_template->properties['nocache_hash']}%%*/<?php \$_smarty = \$_smarty_tpl->smarty; ";
                foreach ($_template->required_plugins['nocache'] as $tmp) {
                    foreach ($tmp as $data) {
                        $file = addslashes($data['file']);
                        if (is_Array($data['function'])) {
                            $output .= addslashes("if (!is_callable(array('{$data['function'][0]}','{$data['function'][1]}'))) require_once '{$file}';\n");
                        } else {
                            $output .= addslashes("if (!is_callable('{$data['function']}')) require_once '{$file}';\n");
                        }
                    }
                }
                $output .= "?>/*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%*/';\n";
            }
        }
        $output .= "?>\n";
        $output = self::appendCode($output, $content);
        return self::appendCode($output, "<?php }\n}\n?>");
    }

    /**
     * Create code frame of compiled template function
     *
     * @param \Smarty_Internal_Template $_template
     * @param string                    $content
     *
     * @return string
     */
    public static function createFunctionFrame(Smarty_Internal_Template $_template, $content = '')
    {
        if (!isset($_template->properties['unifunc'])) {
            $_template->properties['unifunc'] = 'content_' . str_replace(array('.', ','), '_', uniqid('', true));
        }
        $output = "<?php\n";
        $output .= "/*%%SmartyHeaderCode:{$_template->properties['nocache_hash']}%%*/\n";
        $output .= "if (\$_valid && !is_callable('{$_template->properties['unifunc']}')) {\n";
        $output .= "function {$_template->properties['unifunc']} (\$_smarty_tpl) {\n";
        $output .= "?>\n" . $content;
        $output .= "<?php\n";
        $output .= "/*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%*/\n";
        $output .= "}\n}\n?>";
        return $output;
    }

    /**
     * Append code segments and remove unneeded ?> <?php transitions
     *
     * @param string $left
     * @param string $right
     *
     * @return string
     */
    public static function appendCode($left, $right)
    {
        if (preg_match('/\s*\?>$/', $left) && preg_match('/^<\?php\s+/', $right)) {
            $left = preg_replace('/\s*\?>$/', "\n", $left);
            $left .= preg_replace('/^<\?php\s+/', '', $right);
        } else {
            $left .= $right;
        }
        return $left;
    }
}