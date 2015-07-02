<?php
/**
 * Smarty Internal Plugin Smarty Template  Base
 * This file contains the basic shared methods for template handling
 *
 * @package    Smarty
 * @subpackage Template
 * @author     Uwe Tews
 */

/**
 * Class with shared template methods
 *
 * @package    Smarty
 * @subpackage Template
 */
abstract class Smarty_Internal_TemplateBase extends Smarty_Internal_Data
{
    /**
     * Set this if you want different sets of cache files for the same
     * templates.
     *
     * @var string
     */
    public $cache_id = null;
    /**
     * Set this if you want different sets of compiled files for the same
     * templates.
     *
     * @var string
     */
    public $compile_id = null;
    /**
     * caching enabled
     *
     * @var boolean
     */
    public $caching = false;
    /**
     * cache lifetime in seconds
     *
     * @var integer
     */
    public $cache_lifetime = 3600;

    /**
     * test if cache is valid
     *
     * @param  string|object $template   the resource handle of the template file or template object
     * @param  mixed         $cache_id   cache id to be used with this template
     * @param  mixed         $compile_id compile id to be used with this template
     * @param  object        $parent     next higher level of Smarty variables
     *
     * @return boolean       cache status
     */
    public function isCached($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        if ($template === null && $this instanceof $this->template_class) {
            $template = $this;
        } else {
            if (!($template instanceof $this->template_class)) {
                if ($parent === null) {
                    $parent = $this;
                }
                $smarty = isset($this->smarty) ? $this->smarty : $this;
                $template = $smarty->createTemplate($template, $cache_id, $compile_id, $parent, false);
            }
        }
        // return cache status of template
        if (!isset($template->cached)) {
            $template->loadCached();
        }
        return $template->cached->isCached($template);
    }

    /**
     * creates a data object
     *
     * @param object $parent next higher level of Smarty variables
     * @param string $name   optional data block name
     *
     * @returns Smarty_Data data object
     */
    public function createData($parent = null, $name = null)
    {
        $dataObj = new Smarty_Data($parent, $this, $name);
        if ($this->debugging) {
            Smarty_Internal_Debug::register_data($dataObj);
        }
        return $dataObj;
    }

    /**
     * Get unique template id
     *
     * @param string     $template_name
     * @param null|mixed $cache_id
     * @param null|mixed $compile_id
     *
     * @return string
     */
    public function getTemplateId($template_name, $cache_id = null, $compile_id = null)
    {
        $cache_id = isset($cache_id) ? $cache_id : $this->cache_id;
        $compile_id = isset($compile_id) ? $compile_id : $this->compile_id;
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        if ($smarty->allow_ambiguous_resources) {
            $_templateId = Smarty_Resource::getUniqueTemplateName($this, $template_name) . "#{$cache_id}#{$compile_id}";
        } else {
            $_templateId = $smarty->joined_template_dir . "#{$template_name}#{$cache_id}#{$compile_id}";
        }
        if (isset($_templateId[150])) {
            $_templateId = sha1($_templateId);
        }
        return $_templateId;
    }

    /**
     * Registers plugin to be used in templates
     *
     * @param  string   $type       plugin type
     * @param  string   $tag        name of template tag
     * @param  callback $callback   PHP callback to register
     * @param  boolean  $cacheable  if true (default) this fuction is cachable
     * @param  array    $cache_attr caching attributes if any
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     * @throws SmartyException              when the plugin tag is invalid
     */
    public function registerPlugin($type, $tag, $callback, $cacheable = true, $cache_attr = null)
    {
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        if (isset($smarty->registered_plugins[$type][$tag])) {
            throw new SmartyException("Plugin tag \"{$tag}\" already registered");
        } elseif (!is_callable($callback)) {
            throw new SmartyException("Plugin \"{$tag}\" not callable");
        } else {
            $smarty->registered_plugins[$type][$tag] = array($callback, (bool) $cacheable, (array) $cache_attr);
        }

        return $this;
    }

    /**
     * Unregister Plugin
     *
     * @param  string $type of plugin
     * @param  string $tag  name of plugin
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     */
    public function unregisterPlugin($type, $tag)
    {
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        if (isset($smarty->registered_plugins[$type][$tag])) {
            unset($smarty->registered_plugins[$type][$tag]);
        }

        return $this;
    }

    /**
     * Registers a resource to fetch a template
     *
     * @param  string                $type     name of resource type
     * @param  Smarty_Resource|array $callback or instance of Smarty_Resource, or array of callbacks to handle resource
     *                                         (deprecated)
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     */
    public function registerResource($type, $callback)
    {
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        $smarty->registered_resources[$type] = $callback instanceof Smarty_Resource ? $callback : array($callback, false);

        return $this;
    }

