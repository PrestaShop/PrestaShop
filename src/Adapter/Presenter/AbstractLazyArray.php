<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter;

use ArrayAccess;
use ArrayIterator;
use ArrayObject;
use Countable;
use Doctrine\Common\Util\Inflector;
use Iterator;
use JsonSerializable;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

/**
 * This class is useful to provide the same behaviour than an array, but which load the result of each key on demand
 * (LazyLoading).
 *
 * Example:
 *
 * If your want to define the ['addresses'] array access in your lazyArray object, just define the public method
 * getAddresses() and add the annotation arrayAccess to it. e.g:
 *
 *     @arrayAccess
 *
 *     @return array
 *
 *     public function getAddresses()
 *
 * The method name should always be the index name converted to camelCase and prefixed with get. e.g:
 *
 * ['add_to_cart'] => getAddToCart()
 *
 * You can also add an array with already defined key to the lazyArray be calling the appendArray function.
 * e.g.: you have a $product array containing $product['id'] = 10; $product['url'] = 'foo';
 *       If you call ->appendArray($product) on the lazyArray, it will define the key ['id'] and ['url'] as well
 *       for the lazyArray.
 * Note if the key already exists as a method, it will be skip. In our example, if getUrl() is defined with the
 * annotation @arrayAccess, the $product['url'] = 'foo'; will be ignored
 */
abstract class AbstractLazyArray implements Iterator, ArrayAccess, Countable, JsonSerializable
{
    /**
     * @var ArrayObject
     */
    private $arrayAccessList;

    /**
     * @var ArrayIterator
     */
    private $arrayAccessIterator;

    /**
     * @var array
     */
    private $methodCacheResults = array();

