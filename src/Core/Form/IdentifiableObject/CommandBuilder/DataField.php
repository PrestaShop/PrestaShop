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
