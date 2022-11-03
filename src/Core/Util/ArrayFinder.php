<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util;

use ArrayAccess;
use Countable;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * ArrayFinder allows to modify an array content using selectors such as $arrayFinder->get('property_a.property_3.4');
 *
 * This class replaces https://github.com/Shudrum/ArrayFinder/blob/master/ArrayFinder.php that
 * was used in previous PrestaShop versions.
 *
 * Credits to Julien Martin https://github.com/Shudrum for the original class
 */
class ArrayFinder implements ArrayAccess, Countable
{
    /**
     * @var array
     */
    private $array;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @param array $content the array to be searched and manager by ArrayFinder
     */
    public function __construct(array $content = [])
    {
        $this->array = $content;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->array);
    }

    /**
     * @param string|null $path selector to find the desired value. if empty, will return full array
     * @param mixed|null $default default value to be returned if selector matches nothing
     *
     * @return mixed|null
     *
     * Examples of use:
     * $arrayFinder->get('a');
     * $arrayFinder->get('a.e.9');
     * $arrayFinder->get('4');
     */
    public function get(string $path = null, $default = null)
    {
        if ($path === null) {
            return $this->array;
        }
        $path = $this->convertDotPathToArrayPath($path);

        try {
            $value = $this->propertyAccessor->getValue($this->array, $path);
        } catch (UnexpectedTypeException $e) {
            // If a value within the path is neither object nor array
            return null;
        }

        if (null !== $value) {
            return $value;
        }

        return $default;
    }

    /**
     * @param string $path selector for the value to be set
     * @param mixed $value input value to be set inside array
     *
     * @return self
     *
     * Examples of use:
     * $arrayFinder->set('a', $aNewValue);
     * $arrayFinder->set('a.e.9', $aNewValue);
     * $arrayFinder->set('4', $aNewValue);
     */
    public function set(string $path, $value): self
    {
        $this->propertyAccessor->setValue(
            $this->array,
            $this->convertDotPathToArrayPath($path),
            $value
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * Example of use: isset($this->arrayFinder['a']
     */
    public function offsetExists($offset): bool
    {
        if (is_int($offset)) {
            $offset = (string) $offset;
        }

        return $this->propertyAccessor->isReadable(
                $this->array,
                $this->convertDotPathToArrayPath($offset))
            && ($this->get($offset) !== null)
        ;
    }

    /**
     * {@inheritdoc}
     *
     * Examples of use:
     * $arrayFinder[4];
     * $arrayFinder['a'];
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (is_int($offset)) {
            $offset = (string) $offset;
        }

        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     *
     * Example of use: $this->arrayFinder['a'] = $value;
     */
    public function offsetSet($offset, $value): void
    {
        if (is_int($offset)) {
            $offset = (string) $offset;
        }

        if ($offset === null) {
            $this->array[] = $value;
        } else {
            $this->set($offset, $value);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Example of use: unset($this->arrayFinder['a']);
     */
    public function offsetUnset($offset): void
    {
        if (is_int($offset)) {
            $offset = (string) $offset;
        }

        $this->set($offset, null);
    }

    /**
     * Converts paths following format 'dot' structure a.4.9.d.10
     * to Symfony format [a][4][9][d][10]
     *
     * @param string $dotPath
     *
     * @return string
     */
    private function convertDotPathToArrayPath(string $dotPath): string
    {
        if ($dotPath === '[]') {
            return '[0]';
        }

        $expl = explode('.', $dotPath);
        $in = implode('][', $expl);

        return str_replace('[]', '[0]', '[' . $in . ']');
    }
}
