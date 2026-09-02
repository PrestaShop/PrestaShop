<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Data;

use Doctrine\Common\Collections\ArrayCollection;
use PrestaShop\PrestaShop\Core\Exception\TypeException;

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
     * @throws TypeException
     */
    public function __construct(array $elements = [])
    {
        $this->checkElementsType($elements);
        parent::__construct($elements);
    }

    /**
     * @param mixed $element
     *
     * @return bool
     *
     * @throws TypeException
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
     * @return void
     *
     * @throws TypeException
     */
    public function offsetSet($offset, $value): void
    {
        $this->checkElementType($value);

        parent::offsetSet($offset, $value);
    }

    /**
     * @param mixed $element
     *
     * @return bool
     *
     * @throws TypeException
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
     * @throws TypeException
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
     * @throws TypeException
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
     * @throws TypeException
     */
    public function add($element)
    {
        $this->checkElementType($element);

        return parent::add($element);
    }

    /**
     * @param array $elements
     *
     * @throws TypeException
     */
    private function checkElementsType(array $elements)
    {
        foreach ($elements as $element) {
            $this->checkElementType($element);
        }
    }

    /**
     * @param mixed $element
     *
     * @throws TypeException
     */
    private function checkElementType($element)
    {
        $expectedType = $this->getType();
        if (!($element instanceof $expectedType)) {
            throw new TypeException(sprintf('Invalid element type %s, expected %s', get_debug_type($element), $expectedType));
        }
    }
}
