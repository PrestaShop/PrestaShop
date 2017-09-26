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
namespace PrestaShop\PrestaShop\Core\Form\Collection;

use PrestaShop\PrestaShop\Core\Form\Error;
/**
 * A specific collection to collect Form errors.
 */
final class Errors implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * An array containing the errors of this collection.
     *
     * @var array
     */
    private $errors;

    /**
     * Initializes a new instance.
     *
     * @param array $errors
     */
    private function __construct(array $errors = array())
    {
        $this->errors = $errors;
    }

    /**
     * Creates a new instance from the specified elements.
     *
     * This method is provided for derived classes to specify how a new
     * instance should be created when constructor semantics have changed.
     *
     * @param array $errors Elements.
     *
     * @return static
     */
    public static function createFrom(array $errors)
    {
        return new static($errors);
    }

    /**
     * Retrieve the list of collected error instances.
     *
     * @return array[Error]
     */
    public function all()
    {
        return $this->errors;
    }

    /**
     * Gets a native PHP array representation of the collection.
     *
     * @return array
     */
    public function toArray()
    {
        $errors = array();
        foreach ($this->errors as $error) {
            $errors += $error->toArray();
        }

        return $errors;
    }

    /**
     * @return ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->errors);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Required by ArrayAccess interface.
     *
     * {@inheritdoc}
     */
    public function offsetSet($offset, $error)
    {
        if (!isset($offset)) {
            $this->add($error);
            return;
        }
        $this->set($offset, $error);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * Returns true if the error is found in the collection.
     *
     * @param Error $error the error
     * @return bool
     */
    public function contains(Error $error)
    {
        return in_array($error, $this->errors, true);
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf(Error $error)
    {
        return array_search($error, $this->errors, true);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return $this->errors[$key] ? $this->errors[$key] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys()
    {
        return array_keys($this->errors);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return array_values($this->errors);
    }

    /**
     * Add an error in the collection.
     *
     * @param Error $error the specified error
     * @return bool
     */
    public function add(Error $error)
    {
        $this->errors[] = $error;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return empty($this->errors);
    }

    /**
     * Gets the sum of errors of the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->errors);
    }
}
