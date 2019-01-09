<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Data;

use Doctrine\Common\Collections\ArrayCollection;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Class AbstractTypedCollection is an abstract collection class which checks
 * that the inserted elements match the requested type.
 */
abstract class AbstractTypedCollection extends ArrayCollection
{
    /**
     * Define the type of the elements contained in the collection.
     * Example: for a ProductCollection you need to return Product::class
     *
     * @return string
     */
    abstract protected function getType();

    /**
     * AbstractTypedCollection constructor.
     *
     * @param array $elements
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $elements = array())
    {
        $this->checkElementsType($elements);
        parent::__construct($elements);
    }

    /**
     * @param mixed $element
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function removeElement($element)
    {
        $this->checkElementType($element);

        return parent::removeElement($element);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return bool|void
     *
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        $this->checkElementType($value);

        return parent::offsetSet($offset, $value);
    }

    /**
     * @param mixed $element
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function contains($element)
    {
        $this->checkElementType($element);

        return parent::contains($element);
    }

    /**
     * @param mixed $element
     *
     * @return bool|false|int|string
     *
     * @throws InvalidArgumentException
     */
    public function indexOf($element)
    {
        $this->checkElementType($element);

        return parent::indexOf($element);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    public function set($key, $value)
    {
        $this->checkElementType($value);

        parent::set($key, $value);
    }

    /**
     * @param mixed $element
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function add($element)
    {
        $this->checkElementType($element);

        return parent::add($element);
    }

    /**
     * @param array $elements
     *
     * @throws InvalidArgumentException
     */
    protected function checkElementsType(array $elements)
    {
        foreach ($elements as $element) {
            $this->checkElementType($element);
        }
    }

    /**
     * @param mixed $element
     *
     * @throws InvalidArgumentException
     */
    protected function checkElementType($element)
    {
        $expectedType = $this->getType();
        if (!($element instanceof $expectedType)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid element type %s, expected %s',
                get_class($element),
                $expectedType
            ));
        }
    }
}
