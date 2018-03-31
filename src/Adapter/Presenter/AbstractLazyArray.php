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
     * LazyPresenter constructor.
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
     * @param $array
     */
    public function appendArray($array)
    {
        foreach ($array as $key => $value) {
            $this->arrayAccessList->offsetSet(
                $key,
                array(
                    'type' => 'variable',
                    'value' => $value
                )
            );
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->arrayAccessList->count();
    }

    /**
     * @param $methodName
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
     * @param mixed $index
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function offsetGet($index)
    {
        if (isset($this->arrayAccessList[$index])) {
            if ($this->arrayAccessList[$index]['type'] === 'method') {
                $methodName = $this->arrayAccessList[$index]['value'];
                return $this->{$methodName}();
            } else {
                return $this->arrayAccessList[$index]['value'];
            }
        }

        throw new \RuntimeException(
            'Unknown index '.$index.' from LazyPresenter '.get_called_class().'. 
            Make sure the annotation @arrayAccess has properly been added on each methods which should be accessible'
        );
    }

    /**
     * @param mixed $index
     *
     * @return bool
     */
    public function offsetExists($index)
    {
        return isset($this->arrayAccessList[$index]);
    }

    /**
     * @return AbstractLazyArray
     */
    public function getArrayCopy()
    {
        return clone($this);
    }

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function current()
    {
        $key = $this->arrayAccessIterator->key();

        return $this->offsetGet($key);
    }

    /**
     *
     */
    public function next()
    {
        $this->arrayAccessIterator->next();
    }

    /**
     * @return mixed|string
     */
    public function key()
    {
        return $this->arrayAccessIterator->key();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->arrayAccessIterator->valid();
    }

    /**
     *
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
        throw new \RuntimeException(
            'Trying to modify the result of the LazyPresenter '.get_called_class().' is not allowed'
        );
    }

    /**
     * @param mixed $offset
     *
     * @throws \RuntimeException
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException(
            'Trying to modify the result of the LazyPresenter '.get_called_class().' is not allowed'
        );
    }
}
