<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
/**
 * Used to delay loading of external classes with smarty->register_plugin.
 */
class SmartyLazyRegister
{
    protected $registry = [];
    protected static $instances = [];

    /**
     * Register a function or method to be dynamically called later.
     *
     * @param string|array $params function name or array(object name, method name)
     */
    public function register($params)
    {
        if (is_array($params)) {
            $this->registry[$params[1]] = $params;
        } else {
            $this->registry[$params] = $params;
        }
    }

    public function isRegistered($params)
    {
        if (is_array($params)) {
            $params = $params[1];
        }

        return isset($this->registry[$params]);
    }

    /**
     * Dynamically call static function or method.
     *
     * @param string $name function name
     * @param mixed $arguments function argument
     *
     * @return mixed function return
     */
    public function __call($name, $arguments)
    {
        $item = $this->registry[$name];

        // case 1: call to static method
        // case 2 : call to static function
        $args = [];

        foreach ($arguments as $a => $argument) {
            if ($a == 0) {
                $args[] = $arguments[0];
            } else {
                $args[] = &$arguments[$a];
            }
        }

        return call_user_func_array($item, $args);
    }

    public static function getInstance($smarty)
    {
        $hash = spl_object_hash($smarty);

        if (!isset(self::$instances[$hash])) {
            self::$instances[$hash] = new self();
        }

        return self::$instances[$hash];
    }
}