    /**
     * Unregisters a resource
     *
     * @param  string $type name of resource type
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     */
    public function unregisterResource($type)
    {
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        if (isset($smarty->registered_resources[$type])) {
            unset($smarty->registered_resources[$type]);
        }

        return $this;
    }

    /**
     * Registers a cache resource to cache a template's output
     *
     * @param  string               $type     name of cache resource type
     * @param  Smarty_CacheResource $callback instance of Smarty_CacheResource to handle output caching
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     */
    public function registerCacheResource($type, Smarty_CacheResource $callback)
    {
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        $smarty->registered_cache_resources[$type] = $callback;

        return $this;
    }

    /**
     * Unregisters a cache resource
     *
     * @param  string $type name of cache resource type
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     */
    public function unregisterCacheResource($type)
    {
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        if (isset($smarty->registered_cache_resources[$type])) {
            unset($smarty->registered_cache_resources[$type]);
        }

        return $this;
    }

    /**
     * Registers object to be used in templates
     *
     * @param          $object_name
     * @param  object  $object_impl   the referenced PHP object to register
     * @param  array   $allowed       list of allowed methods (empty = all)
     * @param  boolean $smarty_args   smarty argument format, else traditional
     * @param  array   $block_methods list of block-methods
     *
     * @throws SmartyException
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     */
    public function registerObject($object_name, $object_impl, $allowed = array(), $smarty_args = true, $block_methods = array())
    {
        // test if allowed methods callable
        if (!empty($allowed)) {
            foreach ((array) $allowed as $method) {
                if (!is_callable(array($object_impl, $method)) && !property_exists($object_impl, $method)) {
                    throw new SmartyException("Undefined method or property '$method' in registered object");
                }
            }
        }
        // test if block methods callable
        if (!empty($block_methods)) {
            foreach ((array) $block_methods as $method) {
                if (!is_callable(array($object_impl, $method))) {
                    throw new SmartyException("Undefined method '$method' in registered object");
                }
            }
        }
        // register the object
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        $smarty->registered_objects[$object_name] =
            array($object_impl, (array) $allowed, (boolean) $smarty_args, (array) $block_methods);

        return $this;
    }

    /**
     * return a reference to a registered object
     *
     * @param  string $name object name
     *
     * @return object
     * @throws SmartyException if no such object is found
     */
    public function getRegisteredObject($name)
    {
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        if (!isset($smarty->registered_objects[$name])) {
            throw new SmartyException("'$name' is not a registered object");
        }
        if (!is_object($smarty->registered_objects[$name][0])) {
            throw new SmartyException("registered '$name' is not an object");
        }

        return $smarty->registered_objects[$name][0];
    }

    /**
     * unregister an object
     *
     * @param  string $name object name
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     */
    public function unregisterObject($name)
    {
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        if (isset($smarty->registered_objects[$name])) {
            unset($smarty->registered_objects[$name]);
        }

        return $this;
    }

    /**
     * Registers static classes to be used in templates
     *
     * @param         $class_name
     * @param  string $class_impl the referenced PHP class to register
     *
     * @throws SmartyException
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     */
    public function registerClass($class_name, $class_impl)
    {
        // test if exists
        if (!class_exists($class_impl)) {
            throw new SmartyException("Undefined class '$class_impl' in register template class");
        }
        // register the class
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        $smarty->registered_classes[$class_name] = $class_impl;

        return $this;
    }

    /**
     * Registers a default plugin handler
     *
     * @param  callable $callback class/method name
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     * @throws SmartyException              if $callback is not callable
     */
    public function registerDefaultPluginHandler($callback)
    {
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        if (is_callable($callback)) {
            $smarty->default_plugin_handler_func = $callback;
        } else {
            throw new SmartyException("Default plugin handler '$callback' not callable");
        }

        return $this;
    }

    /**
     * Registers a default template handler
     *
     * @param  callable $callback class/method name
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     * @throws SmartyException              if $callback is not callable
     */
    public function registerDefaultTemplateHandler($callback)
    {
        Smarty_Internal_Extension_DefaultTemplateHandler::registerDefaultTemplateHandler($this, $callback);
        return $this;
    }

    /**
     * Registers a default template handler
     *
     * @param  callable $callback class/method name
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     * @throws SmartyException              if $callback is not callable
     */
    public function registerDefaultConfigHandler($callback)
    {
        Smarty_Internal_Extension_DefaultTemplateHandler::registerDefaultConfigHandler($this, $callback);
        return $this;
    }

