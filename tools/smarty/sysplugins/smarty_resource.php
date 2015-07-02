<?php
/**
 * Smarty Resource Plugin
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Rodney Rehm
 */

/**
 * Smarty Resource Plugin
 * Base implementation for resource plugins
 *
 * @package    Smarty
 * @subpackage TemplateResources
 */
abstract class Smarty_Resource
{
    /**
     * Source is bypassing compiler
     *
     * @var boolean
     */
    public $uncompiled = false;

    /**
     * Source must be recompiled on every occasion
     *
     * @var boolean
     */
    public $recompiled = false;
    /**
     * resource handler object
     *
     * @var Smarty_Resource
     */
    public $handler = null;
    /**
     * cache for Smarty_Template_Source instances
     *
     * @var array
     */
    public static $sources = array();
    /**
     * cache for Smarty_Template_Compiled instances
     *
     * @var array
     */
    public static $compileds = array();
    /**
     * resource types provided by the core
     *
     * @var array
     */
    protected static $sysplugins = array(
        'file'    => 'smarty_internal_resource_file.php',
        'string'  => 'smarty_internal_resource_string.php',
        'extends' => 'smarty_internal_resource_extends.php',
        'stream'  => 'smarty_internal_resource_stream.php',
        'eval'    => 'smarty_internal_resource_eval.php',
        'php'     => 'smarty_internal_resource_php.php'
    );

    /**
     * Name of the Class to compile this resource's contents with
     *
     * @var string
     */
    public $compiler_class = 'Smarty_Internal_SmartyTemplateCompiler';

    /**
     * Name of the Class to tokenize this resource's contents with
     *
     * @var string
     */
    public $template_lexer_class = 'Smarty_Internal_Templatelexer';

    /**
     * Name of the Class to parse this resource's contents with
     *
     * @var string
     */
    public $template_parser_class = 'Smarty_Internal_Templateparser';

    /**
     * Load template's source into current template object
     * {@internal The loaded source is assigned to $_template->source->content directly.}}
     *
     * @param  Smarty_Template_Source $source source object
     *
     * @return string                 template source
     * @throws SmartyException        if source cannot be loaded
     */
    abstract public function getContent(Smarty_Template_Source $source);