    /**
     * AbstractLazyArray constructor.
     *
     * @throws ReflectionException
     */
    public function __construct()
    {
        $this->arrayAccessList = new ArrayObject();
        $reflectionClass = new ReflectionClass(get_class($this));
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $methodDoc = $method->getDocComment();
            if (strpos($methodDoc, '@arrayAccess') !== false) {
                $this->arrayAccessList[$this->convertMethodNameToIndex($method->getName())] =
                    array(
                        'type' => 'method',
                        'value' => $method->getName(),
                    );
            }
        }
        $this->arrayAccessIterator = $this->arrayAccessList->getIterator();
    }

    /**
     * Make the lazyArray serializable like an array.
     *
     * @return array
     *
     * @throws RuntimeException
     */
    public function jsonSerialize()
    {
        $arrayResult = array();
        foreach ($this->arrayAccessList as $key => $value) {
            $arrayResult[$key] = $this->offsetGet($key);
        }

        return $arrayResult;
    }

    /**
     * Set array key and values from $array into the LazyArray.
     *
     * @param array $array
     */
    public function appendArray($array)
    {
        foreach ($array as $key => $value) {
            // do not override any existing method
            if (!$this->arrayAccessList->offsetExists($key)) {
                $this->arrayAccessList->offsetSet(
                    $key,
                    array(
                        'type' => 'variable',
                        'value' => $value,
                    )
                );
            }
        }
    }

    /**
     * The number of keys defined into the lazyArray.
     *
     * @return int
     */
    public function count()
    {
        return $this->arrayAccessList->count();
    }

    /**
     * The properties are provided as an array. But callers checking the type of this class (is_object === true)
     * think they must use the object syntax.
     *
     * Check if the index exists inside the lazyArray.
     *
     * @param string $index
     *
     * @return bool
     */
    public function __isset($index)
    {
        return $this->offsetExists($index);
    }

    /**
     * The properties are provided as an array. But callers checking the type of this class (is_object === true)
     * think they must use the object syntax.
     *
     * Get the value associated with the $index from the lazyArray.
     *
     * @param mixed $index
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    public function __get($index)
    {
        return $this->offsetGet($index);
    }

    /**
     * The properties are provided as an array. But callers checking the type of this class (is_object === true)
     * think they must use the object syntax.
     *
     * @param mixed $offset
     * @param mixed $value
     * @param bool $force if set, allow override of an existing method
     *
     * @throws RuntimeException
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * The properties are provided as an array. But callers checking the type of this class (is_object === true)
     * think they must use the object syntax.
     *
     * @param mixed $offset
     * @param bool $force if set, allow unset of an existing method
     *
     * @throws RuntimeException
     */
    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    /**
     * Needed to ensure that any changes to this object won't bleed to other instances
     */
    public function __clone()
    {
        $this->arrayAccessList = clone $this->arrayAccessList;
        $this->arrayAccessIterator = clone $this->arrayAccessIterator;
    }

    /**
     * Get the value associated with the $index from the lazyArray.
     *
     * @param mixed $index
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    public function offsetGet($index)
    {
        if (isset($this->arrayAccessList[$index])) {
            // if the index is associated with a method, execute the method an cache the result
            if ($this->arrayAccessList[$index]['type'] === 'method') {
                if (!isset($this->methodCacheResults[$index])) {
                    $methodName = $this->arrayAccessList[$index]['value'];
                    $this->methodCacheResults[$index] = $this->{$methodName}();
                }
                $result = $this->methodCacheResults[$index];
            } else { // if the index is associated with a value, just return the value
                $result = $this->arrayAccessList[$index]['value'];
            }

            return $result;
        }

        return array();
    }

    /**
     * Check if the index exists inside the lazyArray.
     *
     * @param mixed $index
     *
     * @return bool
     */
    public function offsetExists($index)
    {
        return isset($this->arrayAccessList[$index]);
    }

    /**
     * Copy the lazyArray.
     *
     * @return AbstractLazyArray
     */
    public function getArrayCopy()
    {
        return clone $this;
    }

    /**
     * Get the result associated with the current index.
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    public function current()
    {
        $key = $this->arrayAccessIterator->key();

        return $this->offsetGet($key);
    }

    /**
     * Go to the next result inside the lazyArray.
     */
    public function next()
    {
        $this->arrayAccessIterator->next();
    }

    /**
     * Get the key associated with the current index.
     *
     * @return mixed|string
     */
    public function key()
    {
        return $this->arrayAccessIterator->key();
    }

    /**
     * Check if we are at the end of the lazyArray.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->arrayAccessIterator->valid();
    }

    /**
     * Go back to the first element of the lazyArray.
     */
    public function rewind()
    {
        $this->arrayAccessIterator->rewind();
    }

    /**
     * Set the keys not present in the given $array to null.
     *
     * @param array $array
     *
     * @throws RuntimeException
     */
    public function intersectKey($array)
    {
        $arrayCopy = $this->arrayAccessList->getArrayCopy();
        foreach ($arrayCopy as $key => $value) {
            if (!array_key_exists($key, $array)) {
                $this->offsetUnset($key, true);
            }
        }
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @param bool $force if set, allow override of an existing method
     *
     * @throws RuntimeException
     */
    public function offsetSet($offset, $value, $force = false)
    {
        if (!$force && $this->arrayAccessList->offsetExists($offset)) {
            $result = $this->arrayAccessList->offsetGet($offset);
            if ($result['type'] !== 'variable') {
                throw new RuntimeException(
                    'Trying to set the index ' . print_r($offset, true) . ' of the LazyArray ' . get_class($this) .
                    ' already defined by a method is not allowed'
                );
            }
        }
        $this->arrayAccessList->offsetSet($offset, array(
            'type' => 'variable',
            'value' => $value,
        ));
    }

    /**
     * @param mixed $offset
     * @param bool $force if set, allow unset of an existing method
     *
     * @throws RuntimeException
     */
    public function offsetUnset($offset, $force = false)
    {
        $result = $this->arrayAccessList->offsetGet($offset);
        if ($force || $result['type'] === 'variable') {
            $this->arrayAccessList->offsetUnset($offset);
        } else {
            throw new RuntimeException(
                'Trying to unset the index ' . print_r($offset, true) . ' of the LazyArray ' . get_class($this) .
                ' already defined by a method is not allowed'
            );
        }
    }

    /**
     * @param string $methodName
     *
     * @return string
     */
    private function convertMethodNameToIndex($methodName)
    {
        // remove "get" prefix from the function name
        $strippedMethodName = substr($methodName, 3);

        return Inflector::tableize($strippedMethodName);
    }
}
