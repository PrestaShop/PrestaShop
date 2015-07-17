<?php
/**
 * PEAR, the PHP Extension and Application Repository
 *
 * PEAR class and PEAR_Error class
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   pear
 * @package    PEAR
 * @author     Sterling Hughes <sterling@php.net>
 * @author     Stig Bakken <ssb@php.net>
 * @author     Tomas V.V.Cox <cox@idecnet.com>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2006 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: PEAR.php 6844 2011-06-03 14:46:51Z dMetzger $
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since Release 0.1
 */

/*
 * If the is already loaded (some hosts do that), don't load it again.
 */
// as we need PEAR, PEAR_Exception and PEAR_Error, we have to check their existence separately
if (!class_exists('PEAR', false))
{



	/**#@+
	 * ERROR constants
	 */
	define('PEAR_ERROR_RETURN',     1);
	define('PEAR_ERROR_PRINT',      2);
	define('PEAR_ERROR_TRIGGER',    4);
	define('PEAR_ERROR_DIE',        8);
	define('PEAR_ERROR_CALLBACK',  16);

	/**
	 * WARNING: obsolete
	 * @deprecated
	 */
	define('PEAR_ERROR_EXCEPTION', 32);
	/**#@-*/
	define('PEAR_ZE2', (function_exists('version_compare') &&
											version_compare(zend_version(), "2-dev", "ge")));

	if (substr(PHP_OS, 0, 3) == 'WIN') {
			define('OS_WINDOWS', true);
			define('OS_UNIX',    false);
			define('PEAR_OS',    'Windows');
	} else {
			define('OS_WINDOWS', false);
			define('OS_UNIX',    true);
			define('PEAR_OS',    'Unix'); // blatant assumption
	}

	// instant backwards compatibility
	if (!defined('PATH_SEPARATOR')) {
			if (OS_WINDOWS) {
					define('PATH_SEPARATOR', ';');
			} else {
					define('PATH_SEPARATOR', ':');
			}
	}

	$GLOBALS['_PEAR_default_error_mode']     = PEAR_ERROR_RETURN;
	$GLOBALS['_PEAR_default_error_options']  = E_USER_NOTICE;
	$GLOBALS['_PEAR_destructor_object_list'] = array();
	$GLOBALS['_PEAR_shutdown_funcs']         = array();
	$GLOBALS['_PEAR_error_handler_stack']    = array();

	@ini_set('track_errors', true);

	/**
	 * Base class for other PEAR classes.  Provides rudimentary
	 * emulation of destructors.
	 *
	 * If you want a destructor in your class, inherit PEAR and make a
	 * destructor method called _yourclassname (same name as the
	 * constructor, but with a "_" prefix).  Also, in your constructor you
	 * have to call the PEAR constructor: $this->PEAR();.
	 * The destructor method will be called without parameters.  Note that
	 * at in some SAPI implementations (such as Apache), any output during
	 * the request shutdown (in which destructors are called) seems to be
	 * discarded.  If you need to get any debug information from your
	 * destructor, use error_log(), syslog() or something similar.
	 *
	 * IMPORTANT! To use the emulated destructors you need to create the
	 * objects by reference: $obj =& new PEAR_child;
	 *
	 * @category   pear
	 * @package    PEAR
	 * @author     Stig Bakken <ssb@php.net>
	 * @author     Tomas V.V. Cox <cox@idecnet.com>
	 * @author     Greg Beaver <cellog@php.net>
	 * @copyright  1997-2006 The PHP Group
	 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
	 * @version    Release: 1.4.9
	 * @link       http://pear.php.net/package/PEAR
	 * @see        PEAR_Error
	 * @since      Class available since PHP 4.0.2
	 * @link        http://pear.php.net/manual/en/core.pear.php#core.pear.pear
	 */
	class PEAR
	{
			// {{{ properties

			/**
			 * Whether to enable internal debug messages.
			 *
			 * @var     bool
			 * @access  private
			 */
			var $_debug = false;

			/**
			 * Default error mode for this object.
			 *
			 * @var     int
			 * @access  private
			 */
			var $_default_error_mode = null;

			/**
			 * Default error options used for this object when error mode
			 * is PEAR_ERROR_TRIGGER.
			 *
			 * @var     int
			 * @access  private
			 */
			var $_default_error_options = null;

			/**
			 * Default error handler (callback) for this object, if error mode is
			 * PEAR_ERROR_CALLBACK.
			 *
			 * @var     string
			 * @access  private
			 */
			var $_default_error_handler = '';

			/**
			 * Which class to use for error objects.
			 *
			 * @var     string
			 * @access  private
			 */
			var $_error_class = 'PEAR_Error';

			/**
			 * An array of expected errors.
			 *
			 * @var     array
			 * @access  private
			 */
			var $_expected_errors = array();

			// }}}

			// {{{ constructor

			/**
			 * Constructor.  Registers this object in
			 * $_PEAR_destructor_object_list for destructor emulation if a
			 * destructor object exists.
			 *
			 * @param string $error_class  (optional) which class to use for
			 *        error objects, defaults to PEAR_Error.
			 * @access public
			 * @return void
			 */
			function PEAR($error_class = null)
			{
					$classname = strtolower(get_class($this));
					if ($this->_debug) {
							print "PEAR constructor called, class=$classname\n";
					}
					if ($error_class !== null) {
							$this->_error_class = $error_class;
					}
					while ($classname && strcasecmp($classname, "pear")) {
							$destructor = "_$classname";
							if (method_exists($this, $destructor)) {
									global $_PEAR_destructor_object_list;
									$_PEAR_destructor_object_list[] = &$this;
									if (!isset($GLOBALS['_PEAR_SHUTDOWN_REGISTERED'])) {
											register_shutdown_function("_PEAR_call_destructors");
											$GLOBALS['_PEAR_SHUTDOWN_REGISTERED'] = true;
									}
									break;
							} else {
									$classname = get_parent_class($classname);
							}
					}
			}

			// }}}
			// {{{ destructor

			/**
			 * Destructor (the emulated type of...).  Does nothing right now,
			 * but is included for forward compatibility, so subclass
			 * destructors should always call it.
			 *
			 * See the note in the class desciption about output from
			 * destructors.
			 *
			 * @access public
			 * @return void
			 */
			function _PEAR() {
					if ($this->_debug) {
							printf("PEAR destructor called, class=%s\n", strtolower(get_class($this)));
					}
			}

			// }}}
			// {{{ getStaticProperty()

			/**
			* If you have a class that's mostly/entirely static, and you need static
			* properties, you can use this method to simulate them. Eg. in your method(s)
			* do this: $myVar = &PEAR::getStaticProperty('myclass', 'myVar');
			* You MUST use a reference, or they will not persist!
			*
			* @access public
			* @param  string $class  The calling classname, to prevent clashes
			* @param  string $var    The variable to retrieve.
			* @return mixed   A reference to the variable. If not set it will be
			*                 auto initialised to NULL.
			*/
			function &getStaticProperty($class, $var)
			{
					static $properties;
					return $properties[$class][$var];
			}

			// }}}
			// {{{ registerShutdownFunc()

			/**
			* Use this function to register a shutdown method for static
			* classes.
			*
			* @access public
			* @param  mixed $func  The function name (or array of class/method) to call
			* @param  mixed $args  The arguments to pass to the function
			* @return void
			*/
			function registerShutdownFunc($func, $args = array())
			{
					// if we are called statically, there is a potential
					// that no shutdown func is registered.  Bug #6445
					if (!isset($GLOBALS['_PEAR_SHUTDOWN_REGISTERED'])) {
							register_shutdown_function("_PEAR_call_destructors");
							$GLOBALS['_PEAR_SHUTDOWN_REGISTERED'] = true;
					}
					$GLOBALS['_PEAR_shutdown_funcs'][] = array($func, $args);
			}

			// }}}
			// {{{ isError()

			/**
			 * Tell whether a value is a PEAR error.
			 *
			 * @param   mixed $data   the value to test
			 * @param   int   $code   if $data is an error object, return true
			 *                        only if $code is a string and
			 *                        $obj->getMessage() == $code or
			 *                        $code is an integer and $obj->getCode() == $code
			 * @access  public
			 * @return  bool    true if parameter is an error
			 */
			function isError($data, $code = null)
			{
					if (is_a($data, 'PEAR_Error')) {
							if (is_null($code)) {
									return true;
							} elseif (is_string($code)) {
									return $data->getMessage() == $code;
							} else {
									return $data->getCode() == $code;
							}
					}
					return false;
			}

			// }}}
			// {{{ setErrorHandling()

			/**
			 * Sets how errors generated by this object should be handled.
			 * Can be invoked both in objects and statically.  If called
			 * statically, setErrorHandling sets the default behaviour for all
			 * PEAR objects.  If called in an object, setErrorHandling sets
			 * the default behaviour for that object.
			 *
			 * @param int $mode
			 *        One of PEAR_ERROR_RETURN, PEAR_ERROR_PRINT,
			 *        PEAR_ERROR_TRIGGER, PEAR_ERROR_DIE,
			 *        PEAR_ERROR_CALLBACK or PEAR_ERROR_EXCEPTION.
			 *
			 * @param mixed $options
			 *        When $mode is PEAR_ERROR_TRIGGER, this is the error level (one
			 *        of E_USER_NOTICE, E_USER_WARNING or E_USER_ERROR).
			 *
			 *        When $mode is PEAR_ERROR_CALLBACK, this parameter is expected
			 *        to be the callback function or method.  A callback
			 *        function is a string with the name of the function, a
			 *        callback method is an array of two elements: the element
			 *        at index 0 is the object, and the element at index 1 is
			 *        the name of the method to call in the object.
			 *
			 *        When $mode is PEAR_ERROR_PRINT or PEAR_ERROR_DIE, this is
			 *        a printf format string used when printing the error
			 *        message.
			 *
			 * @access public
			 * @return void
			 * @see PEAR_ERROR_RETURN
			 * @see PEAR_ERROR_PRINT
			 * @see PEAR_ERROR_TRIGGER
			 * @see PEAR_ERROR_DIE
			 * @see PEAR_ERROR_CALLBACK
			 * @see PEAR_ERROR_EXCEPTION
			 *
			 * @since PHP 4.0.5
			 */

			function setErrorHandling($mode = null, $options = null)
			{
					if (isset($this) && is_a($this, 'PEAR')) {
							$setmode     = &$this->_default_error_mode;
							$setoptions  = &$this->_default_error_options;
					} else {
							$setmode     = &$GLOBALS['_PEAR_default_error_mode'];
							$setoptions  = &$GLOBALS['_PEAR_default_error_options'];
					}

					switch ($mode) {
							case PEAR_ERROR_EXCEPTION:
							case PEAR_ERROR_RETURN:
							case PEAR_ERROR_PRINT:
							case PEAR_ERROR_TRIGGER:
							case PEAR_ERROR_DIE:
							case null:
									$setmode = $mode;
									$setoptions = $options;
									break;

							case PEAR_ERROR_CALLBACK:
									$setmode = $mode;
									// class/object method callback
									if (is_callable($options)) {
											$setoptions = $options;
									} else {
											trigger_error("invalid error callback", E_USER_WARNING);
									}
									break;

							default:
									trigger_error("invalid error mode", E_USER_WARNING);
									break;
					}
			}

			// }}}
			// {{{ expectError()

			/**
			 * This method is used to tell which errors you expect to get.
			 * Expected errors are always returned with error mode
			 * PEAR_ERROR_RETURN.  Expected error codes are stored in a stack,
			 * and this method pushes a new element onto it.  The list of
			 * expected errors are in effect until they are popped off the
			 * stack with the popExpect() method.
			 *
			 * Note that this method can not be called statically
			 *
			 * @param mixed $code a single error code or an array of error codes to expect
			 *
			 * @return int     the new depth of the "expected errors" stack
			 * @access public
			 */
			function expectError($code = '*')
			{
					if (is_array($code)) {
							array_push($this->_expected_errors, $code);
					} else {
							array_push($this->_expected_errors, array($code));
					}
					return sizeof($this->_expected_errors);
			}

			// }}}
			// {{{ popExpect()

			/**
			 * This method pops one element off the expected error codes
			 * stack.
			 *
			 * @return array   the list of error codes that were popped
			 */
			function popExpect()
			{
					return array_pop($this->_expected_errors);
			}

			// }}}
			// {{{ _checkDelExpect()

			/**
			 * This method checks unsets an error code if available
			 *
			 * @param mixed error code
			 * @return bool true if the error code was unset, false otherwise
			 * @access private
			 * @since PHP 4.3.0
			 */
			function _checkDelExpect($error_code)
			{
					$deleted = false;

					foreach ($this->_expected_errors as $key => $error_array) {
							if (in_array($error_code, $error_array)) {
									unset($this->_expected_errors[$key][array_search($error_code, $error_array)]);
									$deleted = true;
							}

							// clean up empty arrays
							if (0 == sizeof($this->_expected_errors[$key])) {
									unset($this->_expected_errors[$key]);
							}
					}
					return $deleted;
			}

			// }}}
			// {{{ delExpect()

			/**
			 * This method deletes all occurences of the specified element from
			 * the expected error codes stack.
			 *
			 * @param  mixed $error_code error code that should be deleted
			 * @return mixed list of error codes that were deleted or error
			 * @access public
			 * @since PHP 4.3.0
			 */
			function delExpect($error_code)
			{
					$deleted = false;

					if ((is_array($error_code) && (0 != sizeof($error_code)))) {
							// $error_code is a non-empty array here;
							// we walk through it trying to unset all
							// values
							foreach($error_code as $key => $error) {
									if ($this->_checkDelExpect($error)) {
											$deleted =  true;
									} else {
											$deleted = false;
									}
							}
							return $deleted ? true : PEAR::raiseError("The expected error you submitted does not exist"); // IMPROVE ME
					} elseif (!empty($error_code)) {
							// $error_code comes alone, trying to unset it
							if ($this->_checkDelExpect($error_code)) {
									return true;
							} else {
									return PEAR::raiseError("The expected error you submitted does not exist"); // IMPROVE ME
							}
					} else {
							// $error_code is empty
							return PEAR::raiseError("The expected error you submitted is empty"); // IMPROVE ME
					}
			}

			// }}}
			// {{{ raiseError()

			/**
			 * This method is a wrapper that returns an instance of the
			 * configured error class with this object's default error
			 * handling applied.  If the $mode and $options parameters are not
			 * specified, the object's defaults are used.
			 *
			 * @param mixed $message a text error message or a PEAR error object
			 *
			 * @param int $code      a numeric error code (it is up to your class
			 *                  to define these if you want to use codes)
			 *
			 * @param int $mode      One of PEAR_ERROR_RETURN, PEAR_ERROR_PRINT,
			 *                  PEAR_ERROR_TRIGGER, PEAR_ERROR_DIE,
			 *                  PEAR_ERROR_CALLBACK, PEAR_ERROR_EXCEPTION.
			 *
			 * @param mixed $options If $mode is PEAR_ERROR_TRIGGER, this parameter
			 *                  specifies the PHP-internal error level (one of
			 *                  E_USER_NOTICE, E_USER_WARNING or E_USER_ERROR).
			 *                  If $mode is PEAR_ERROR_CALLBACK, this
			 *                  parameter specifies the callback function or
			 *                  method.  In other error modes this parameter
			 *                  is ignored.
			 *
			 * @param string $userinfo If you need to pass along for example debug
			 *                  information, this parameter is meant for that.
			 *
			 * @param string $error_class The returned error object will be
			 *                  instantiated from this class, if specified.
			 *
			 * @param bool $skipmsg If true, raiseError will only pass error codes,
			 *                  the error message parameter will be dropped.
			 *
			 * @access public
			 * @return object   a PEAR error object
			 * @see PEAR::setErrorHandling
			 * @since PHP 4.0.5
			 */
			function &raiseError($message = null,
													 $code = null,
													 $mode = null,
													 $options = null,
													 $userinfo = null,
													 $error_class = null,
													 $skipmsg = false)
			{
					// The error is yet a PEAR error object
					if (is_object($message)) {
							$code        = $message->getCode();
							$userinfo    = $message->getUserInfo();
							$error_class = $message->getType();
							$message->error_message_prefix = '';
							$message     = $message->getMessage();
					}

					if (isset($this) && isset($this->_expected_errors) && sizeof($this->_expected_errors) > 0 && sizeof($exp = end($this->_expected_errors))) {
							if ($exp[0] == "*" ||
									(is_int(reset($exp)) && in_array($code, $exp)) ||
									(is_string(reset($exp)) && in_array($message, $exp))) {
									$mode = PEAR_ERROR_RETURN;
							}
					}
					// No mode given, try global ones
					if ($mode === null) {
							// Class error handler
							if (isset($this) && isset($this->_default_error_mode)) {
									$mode    = $this->_default_error_mode;
									$options = $this->_default_error_options;
							// Global error handler
							} elseif (isset($GLOBALS['_PEAR_default_error_mode'])) {
									$mode    = $GLOBALS['_PEAR_default_error_mode'];
									$options = $GLOBALS['_PEAR_default_error_options'];
							}
					}

					if ($error_class !== null) {
							$ec = $error_class;
					} elseif (isset($this) && isset($this->_error_class)) {
							$ec = $this->_error_class;
					} else {
							$ec = 'PEAR_Error';
					}
					if ($skipmsg) {
							$a = new $ec($code, $mode, $options, $userinfo);
							return $a;
					} else {
							$a = new $ec($message, $code, $mode, $options, $userinfo);
							return $a;
					}
			}

			// }}}
			// {{{ throwError()

			/**
			 * Simpler form of raiseError with fewer options.  In most cases
			 * message, code and userinfo are enough.
			 *
			 * @param string $message
			 *
			 */
			function &throwError($message = null,
													 $code = null,
													 $userinfo = null)
			{
					if (isset($this) && is_a($this, 'PEAR')) {
							$a = &$this->raiseError($message, $code, null, null, $userinfo);
							return $a;
					} else {
							$a = &PEAR::raiseError($message, $code, null, null, $userinfo);
							return $a;
					}
			}

			// }}}
			function staticPushErrorHandling($mode, $options = null)
			{
					$stack = &$GLOBALS['_PEAR_error_handler_stack'];
					$def_mode    = &$GLOBALS['_PEAR_default_error_mode'];
					$def_options = &$GLOBALS['_PEAR_default_error_options'];
					$stack[] = array($def_mode, $def_options);
					switch ($mode) {
							case PEAR_ERROR_EXCEPTION:
							case PEAR_ERROR_RETURN:
							case PEAR_ERROR_PRINT:
							case PEAR_ERROR_TRIGGER:
							case PEAR_ERROR_DIE:
							case null:
									$def_mode = $mode;
									$def_options = $options;
									break;

							case PEAR_ERROR_CALLBACK:
									$def_mode = $mode;
									// class/object method callback
									if (is_callable($options)) {
											$def_options = $options;
									} else {
											trigger_error("invalid error callback", E_USER_WARNING);
									}
									break;

							default:
									trigger_error("invalid error mode", E_USER_WARNING);
									break;
					}
					$stack[] = array($mode, $options);
					return true;
			}

			function staticPopErrorHandling()
			{
					$stack = &$GLOBALS['_PEAR_error_handler_stack'];
					$setmode     = &$GLOBALS['_PEAR_default_error_mode'];
					$setoptions  = &$GLOBALS['_PEAR_default_error_options'];
					array_pop($stack);
					list($mode, $options) = $stack[sizeof($stack) - 1];
					array_pop($stack);
					switch ($mode) {
							case PEAR_ERROR_EXCEPTION:
							case PEAR_ERROR_RETURN:
							case PEAR_ERROR_PRINT:
							case PEAR_ERROR_TRIGGER:
							case PEAR_ERROR_DIE:
							case null:
									$setmode = $mode;
									$setoptions = $options;
									break;

							case PEAR_ERROR_CALLBACK:
									$setmode = $mode;
									// class/object method callback
									if (is_callable($options)) {
											$setoptions = $options;
									} else {
											trigger_error("invalid error callback", E_USER_WARNING);
									}
									break;

							default:
									trigger_error("invalid error mode", E_USER_WARNING);
									break;
					}
					return true;
			}

			// {{{ pushErrorHandling()

			/**
			 * Push a new error handler on top of the error handler options stack. With this
			 * you can easily override the actual error handler for some code and restore
			 * it later with popErrorHandling.
			 *
			 * @param mixed $mode (same as setErrorHandling)
			 * @param mixed $options (same as setErrorHandling)
			 *
			 * @return bool Always true
			 *
			 * @see PEAR::setErrorHandling
			 */
			function pushErrorHandling($mode, $options = null)
			{
					$stack = &$GLOBALS['_PEAR_error_handler_stack'];
					if (isset($this) && is_a($this, 'PEAR')) {
							$def_mode    = &$this->_default_error_mode;
							$def_options = &$this->_default_error_options;
					} else {
							$def_mode    = &$GLOBALS['_PEAR_default_error_mode'];
							$def_options = &$GLOBALS['_PEAR_default_error_options'];
					}
					$stack[] = array($def_mode, $def_options);

					if (isset($this) && is_a($this, 'PEAR')) {
							$this->setErrorHandling($mode, $options);
					} else {
							PEAR::setErrorHandling($mode, $options);
					}
					$stack[] = array($mode, $options);
					return true;
			}

			// }}}
			// {{{ popErrorHandling()

			/**
			* Pop the last error handler used
			*
			* @return bool Always true
			*
			* @see PEAR::pushErrorHandling
			*/
			function popErrorHandling()
			{
					$stack = &$GLOBALS['_PEAR_error_handler_stack'];
					array_pop($stack);
					list($mode, $options) = $stack[sizeof($stack) - 1];
					array_pop($stack);
					if (isset($this) && is_a($this, 'PEAR')) {
							$this->setErrorHandling($mode, $options);
					} else {
							PEAR::setErrorHandling($mode, $options);
					}
					return true;
			}

			// }}}
			// {{{ loadExtension()

			/**
			* OS independant PHP extension load. Remember to take care
			* on the correct extension name for case sensitive OSes.
			*
			* @param string $ext The extension name
			* @return bool Success or not on the dl() call
			*/
			function loadExtension($ext)
			{
					if (!extension_loaded($ext)) {
							// if either returns true dl() will produce a FATAL error, stop that
							if ((ini_get('enable_dl') != 1) || (ini_get('safe_mode') == 1)) {
									return false;
							}
							if (OS_WINDOWS) {
									$suffix = '.dll';
							} elseif (PHP_OS == 'HP-UX') {
									$suffix = '.sl';
							} elseif (PHP_OS == 'AIX') {
									$suffix = '.a';
							} elseif (PHP_OS == 'OSX') {
									$suffix = '.bundle';
							} else {
									$suffix = '.so';
							}
							return @dl('php_'.$ext.$suffix) || @dl($ext.$suffix);
					}
					return true;
			}

			// }}}
	}

	// {{{ _PEAR_call_destructors()

	function _PEAR_call_destructors()
	{
			global $_PEAR_destructor_object_list;
			if (is_array($_PEAR_destructor_object_list) &&
					sizeof($_PEAR_destructor_object_list))
			{
					reset($_PEAR_destructor_object_list);
					if (@PEAR::getStaticProperty('PEAR', 'destructlifo')) {
							$_PEAR_destructor_object_list = array_reverse($_PEAR_destructor_object_list);
					}
					while (list($k, $objref) = each($_PEAR_destructor_object_list)) {
							$classname = get_class($objref);
							while ($classname) {
									$destructor = "_$classname";
									if (method_exists($objref, $destructor)) {
											$objref->$destructor();
											break;
									} else {
											$classname = get_parent_class($classname);
									}
							}
					}
					// Empty the object list to ensure that destructors are
					// not called more than once.
					$_PEAR_destructor_object_list = array();
			}

			// Now call the shutdown functions
			if (is_array($GLOBALS['_PEAR_shutdown_funcs']) and !empty($GLOBALS['_PEAR_shutdown_funcs'])) {
					foreach ($GLOBALS['_PEAR_shutdown_funcs'] as $value) {
							call_user_func_array($value[0], $value[1]);
					}
			}
	}
}