    /**
     * populate Source Object with meta data from Resource
     *
     * @param Smarty_Template_Source   $source    source object
     * @param Smarty_Internal_Template $_template template object
     */
    abstract public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template = null);

    /**
     * populate Source Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Source $source source object
     */
    public function populateTimestamp(Smarty_Template_Source $source)
    {
        // intentionally left blank
    }

    /**
     * modify resource_name according to resource handlers specifications
     *
     * @param  Smarty  $smarty        Smarty instance
     * @param  string  $resource_name resource_name to make unique
     * @param  boolean $isConfig      flag for config resource
     *
     * @return string unique resource name
     */
    public function buildUniqueResourceName(Smarty $smarty, $resource_name, $isConfig = false)
    {
        if ($isConfig) {
            return get_class($this) . '#' . $smarty->joined_config_dir . '#' . $resource_name;
        } else {
            return get_class($this) . '#' . $smarty->joined_template_dir . '#' . $resource_name;
        }
    }

    /**
     * Determine basename for compiled filename
     *
     * @param  Smarty_Template_Source $source source object
     *
     * @return string                 resource's basename
     */
    public function getBasename(Smarty_Template_Source $source)
    {
        return null;
    }

    /**
     * Load Resource Handler
     *
     * @param  Smarty $smarty smarty object
     * @param  string $type   name of the resource
     *
     * @throws SmartyException
     * @return Smarty_Resource Resource Handler
     */
    public static function load(Smarty $smarty, $type)
    {
        // try smarty's cache
        if (isset($smarty->_resource_handlers[$type])) {
            return $smarty->_resource_handlers[$type];
        }

        // try registered resource
        if (isset($smarty->registered_resources[$type])) {
            if ($smarty->registered_resources[$type] instanceof Smarty_Resource) {
                $smarty->_resource_handlers[$type] = $smarty->registered_resources[$type];
            } else {
                $smarty->_resource_handlers[$type] = new Smarty_Internal_Resource_Registered();
            }

            return $smarty->_resource_handlers[$type];
        }

        // try sysplugins dir
        if (isset(self::$sysplugins[$type])) {
            $_resource_class = 'Smarty_Internal_Resource_' . ucfirst($type);
            if (!class_exists($_resource_class, false)) {
                require SMARTY_SYSPLUGINS_DIR . self::$sysplugins[$type];
            }
            return $smarty->_resource_handlers[$type] = new $_resource_class();
        }

        // try plugins dir
        $_resource_class = 'Smarty_Resource_' . ucfirst($type);
        if ($smarty->loadPlugin($_resource_class)) {
            if (class_exists($_resource_class, false)) {
                return $smarty->_resource_handlers[$type] = new $_resource_class();
            } else {
                $smarty->registerResource($type, array(
                    "smarty_resource_{$type}_source",
                    "smarty_resource_{$type}_timestamp",
                    "smarty_resource_{$type}_secure",
                    "smarty_resource_{$type}_trusted"
                ));
                // give it another try, now that the resource is registered properly
                return self::load($smarty, $type);
            }
        }

        // try streams
        $_known_stream = stream_get_wrappers();
        if (in_array($type, $_known_stream)) {
            // is known stream
            if (is_object($smarty->security_policy)) {
                $smarty->security_policy->isTrustedStream($type);
            }
            return $smarty->_resource_handlers[$type] = new Smarty_Internal_Resource_Stream();;
        }

        // TODO: try default_(template|config)_handler

        // give up
        throw new SmartyException("Unknown resource type '{$type}'");
    }

    /**
     * extract resource_type and resource_name from template_resource and config_resource
     * @note "C:/foo.tpl" was forced to file resource up till Smarty 3.1.3 (including).
     *
     * @param  string $resource_name    template_resource or config_resource to parse
     * @param  string $default_resource the default resource_type defined in $smarty
     *
     * @return array with parsed resource name and type
     */
    public static function parseResourceName($resource_name, $default_resource)
    {
         if (preg_match('/^([A-Za-z0-9_\-]{2,})[:]/', $resource_name, $match)) {
            $type = $match[1];
            $name = substr($resource_name, strlen($match[0]));
        } else {
            // no resource given, use default
            // or single character before the colon is not a resource type, but part of the filepath
            $type = $default_resource;
            $name = $resource_name;

        }
        return array($name, $type);
    }

    /**
     * modify resource_name according to resource handlers specifications
     *
     * @param  Smarty $smarty        Smarty instance
     * @param  string $resource_name resource_name to make unique
     *
     * @return string unique resource name
     */

    /**
     * modify template_resource according to resource handlers specifications
     *
     * @param  Smarty_Internal_template $template          Smarty instance
     * @param  string                   $template_resource template_resource to extract resource handler and name of
     *
     * @return string unique resource name
     */
    public static function getUniqueTemplateName($template, $template_resource)
    {
        $smarty = isset($template->smarty) ? $template->smarty : $template;
        list($name, $type) = self::parseResourceName($template_resource, $smarty->default_resource_type);
        // TODO: optimize for Smarty's internal resource types
        $resource = Smarty_Resource::load($smarty, $type);
        // go relative to a given template?
        $_file_is_dotted = $name[0] == '.' && ($name[1] == '.' || $name[1] == '/');
        if ($template instanceof Smarty_Internal_Template && $_file_is_dotted && ($template->source->type == 'file' || $template->parent->source->type == 'extends')) {
            $name = dirname($template->source->filepath) . DS . $name;
        }
        return $resource->buildUniqueResourceName($smarty, $name);
    }

    /**
     * initialize Source Object for given resource
     * wrapper for backward compatibility to versions < 3.1.22
     * Either [$_template] or [$smarty, $template_resource] must be specified
     *
     * @param  Smarty_Internal_Template $_template         template object
     * @param  Smarty                   $smarty            smarty object
     * @param  string                   $template_resource resource identifier
     *
     * @return Smarty_Template_Source   Source Object
     */
    public static function source(Smarty_Internal_Template $_template = null, Smarty $smarty = null, $template_resource = null)
    {
        return Smarty_Template_Source::load($_template, $smarty, $template_resource);
    }
}

