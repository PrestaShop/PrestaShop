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

/**
 * Config that gives information to the CommandAccessor component to correctly check the data and prefill the command
 * object, each data property is associated to a setter in the command and its appropriate type for casting. Example:
 *
 * $config = new CommandAccessorConfig('modify_all_');
 * $config
 *     ->addField('[name]', 'setName', CommandField::TYPE_STRING)
 *     ->addField('[command][isValid]', 'setIsValid', CommandField::TYPE_BOOL)
 *     ->addField('[_number]', 'setCount', CommandField::TYPE_INT)
 *     ->addField('[parent][children]', 'setChildren', CommandField::TYPE_ARRAY)
 * ;
 */
class CommandAccessorConfig
{
    /**
     * @var string
     */
    private $modifyAllNamePrefix;

    /**
     * @var CommandField[]
     */
    private $fields = [];

    /**
     * @param string $modifyAllNamePrefix
     */
    public function __construct(string $modifyAllNamePrefix = '')
    {
        $this->modifyAllNamePrefix = $modifyAllNamePrefix;
    }

    /**
     * @param string $propertyPath
     * @param string $commandSetter
     * @param string $propertyType
     *
     * @return $this
     *
     * @throws InvalidCommandFieldTypeException
     */
    public function addField(string $propertyPath, string $commandSetter, string $propertyType): self
    {
        $this->fields[] = new CommandField(
            $propertyPath,
            $commandSetter,
            $propertyType,
            false
        );

        return $this;
    }

    /**
     * @param string $propertyPath
     * @param string $commandSetter
     * @param string $propertyType
     *
     * @return $this
     *
     * @throws InvalidCommandFieldTypeException
     */
    public function addMultiStoreField(string $propertyPath, string $commandSetter, string $propertyType): self
    {
        $this->fields[] = new CommandField(
            $propertyPath,
            $commandSetter,
            $propertyType,
            true
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getModifyAllNamePrefix(): string
    {
        return $this->modifyAllNamePrefix;
    }

    /**
     * @return array<int, CommandField>
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