// }}}
/**
 * Standard PEAR error class for PHP 4
 *
 * This class is supserseded by {@link PEAR_Exception} in PHP 5
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Tomas V.V. Cox <cox@idecnet.com>
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  1997-2006 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 1.4.9
 * @link       http://pear.php.net/manual/en/core.pear.pear-error.php
 * @see        PEAR::raiseError(), PEAR::throwError()
 * @since      Class available since PHP 4.0.2
 */
if (!class_exists('PEAR_Error', false))
{
	class PEAR_Error
	{
			// {{{ properties

			var $error_message_prefix = '';
			var $mode                 = PEAR_ERROR_RETURN;
			var $level                = E_USER_NOTICE;
			var $code                 = -1;
			var $message              = '';
			var $userinfo             = '';
			var $backtrace            = null;

			// }}}
			// {{{ constructor

			/**
			 * PEAR_Error constructor
			 *
			 * @param string $message  message
			 *
			 * @param int $code     (optional) error code
			 *
			 * @param int $mode     (optional) error mode, one of: PEAR_ERROR_RETURN,
			 * PEAR_ERROR_PRINT, PEAR_ERROR_DIE, PEAR_ERROR_TRIGGER,
			 * PEAR_ERROR_CALLBACK or PEAR_ERROR_EXCEPTION
			 *
			 * @param mixed $options   (optional) error level, _OR_ in the case of
			 * PEAR_ERROR_CALLBACK, the callback function or object/method
			 * tuple.
			 *
			 * @param string $userinfo (optional) additional user/debug info
			 *
			 * @access public
			 *
			 */
			function PEAR_Error($message = 'unknown error', $code = null,
													$mode = null, $options = null, $userinfo = null)
			{
					if ($mode === null) {
							$mode = PEAR_ERROR_RETURN;
					}
					$this->message   = $message;
					$this->code      = $code;
					$this->mode      = $mode;
					$this->userinfo  = $userinfo;
					if (function_exists("debug_backtrace")) {
							if (@!PEAR::getStaticProperty('PEAR_Error', 'skiptrace')) {
									$this->backtrace = debug_backtrace();
							}
					}
					if ($mode & PEAR_ERROR_CALLBACK) {
							$this->level = E_USER_NOTICE;
							$this->callback = $options;
					} else {
							if ($options === null) {
									$options = E_USER_NOTICE;
							}
							$this->level = $options;
							$this->callback = null;
					}
					if ($this->mode & PEAR_ERROR_PRINT) {
							if (is_null($options) || is_int($options)) {
									$format = "%s";
							} else {
									$format = $options;
							}
							printf($format, $this->getMessage());
					}
					if ($this->mode & PEAR_ERROR_TRIGGER) {
							trigger_error($this->getMessage(), $this->level);
					}
					if ($this->mode & PEAR_ERROR_DIE) {
							$msg = $this->getMessage();
							if (is_null($options) || is_int($options)) {
									$format = "%s";
									if (substr($msg, -1) != "\n") {
											$msg .= "\n";
									}
							} else {
									$format = $options;
							}
							die(sprintf($format, $msg));
					}
					if ($this->mode & PEAR_ERROR_CALLBACK) {
							if (is_callable($this->callback)) {
									call_user_func($this->callback, $this);
							}
					}
					if ($this->mode & PEAR_ERROR_EXCEPTION) {
							trigger_error("PEAR_ERROR_EXCEPTION is obsolete, use class PEAR_Exception for exceptions", E_USER_WARNING);
							eval('$e = new Exception($this->message, $this->code);throw($e);');
					}
			}

			// }}}
			// {{{ getMode()

			/**
			 * Get the error mode from an error object.
			 *
			 * @return int error mode
			 * @access public
			 */
			function getMode() {
					return $this->mode;
			}

			// }}}
			// {{{ getCallback()

			/**
			 * Get the callback function/method from an error object.
			 *
			 * @return mixed callback function or object/method array
			 * @access public
			 */
			function getCallback() {
					return $this->callback;
			}

			// }}}
			// {{{ getMessage()


			/**
			 * Get the error message from an error object.
			 *
			 * @return  string  full error message
			 * @access public
			 */
			function getMessage()
			{
					return ($this->error_message_prefix . $this->message);
			}


			// }}}
			// {{{ getCode()

			/**
			 * Get error code from an error object
			 *
			 * @return int error code
			 * @access public
			 */
			 function getCode()
			 {
					return $this->code;
			 }

			// }}}
			// {{{ getType()

			/**
			 * Get the name of this error/exception.
			 *
			 * @return string error/exception name (type)
			 * @access public
			 */
			function getType()
			{
					return get_class($this);
			}

			// }}}
			// {{{ getUserInfo()

			/**
			 * Get additional user-supplied information.
			 *
			 * @return string user-supplied information
			 * @access public
			 */
			function getUserInfo()
			{
					return $this->userinfo;
			}

			// }}}
			// {{{ getDebugInfo()

			/**
			 * Get additional debug information supplied by the application.
			 *
			 * @return string debug information
			 * @access public
			 */
			function getDebugInfo()
			{
					return $this->getUserInfo();
			}

			// }}}
			// {{{ getBacktrace()

			/**
			 * Get the call backtrace from where the error was generated.
			 * Supported with PHP 4.3.0 or newer.
			 *
			 * @param int $frame (optional) what frame to fetch
			 * @return array Backtrace, or NULL if not available.
			 * @access public
			 */
			function getBacktrace($frame = null)
			{
					if (defined('PEAR_IGNORE_BACKTRACE')) {
							return null;
					}
					if ($frame === null) {
							return $this->backtrace;
					}
					return $this->backtrace[$frame];
			}

			// }}}
			// {{{ addUserInfo()

			function addUserInfo($info)
			{
					if (empty($this->userinfo)) {
							$this->userinfo = $info;
					} else {
							$this->userinfo .= " ** $info";
					}
			}

			// }}}
			// {{{ toString()

			/**
			 * Make a string representation of this object.
			 *
			 * @return string a string with an object summary
			 * @access public
			 */
			function toString() {
					$modes = array();
					$levels = array(E_USER_NOTICE  => 'notice',
													E_USER_WARNING => 'warning',
													E_USER_ERROR   => 'error');
					if ($this->mode & PEAR_ERROR_CALLBACK) {
							if (is_array($this->callback)) {
									$callback = (is_object($this->callback[0]) ?
											strtolower(get_class($this->callback[0])) :
											$this->callback[0]) . '::' .
											$this->callback[1];
							} else {
									$callback = $this->callback;
							}
							return sprintf('[%s: message="%s" code=%d mode=callback '.
														 'callback=%s prefix="%s" info="%s"]',
														 strtolower(get_class($this)), $this->message, $this->code,
														 $callback, $this->error_message_prefix,
														 $this->userinfo);
					}
					if ($this->mode & PEAR_ERROR_PRINT) {
							$modes[] = 'print';
					}
					if ($this->mode & PEAR_ERROR_TRIGGER) {
							$modes[] = 'trigger';
					}
					if ($this->mode & PEAR_ERROR_DIE) {
							$modes[] = 'die';
					}
					if ($this->mode & PEAR_ERROR_RETURN) {
							$modes[] = 'return';
					}
					return sprintf('[%s: message="%s" code=%d mode=%s level=%s '.
												 'prefix="%s" info="%s"]',
												 strtolower(get_class($this)), $this->message, $this->code,
												 implode("|", $modes), $levels[$this->level],
												 $this->error_message_prefix,
												 $this->userinfo);
			}

			// }}}
	}
}
if (!class_exists('PEAR_Exception', false))
{
	class PEAR_Exception extends Exception
	{
			const OBSERVER_PRINT = -2;
			const OBSERVER_TRIGGER = -4;
			const OBSERVER_DIE = -8;
			protected $cause;
			private static $_observers = array();
			private static $_uniqueid = 0;
			private $_trace;

			/**
			 * Supported signatures:
			 *  - PEAR_Exception(string $message);
			 *  - PEAR_Exception(string $message, int $code);
			 *  - PEAR_Exception(string $message, Exception $cause);
			 *  - PEAR_Exception(string $message, Exception $cause, int $code);
			 *  - PEAR_Exception(string $message, PEAR_Error $cause);
			 *  - PEAR_Exception(string $message, PEAR_Error $cause, int $code);
			 *  - PEAR_Exception(string $message, array $causes);
			 *  - PEAR_Exception(string $message, array $causes, int $code);
			 * @param string exception message
			 * @param int|Exception|PEAR_Error|array|null exception cause
			 * @param int|null exception code or null
			 */
			public function __construct($message, $p2 = null, $p3 = null)
			{
					if (is_int($p2)) {
							$code = $p2;
							$this->cause = null;
					} elseif (is_object($p2) || is_array($p2)) {
							// using is_object allows both Exception and PEAR_Error
							if (is_object($p2) && !($p2 instanceof Exception)) {
									if (!class_exists('PEAR_Error') || !($p2 instanceof PEAR_Error)) {
											throw new PEAR_Exception('exception cause must be Exception, ' .
													'array, or PEAR_Error');
									}
							}
							$code = $p3;
							if (is_array($p2) && isset($p2['message'])) {
									// fix potential problem of passing in a single warning
									$p2 = array($p2);
							}
							$this->cause = $p2;
					} else {
							$code = null;
							$this->cause = null;
					}
					parent::__construct($message, $code);
					$this->signal();
			}

			/**
			 * @param mixed $callback  - A valid php callback, see php func is_callable()
			 *                         - A PEAR_Exception::OBSERVER_* constant
			 *                         - An array(const PEAR_Exception::OBSERVER_*,
			 *                           mixed $options)
			 * @param string $label    The name of the observer. Use this if you want
			 *                         to remove it later with removeObserver()
			 */
			public static function addObserver($callback, $label = 'default')
			{
					self::$_observers[$label] = $callback;
			}

			public static function removeObserver($label = 'default')
			{
					unset(self::$_observers[$label]);
			}

			/**
			 * @return int unique identifier for an observer
			 */
			public static function getUniqueId()
			{
					return self::$_uniqueid++;
			}

			private function signal()
			{
					foreach (self::$_observers as $func) {
							if (is_callable($func)) {
									call_user_func($func, $this);
									continue;
							}
							settype($func, 'array');
							switch ($func[0]) {
									case self::OBSERVER_PRINT :
											$f = (isset($func[1])) ? $func[1] : '%s';
											printf($f, $this->getMessage());
											break;
									case self::OBSERVER_TRIGGER :
											$f = (isset($func[1])) ? $func[1] : E_USER_NOTICE;
											trigger_error($this->getMessage(), $f);
											break;
									case self::OBSERVER_DIE :
											$f = (isset($func[1])) ? $func[1] : '%s';
											die(printf($f, $this->getMessage()));
											break;
									default:
											trigger_error('invalid observer type', E_USER_WARNING);
							}
					}
			}

			/**
			 * Return specific error information that can be used for more detailed
			 * error messages or translation.
			 *
			 * This method may be overridden in child exception classes in order
			 * to add functionality not present in PEAR_Exception and is a placeholder
			 * to define API
			 *
			 * The returned array must be an associative array of parameter => value like so:
			 * <pre>
			 * array('name' => $name, 'context' => array(...))
			 * </pre>
			 * @return array
			 */
			public function getErrorData()
			{
					return array();
			}

			/**
			 * Returns the exception that caused this exception to be thrown
			 * @access public
			 * @return Exception|array The context of the exception
			 */
			public function getCause()
			{
					return $this->cause;
			}

			/**
			 * Function must be public to call on caused exceptions
			 * @param array
			 */
			public function getCauseMessage(&$causes)
			{
					$trace = $this->getTraceSafe();
					$cause = array('class'   => get_class($this),
												 'message' => $this->message,
												 'file' => 'unknown',
												 'line' => 'unknown');
					if (isset($trace[0])) {
							if (isset($trace[0]['file'])) {
									$cause['file'] = $trace[0]['file'];
									$cause['line'] = $trace[0]['line'];
							}
					}
					$causes[] = $cause;
					if ($this->cause instanceof PEAR_Exception) {
							$this->cause->getCauseMessage($causes);
					} elseif ($this->cause instanceof Exception) {
							$causes[] = array('class'   => get_class($this->cause),
																'message' => $this->cause->getMessage(),
																'file' => $this->cause->getFile(),
																'line' => $this->cause->getLine());
					} elseif (class_exists('PEAR_Error') && $this->cause instanceof PEAR_Error) {
							$causes[] = array('class' => get_class($this->cause),
																'message' => $this->cause->getMessage(),
																'file' => 'unknown',
																'line' => 'unknown');
					} elseif (is_array($this->cause)) {
							foreach ($this->cause as $cause) {
									if ($cause instanceof PEAR_Exception) {
											$cause->getCauseMessage($causes);
									} elseif ($cause instanceof Exception) {
											$causes[] = array('class'   => get_class($cause),
																		 'message' => $cause->getMessage(),
																		 'file' => $cause->getFile(),
																		 'line' => $cause->getLine());
									} elseif (class_exists('PEAR_Error') && $cause instanceof PEAR_Error) {
											$causes[] = array('class' => get_class($cause),
																				'message' => $cause->getMessage(),
																				'file' => 'unknown',
																				'line' => 'unknown');
									} elseif (is_array($cause) && isset($cause['message'])) {
											// PEAR_ErrorStack warning
											$causes[] = array(
													'class' => $cause['package'],
													'message' => $cause['message'],
													'file' => isset($cause['context']['file']) ?
																							$cause['context']['file'] :
																							'unknown',
													'line' => isset($cause['context']['line']) ?
																							$cause['context']['line'] :
																							'unknown',
											);
									}
							}
					}
			}

			public function getTraceSafe()
			{   
					if (!isset($this->_trace)) {
							$this->_trace = $this->getTrace();
							if (empty($this->_trace)) {
									$backtrace = debug_backtrace();
									$this->_trace = array($backtrace[sizeof($backtrace)-1]);
							}
					}
					return $this->_trace;
			}

			public function getErrorClass()
			{
					$trace = $this->getTraceSafe();
					return $trace[0]['class'];
			}

			public function getErrorMethod()
			{
					$trace = $this->getTraceSafe();
					return $trace[0]['function'];
			}

			public function __toString()
			{
					if (isset($_SERVER['REQUEST_URI'])) {
							return $this->toHtml();
					}
					return $this->toText();
			}

			public function toHtml()
			{
					$trace = $this->getTraceSafe();
					$causes = array();
					$this->getCauseMessage($causes);
					$html =  '<table border="1" cellspacing="0">' . "\n";
					foreach ($causes as $i => $cause) {
							$html .= '<tr><td colspan="3" bgcolor="#ff9999">'
								 . str_repeat('-', $i) . ' <b>' . $cause['class'] . '</b>: '
								 . htmlspecialchars($cause['message']) . ' in <b>' . $cause['file'] . '</b> '
								 . 'on line <b>' . $cause['line'] . '</b>'
								 . "</td></tr>\n";
					}
					$html .= '<tr><td colspan="3" bgcolor="#aaaaaa" align="center"><b>Exception trace</b></td></tr>' . "\n"
								 . '<tr><td align="center" bgcolor="#cccccc" width="20"><b>#</b></td>'
								 . '<td align="center" bgcolor="#cccccc"><b>Function</b></td>'
								 . '<td align="center" bgcolor="#cccccc"><b>Location</b></td></tr>' . "\n";

					foreach ($trace as $k => $v) {
							$html .= '<tr><td align="center">' . $k . '</td>'
										 . '<td>';
							if (!empty($v['class'])) {
									$html .= $v['class'] . $v['type'];
							}
							$html .= $v['function'];
							$args = array();
							if (!empty($v['args'])) {
									foreach ($v['args'] as $arg) {
											if (is_null($arg)) $args[] = 'null';
											elseif (is_array($arg)) $args[] = 'Array';
											elseif (is_object($arg)) $args[] = 'Object('.get_class($arg).')';
											elseif (is_bool($arg)) $args[] = $arg ? 'true' : 'false';
											elseif (is_int($arg) || is_double($arg)) $args[] = $arg;
											else {
													$arg = (string)$arg;
													$str = htmlspecialchars(substr($arg, 0, 16));
													if (strlen($arg) > 16) $str .= '&hellip;';
													$args[] = "'" . $str . "'";
											}
									}
							}
							$html .= '(' . implode(', ',$args) . ')'
										 . '</td>'
										 . '<td>' . (isset($v['file']) ? $v['file'] : 'unknown')
										 . ':' . (isset($v['line']) ? $v['line'] : 'unknown')
										 . '</td></tr>' . "\n";
					}
					$html .= '<tr><td align="center">' . ($k+1) . '</td>'
								 . '<td>{main}</td>'
								 . '<td>&nbsp;</td></tr>' . "\n"
								 . '</table>';
					return $html;
			}

			public function toText()
			{
					$causes = array();
					$this->getCauseMessage($causes);
					$causeMsg = '';
					foreach ($causes as $i => $cause) {
							$causeMsg .= str_repeat(' ', $i) . $cause['class'] . ': '
										 . $cause['message'] . ' in ' . $cause['file']
										 . ' on line ' . $cause['line'] . "\n";
					}
					return $causeMsg . $this->getTraceAsString();
			}
	}
}
/*
 * Local Variables:
 * mode: php
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */
?>
