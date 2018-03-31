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

abstract class AbstractLazyPresenter implements \Iterator, \ArrayAccess, \Countable, PresenterInterface
{
    /**
     * @var \ArrayObject
     */
    private $arrayAccessMethods;

    /**
     * @var \ArrayIterator
     */
    private $arrayAccessIterator;

    /**
     * LazyPresenter constructor.
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->arrayAccessMethods = new \ArrayObject();
        $reflexionClass = new \ReflectionClass(get_called_class());
        $methods = $reflexionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $methodDoc = $method->getDocComment();
            if (strpos($methodDoc, '@arrayAccess') !== false) {
                $this->arrayAccessMethods[] = $method->getName();
            }
        }
        $this->arrayAccessIterator = $this->arrayAccessMethods->getIterator();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->arrayAccessMethods->count();
    }

    /**
     * @param $methodName
     *
     * @return string
     */
    private function convertMethodNameToArray($methodName)
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
        $methodName = 'get'.ucfirst($index);
        $methodName = Inflector::camelize($methodName);

        if (method_exists($this, $methodName)) {
            return $this->{$methodName}();
        }

        throw new \RuntimeException(
            'Unknown index '.$index.' associated with method '.$methodName.' from LazyPresenter '.get_called_class().'. 
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
        $methodName = 'get'.ucfirst($index);
        $methodName = Inflector::camelize($methodName);

        return method_exists($this, $methodName);
    }

    /**
     * @return AbstractLazyPresenter
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
        $methodName = $this->arrayAccessIterator->current();
        $indexName = $this->convertMethodNameToArray($methodName);

        return $this->offsetGet($indexName);
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
        $methodName = $this->arrayAccessIterator->current();
        $indexName = $this->convertMethodNameToArray($methodName);

        return $indexName;
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
