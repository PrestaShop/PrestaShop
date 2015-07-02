<?php

/**
 * Smarty Resource Data Object
 * Meta Data Container for Template Files
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Rodney Rehm
 * @property integer $timestamp Source Timestamp
 * @property boolean $exists    Source Existence
 * @property boolean $template  Extended Template reference
 * @property string  $content   Source Content
 */
class Smarty_Template_Source
{
    /**
     * Name of the Class to compile this resource's contents with
     *
     * @var string
     */
    public $compiler_class = null;

    /**
     * Name of the Class to tokenize this resource's contents with
     *
     * @var string
     */
    public $template_lexer_class = null;

    /**
     * Name of the Class to parse this resource's contents with
     *
     * @var string
     */
    public $template_parser_class = null;

    /**
     * Unique Template ID
     *
     * @var string
     */
    public $uid = null;

    /**
     * Template Resource (Smarty_Internal_Template::$template_resource)
     *
     * @var string
     */
    public $resource = null;

    /**
     * Resource Type
     *
     * @var string
     */
    public $type = null;

    /**
     * Resource Name
     *
     * @var string
     */
    public $name = null;

    /**
     * Unique Resource Name
     *
     * @var string
     */
    public $unique_resource = null;

    /**
     * Source Filepath
     *
     * @var string
     */
    public $filepath = null;
    /**
     * Source File Base name
     *
     * @var string
     */
    public $basename = null;

    /**
     * The Components an extended template is made of
     *
     * @var array
     */
    public $components = null;

    /**
     * Resource Handler
     *
     * @var Smarty_Resource
     */
    public $handler = null;

    /**
     * Smarty instance
     *
     * @var Smarty
     */
    public $smarty = null;
    /**
     * Resource is source
     *
     * @var bool
     */
    public $isConfig = false;
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
     * cache for Smarty_Template_Compiled instances
     *
     * @var array
     */
    public $compileds = array();

    /**
     * create Source Object container
     *
     * @param Smarty_Resource $handler  Resource Handler this source object communicates with
     * @param Smarty          $smarty   Smarty instance this source object belongs to
     * @param string          $resource full template_resource
     * @param string          $type     type of resource
     * @param string          $name     resource name
     *
     * @internal param string $unique_resource unique resource name
     */
    public function __construct(Smarty_Resource $handler, Smarty $smarty, $resource, $type, $name)
    {
        $this->handler = $handler; // Note: prone to circular references

        $this->recompiled = $handler->recompiled;
        $this->uncompiled = $handler->uncompiled;
        $this->compiler_class = $handler->compiler_class;
        $this->template_lexer_class = $handler->template_lexer_class;
        $this->template_parser_class = $handler->template_parser_class;

        $this->smarty = $smarty;
        $this->resource = $resource;
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * initialize Source Object for given resource
     * Either [$_template] or [$smarty, $template_resource] must be specified
     *
     * @param  Smarty_Internal_Template $_template         template object
     * @param  Smarty                   $smarty            smarty object
     * @param  string                   $template_resource resource identifier
     *
     * @return Smarty_Template_Source Source Object
     * @throws SmartyException
     */
    public static function load(Smarty_Internal_Template $_template = null, Smarty $smarty = null, $template_resource = null)
    {
        if ($_template) {
            $smarty = $_template->smarty;
            $template_resource = $_template->template_resource;
        }
        if (empty($template_resource)) {
            throw new SmartyException('Missing template name');
        }
        // parse resource_name, load resource handler, identify unique resource name
        list($name, $type) = Smarty_Resource::parseResourceName($template_resource, $smarty->default_resource_type);
        $resource = Smarty_Resource::load($smarty, $type);
        // if resource is not recompiling and resource name is not dotted we can check the source cache
        if ($smarty->resource_caching && !$resource->recompiled && !(isset($name[1]) && $name[0] == '.' && ($name[1] == '.' || $name[1] == '/'))) {
            $unique_resource = $resource->buildUniqueResourceName($smarty, $name);
            if (isset($smarty->source_objects[$unique_resource])) {
                return $smarty->source_objects[$unique_resource];
            }
        } else {
            $unique_resource = null;
        }
        // create new source  object
        $source = new Smarty_Template_Source($resource, $smarty, $template_resource, $type, $name);
        $resource->populate($source, $_template);
        if ((!isset($source->exists) || !$source->exists) && isset($_template->smarty->default_template_handler_func)) {
            Smarty_Internal_Extension_DefaultTemplateHandler::_getDefault($_template, $source, $resObj);
        }
        // on recompiling resources we are done
        if ($smarty->resource_caching && !$resource->recompiled) {
            // may by we have already $unique_resource
            $is_relative = false;
            if (!isset($unique_resource)) {
                $is_relative = isset($name[1]) && $name[0] == '.' && ($name[1] == '.' || $name[1] == '/') &&
                    ($type == 'file' || (isset($_template->parent->source) && $_template->parent->source->type == 'extends'));
                $unique_resource = $resource->buildUniqueResourceName($smarty, $is_relative ? $source->filepath . $name : $name);
            }
            $source->unique_resource = $unique_resource;
            // save in runtime cache if not relative
            if (!$is_relative) {
                $smarty->source_objects[$unique_resource] = $source;
            }
        }
        return $source;
    }

    /**
     * render the uncompiled source
     *
     * @param Smarty_Internal_Template $_template template object
     */
    public function renderUncompiled(Smarty_Internal_Template $_template)
    {
        $level = ob_get_level();
        ob_start();
        try {
            $this->handler->renderUncompiled($_template->source, $_template);
            return ob_get_clean();
        }
        catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        }
    }

    /**
     * <<magic>> Generic Setter.
     *
     * @param  string $property_name valid: timestamp, exists, content, template
     * @param  mixed  $value         new value (is not checked)
     *
     * @throws SmartyException if $property_name is not valid
     */
    public function __set($property_name, $value)
    {
        switch ($property_name) {
            // regular attributes
            case 'timestamp':
            case 'exists':
            case 'content':
                // required for extends: only
            case 'template':
                $this->$property_name = $value;
                break;

            default:
                throw new SmartyException("source property '$property_name' does not exist.");
        }
    }

    /**
     * <<magic>> Generic getter.
     *
     * @param  string $property_name valid: timestamp, exists, content
     *
     * @return mixed
     * @throws SmartyException if $property_name is not valid
     */
    public function __get($property_name)
    {
        switch ($property_name) {
            case 'timestamp':
            case 'exists':
                $this->handler->populateTimestamp($this);

                return $this->$property_name;

            case 'content':
                return $this->content = $this->handler->getContent($this);

            default:
                throw new SmartyException("source property '$property_name' does not exist.");
        }
    }
}
