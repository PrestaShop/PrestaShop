<?php
/**
 * Smarty Autoloader
 *
 * @package    Smarty
 */

/**
 * Smarty Autoloader
 *
 * @package    Smarty
 * @author     Uwe Tews
 *             Usage:
 *             require_once '...path/Autoloader.php';
 *             Smarty_Autoloader::register();
 *             $smarty = new Smarty();
 *             Note:       This autoloader is not needed if you use Composer.
 *             Composer will automatically add the classes of the Smarty package to it common autoloader.
 */
class Smarty_Autoloader
{
    /**
     * Filepath to Smarty root
     *
     * @var string
     */
    public static $SMARTY_DIR = '';
    /**
     * Filepath to Smarty internal plugins
     *
     * @var string
     */
    public static $SMARTY_SYSPLUGINS_DIR = '';
    /**
     * Array of not existing classes to avoid is_file calls for  already tested classes
     *
     * @var array
     */
    public static $unknown = array();
    /**
     * Array with Smarty core classes and their filename
     *
     * @var array
     */
    public static $rootClasses = array('Smarty'   => 'Smarty.class.php',
                                       'SmartyBC' => 'SmartyBC.class.php',
    );

    private static $syspluginsClasses = array(
        'smarty_config_source'                  => true,
        'smarty_security'                       => true,
        'smarty_cacheresource'                  => true,
        'smarty_compiledresource'               => true,
        'smarty_cacheresource_custom'           => true,
        'smarty_cacheresource_keyvaluestore'    => true,
        'smarty_resource'                       => true,
        'smarty_resource_custom'                => true,
        'smarty_resource_uncompiled'            => true,
        'smarty_resource_recompiled'            => true,
        'smarty_template_source'                => true,
        'smarty_template_compiled'              => true,
        'smarty_template_cached'                => true,
        'smarty_template_config'                => true,
        'smarty_data'                           => true,
        'smarty_variable'                       => true,
        'smarty_undefined_variable'             => true,
        'smartyexception'                       => true,
        'smartycompilerexception'               => true,
        'smarty_internal_data'                  => true,
        'smarty_internal_template'              => true,
        'smarty_internal_templatebase'          => true,
        'smarty_internal_resource_file'         => true,
        'smarty_internal_resource_extends'      => true,
        'smarty_internal_resource_eval'         => true,
        'smarty_internal_resource_string'       => true,
        'smarty_internal_resource_registered'   => true,
        'smarty_internal_extension_codeframe'   => true,
        'smarty_internal_extension_config'      => true,
        'smarty_internal_filter_handler'        => true,
        'smarty_internal_function_call_handler' => true,
        'smarty_internal_cacheresource_file'    => true,
        'smarty_internal_write_file'    => true,
    );

    /**
     * Registers Smarty_Autoloader backward compatible to older installations.
     *
     * @param bool $prepend Whether to prepend the autoloader or not.
     */
    public static function registerBC($prepend = false)
    {
        /**
         * register the class autoloader
         */
        if (!defined('SMARTY_SPL_AUTOLOAD')) {
            define('SMARTY_SPL_AUTOLOAD', 0);
        }
        if (SMARTY_SPL_AUTOLOAD && set_include_path(get_include_path() . PATH_SEPARATOR . SMARTY_SYSPLUGINS_DIR) !== false) {
            $registeredAutoLoadFunctions = spl_autoload_functions();
            if (!isset($registeredAutoLoadFunctions['spl_autoload'])) {
                spl_autoload_register();
            }
        } else {
            self::register($prepend);
        }
    }

    /**
     * Registers Smarty_Autoloader as an SPL autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not.
     */
    public static function register($prepend = false)
    {
        self::$SMARTY_DIR = defined('SMARTY_DIR') ? SMARTY_DIR : dirname(__FILE__) . '/';
        self::$SMARTY_SYSPLUGINS_DIR = defined('SMARTY_SYSPLUGINS_DIR') ? SMARTY_SYSPLUGINS_DIR : self::$SMARTY_DIR . 'sysplugins/';
        if (version_compare(phpversion(), '5.3.0', '>=')) {
            spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
        } else {
            spl_autoload_register(array(__CLASS__, 'autoload'));
        }
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class A class name.
     */
    public static function autoload($class)
    {
        // Request for Smarty or already unknown class
        if (isset(self::$unknown[$class])) {
            return;
        }
        $_class = strtolower($class);
        if (isset(self::$syspluginsClasses[$_class])) {
            $_class = (self::$syspluginsClasses[$_class] === true) ? $_class : self::$syspluginsClasses[$_class];
            $file = self::$SMARTY_SYSPLUGINS_DIR . $_class . '.php';
            require_once $file;
            return;
        } elseif (0 !== strpos($_class, 'smarty_internal_')) {
            if (isset(self::$rootClasses[$class])) {
                $file = self::$SMARTY_DIR . self::$rootClasses[$class];
                require_once $file;
                return;
            }
            self::$unknown[$class] = true;
            return;
        }
        $file = self::$SMARTY_SYSPLUGINS_DIR . $_class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
        self::$unknown[$class] = true;
        return;
    }
}
