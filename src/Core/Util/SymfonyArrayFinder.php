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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Util;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class SymfonyArrayFinder implements \ArrayAccess, \Countable
{
    /**
     * @var array
     */
    private $array;
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    public function __construct(array $content = [])
    {
        $this->array = $content;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->array);
    }

    private function convertDotPathToArrayPath(string $dotPath): string
    {
        if ($dotPath === '[]') {
            return '[0]';
        }

        $expl = explode('.', $dotPath);
        $in = implode('][', $expl);

        return '[' . $in . ']';
    }

    /**
     * @param string $path
     * @param mixed $default
     *
     * @return mixed|null
     */
    public function get(string $path = null, $default = null)
    {
        if ($path === null) {
            return $this->array;
        }
        $path = $this->convertDotPathToArrayPath($path);

        $value = $this->propertyAccessor->getValue($this->array, $path);

        if (null !== $value) {
            return $value;
        }

        return $default;
    }

    /**
     * @param string $path
     * @param mixed $value
     *
     * @return $this
     */
    public function set(string $path, $value): SymfonyArrayFinder
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
     */
    public function offsetExists($offset)
    {
        return $this->propertyAccessor->isReadable(
            $this->array,
            $this->convertDotPathToArrayPath($offset)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->array[] = $value;
        } else {
            $this->set($offset, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        return $this->set($offset, null);
    }
}
