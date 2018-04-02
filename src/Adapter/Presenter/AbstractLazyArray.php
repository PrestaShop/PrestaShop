<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Presenter;

use Doctrine\Common\Util\Inflector;

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
 *
 */
abstract class AbstractLazyArray implements \Iterator, \ArrayAccess, \Countable
{
    /**
     * @var \ArrayObject
     */
    private $arrayAccessList;

    /**
     * @var \ArrayIterator
     */
    private $arrayAccessIterator;

    /**
     * @var array
     */
    private $methodCacheResults = array();

    /**
     * AbstractLazyArray constructor.
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->arrayAccessList = new \ArrayObject();
        $reflexionClass = new \ReflectionClass(get_called_class());
        $methods = $reflexionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $methodDoc = $method->getDocComment();
            if (strpos($methodDoc, '@arrayAccess') !== false) {
                $this->arrayAccessList[$this->convertMethodNameToIndex($method->getName())] =
                    array(
                        'type' => 'method',
                        'value' => $method->getName()
                    );
            }
        }
        $this->arrayAccessIterator = $this->arrayAccessList->getIterator();
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
                        'value' => $value
                    )
                );
            }
        }
    }

    /**
     * The number of keys defined into the lazyArray
     *
     * @return int
     */
    public function count()
    {
        return $this->arrayAccessList->count();
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

    /**
     * Get the value associated with the $index from the lazyArray
     *
     * @param mixed $index
     *
     * @return mixed
     * @throws \RuntimeException
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
                return $this->methodCacheResults[$index];
            } else { // if the index is associated with a value, just return the value
                return $this->arrayAccessList[$index]['value'];
            }
        }

        throw new \RuntimeException(
            'Unknown index '.$index.' from LazyArray '.get_called_class().'. 
            Make sure the annotation @arrayAccess has properly been added on each methods which should be accessible'
        );
    }

    /**
     * Check if the index exists inside the lazyArray
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
     * Copy the lazyArray
     *
     * @return AbstractLazyArray
     */
    public function getArrayCopy()
    {
        return clone($this);
    }

    /**
     * Get the result associated with the current index
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function current()
    {
        $key = $this->arrayAccessIterator->key();

        return $this->offsetGet($key);
    }

    /**
     * Go to the next result inside the lazyArray
     */
    public function next()
    {
        $this->arrayAccessIterator->next();
    }

    /**
     * Get the key associated with the current index
     *
     * @return mixed|string
     */
    public function key()
    {
        return $this->arrayAccessIterator->key();
    }

    /**
     * Check if we are at the end of the lazyArray
     *
     * @return bool
     */
    public function valid()
    {
        return $this->arrayAccessIterator->valid();
    }

    /**
     * Go back to the first element of the lazyArray
     */
    public function rewind()
    {
        $this->arrayAccessIterator->rewind();
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @throws \RuntimeException
     */
    public function offsetSet($offset, $value)
    {
        if ($this->arrayAccessList->offsetExists($offset)) {
            $result = $this->arrayAccessList->offsetGet($offset);
            if ($result['type'] !== 'variable') {
                throw new \RuntimeException(
                    'Trying to set the index '.$offset.' of the LazyArray '.get_called_class().
                    ' already defined by a method is not allowed'
                );
            }
        }
        $this->arrayAccessList->offsetSet($offset, array(
            'type' => 'variable',
            'value' => $value
        ));
    }

    /**
     * @param mixed $offset
     *
     * @throws \RuntimeException
     */
    public function offsetUnset($offset)
    {
        $result = $this->arrayAccessList->offsetGet($offset);
        if ($result['type'] === 'variable') {
            $this->arrayAccessList->offsetUnset($offset);
        } else {
            throw new \RuntimeException(
                'Trying to unset the index '.$offset.' of the LazyArray '.get_called_class().
                ' already defined by a method is not allowed'
            );
        }
    }
}
