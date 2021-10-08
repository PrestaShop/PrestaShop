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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Accessor;

use Symfony\Component\PropertyAccess\PropertyPath;

class CommandField
{
    public const TYPE_STRING = 'string';
    public const TYPE_BOOL = 'bool';
    public const TYPE_INT = 'int';
    public const TYPE_ARRAY = 'array';

    public const ACCEPTED_TYPES = [
        self::TYPE_STRING,
        self::TYPE_BOOL,
        self::TYPE_INT,
        self::TYPE_ARRAY,
    ];

    /**
     * @var PropertyPath
     */
    private $dataPath;

    /**
     * @var string
     */
    private $commandSetter;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $multistoreField;

    /**
     * @param string $dataPath
     * @param string $commandSetter
     * @param string $type
     *
     * @throws InvalidCommandFieldTypeException
     */
    public function __construct(
        string $dataPath,
        string $commandSetter,
        string $type,
        bool $multistoreField
    ) {
        if (!in_array($type, self::ACCEPTED_TYPES)) {
            throw new InvalidCommandFieldTypeException(sprintf(
                'Invalid type "%s" used, only accepted values are: %s',
                $type,
                implode(',', self::ACCEPTED_TYPES)
            ));
        }

        $this->dataPath = new PropertyPath($dataPath);
        $this->commandSetter = $commandSetter;
        $this->type = $type;
        $this->multistoreField = $multistoreField;
    }

    /**
     * @return PropertyPath
     */
    public function getDataPath(): PropertyPath
    {
        return $this->dataPath;
    }

    /**
     * @return string
     */
    public function getCommandSetter(): string
    {
        return $this->commandSetter;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isMultistoreField(): bool
    {
        return $this->multistoreField;
    }
}
