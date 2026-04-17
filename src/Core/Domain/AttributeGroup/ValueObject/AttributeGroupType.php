<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\InvalidAttributeGroupTypeException;

/**
 * Defines Attribute group type with its constraints.
 */
class AttributeGroupType
{
    public const ATTRIBUTE_GROUP_TYPE_SELECT = 'select';
    public const ATTRIBUTE_GROUP_TYPE_RADIO = 'radio';
    public const ATTRIBUTE_GROUP_TYPE_COLOR = 'color';

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     *
     * @throws InvalidAttributeGroupTypeException
     */
    public function __construct(string $type)
    {
        $this->assertTypeExists($type);

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return void
     *
     * @throws InvalidAttributeGroupTypeException
     */
    private function assertTypeExists(string $type): void
    {
        $types = [static::ATTRIBUTE_GROUP_TYPE_COLOR, static::ATTRIBUTE_GROUP_TYPE_SELECT, static::ATTRIBUTE_GROUP_TYPE_RADIO];
        if (!in_array($type, $types)) {
            throw new InvalidAttributeGroupTypeException(
                sprintf(
                    'Invalid attributeGroup type %s supplied.',
                    $type
                )
            );
        }
    }
}
