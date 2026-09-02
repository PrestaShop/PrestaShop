<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder;

use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * Description of a field to fetch in any data: path, type, default value.
 * If no value is found with the given path, then the default value is used instead.
 * Example:
 * new DataField('[foo][bar]', DataField::TYPE_STRING);
 * new DataField('[foo][bar]', DataField::TYPE_STRING, 'default value');
 */
class DataField
{
    public const TYPE_STRING = 'string';
    public const TYPE_BOOL = 'bool';
    public const TYPE_INT = 'int';
    public const TYPE_ARRAY = 'array';
    public const TYPE_DATETIME = 'datetime';

    public const ACCEPTED_TYPES = [
        self::TYPE_STRING,
        self::TYPE_BOOL,
        self::TYPE_INT,
        self::TYPE_ARRAY,
        self::TYPE_DATETIME,
    ];

    /**
     * @var PropertyPath
     */
    private $propertyPath;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $hasDefaultValue = false;

    /**
     * @var mixed
     */
    private $defaultValue;

    /**
     * A default value has to be provided explicitly as 3rd argument of the constructor,
     * otherwise the field has no default value.
     *
     * @throws DataFieldException
     */
    public function __construct(string $path, string $type, $defaultValue = null)
    {
        if (!in_array($type, static::ACCEPTED_TYPES)) {
            throw new DataFieldException(sprintf(
                'Invalid type "%s" used, only accepted values are: %s',
                $type,
                implode(',', static::ACCEPTED_TYPES)
            ));
        }
        if (2 < func_num_args()) {
            $this->setDefaultValue($defaultValue);
        }
        $this->propertyPath = new PropertyPath($path);
        $this->type = $type;
    }

    public function getPropertyPath(): PropertyPath
    {
        return $this->propertyPath;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    /**
     * @return mixed
     *
     * @throws DataFieldException
     */
    public function getDefaultValue()
    {
        if ($this->hasDefaultValue()) {
            return $this->defaultValue;
        }
        throw new DataFieldException('Cannot return undefined default value');
    }

    /**
     * @param mixed $defaultValue
     */
    protected function setDefaultValue($defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        $this->hasDefaultValue = true;

        return $this;
    }
}