    /**
     * Registers a filter function
     *
     * @param  string   $type filter type
     * @param  callback $callback
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     */
    public function registerFilter($type, $callback)
    {
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        $smarty->registered_filters[$type][$this->_get_filter_name($callback)] = $callback;

        return $this;
    }

    /**
     * Unregisters a filter function
     *
     * @param  string   $type filter type
     * @param  callback $callback
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     */
    public function unregisterFilter($type, $callback)
    {
        $name = $this->_get_filter_name($callback);
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        if (isset($smarty->registered_filters[$type][$name])) {
            unset($smarty->registered_filters[$type][$name]);
        }

        return $this;
    }

    /**
     * Return internal filter name
     *
     * @param  callback $function_name
     *
     * @return string   internal filter name
     */
    public function _get_filter_name($function_name)
    {
        if (is_array($function_name)) {
            $_class_name = (is_object($function_name[0]) ?
                get_class($function_name[0]) : $function_name[0]);

            return $_class_name . '_' . $function_name[1];
        } else {
            return $function_name;
        }
    }

    /**
     * load a filter of specified type and name
     *
     * @param  string $type filter type
     * @param  string $name filter name
     *
     * @throws SmartyException if filter could not be loaded
     */
    public function loadFilter($type, $name)
    {
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        $_plugin = "smarty_{$type}filter_{$name}";
        $_filter_name = $_plugin;
        if ($smarty->loadPlugin($_plugin)) {
            if (class_exists($_plugin, false)) {
                $_plugin = array($_plugin, 'execute');
            }
            if (is_callable($_plugin)) {
                $smarty->registered_filters[$type][$_filter_name] = $_plugin;

                return true;
            }
        }
        throw new SmartyException("{$type}filter \"{$name}\" not callable");
    }

    /**
     * unload a filter of specified type and name
     *
     * @param  string $type filter type
     * @param  string $name filter name
     *
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or
     *                                      Smarty_Internal_Template) instance for chaining
     */
    public function unloadFilter($type, $name)
    {
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        $_filter_name = "smarty_{$type}filter_{$name}";
        if (isset($smarty->registered_filters[$type][$_filter_name])) {
            unset ($smarty->registered_filters[$type][$_filter_name]);
        }

        return $this;
    }

    /**
     * preg_replace callback to convert camelcase getter/setter to underscore property names
     *
     * @param  string $match match string
     *
     * @return string replacemant
     */
    private function replaceCamelcase($match)
    {
        return "_" . strtolower($match[1]);
    }

    /**
     * Handle unknown class methods
     *
     * @param string $name unknown method-name
     * @param array  $args argument array
     *
     * @throws SmartyException
     */
    public function __call($name, $args)
    {
        static $_prefixes = array('set' => true, 'get' => true);
        static $_resolved_property_name = array();
        static $_resolved_property_source = array();

        // see if this is a set/get for a property
        $first3 = strtolower(substr($name, 0, 3));
        if (isset($_prefixes[$first3]) && isset($name[3]) && $name[3] !== '_') {
            if (isset($_resolved_property_name[$name])) {
                $property_name = $_resolved_property_name[$name];
            } else {
                // try to keep case correct for future PHP 6.0 case-sensitive class methods
                // lcfirst() not available < PHP 5.3.0, so improvise
                $property_name = strtolower(substr($name, 3, 1)) . substr($name, 4);
                // convert camel case to underscored name
                $property_name = preg_replace_callback('/([A-Z])/', array($this, 'replaceCamelcase'), $property_name);
                $_resolved_property_name[$name] = $property_name;
            }
            if (isset($_resolved_property_source[$property_name])) {
                $status = $_resolved_property_source[$property_name];
            } else {
                $status = null;
                if (property_exists($this, $property_name)) {
                    $status = true;
                } elseif (property_exists($this->smarty, $property_name)) {
                    $status = false;
                }
                $_resolved_property_source[$property_name] = $status;
            }
            $smarty = null;
            if ($status === true) {
                $smarty = $this;
            } elseif ($status === false) {
                $smarty = $this->smarty;
            }
            if ($smarty) {
                if ($first3 == 'get') {
                    return $smarty->$property_name;
                } else {
                    return $smarty->$property_name = $args[0];
                }
            }
            throw new SmartyException("property '$property_name' does not exist.");
        }
        throw new SmartyException("Call of unknown method '$name'.");
    }
}

